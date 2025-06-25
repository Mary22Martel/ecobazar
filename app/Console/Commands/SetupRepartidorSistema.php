<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SetupRepartidorSistema extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'repartidor:setup {--migrate-orders : Migrar pedidos existentes al repartidor del sistema}';

    /**
     * The console command description.
     */
    protected $description = 'Configura el sistema de repartidores y crea el repartidor del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Configurando sistema de repartidores...');
        
        try {
            DB::beginTransaction();
            
            // 1. Crear repartidor del sistema
            $this->crearRepartidorSistema();
            
            // 2. Asignar todas las zonas al repartidor del sistema
            $this->asignarZonasAlSistema();
            
            // 3. Migrar pedidos existentes si se solicita
            if ($this->option('migrate-orders')) {
                $this->migrarPedidosExistentes();
            }
            
            // 4. Mostrar resumen
            $this->mostrarResumen();
            
            DB::commit();
            
            $this->info('✅ Sistema de repartidores configurado exitosamente!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error configurando sistema: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function crearRepartidorSistema()
    {
        $this->info('📦 Creando repartidor del sistema...');
        
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
        
        if ($repartidorSistema->wasRecentlyCreated) {
            $this->line("✓ Repartidor del sistema creado con ID: {$repartidorSistema->id}");
        } else {
            $this->line("✓ Repartidor del sistema ya existe con ID: {$repartidorSistema->id}");
        }
        
        return $repartidorSistema;
    }
    
    private function asignarZonasAlSistema()
    {
        $this->info('🗺️  Asignando zonas al repartidor del sistema...');
        
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
        $todasLasZonas = Zone::where('active', true)->get();
        
        if ($todasLasZonas->isEmpty()) {
            $this->warn('⚠️  No se encontraron zonas activas. Creando zona de ejemplo...');
            
            Zone::create([
                'name' => 'Huánuco Centro',
                'delivery_cost' => 5.00,
                'active' => true
            ]);
            
            $todasLasZonas = Zone::where('active', true)->get();
        }
        
        $asignacionesData = [];
        foreach ($todasLasZonas as $zona) {
            $asignacionesData[$zona->id] = [
                'fecha_asignacion' => now()->toDateString(),
                'activa' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Sincronizar zonas (no duplicar)
        $repartidorSistema->zones()->syncWithoutDetaching($asignacionesData);
        
        $this->line("✓ {$todasLasZonas->count()} zonas asignadas al repartidor del sistema");
    }
    
    private function migrarPedidosExistentes()
    {
        $this->info('📋 Migrando pedidos existentes al repartidor del sistema...');
        
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
        
        // Buscar pedidos sin repartidor asignado
        $pedidosSinRepartidor = Order::whereNull('repartidor_id')
                                    ->orWhere('repartidor_id', 0)
                                    ->count();
        
        if ($pedidosSinRepartidor > 0) {
            Order::whereNull('repartidor_id')
                 ->orWhere('repartidor_id', 0)
                 ->update(['repartidor_id' => $repartidorSistema->id]);
            
            $this->line("✓ {$pedidosSinRepartidor} pedidos migrados al repartidor del sistema");
        } else {
            $this->line("✓ No hay pedidos que migrar");
        }
    }
    
    private function mostrarResumen()
    {
        $this->info('📊 Resumen del sistema:');
        
        $repartidorSistema = User::where('email', 'sistema.repartidor@puntoVerde.com')->first();
        $totalZonas = Zone::where('active', true)->count();
        $totalPedidosSistema = Order::where('repartidor_id', $repartidorSistema->id)->count();
        $repartidoresReales = User::where('role', 'repartidor')
                                 ->where('email', '!=', 'sistema.repartidor@puntoVerde.com')
                                 ->count();
        
        $this->table([
            'Concepto', 'Valor'
        ], [
            ['Repartidor Sistema ID', $repartidorSistema->id],
            ['Email Sistema', $repartidorSistema->email],
            ['Zonas Activas', $totalZonas],
            ['Pedidos en Sistema', $totalPedidosSistema],
            ['Repartidores Reales', $repartidoresReales],
        ]);
        
        $this->newLine();
        $this->line('🎯 Próximos pasos:');
        $this->line('1. Accede a /admin/repartidores para gestionar asignaciones');
        $this->line('2. Crea usuarios con rol "repartidor" si necesitas más repartidores');
        $this->line('3. Los pedidos nuevos se asignarán automáticamente al sistema');
        $this->line('4. Cada sábado, asigna zonas a repartidores reales');
    }
}