<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public array $games = [
        ['name' => 'Bomb', 'title' => 'بمب'],
        ['name' => 'FireFighter', 'title' => 'آتش نشان'],
        ['name' => 'SaviourGame', 'title' => 'نجات دهنده'],
    ];

    public function run(): void
    {
        foreach ($this->games as $game) {
            Game::create([
                'name' => $game['name'],
                'title' => $game['title'],
            ]);
        }
    }
}
