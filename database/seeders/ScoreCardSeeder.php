<?php

namespace Database\Seeders;

use App\Models\ScoreCard;
use Illuminate\Database\Seeder;

class ScoreCardSeeder extends Seeder
{
    static array $scoreCards = [];

    public function run(): void
    {
        // skip header
        array_shift(self::$scoreCards);

        $header = [
            'score',
            'count',
            'name'
        ];

        foreach (self::$scoreCards as $row) {
            $row = array_combine($header, $row);
            if ($row['count'] == 1) {
                ScoreCard::create([
                    'name' => $row['name'],
                    'score' => $row['score'],
                ]);
            } else if ($row['count'] > 1) {
                for ($i = 1; $i <= $row['count']; $i++) {
                    ScoreCard::create([
                        'name' => $row['name'] . ' ' . $i,
                        'score' => $row['score'],
                    ]);
                }
            }
        }
    }
}
