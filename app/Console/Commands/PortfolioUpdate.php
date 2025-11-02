<?php

namespace App\Console\Commands;

use App\Services\PortfolioUpdateService;
use Illuminate\Console\Command;

class PortfolioUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portfolio:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the risk portfolio';

    /**
     * Execute the console command.
     */
    public function handle(PortfolioUpdateService $service)
    {
        //
        $service->update();
        $this->info('Risk portfolio updated successfully');
    }
}
