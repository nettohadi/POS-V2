<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Ayam Goreng', 'desc' => 'Ayam Goreng','type_id' => 1, 'created_at' => Carbon::now()],
            ['name' => 'Kentang Goreng', 'desc' => 'Kentang Goreng','type_id' => 1, 'created_at' => Carbon::now()],
            ['name' => 'Es Lemon Tea', 'desc' => 'Es Lemon','type_id' => 2, 'created_at' => Carbon::now()],
            ['name' => 'Paket Geprek 1', 'desc' => 'Paket Ayam Geprek','type_id' => 5, 'created_at' => Carbon::now()],
            ['name' => 'Paket Sehat 1', 'desc' => 'Paket Menu Sehat','type_id' => 5, 'created_at' => Carbon::now()],
            ['name' => 'Kotak Nasi', 'desc' => 'Kotak Nasi','type_id' => 6, 'created_at' => Carbon::now()],
        ];

        Category::insert($categories);
    }
}
