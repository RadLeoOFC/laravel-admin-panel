<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desk;

class DeskSeeder extends Seeder
{
    public function run()
    {
        Desk::factory(1)->create();
    }
}

