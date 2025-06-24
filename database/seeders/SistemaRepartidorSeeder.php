<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\Hash;

class SistemaRepartidorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o encontrar el repartidor del sistema
        $repartidorSistema = User::firstOrCreate(
            ['email' => 'sistema.repartidor@puntoVerde.com'],
            [
                'name' => 'Repartidor Sistema',
                'password' => Hash::make('sistema123'),
                'role' => 'repartidor',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Asignar todas las zonas al repartidor del sistema
        $todasLasZonas = Zone::all();
        
        foreach ($todasLasZonas as $zona) {
            $repartidorSistema->zones()->syncWithoutDetaching([
                $zona->id => [
                    'fecha_asignacion' => now()->toDateString(),
                    'activa' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

        $this->command->info("Repartidor del sistema creado con ID: {$repartidorSistema->id}");
        $this->command->info("Zonas asignadas: " . $todasLasZonas->count());
    }
}