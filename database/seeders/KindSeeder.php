<?php

namespace Database\Seeders;

use App\Models\Kind;
use Illuminate\Database\Seeder;

class KindSeeder extends Seeder
{
    //seed the kinds table
    public function run(): void
    {
        $kinds = [
            [
                'name' => 'movie',
            ],
            [
                'name' => 'tv',
            ],
            [
                'name' => 'book',
            ],
            [
                'name' => 'anime'
            ],
            [
                'name' => 'game',
            ],
        ];

        foreach ($kinds as $kind) {
            Kind::create($kind);
        }
    }

}
