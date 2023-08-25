<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\models\SubInterest;

class SubInterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sub_interests = [
            [
                'interest_id' => 1, // 1=>Earth
                'name' => 'Tree Plantation'
            ],
            [
                'interest_id' => 1,
                'name' => 'Solid Waste Management'
            ],
            [
                'interest_id' => 1,
                'name' => 'Environment Friendly Products'
            ],
            [
                'interest_id' => 1,
                'name' => 'Consumption Management'
            ],
            [
                'interest_id' => 2,  // 2=>Fire 
                'name' => 'Electricity Conservation'
            ],
            [
                'interest_id' => 2,
                'name' => 'Non-Recyclable Fuel Conservation'
            ],
            [
                'interest_id' => 2,
                'name' => 'Renewable Energy Sourcing'
            ],
            [
                'interest_id' => 2,
                'name' => 'Alternative Energy Sources'
            ],
            [
                'interest_id' => 3, // 3=>Water 
                'name' => 'Waste Water Management'
            ],
            [
                'interest_id' => 3,
                'name' => 'Water Consumption Reduction'
            ],
            [
                'interest_id' => 3,
                'name' => 'Water Body Rejuvenation'
            ],
            [
                'interest_id' => 3,
                'name' => 'Water Body Cleanup'
            ],
            [
                'interest_id' => 4, // 4=>Air 
                'name' => 'Air Pollution Reduction'
            ],
            [
                'interest_id' => 4,
                'name' => 'Carbon Sequestration'
            ],
            [
                'interest_id' => 4,
                'name' => 'Exhaust Air Filtration'
            ],
            [
                'interest_id' => 5, // 5=>Soul
                'name' => 'Wildlife Conservation'
            ],
            [
                'interest_id' => 5,
                'name' => 'Help Climate Change Impacted Communities'
            ],
            [
                'interest_id' => 5,
                'name' => 'Animal Life Conservation'
            ],
            [
                'interest_id' => 5,
                'name' => 'Indigenous Communities'
            ],
            [
                'interest_id' => 5,
                'name' => 'Learning & Development'
            ],
        ];

        SubInterest::insert($sub_interests);
    }
    
}
