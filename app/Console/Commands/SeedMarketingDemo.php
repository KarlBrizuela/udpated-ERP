<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedMarketingDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:marketing {--force : Run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed demo data for the Marketing dashboard (customers, books, sales orders)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will insert demo marketing data into the database. Continue?')) {
                $this->info('Cancelled.');
                return 1;
            }
        }

        $this->info('Seeding marketing demo data...');
        try {
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\MarketingDemoSeeder']);
            $this->info(trim(Artisan::output()));
            $this->info('Done.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Seeder failed: ' . $e->getMessage());
            return 1;
        }
    }
}
