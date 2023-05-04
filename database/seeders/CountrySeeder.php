<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //some countries and ISO_3166_1 codes

        $countries = [
            ['iso_3166_1' => 'AE', 'name' => 'United Arab Emirates'],
            ['iso_3166_1' => 'AR', 'name' => 'Argentina'],
            ['iso_3166_1' => 'AT', 'name' => 'Austria'],
            ['iso_3166_1' => 'AU', 'name' => 'Australia'],
            ['iso_3166_1' => 'BE', 'name' => 'Belgium'],
            ['iso_3166_1' => 'BR', 'name' => 'Brazil'],
            ['iso_3166_1' => 'CA', 'name' => 'Canada'],
            ['iso_3166_1' => 'CH', 'name' => 'Switzerland'],
        ];

        foreach ($countries as $country) {
            \App\Models\Country::create($country);
        }

    }
}
