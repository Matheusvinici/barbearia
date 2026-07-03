<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'push:generate-keys';
    protected $description = 'Generate VAPID keys for WebPush';

    public function handle(): void
    {
        $keys = \Minishlink\WebPush\VAPID::createVapidKeys();

        $this->info('Add these to your .env file:');
        $this->line("VAPID_PUBLIC_KEY={$keys['publicKey']}");
        $this->line("VAPID_PRIVATE_KEY={$keys['privateKey']}");
    }
}
