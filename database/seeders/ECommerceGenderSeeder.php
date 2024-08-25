<?php

namespace Database\Seeders;

use App\Models\ECommerce\ECommerceGender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ECommerceGenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ECommerceGender::create(['name' => 'Men']);
        ECommerceGender::create(['name' => 'Women']);
        ECommerceGender::create(['name' => 'Kids']);
    }
}
