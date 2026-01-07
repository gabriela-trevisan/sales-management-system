<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomerSegmentSeeder::class,
            ProductCategorySeeder::class,
            PipelineStageSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            OpportunitySeeder::class,
            ProposalSeeder::class,
        ]);
    }
}
