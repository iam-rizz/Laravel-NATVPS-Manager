<?php

namespace App\Console\Commands;

use App\Services\CronService;
use Illuminate\Console\Command;

class CronHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cron heartbeat timestamp';

    /**
     * Execute the console command.
     */
    public function handle(CronService $cronService): int
    {
        $cronService->updateHeartbeat();
        
        $this->info('Cron heartbeat updated.');
        
        return self::SUCCESS;
    }
}
