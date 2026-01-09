<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TelegramController;
use GuzzleHttp\Client;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling';
    protected $description = 'Run Telegram bot with long polling for localhost testing';

    private $botToken;
    private $offset = 0;
    private $client;

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
            'verify' => false, // For localhost, set to true in production
        ]);
    }

    public function handle()
    {
        $this->info('Telegram bot polling started. Press Ctrl+C to stop.');
        $this->info('Bot Token: ' . substr($this->botToken, 0, 15) . '...');
        
        // Remove webhook first
        try {
            $this->client->get("deleteWebhook");
            $this->info('Webhook deleted successfully.');
        } catch (\Exception $e) {
            $this->error('Error deleting webhook: ' . $e->getMessage());
        }
        
        $controller = new TelegramController();
        
        while (true) {
            try {
                $response = $this->client->get("getUpdates", [
                    'query' => [
                        'offset' => $this->offset,
                        'timeout' => 30
                    ]
                ]);

                $body = json_decode($response->getBody()->getContents(), true);
                $updates = $body['result'] ?? [];

                foreach ($updates as $update) {
                    $this->info('Processing update: ' . $update['update_id']);
                    
                    // Create a mock request
                    $request = new \Illuminate\Http\Request();
                    $request->replace($update);
                    
                    // Call the webhook method
                    $controller->webhook($request);
                    
                    $this->offset = $update['update_id'] + 1;
                }

                usleep(100000); // 0.1 second delay
            } catch (\Exception $e) {
                $this->error('Error: ' . $e->getMessage());
                sleep(5);
            }
        }
    }
}
