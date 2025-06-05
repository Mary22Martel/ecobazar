<x-filament-panels::page>
    <div class="space-y-6">
        
        @php
            // Calcular toda la data necesaria
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

            $items = $query->with(['product', 'order'])->get();
            
            // KPIs principales
            $totalVentas = $items->sum(function ($item) { return $item->precio * $item->cantidad; });
            $totalCantidad = $items->sum('cantidad');
            $totalPedidos = $items->pluck('order_id')->unique()->count();
            $numAgricultores = $items->pluck('product.user_id')->unique()->count();
            $promedioCompraPorPedido = $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0;
            
            // Análisis de productos
            $productosStats = $items->groupBy('producto_id')->map(function($grupo) {
                $producto = $grupo->first()->product;
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                    'cantidad' => $grupo->sum('cantidad'),
                    'monto' => $grupo->sum(function($item) { return $item->precio * $item->cantidad; }),
                    'pedidos' => $grupo->pluck('order_id')->unique()->count(),
                    'precio_promedio' => $grupo->avg('precio')
                ];
            })->sortByDesc('cantidad');
            
            $top3Productos = $productosStats->take(3);
            $bottom3Productos = $productosStats->reverse()->take(3);
            
            // Análisis de agricultores
            $agricultoresStats = $items->groupBy('product.user_id')->map(function($grupo) {
                $agricultor = $grupo->first()->product->user;
                return [
                    'id' => $agricultor->id,
                    'nombre' => $agricultor->name,
                    'monto' => $grupo->sum(function($item) { return $item->precio * $item->cantidad; }),
                    'cantidad' => $grupo->sum('cantidad'),
                    'pedidos' => $grupo->pluck('order_id')->unique()->count(),
                    'productos_diferentes' => $grupo->pluck('producto_id')->unique()->count()
                ];
            })->sortByDesc('monto');
            
            $topAgricultor = $agricultoresStats->first();
            $bottomAgricultor = $agricultoresStats->last();
            
            // Agricultores sin ventas
            $agricultoresSinVentas = \App\Models\User::where('role', 'agricultor')
                ->whereNotIn('id', $agricultoresStats->pluck('id'))
                ->count();
            
            // Análisis por categorías
            $categoriaStats = $items->groupBy('product.categoria.nombre')->map(function($grupo, $categoria) {
                return [
                    'categoria' => $categoria ?: 'Sin categoría',
                    'monto' => $grupo->sum(function($item) { return $item->precio * $item->cantidad; }),
                    'cantidad' => $grupo->sum('cantidad'),
                    'productos' => $grupo->pluck('producto_id')->unique()->count()
                ];
            })->sortByDesc('monto');
            
            // Productos sin ventas
            $productosSinVentas = \App\Models\Product::whereNotIn('id', $productosStats->pluck('id'))->count();
            
            // Análisis temporal (si tenemos datos de períodos anteriores)
            $fechaInicioAnterior = \Carbon\Carbon::parse($this->fecha_inicio)->subWeek();
            $fechaFinAnterior = \Carbon\Carbon::parse($this->fecha_fin)->subWeek();
            
            $itemsAnterior = \App\Models\OrderItem::query()
                ->join('productos', 'order_items.producto_id', '=', 'productos.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('users', 'productos.user_id', '=', 'users.id')
                ->where('users.role', 'agricultor')
                ->whereBetween('orders.created_at', [
                    $fechaInicioAnterior->startOfDay(),
                    $fechaFinAnterior->endOfDay()
                ]);
                
            if ($this->estado_filtro && $this->estado_filtro !== 'todos') {
                $itemsAnterior->where('orders.estado', $this->estado_filtro);
            }
            
            $itemsAnterior = $itemsAnterior->get();
            $ventasAnterior = $itemsAnterior->sum(function ($item) { return $item->precio * $item->cantidad; });
            $crecimiento = $ventasAnterior > 0 ? (($totalVentas - $ventasAnterior) / $ventasAnterior) * 100 : 0;
        @endphp

        <!-- KPIs Principales - Fila Superior -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Total de Ventas -->
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-black shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="text-sm font-medium opacity-90">Total Ventas</span>
                        </div>
                        <p class="text-2xl font-bold">S/ {{ number_format($totalVentas, 2) }}</p>
                        @if($crecimiento != 0)
                            <p class="text-xs opacity-75 mt-1">
                                @if($crecimiento > 0)
                                    📈 +{{ number_format($crecimiento, 1) }}% vs anterior
                                @else
                                    📉 {{ number_format($crecimiento, 1) }}% vs anterior
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Total Productos -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-black shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span class="text-sm font-medium opacity-90">Productos</span>
                        </div>
                        <p class="text-2xl font-bold">{{ number_format($totalCantidad) }}</p>
                        <p class="text-xs opacity-75">{{ $productosStats->count() }} diferentes</p>
                    </div>
                </div>
            </div>

            <!-- Agricultores -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-black shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm font-medium opacity-90">Agricultores</span>
                        </div>
                        <p class="text-2xl font-bold">{{ $numAgricultores }}</p>
                        @if($agricultoresSinVentas > 0)
                            <p class="text-xs opacity-75">⚠️ {{ $agricultoresSinVentas }} sin ventas</p>
                        @else
                            <p class="text-xs opacity-75">✅ Todos activos</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pedidos -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-black shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="text-sm font-medium opacity-90">Pedidos</span>
                        </div>
                        <p class="text-2xl font-bold">{{ number_format($totalPedidos) }}</p>
                        <p class="text-xs opacity-75">S/ {{ number_format($promedioCompraPorPedido, 2) }} por pedido</p>
                    </div>
                </div>
            </div>

            <!-- Estado Filtro -->
            <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl p-4 text-black shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span class="text-sm font-medium opacity-90">Filtro</span>
                        </div>
                        @php
                            $estadoLabels = [
                                'armado' => '✅ Armado',
                                'todos' => '📋 Todos',
                                'pagado' => '💰 Pagado',
                                'entregado' => '🚚 Entregado'
                            ];
                        @endphp
                        <p class="text-lg font-bold">{{ $estadoLabels[$this->estado_filtro] ?? ucfirst($this->estado_filtro) }}</p>
                        <p class="text-xs opacity-75">{{ \Carbon\Carbon::parse($this->fecha_inicio)->diffInDays(\Carbon\Carbon::parse($this->fecha_fin)) + 1 }} días</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis de Productos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Top 3 Productos Más Vendidos -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">🏆 Top 3 Productos Más Vendidos</h3>
                </div>
                <div class="space-y-3">
                    @foreach($top3Productos as $index => $producto)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="flex items-center justify-center w-8 h-8 bg-green-500 text-black rounded-full font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $producto['nombre'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $producto['categoria'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600 dark:text-green-400">{{ number_format($producto['cantidad']) }} Und</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">S/ {{ number_format($producto['monto'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top 3 Productos Menos Vendidos -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">📉 Productos con Pocas Ventas</h3>
                </div>
                <div class="space-y-3">
                    @foreach($bottom3Productos as $index => $producto)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="flex items-center justify-center w-8 h-8 bg-red-500 text-black rounded-full font-bold text-sm">
                                    !
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $producto['nombre'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $producto['categoria'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-red-600 dark:text-red-400">{{ number_format($producto['cantidad']) }} Und</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">S/ {{ number_format($producto['monto'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                    @if($productosSinVentas > 0)
                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                ⚠️ <strong>{{ $productosSinVentas }} productos sin ventas</strong> en este período
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Análisis de Agricultores y Categorías -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Top Agricultor -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">🥇 Top Agricultor</h3>
                </div>
                @if($topAgricultor)
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl text-black">👨‍🌾</span>
                        </div>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{ $topAgricultor['nombre'] }}</p>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="text-center">
                                <p class="font-bold text-2xl text-blue-600 dark:text-blue-400">S/ {{ number_format($topAgricultor['monto'], 2) }}</p>
                                <p class="text-gray-500 dark:text-gray-400">Total Ventas</p>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-2xl text-green-600 dark:text-green-400">{{ $topAgricultor['productos_diferentes'] }}</p>
                                <p class="text-gray-500 dark:text-gray-400">Productos</p>
                            </div>
                        </div>
                        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            🎯 Modelo a seguir
                        </div>
                    </div>
                @endif
            </div>

            <!-- Agricultor que Necesita Apoyo -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">💪 Necesita Apoyo</h3>
                </div>
                @if($bottomAgricultor)
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl text-white">🌱</span>
                        </div>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{ $bottomAgricultor['nombre'] }}</p>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="text-center">
                                <p class="font-bold text-2xl text-yellow-600 dark:text-yellow-400">S/ {{ number_format($bottomAgricultor['monto'], 2) }}</p>
                                <p class="text-gray-500 dark:text-gray-400">Total Ventas</p>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-2xl text-orange-600 dark:text-orange-400">{{ $bottomAgricultor['productos_diferentes'] }}</p>
                                <p class="text-gray-500 dark:text-gray-400">Productos</p>
                            </div>
                        </div>
                        <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            📚 Capacitación necesaria
                        </div>
                    </div>
                @endif
            </div>

            <!-- Análisis por Categorías -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">📂 Por Categorías</h3>
                </div>
                <div class="space-y-3">
                    @foreach($categoriaStats->take(5) as $categoria)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $categoria['categoria'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $categoria['productos'] }} productos</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-purple-600 dark:text-purple-400">S/ {{ number_format($categoria['monto'], 2) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($categoria['cantidad']) }} Und</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Alertas y Recomendaciones Estratégicas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Alertas Críticas -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">🚨 Alertas Críticas</h3>
                </div>
                <div class="space-y-3">
                    @if($agricultoresSinVentas > 0)
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-center space-x-2">
                                <span class="text-red-600 dark:text-red-400 text-lg">👥</span>
                                <div>
                                    <p class="font-semibold text-red-800 dark:text-red-200">{{ $agricultoresSinVentas }} Agricultores Inactivos</p>
                                    <p class="text-sm text-red-600 dark:text-red-400">No registraron ventas en este período</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($productosSinVentas > 0)
                        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                            <div class="flex items-center space-x-2">
                                <span class="text-orange-600 dark:text-orange-400 text-lg">📦</span>
                                <div>
                                    <p class="font-semibold text-orange-800 dark:text-orange-200">{{ $productosSinVentas }} Productos sin Vender</p>
                                    <p class="text-sm text-orange-600 dark:text-orange-400">Inventario muerto que necesita promoción</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($promedioCompraPorPedido < 50)
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-center space-x-2">
                                <span class="text-yellow-600 dark:text-yellow-400 text-lg">💰</span>
                                <div>
                                    <p class="font-semibold text-yellow-800 dark:text-yellow-200">Compra Promedio Baja</p>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-400">S/ {{ number_format($promedioCompraPorPedido, 2) }} por pedido - Los clientes podrían comprar más</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($crecimiento < -10)
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-center space-x-2">
                                <span class="text-red-600 dark:text-red-400 text-lg">📉</span>
                                <div>
                                    <p class="font-semibold text-red-800 dark:text-red-200">Caída en Ventas</p>
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ number_format($crecimiento, 1) }}% vs período anterior</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($agricultoresSinVentas == 0 && $productosSinVentas == 0 && $promedioCompraPorPedido >= 50 && $crecimiento >= -10)
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-center space-x-2">
                                <span class="text-green-600 dark:text-green-400 text-lg">✅</span>
                                <div>
                                    <p class="font-semibold text-green-800 dark:text-green-200">¡Todo en Orden!</p>
                                    <p class="text-sm text-green-600 dark:text-green-400">No hay alertas críticas en este momento</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estrategias Recomendadas -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">💡 Estrategias Recomendadas</h3>
                </div>
                <div class="space-y-3">
                    @if($top3Productos->first())
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <p class="font-semibold text-blue-800 dark:text-blue-200 flex items-center">
                                <span class="mr-2">🎯</span>
                                Impulsar "{{ $top3Productos->first()['nombre'] }}"
                            </p>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Es tu producto estrella, considera promociones especiales</p>
                        </div>
                    @endif
                    
                    @if($topAgricultor && $bottomAgricultor)
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <p class="font-semibold text-purple-800 dark:text-purple-200 flex items-center">
                                <span class="mr-2">🤝</span>
                                Programa de Mentorías
                            </p>
                            <p class="text-sm text-purple-600 dark:text-purple-400">{{ $topAgricultor['nombre'] }} puede entrenar a {{ $bottomAgricultor['nombre'] }}</p>
                        </div>
                    @endif
                    
                    @if($categoriaStats->first())
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <p class="font-semibold text-green-800 dark:text-green-200 flex items-center">
                                <span class="mr-2">📈</span>
                                Expandir "{{ $categoriaStats->first()['categoria'] }}"
                            </p>
                            <p class="text-sm text-green-600 dark:text-green-400">Categoría más exitosa, busca más agricultores de este tipo</p>
                        </div>
                    @endif
                    
                    @if($bottom3Productos->first())
                        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <p class="font-semibold text-orange-800 dark:text-orange-200 flex items-center">
                                <span class="mr-2">🔄</span>
                                Reevaluar "{{ $bottom3Productos->first()['nombre'] }}"
                            </p>
                            <p class="text-sm text-orange-600 dark:text-orange-400">Bajas ventas - ajustar precio o promocionar mejor</p>
                        </div>
                    @endif
                    
                    @if($promedioCompraPorPedido < 50)
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <p class="font-semibold text-yellow-800 dark:text-yellow-200 flex items-center">
                                <span class="mr-2">💎</span>
                                Estrategia de Paquetes
                            </p>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">Arma combos de productos para que cada cliente compre más</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Análisis Comparativo y Tendencias -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">📊 Análisis Comparativo</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Comparación vs Período Anterior -->
                <div class="text-center">
                    <div class="p-4 rounded-lg {{ $crecimiento >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
                        <div class="text-3xl mb-2">
                            @if($crecimiento > 0)
                                📈
                            @elseif($crecimiento < 0)
                                📉
                            @else
                                ➡️
                            @endif
                        </div>
                        <p class="text-2xl font-bold {{ $crecimiento >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ number_format(abs($crecimiento), 1) }}%
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">vs Período Anterior</p>
                    </div>
                </div>
                
                <!-- Eficiencia por Agricultor -->
                <div class="text-center">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-3xl mb-2">⚡</div>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($totalVentas / max($numAgricultores, 1), 0) }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">S/ Promedio por Agricultor</p>
                    </div>
                </div>
                
                <!-- Diversificación -->
                <div class="text-center">
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-3xl mb-2">🎯</div>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ number_format($productosStats->count() / max($numAgricultores, 1), 1) }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Productos por Agricultor</p>
                    </div>
                </div>
                
                <!-- Concentración de Ventas -->
                <div class="text-center">
                    <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <div class="text-3xl mb-2">🎪</div>
                        @php
                            $concentracion = $top3Productos->sum('monto') / max($totalVentas, 1) * 100;
                        @endphp
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ number_format($concentracion, 0) }}%
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Representan del Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Período -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">
                        📅 Período de Análisis: {{ \Carbon\Carbon::parse($this->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($this->fecha_fin)->format('d/m/Y') }}
                    </h3>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <span class="flex items-center space-x-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span>{{ $items->count() }} transacciones analizadas</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span>{{ $categoriaStats->count() }} categorías activas</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                            <span>Estado: {{ $estadoLabels[$this->estado_filtro] ?? ucfirst($this->estado_filtro) }}</span>
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl mb-1">🎯</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Centro de Comando</p>
                </div>
            </div>
        </div>

        <!-- Tabla principal con agricultores -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 flex items-center space-x-2">
                    <span class="text-2xl">👨‍🌾</span>
                    <span>Detalle por Agricultor</span>
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gestiona pagos individuales y analiza rendimiento detallado</p>
            </div>
            
            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>
        
    </div>
    
    <style>
        /* Animaciones suaves */
        .transform {
            transition: all 0.3s ease-in-out;
        }
        
        .hover\:-translate-y-1:hover {
            transform: translateY(-4px);
        }
        
        .hover\:shadow-xl:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .dark .hover\:shadow-xl:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
        
        /* Gradientes más ricos */
        .bg-gradient-to-br {
            background-attachment: local;
        }
        
        /* Mejor contraste en modo oscuro */
        .dark .text-gray-800 {
            color: rgb(229 231 235);
        }
        
        .dark .text-gray-600 {
            color: rgb(156 163 175);
        }
        
        .dark .text-gray-500 {
            color: rgb(107 114 128);
        }
        
        /* Efectos de hover para cards informativos */
        .bg-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .dark .bg-gray-800:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
    </style>
</x-filament-panels::page>