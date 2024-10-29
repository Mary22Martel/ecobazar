<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RepartidorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('users')->updateOrInsert(
            ['email' => 'repartidor@example.com'],
            [
                'name' => 'Repartidor',
                'password' => Hash::make('password'),
                'role' => 'repartidor',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        Schema::enableForeignKeyConstraints();
    }
}
