<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductionErpIntegrationService;

class SyncProductionCosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:production-costing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically pull production costing snapshot parameters from WordPress Production ERP (CCFI)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ProductionErpIntegrationService $integrationService)
    {
        $this->info('Starting sync with WordPress Production ERP (http://erpccfi.claretianpublications.ph)...');

        $result = $integrationService->syncCostings();

        if ($result['success']) {
            $this->info("✓ " . $result['message']);
            return Command::SUCCESS;
        } else {
            $this->warn("⚠ " . $result['message']);
            return Command::FAILURE;
        }
    }
}
