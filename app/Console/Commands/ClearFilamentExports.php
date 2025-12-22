<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearFilamentExports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-filament-exports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all Filament export files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (Storage::directoryExists('filament_exports')) {
            Storage::deleteDirectory('filament_exports');
            $this->info('✅ Filament exports directory cleared successfully.');
        } else {
            $this->info('⚠️ Filament exports directory does not exist.');
        }
    }
}
