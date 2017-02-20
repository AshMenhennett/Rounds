<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearCompiledViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the compiled blade views.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = glob(storage_path() . '/framework/views/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->info('Compiled views have been cleared');
    }
}
