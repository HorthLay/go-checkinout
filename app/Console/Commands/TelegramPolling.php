<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TelegramController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling {--reset : Reset offset to start from beginning}';
    protected $description = 'Run Telegram bot with long polling for localhost testing';

    private $botToken;
    private $offset = 0;
    private $client;
    private $isRunning = true;

    public function __construct()
    {
        parent::__construct();
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        
        // Validate bot token
        if (empty($this->botToken) || !preg_match('/^\d+:[A-Za-z0-9_-]+$/', $this->botToken)) {
            $this->error('Invalid TELEGRAM_BOT_TOKEN format in .env file');
            exit(1);
        }
        
        $this->client = new Client([
            'base_uri' => "https://api.telegram.org/bot{$this->botToken}/",
            'timeout' => 35,
            'verify' => true, // Set to false for localhost testing if needed
            'http_errors' => false,
        ]);
    }

    public function handle()
    {
        // Register signal handlers for graceful shutdown
        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
            pcntl_signal(SIGINT, [$this, 'handleShutdown']);
        }

        $this->displayBanner();
        
        // Remove webhook first
        $this->removeWebhook();
        
        // Verify bot information
        if (!$this->verifyBot()) {
            $this->error('Failed to verify bot. Please check your bot token.');
            return 1;
        }

        // Reset offset if requested
        if ($this->option('reset')) {
            $this->offset = 0;
            $this->info('Offset reset to 0');
        }
        
        $controller = new TelegramController();
        $updateCount = 0;
        $errorCount = 0;
        
        $this->info('🤖 Bot is now listening for updates...');
        $this->line('');
        
        while ($this->isRunning) {
            try {
                // Process signals
                if (extension_loaded('pcntl')) {
                    pcntl_signal_dispatch();
                }

                $response = $this->client->get("getUpdates", [
                    'query' => [
                        'offset' => $this->offset,
                        'timeout' => 30,
                        'allowed_updates' => ['message', 'callback_query']
                    ]
                ]);

                if ($response->getStatusCode() !== 200) {
                    $this->warn('API returned status code: ' . $response->getStatusCode());
                    sleep(5);
                    continue;
                }

                $body = json_decode($response->getBody()->getContents(), true);
                
                if (!isset($body['ok']) || !$body['ok']) {
                    $this->error('API returned error: ' . ($body['description'] ?? 'Unknown error'));
                    sleep(5);
                    continue;
                }

                $updates = $body['result'] ?? [];

                foreach ($updates as $update) {
                    $updateCount++;
                    $updateId = $update['update_id'];
                    
                    $this->processUpdate($update, $controller, $updateCount);
                    
                    // Update offset to next update
                    $this->offset = $updateId + 1;
                    
                    // Reset error count on successful update
                    $errorCount = 0;
                }

                // Small delay between polling
                usleep(100000); // 0.1 second
                
            } catch (GuzzleException $e) {
                $errorCount++;
                $this->error("❌ Guzzle Error ({$errorCount}): " . $e->getMessage());
                
                // Log the error
                Log::error('Telegram polling error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Exponential backoff on errors
                $sleepTime = min(pow(2, $errorCount), 60);
                $this->warn("Retrying in {$sleepTime} seconds...");
                sleep($sleepTime);
                
                // Reset error count after too many errors
                if ($errorCount > 10) {
                    $this->error('Too many errors. Resetting...');
                    $errorCount = 0;
                }
                
            } catch (\Exception $e) {
                $this->error('❌ Exception: ' . $e->getMessage());
                Log::error('Telegram polling exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                sleep(5);
            }
        }
        
        $this->info('');
        $this->info('🛑 Bot stopped gracefully');
        $this->info("📊 Total updates processed: {$updateCount}");
        
        return 0;
    }

    /**
     * Display startup banner
     */
    private function displayBanner()
    {
        $this->line('');
        $this->line('╔════════════════════════════════════════════════════╗');
        $this->line('║          🤖 Telegram Bot Polling Active          ║');
        $this->line('╚════════════════════════════════════════════════════╝');
        $this->line('');
        $this->info('📡 Status: Starting up...');
        $this->info('🔑 Bot Token: ' . substr($this->botToken, 0, 20) . '...');
        $this->info('⚠️  Press Ctrl+C to stop');
        $this->line('');
    }

    /**
     * Remove webhook to enable polling
     */
    private function removeWebhook()
    {
        try {
            $response = $this->client->post("deleteWebhook", [
                'json' => ['drop_pending_updates' => true]
            ]);
            
            $body = json_decode($response->getBody()->getContents(), true);
            
            if ($body['ok']) {
                $this->info('✅ Webhook deleted successfully');
            } else {
                $this->warn('⚠️  Could not delete webhook: ' . ($body['description'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('❌ Error deleting webhook: ' . $e->getMessage());
        }
    }

    /**
     * Verify bot credentials
     */
    private function verifyBot()
    {
        try {
            $response = $this->client->get("getMe");
            $body = json_decode($response->getBody()->getContents(), true);
            
            if ($body['ok']) {
                $bot = $body['result'];
                $this->info('✅ Bot verified: @' . $bot['username']);
                $this->info('   Name: ' . $bot['first_name']);
                $this->info('   ID: ' . $bot['id']);
                $this->line('');
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            $this->error('Bot verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process a single update
     */
    private function processUpdate($update, $controller, $updateCount)
    {
        $updateId = $update['update_id'];
        $timestamp = now()->format('H:i:s');
        
        // Extract update information
        $updateType = 'unknown';
        $from = 'N/A';
        $chatId = 'N/A';
        $text = '';
        
        if (isset($update['message'])) {
            $updateType = 'message';
            $message = $update['message'];
            $from = $message['from']['username'] ?? $message['from']['first_name'] ?? 'Unknown';
            $chatId = $message['chat']['id'];
            
            if (isset($message['text'])) {
                $text = $message['text'];
            } elseif (isset($message['contact'])) {
                $text = '📱 Contact: ' . $message['contact']['phone_number'];
            } else {
                $text = '[' . (array_keys($message)[0] ?? 'unknown') . ']';
            }
        } elseif (isset($update['callback_query'])) {
            $updateType = 'callback';
            $from = $update['callback_query']['from']['username'] ?? 
                    $update['callback_query']['from']['first_name'] ?? 'Unknown';
            $chatId = $update['callback_query']['message']['chat']['id'];
            $text = $update['callback_query']['data'];
        }
        
        // Display update info
        $this->line("─────────────────────────────────────────────────────");
        $this->info("📨 Update #{$updateCount} [{$timestamp}]");
        $this->line("   ID: {$updateId}");
        $this->line("   Type: {$updateType}");
        $this->line("   From: @{$from}");
        $this->line("   Chat: {$chatId}");
        
        if ($text) {
            $displayText = strlen($text) > 50 ? substr($text, 0, 50) . '...' : $text;
            $this->line("   Text: {$displayText}");
        }
        
        try {
            // Create a mock request
            $request = new \Illuminate\Http\Request();
            $request->replace($update);
            
            // Call the webhook method
            $controller->webhook($request);
            
            $this->comment("   ✓ Processed successfully");
            
        } catch (\Exception $e) {
            $this->error("   ✗ Processing failed: " . $e->getMessage());
            Log::error('Update processing failed', [
                'update_id' => $updateId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle shutdown signals
     */
    public function handleShutdown($signal)
    {
        $this->isRunning = false;
        $this->line('');
        $this->warn('⚠️  Shutdown signal received, stopping gracefully...');
    }
}