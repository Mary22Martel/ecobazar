<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('users')->updateOrInsert(
            ['email' => 'cliente@example.com'],
            [
                'name' => 'Cliente',
                'password' => Hash::make('password'),
                'role' => 'cliente',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        Schema::enableForeignKeyConstraints();
    }
}
