<x-filament-panels::page>
    <div class="space-y-8">
        
        <!-- Header con estadísticas principales mejorado -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <!-- Total de Ventas -->
            <div class="bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700 dark:from-emerald-600 dark:via-emerald-700 dark:to-emerald-800 rounded-2xl p-6 text-black shadow-xl transform hover:scale-105 transition-all duration-300 ring-1 ring-emerald-500/20">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="p-2 bg-white/20 dark:bg-black/10 rounded-lg backdrop-blur-sm border border-white/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <span class="text-emerald-100 dark:text-emerald-200 text-sm font-medium">Total de Ventas</span>
                        </div>
                        @php
                            // Calcular total de ventas directamente en la vista
                            $totalVentas = 0;
                            try {
                                $query = \App\Models\OrderItem::query()
                                    ->join('productos', 'order_items.producto_id', '=', 'productos.id')
                                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                                    ->join('users', 'productos.user_id', '=', 'users.id')
                                    ->where('users.role', 'agricultor');

                                if ($this->fecha_inicio && $this->fecha_fin) {
                                    $query->whereBetween('orders.created_at', [
                                        \Carbon\Carbon::parse($this->fecha_inicio)->startOfDay(),
                                        \Carbon\Carbon::parse($this->fecha_fin)->endOfDay()
                                    ]);
                                }

                                if ($this->estado_filtro && $this->estado_filtro !== 'todos') {
                                    $query->where('orders.estado', $this->estado_filtro);
                                }

                                $items = $query->get();
                                $totalVentas = $items->sum(function ($item) {
                                    return $item->precio * $item->cantidad;
                                });
                            } catch (\Exception $e) {
                                $totalVentas = 0;
                            }
                        @endphp
                        <p class="text-3xl font-bold mb-1">S/ {{ number_format($totalVentas, 2) }}</p>
                        <p class="text-emerald-200 text-xs">En el período seleccionado</p>
                    </div>
                </div>
            </div>

            <!-- Agricultores Activos -->
            <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 dark:from-blue-600 dark:via-blue-700 dark:to-blue-800 rounded-2xl p-6 text-black shadow-xl transform hover:scale-105 transition-all duration-300 ring-1 ring-blue-500/20">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="p-2 bg-black/20 dark:bg-black/10 rounded-lg backdrop-blur-sm border border-white/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="text-blue-100 dark:text-blue-200 text-sm font-medium">Agricultores</span>
                        </div>
                        @php
                            // Calcular número de agricultores con ventas
                            $numAgricultores = 0;
                            try {
                                $numAgricultores = \App\Models\User::query()
                                    ->where('role', 'agricultor')
                                    ->whereHas('productos', function ($query) {
                                        $query->whereHas('orderItems', function ($subQuery) {
                                            $subQuery->whereHas('order', function ($orderQuery) {
                                                if ($this->fecha_inicio && $this->fecha_fin) {
                                                    $orderQuery->whereBetween('created_at', [
                                                        \Carbon\Carbon::parse($this->fecha_inicio)->startOfDay(),
                                                        \Carbon\Carbon::parse($this->fecha_fin)->endOfDay()
                                                    ]);
                                                }
                                                if ($this->estado_filtro && $this->estado_filtro !== 'todos') {
                                                    $orderQuery->where('estado', $this->estado_filtro);
                                                }
                                            });
                                        });
                                    })
                                    ->count();
                            } catch (\Exception $e) {
                                $numAgricultores = 0;
                            }
                        @endphp
                        <p class="text-3xl font-bold mb-1">{{ $numAgricultores }}</p>
                        <p class="text-blue-200 text-xs">Con ventas activas</p>
                    </div>
                </div>
            </div>

            <!-- Promedio por Agricultor -->
            <div class="bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 dark:from-purple-600 dark:via-purple-700 dark:to-purple-800 rounded-2xl p-6 text-black shadow-xl transform hover:scale-105 transition-all duration-300 ring-1 ring-purple-500/20">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="p-2 bg-white/20 dark:bg-black/10 rounded-lg backdrop-blur-sm border border-black/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <span class="text-purple-100 dark:text-purple-200 text-sm font-medium">Promedio</span>
                        </div>
                        @php
                            $promedio = $numAgricultores > 0 ? $totalVentas / $numAgricultores : 0;
                        @endphp
                        <p class="text-3xl font-bold mb-1">S/ {{ number_format($promedio, 2) }}</p>
                        <p class="text-purple-200 text-xs">Por agricultor</p>
                    </div>
                </div>
            </div>

            <!-- Estado Actual -->
            <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 dark:from-amber-600 dark:via-amber-700 dark:to-amber-800 rounded-2xl p-6 text-black shadow-xl transform hover:scale-105 transition-all duration-300 ring-1 ring-amber-500/20">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="p-2 bg-black/20 dark:bg-black/10 rounded-lg backdrop-blur-sm border border-white/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-amber-100 dark:text-amber-200 text-sm font-medium">Estado</span>
                        </div>
                        <p class="text-lg font-bold mb-1">
                            @php
                                $estadoLabels = [
                                    'armado' => '✅ Armado',
                                    'todos' => '📋 Todos',
                                    'pagado' => '💰 Pagado',
                                    'entregado' => '🚚 Entregado'
                                ];
                            @endphp
                            {{ $estadoLabels[$this->estado_filtro] ?? ucfirst($this->estado_filtro) }}
                        </p>
                        <p class="text-amber-200 text-xs">Filtrando pedidos</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Información del período actual mejorada -->
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-gray-800 dark:to-gray-900 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl flex items-center justify-center shadow-lg ring-1 ring-blue-500/20">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-gray-200 mb-1">
                        📅 Período: {{ \Carbon\Carbon::parse($this->fecha_inicio)->format('d/m/Y') }} 
                        - {{ \Carbon\Carbon::parse($this->fecha_fin)->format('d/m/Y') }}
                    </h3>
                    <div class="flex flex-wrap gap-4 text-sm text-slate-600 dark:text-gray-400">
                        <span class="flex items-center space-x-1">
                            <span class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full"></span>
                            <span>Solo productos vendidos</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <span class="w-2 h-2 bg-blue-500 dark:bg-blue-400 rounded-full"></span>
                            <span>Sin costos de delivery</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <span class="w-2 h-2 bg-purple-500 dark:bg-purple-400 rounded-full"></span>
                            <span>Sin comisiones</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla principal con estilos mejorados -->
        <div class="bg-black dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-slate-200 dark:border-gray-700">
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-gray-800 dark:to-gray-900 px-6 py-4 border-b border-slate-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-slate-800 dark:text-gray-200 flex items-center space-x-2">
                    <span class="text-2xl">👨‍🌾</span>
                    <span>Lista de Pagos a Agricultores</span>
                </h2>
                <p class="text-sm text-slate-600 dark:text-gray-400 mt-1">Gestiona los pagos pendientes y completados</p>
            </div>
            
            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>
        
    </div>
    
    <style>
        /* Estilos personalizados para mejorar la experiencia en modo claro y oscuro */
        .fi-ta-table {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .fi-ta-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-weight: 600;
        }
        
        .dark .fi-ta-header {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }
        
        .fi-ta-row:hover {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
        }
        
        .dark .fi-ta-row:hover {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .fi-badge {
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 4px 8px;
        }
        
        .fi-btn {
            transition: all 0.2s ease-in-out;
            border-radius: 8px;
        }
        
        .fi-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .dark .fi-btn:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }
        
        /* Mejoras para los iconos en modo oscuro */
        .dark .fi-ta-icon {
            filter: brightness(1.2);
        }
        
        /* Mejor contraste para badges en modo oscuro */
        .dark .fi-badge-info {
            background-color: rgb(59 130 246 / 0.8);
            color: rgb(191 219 254);
        }
        
        .dark .fi-badge-warning {
            background-color: rgb(245 158 11 / 0.8);
            color: rgb(254 240 138);
        }
        
        .dark .fi-badge-success {
            background-color: rgb(34 197 94 / 0.8);
            color: rgb(187 247 208);
        }
        
        .dark .fi-badge-secondary {
            background-color: rgb(107 114 128 / 0.8);
            color: rgb(209 213 219);
        }
        
        /* Animaciones suaves para cambios de tema */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        
        /* Mejor legibilidad para texto en modo oscuro */
        .dark .fi-ta-text {
            color: rgb(229 231 235);
        }
        
        .dark .fi-ta-text-secondary {
            color: rgb(156 163 175);
        }
        
        /* Sombras más suaves en modo oscuro */
        .dark .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Hover effects mejorados para cards */
        .transform:hover {
            transform: translateY(-2px) scale(1.02);
        }
        
        /* Ring effects para mejor focus */
        .ring-1 {
            box-shadow: 0 0 0 1px var(--tw-ring-color);
        }
    </style>
</x-filament-panels::page>