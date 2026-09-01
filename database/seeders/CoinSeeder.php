<?php

namespace Database\Seeders;

use App\Models\Coin;
use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    static array $coins = [];

    public function run(): void
    {
        // skip header
        array_shift(self::$coins);

        $header = [
            'coin',
            'count',
            'name'
        ];

        foreach (self::$coins as $row) {
            $row = array_combine($header, $row);
            if ($row['count'] == 1) {
                Coin::create([
                    'name' => $row['name'],
                    'coin' => $row['coin'],
                ]);
            } else if ($row['count'] > 1) {
                for ($i = 1; $i <= $row['count']; $i++) {
                    Coin::create([
                        'name' => $row['name'] . ' ' . $i,
                        'coin' => $row['coin'],
                    ]);
                }
            }
        }
    }
}
