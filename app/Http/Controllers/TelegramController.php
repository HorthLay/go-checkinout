<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\User;
use App\Services\TelegramService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    private $botToken;
    private $botUsername;
    private $client;
    protected $telegramService;

    public function __construct(TelegramService $telegramService = null)
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->botUsername = env('TELEGRAM_BOT_USERNAME');
        $this->telegramService = $telegramService ?? app(TelegramService::class);
        
        // Validate bot token format
        if (empty($this->botToken) || !preg_match('/^\d+:[A-Za-z0-9_-]+$/', $this->botToken)) {
            throw new \Exception('Invalid TELEGRAM_BOT_TOKEN format in .env file');
        }
        
        $this->client = new Client([
            'base_uri' => "https://api.telegram.org/bot{$this->botToken}/",
            'timeout' => 30,
            'verify' => false, // For localhost testing, set to true in production
        ]);
    }

    // ============================================
    // ACCOUNT BINDING METHODS
    // ============================================

    public function initiateBind()
    {
        $user = Auth::user();
        
        if ($user->telegram_id) {
            return redirect()->back()->with('error', 'Your account is already bound to Telegram');
        }

        $token = Str::random(32);
        
        Cache::put("telegram_bind_{$token}", [
            'user_id' => $user->id,
            'user_phone' => $user->phone
        ], now()->addMinutes(10));

        $deepLink = "https://t.me/{$this->botUsername}?start=bind_{$token}";
        
        return redirect($deepLink);
    }

    public function unbind()
    {
        $user = Auth::user();
        
        if (!$user->telegram_id) {
            return redirect()->back()->with('error', 'Your account is not bound to any Telegram account');
        }

        if ($user->telegram_chat_id) {
            try {
                $this->sendUnbindNotification($user->telegram_chat_id, $user->name ?? 'User');
            } catch (\Exception $e) {
                Log::error('Failed to send unbind notification: ' . $e->getMessage());
            }
        }

        $user->telegram_id = null;
        $user->telegram_chat_id = null;
        $user->save();

        return redirect()->back()->with('success', 'Your Telegram account has been successfully unbound');
    }

    private function sendUnbindNotification($chatId, $userName)
    {
        $message = "🔓 <b>Account Unbound</b>\n\n";
        $message .= "Your account ({$userName}) has been unbound from this Telegram chat.\n\n";
        $message .= "You will no longer receive notifications from this account.\n\n";
        $message .= "If you want to bind again, please visit the website and click 'Bind Telegram Account'.";

        try {
            $this->client->post("sendMessage", [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error('Telegram unbind notification error: ' . $e->getMessage());
            throw $e;
        }
    }

    // ============================================
    // WEBHOOK HANDLER
    // ============================================

    public function webhook(Request $request)
    {
        Log::info('Telegram webhook received', ['update' => $request->all()]);

        $update = $request->all();

        if (empty($update)) {
            Log::warning('Empty update received');
            return response()->json(['ok' => false, 'error' => 'Empty update']);
        }

        try {
            // Handle callback queries (mission buttons) - PRIORITY!
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
                return response()->json(['ok' => true]);
            }

            // Handle messages
            if (isset($update['message']['text'])) {
                $text = $update['message']['text'];
                $chatId = $update['message']['chat']['id'];
                $telegramUserId = $update['message']['from']['id'];

                if (preg_match('/\/start bind_(.+)/', $text, $matches)) {
                    $token = $matches[1];
                    $this->handleBinding($chatId, $telegramUserId, $token);
                }
                elseif (trim($text) === '/start') {
                    $this->handleStart($chatId, $telegramUserId);
                }
                elseif (trim($text) === '/unbind') {
                    $this->handleBotUnbind($chatId, $telegramUserId);
                }
                elseif (trim($text) === '/status') {
                    $this->handleStatus($chatId, $telegramUserId);
                }
                elseif (trim($text) === '/help') {
                    $this->handleHelp($chatId);
                }
            }
            // Handle phone number contact sharing
            elseif (isset($update['message']['contact'])) {
                $chatId = $update['message']['chat']['id'];
                $telegramUserId = $update['message']['from']['id'];
                $contact = $update['message']['contact'];
                
                if ($contact['user_id'] == $telegramUserId) {
                    $phoneNumber = $contact['phone_number'];
                    $this->verifyPhoneNumber($chatId, $telegramUserId, $phoneNumber);
                } else {
                    $this->sendMessage($chatId, '❌ Please share your own phone number, not someone else\'s.');
                }
            }

        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // ============================================
    // MISSION CALLBACK HANDLER
    // ============================================

    private function handleCallbackQuery($callbackQuery)
    {
        $callbackData = $callbackQuery['data'];
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];

        $admin = User::where('telegram_chat_id', $chatId)
                    ->where('role_type', 'admin')
                    ->first();

        if (!$admin) {
            $this->answerCallbackQuery($callbackQuery['id'], '❌ Unauthorized. Admin access required.');
            return;
        }

        if (preg_match('/^approve_mission_(\d+)$/', $callbackData, $matches)) {
            $this->handleApproveMission($matches[1], $admin, $chatId, $messageId, $callbackQuery['id']);
        } elseif (preg_match('/^reject_mission_(\d+)$/', $callbackData, $matches)) {
            $this->handleRejectMission($matches[1], $admin, $chatId, $messageId, $callbackQuery['id']);
        }
    }

    private function handleApproveMission($missionId, $admin, $chatId, $messageId, $callbackQueryId)
    {
        $mission = Mission::with(['user', 'attendance'])->find($missionId);

        if (!$mission) {
            $this->answerCallbackQuery($callbackQueryId, '❌ Mission not found');
            return;
        }

        if ($mission->status !== 'pending') {
            $this->answerCallbackQuery($callbackQueryId, '⚠️ Mission already ' . $mission->status);
            return;
        }

        try {
            $mission->approve($admin->id);
            $this->updateMissionMessageStatus($chatId, $messageId, $mission, 'approved', $admin->name);
            
            // Notify other admins
            $this->telegramService->notifyAdminsAboutApproval($mission, $admin->name);
            
            $this->answerCallbackQuery($callbackQueryId, '✅ Mission approved successfully!');
            Log::info("Mission #{$missionId} approved by admin {$admin->id} via Telegram");

        } catch (\Exception $e) {
            Log::error("Error approving mission via Telegram: " . $e->getMessage());
            $this->answerCallbackQuery($callbackQueryId, '❌ Error approving mission');
        }
    }

    private function handleRejectMission($missionId, $admin, $chatId, $messageId, $callbackQueryId)
    {
        $mission = Mission::with(['user', 'attendance'])->find($missionId);

        if (!$mission) {
            $this->answerCallbackQuery($callbackQueryId, '❌ Mission not found');
            return;
        }

        if ($mission->status !== 'pending') {
            $this->answerCallbackQuery($callbackQueryId, '⚠️ Mission already ' . $mission->status);
            return;
        }

        try {
            $reason = "Rejected via Telegram by admin";
            $mission->reject($admin->id, $reason);
            $this->updateMissionMessageStatus($chatId, $messageId, $mission, 'rejected', $admin->name);
            
            // Notify other admins
            $this->telegramService->notifyAdminsAboutRejection($mission, $admin->name, $reason);
            
            $this->answerCallbackQuery($callbackQueryId, '❌ Mission rejected. You can add a detailed reason on the website.');
            Log::info("Mission #{$missionId} rejected by admin {$admin->id} via Telegram");

        } catch (\Exception $e) {
            Log::error("Error rejecting mission via Telegram: " . $e->getMessage());
            $this->answerCallbackQuery($callbackQueryId, '❌ Error rejecting mission');
        }
    }

    private function updateMissionMessageStatus($chatId, $messageId, $mission, $status, $adminName)
    {
        $statusEmoji = $status === 'approved' ? '✅' : '❌';
        $statusText = ucfirst($status);
        $statusKh = $status === 'approved' ? 'បានអនុម័ត' : 'បានបដិសេធ';

        $updatedMessage = "📋 <b>ការស្នើសុំចូលធ្វើការបេសកកម្ម / MISSION CHECK-IN REQUEST</b>\n";
        $updatedMessage .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $updatedMessage .= "👤 <b>Employee:</b> {$mission->user->name}\n";
        $updatedMessage .= "📅 <b>Date:</b> " . $mission->mission_date->format('F j, Y') . "\n";
        $updatedMessage .= "🕐 <b>Request Time:</b> " . $mission->created_at->format('h:i A') . "\n\n";
        $updatedMessage .= "{$statusEmoji} <b>Status:</b> {$statusKh} / {$statusText}\n";
        $updatedMessage .= "👤 <b>By:</b> {$adminName}\n";
        $updatedMessage .= "🕐 <b>Time:</b> " . now()->format('h:i A') . "\n";
        $updatedMessage .= "\n━━━━━━━━━━━━━━━━━━━━━━";
        $updatedMessage .= "\n<i>Mission ID: #{$mission->id}</i>";

        try {
            Http::post(
                "https://api.telegram.org/bot{$this->botToken}/editMessageText",
                [
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $updatedMessage,
                    'parse_mode' => 'HTML',
                ]
            );
        } catch (\Exception $e) {
            Log::error("Error updating Telegram message: " . $e->getMessage());
        }
    }

    private function answerCallbackQuery($callbackQueryId, $text, $showAlert = true)
    {
        try {
            Http::post(
                "https://api.telegram.org/bot{$this->botToken}/answerCallbackQuery",
                [
                    'callback_query_id' => $callbackQueryId,
                    'text' => $text,
                    'show_alert' => $showAlert,
                ]
            );
        } catch (\Exception $e) {
            Log::error("Error answering callback query: " . $e->getMessage());
        }
    }

    // ============================================
    // BOT COMMAND HANDLERS
    // ============================================

    private function handleStart($chatId, $telegramUserId)
    {
        $user = User::where('telegram_id', $telegramUserId)->first();
        
        if ($user) {
            $message = "👋 <b>Welcome back, {$user->name}!</b>\n\n";
            $message .= "Your account is already bound.\n\n";
            $message .= "📧 Email: {$user->email}\n";
            $message .= "📱 Phone: " . ($user->phone ?? 'N/A') . "\n\n";
            $message .= "Use /status to see your account status\n";
            $message .= "Use /help to see available commands";
        } else {
            $message = "👋 <b>Welcome to Attendify Bot!</b>\n\n";
            $message .= "To get started, please bind your account from the website.\n\n";
            $message .= "📱 Go to your account settings\n";
            $message .= "🔗 Click 'Bind Telegram Account'\n";
            $message .= "✅ Follow the instructions\n\n";
            $message .= "Use /help to see available commands";
        }
        
        $this->sendMessage($chatId, $message);
    }

    private function handleBotUnbind($chatId, $telegramUserId)
    {
        $user = User::where('telegram_id', $telegramUserId)->first();

        if (!$user) {
            $this->sendMessage($chatId, '❌ No account is bound to this Telegram account.');
            return;
        }

        $userName = $user->name ?? 'User';

        $user->telegram_id = null;
        $user->telegram_chat_id = null;
        $user->save();

        $message = "🔓 <b>Account Unbound Successfully</b>\n\n";
        $message .= "Your account ({$userName}) has been unbound from this Telegram chat.\n\n";
        $message .= "✓ Telegram ID removed\n";
        $message .= "✓ Chat ID removed\n\n";
        $message .= "You will no longer receive notifications.\n\n";
        $message .= "To bind again, visit the website and click 'Bind Telegram Account'.";

        $this->sendMessage($chatId, $message, true);
    }

    private function handleStatus($chatId, $telegramUserId)
    {
        $user = User::where('telegram_id', $telegramUserId)->first();

        if (!$user) {
            $message = "ℹ️ <b>Status</b>\n\n";
            $message .= "❌ Not bound to any account\n\n";
            $message .= "To bind an account, visit the website and click 'Bind Telegram Account'.";
        } else {
            $message = "ℹ️ <b>Status</b>\n\n";
            $message .= "✅ Account is bound\n\n";
            $message .= "👤 Name: " . ($user->name ?? 'N/A') . "\n";
            $message .= "📧 Email: " . ($user->email ?? 'N/A') . "\n";
            $message .= "📱 Phone: " . ($user->phone ?? 'N/A') . "\n";
            $message .= "👔 Role: " . ($user->role_type ?? 'user') . "\n\n";
            $message .= "To unbind, use /unbind command or visit the website.";
        }

        $this->sendMessage($chatId, $message);
    }

    private function handleHelp($chatId)
    {
        $message = "🤖 <b>Attendify Bot - Help</b>\n\n";
        $message .= "<b>Available Commands:</b>\n\n";
        $message .= "/start - Start the bot\n";
        $message .= "/status - Check binding status\n";
        $message .= "/unbind - Unbind your account\n";
        $message .= "/help - Show this help message\n\n";
        $message .= "<b>Features:</b>\n";
        $message .= "• Receive check-in/out notifications\n";
        $message .= "• Mission approval (for admins)\n";
        $message .= "• Real-time attendance updates\n";
        $message .= "• Account binding with phone verification\n\n";
        $message .= "<b>Need Help?</b>\n";
        $message .= "Contact your administrator for support.";

        $this->sendMessage($chatId, $message);
    }

    private function handleBinding($chatId, $telegramUserId, $token)
    {
        $bindingData = Cache::get("telegram_bind_{$token}");

        if (!$bindingData) {
            $this->sendMessage($chatId, '❌ Invalid or expired binding link. Please try again from the website.');
            return;
        }

        $user = User::find($bindingData['user_id']);

        if (!$user) {
            $this->sendMessage($chatId, '❌ User not found. Please try again.');
            return;
        }

        $existingUser = User::where('telegram_id', $telegramUserId)->first();
        if ($existingUser) {
            $this->sendMessage($chatId, '❌ This Telegram account is already bound to another user.');
            return;
        }

        Cache::put("telegram_verify_{$telegramUserId}", [
            'user_id' => $bindingData['user_id'],
            'expected_phone' => $bindingData['user_phone'] ?? null
        ], now()->addMinutes(10));

        $this->requestPhoneNumber($chatId, !empty($bindingData['user_phone']));
        Cache::forget("telegram_bind_{$token}");
    }

    private function requestPhoneNumber($chatId, $hasPhone = true)
    {
        $keyboard = [
            'keyboard' => [
                [
                    [
                        'text' => '📱 Share Phone Number',
                        'request_contact' => true
                    ]
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $message = "🔐 To complete the binding process, please confirm by sharing your phone number.\n\n";
        
        if ($hasPhone) {
            $message .= "This will verify your identity and link your account.\n\n";
        } else {
            $message .= "This will update your account with your phone number and complete the binding.\n\n";
        }
        
        $message .= "Click the button below to share your phone number.";

        try {
            $this->client->post("sendMessage", [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'reply_markup' => $keyboard
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error('Telegram API Error: ' . $e->getMessage());
        }
    }

    private function verifyPhoneNumber($chatId, $telegramUserId, $phoneNumber)
    {
        $verificationData = Cache::get("telegram_verify_{$telegramUserId}");

        if (!$verificationData) {
            $this->sendMessage($chatId, '❌ No pending verification found. Please start the binding process again from the website.', true);
            return;
        }

        $user = User::find($verificationData['user_id']);
        
        if (!$user) {
            $this->sendMessage($chatId, '❌ User not found. Please try again.', true);
            return;
        }

        if (empty($verificationData['expected_phone']) || empty($user->phone)) {
            $user->phone = $phoneNumber;
            $user->telegram_id = $telegramUserId;
            $user->telegram_chat_id = $chatId;
            $user->save();

            $this->sendMessage(
                $chatId, 
                "✅ Success! Your account has been successfully bound to Telegram.\n\n" .
                "✓ Phone number saved: {$phoneNumber}\n" .
                "✓ Account linked\n\n" .
                "You can now receive notifications and updates through this chat.",
                true
            );

            Cache::forget("telegram_verify_{$telegramUserId}");
            return;
        }

        $normalizedReceived = preg_replace('/[^0-9]/', '', $phoneNumber);
        $normalizedExpected = preg_replace('/[^0-9]/', '', $verificationData['expected_phone']);

        if ($normalizedReceived !== $normalizedExpected) {
            $this->sendMessage(
                $chatId, 
                "❌ Phone number mismatch. The phone number you shared doesn't match your account.\n\n" .
                "Expected: {$verificationData['expected_phone']}\n" .
                "Received: {$phoneNumber}\n\n" .
                "Please use the same phone number registered in your account or contact support to update your phone number.",
                true
            );
            return;
        }

        $user->telegram_id = $telegramUserId;
        $user->telegram_chat_id = $chatId;
        $user->save();

        $this->sendMessage(
            $chatId, 
            "✅ Success! Your account has been successfully bound to Telegram.\n\n" .
            "✓ Phone verified: {$phoneNumber}\n" .
            "✓ Account linked\n\n" .
            "You can now receive notifications and updates through this chat.",
            true
        );

        Cache::forget("telegram_verify_{$telegramUserId}");
    }

    private function sendMessage($chatId, $text, $removeKeyboard = false)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        if ($removeKeyboard) {
            $params['reply_markup'] = ['remove_keyboard' => true];
        }

        try {
            $this->client->post("sendMessage", [
                'json' => $params
            ]);
        } catch (GuzzleException $e) {
            Log::error('Telegram API Error: ' . $e->getMessage());
        }
    }

    // ============================================
    // WEBHOOK MANAGEMENT
    // ============================================

    public function setupWebhook()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $webhookUrl = env('APP_URL') . '/api/telegram/webhook';
        
        if (empty($botToken)) {
            return response()->json([
                'success' => false,
                'message' => 'TELEGRAM_BOT_TOKEN not set in .env file'
            ], 500);
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                'url' => $webhookUrl,
                'allowed_updates' => ['message', 'callback_query'],
                'drop_pending_updates' => true,
                'max_connections' => 40,
            ]);

            $result = $response->json();

            if ($result['ok']) {
                $infoResponse = Http::get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
                $info = $infoResponse->json();

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook set successfully!',
                    'webhook_url' => $webhookUrl,
                    'webhook_info' => $info['result'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to set webhook',
                    'error' => $result['description'] ?? 'Unknown error'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function webhookInfo()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        
        if (empty($botToken)) {
            return response()->json([
                'success' => false,
                'message' => 'TELEGRAM_BOT_TOKEN not set in .env file'
            ], 500);
        }

        try {
            $response = Http::get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
            $result = $response->json();

            if ($result['ok']) {
                $info = $result['result'];
                
                return response()->json([
                    'success' => true,
                    'webhook_info' => [
                        'url' => $info['url'] ?? 'Not set',
                        'has_custom_certificate' => $info['has_custom_certificate'] ?? false,
                        'pending_update_count' => $info['pending_update_count'] ?? 0,
                        'max_connections' => $info['max_connections'] ?? 40,
                        'allowed_updates' => $info['allowed_updates'] ?? [],
                        'last_error_date' => isset($info['last_error_date']) ? date('Y-m-d H:i:s', $info['last_error_date']) : null,
                        'last_error_message' => $info['last_error_message'] ?? null,
                        'ip_address' => $info['ip_address'] ?? null,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get webhook info',
                    'error' => $result['description'] ?? 'Unknown error'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeWebhook()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        
        if (empty($botToken)) {
            return response()->json([
                'success' => false,
                'message' => 'TELEGRAM_BOT_TOKEN not set in .env file'
            ], 500);
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/deleteWebhook", [
                'drop_pending_updates' => true
            ]);

            $result = $response->json();

            if ($result['ok']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook removed successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove webhook',
                    'error' => $result['description'] ?? 'Unknown error'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}