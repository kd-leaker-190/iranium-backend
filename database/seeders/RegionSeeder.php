<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    static array $regions = [];

    public function run(): void
    {
        // skip header
        array_shift(self::$regions);

        $header = [
            'id',
            'name',
            'lockable',
        ];

        $order = 1;

        foreach (self::$regions as $region) {
            $region = array_combine($header, $region);

            Region::create([
                'id' => $region['id'],
                'name' => $region['name'],
                'order' => $order,
                'lockable' => $region['lockable'],
            ]);

            $order++;
        }
    }
}
