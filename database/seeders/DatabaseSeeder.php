<?php

namespace Database\Seeders;

use App\Imports\OldDataImport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
        ]);

        $sheets = Excel::toCollection(new OldDataImport(), public_path('excel/data.xlsx'));
        RegionSeeder::$regions = $sheets[0]->toArray();
        ActionSeeder::$actions = $sheets[1]->toArray();
        TasksSeeder::$tasks = $sheets[2]->toArray();
        CoinSeeder::$coins = $sheets[3]->toArray();
        ScoreCardSeeder::$scoreCards = $sheets[4]->toArray();

        $this->call([
            AdminUserSeeder::class,
            TeamsSeeder::class,
            RegionSeeder::class,
            ActionSeeder::class,
            TasksSeeder::class,
            CoinSeeder::class,
            ScoreCardSeeder::class,
            GameSeeder::class,
        ]);
    }
}
