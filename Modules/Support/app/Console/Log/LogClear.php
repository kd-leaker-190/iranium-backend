<?php

namespace Modules\Support\Console\Log;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LogClear extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'log:clear';

    /**
     * The console command description.
     */
    protected $description = 'Clear laravel.log file';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            $this->info('Log file cleared.');
        } else {
            $this->info('Log file does not exist.');
        }
    }
}
