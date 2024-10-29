<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AgricultorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('users')->updateOrInsert(
            ['email' => 'agricultor@example.com'],
            [
                'name' => 'Agricultor',
                'password' => Hash::make('password'),
                'role' => 'agricultor',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        Schema::enableForeignKeyConstraints();
    }
}
