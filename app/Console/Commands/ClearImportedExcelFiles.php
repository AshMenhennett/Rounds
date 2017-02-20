<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearImportedExcelFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:imports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the excel imports directory.';

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
        $files = glob(storage_path() . '/import/excel/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->info('Imported excel views have been cleared');
    }
}
