<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    private $botToken;
    private $botUsername;
    private $client;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->botUsername = env('TELEGRAM_BOT_USERNAME');
        
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

    public function initiateBind()
    {
        $user = Auth::user();
        
        // Check if already bound
        if ($user->telegram_id) {
            return redirect()->back()->with('error', 'Your account is already bound to Telegram');
        }

        // Generate unique binding token
        $token = Str::random(32);
        
        // Store token with user ID and phone for 10 minutes
        Cache::put("telegram_bind_{$token}", [
            'user_id' => $user->id,
            'user_phone' => $user->phone // Assuming user has phone in database
        ], now()->addMinutes(10));

        // Redirect to Telegram bot with start parameter
        $deepLink = "https://t.me/{$this->botUsername}?start=bind_{$token}";
        
        return redirect($deepLink);
    }

    public function unbind()
    {
        $user = Auth::user();
        
        // Check if account is bound
        if (!$user->telegram_id) {
            return redirect()->back()->with('error', 'Your account is not bound to any Telegram account');
        }

        // Send notification to Telegram before unbinding
        if ($user->telegram_chat_id) {
            try {
                $this->sendUnbindNotification($user->telegram_chat_id, $user->name ?? 'User');
            } catch (\Exception $e) {
                Log::error('Failed to send unbind notification: ' . $e->getMessage());
            }
        }

        // Store data for display
        $telegramId = $user->telegram_id;
        
        // Remove Telegram binding
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

  public function webhook(Request $request)
{
    $update = $request->all();

    // Handle messages
    if (isset($update['message']['text'])) {
        $text = $update['message']['text'];
        $chatId = $update['message']['chat']['id'];
        $telegramUserId = $update['message']['from']['id'];

        // Check if it's a start command with bind parameter
        if (preg_match('/\/start bind_(.+)/', $text, $matches)) {
            $token = $matches[1];
            $this->handleBinding($chatId, $telegramUserId, $token);
        }
        // Handle /start command
        elseif (trim($text) === '/start') {
            $this->handleStart($chatId, $telegramUserId);
        }
        // Handle /unbind command from bot
        elseif (trim($text) === '/unbind') {
            $this->handleBotUnbind($chatId, $telegramUserId);
        }
        // Handle /status command
        elseif (trim($text) === '/status') {
            $this->handleStatus($chatId, $telegramUserId);
        }
        // Handle /help command
        elseif (trim($text) === '/help') {
            $this->handleHelp($chatId);
        }
    }
    // Handle phone number contact sharing
    elseif (isset($update['message']['contact'])) {
        $chatId = $update['message']['chat']['id'];
        $telegramUserId = $update['message']['from']['id'];
        $contact = $update['message']['contact'];
        
        // Verify it's the user's own phone number
        if ($contact['user_id'] == $telegramUserId) {
            $phoneNumber = $contact['phone_number'];
            $this->verifyPhoneNumber($chatId, $telegramUserId, $phoneNumber);
        } else {
            $this->sendMessage($chatId, '❌ Please share your own phone number, not someone else\'s.');
        }
    }

    return response()->json(['ok' => true]);
}

/**
 * Handle /start command
 */
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
        // Find user by telegram_id
        $user = User::where('telegram_id', $telegramUserId)->first();

        if (!$user) {
            $this->sendMessage($chatId, '❌ No account is bound to this Telegram account.');
            return;
        }

        // Store user info for notification
        $userName = $user->name ?? 'User';

        // Unbind the account
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
        // Find user by telegram_id
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
            $message .= "📱 Phone: " . ($user->phone ?? 'N/A') . "\n\n";
            $message .= "To unbind, use /unbind command or visit the website.";
        }

        $this->sendMessage($chatId, $message);
    }

    private function handleBinding($chatId, $telegramUserId, $token)
    {
        // Retrieve binding data from cache
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

        // Check if this Telegram account is already bound to another user
        $existingUser = User::where('telegram_id', $telegramUserId)->first();
        if ($existingUser) {
            $this->sendMessage($chatId, '❌ This Telegram account is already bound to another user.');
            return;
        }

        // Store pending verification
        Cache::put("telegram_verify_{$telegramUserId}", [
            'user_id' => $bindingData['user_id'],
            'expected_phone' => $bindingData['user_phone'] ?? null
        ], now()->addMinutes(10));

        // Request phone number with custom keyboard
        $this->requestPhoneNumber($chatId, !empty($bindingData['user_phone']));

        // Delete the binding token
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

        // Check if user has a phone number registered
        if (empty($verificationData['expected_phone']) || empty($user->phone)) {
            // No phone number in account - save the provided phone and bind
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

        // Normalize phone numbers for comparison (remove + and spaces)
        $normalizedReceived = preg_replace('/[^0-9]/', '', $phoneNumber);
        $normalizedExpected = preg_replace('/[^0-9]/', '', $verificationData['expected_phone']);

        // Check if phone numbers match
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

        // Phone number verified - bind the account
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

        // Clear the verification cache
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


    /**
 * Handle help command
 */
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
    $message .= "• Real-time attendance updates\n";
    $message .= "• Account binding with phone verification\n\n";
    $message .= "<b>Need Help?</b>\n";
    $message .= "Contact your administrator for support.";

    $this->sendMessage($chatId, $message);
}
}