<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\models\Interest;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            ['name' => 'Earth'],
            ['name' => 'Fire'],
            ['name' => 'Water'],
            ['name' => 'Air'],
            ['name' => 'Soul']            
        ];

        Interest::insert($interests);
    }
}
