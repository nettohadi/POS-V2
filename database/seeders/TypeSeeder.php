<?php

namespace Database\Seeders;

use App\Models\Type;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['name' => 'Makanan', 'desc' => 'Jenis Makanan', 'created_at' => Carbon::now()],
            ['name' => 'Minuman', 'desc' => 'Jenis Minuman', 'created_at' => Carbon::now()],
            ['name' => 'Bahan Baku', 'desc' => 'Jenis Bahan Baku', 'created_at' => Carbon::now()],
            ['name' => 'Lain - Lain', 'desc' => 'Jenis Lain Lain', 'created_at' => Carbon::now()],
            ['name' => 'Paket', 'desc' => 'Jenis Paket Makanan & Minuman', 'created_at' => Carbon::now()],
            ['name' => 'Kemasan', 'desc' => 'Jenis Kemasan', 'created_at' => Carbon::now()],
        ];

        Type::insert($types);
    }
}
