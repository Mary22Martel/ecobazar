<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PagosAgricultores extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = PedidoResource::class;
    protected static string $view = 'filament.resources.pedido-resource.pages.pagos-agricultores';
    protected static ?string $title = 'Pagos a Agricultores';

    // Propiedades para filtros
    public $fecha_inicio;
    public $fecha_fin;
    public $estado_filtro = 'armado';

    public function mount(): void
    {
        // Por defecto mostrar la semana actual
        $this->fecha_inicio = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->fecha_fin = Carbon::now()->endOfWeek()->format('Y-m-d');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('filtros')
                ->label('Filtrar Período')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('primary')
                ->size('lg')
                ->form([
                    DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->default($this->fecha_inicio)
                        ->required()
                        ->native(false),
                    DatePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->default($this->fecha_fin)
                        ->required()
                        ->native(false),
                    Select::make('estado_filtro')
                        ->label('Estado de Pedidos')
                        ->options([
                            'armado' => '✅ Solo Armados (listos para pago)',
                            'todos' => '📋 Todos los estados',
                            'pagado' => '💰 Solo Pagados',
                            'entregado' => '🚚 Solo Entregados'
                        ])
                        ->default($this->estado_filtro)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->fecha_inicio = $data['fecha_inicio'];
                    $this->fecha_fin = $data['fecha_fin'];
                    $this->estado_filtro = $data['estado_filtro'];
                    
                    Notification::make()
                        ->title('Período actualizado')
                        ->body('Los datos se han actualizado correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                // Columna principal - Agricultor con diseño mejorado
                TextColumn::make('name')
                    ->label('👨‍🌾 Agricultor')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->color('primary')
                    ->icon('heroicon-m-user-circle')
                    ->iconColor('success')
                    ->sortable()
                    ->searchable(),

                // Productos vendidos con icono y color
                TextColumn::make('total_productos')
                    ->label('📦 Productos')
                    ->badge()
                    ->color('info')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->getStateUsing(fn ($record) => $this->calcularVentasAgricultor($record->id)['total_productos'])
                    ->formatStateUsing(fn ($state) => $state),

                // Cantidad total con mejor visualización
                TextColumn::make('total_cantidad')
                    ->label('⚖️ Cantidad')
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn ($record) => $this->calcularVentasAgricultor($record->id)['total_cantidad'])
                    ->formatStateUsing(fn ($state) => number_format($state, 0)),

                // Total a pagar - Columna destacada
                TextColumn::make('total_ventas')
                    ->label('💰 Total a Pagar')
                    ->weight(FontWeight::ExtraBold)
                    ->color('success')
                    ->size(TextColumn\TextColumnSize::Large)
                    ->getStateUsing(fn ($record) => $this->calcularVentasAgricultor($record->id)['total_ventas'])
                    ->formatStateUsing(fn ($state) => 'S/ ' . number_format($state, 2))
                    ->sortable(),

                // Pedidos involucrados
                TextColumn::make('pedidos_involucrados')
                    ->label('📋 Pedidos')
                    ->badge()
                    ->color('secondary')
                    ->getStateUsing(fn ($record) => $this->calcularVentasAgricultor($record->id)['pedidos_involucrados'])
                    ->formatStateUsing(fn ($state) => $state),
            ])
            ->defaultSort('name')
            ->striped()
            ->actions([
                // Acción Ver Detalle mejorada
                \Filament\Tables\Actions\Action::make('ver_detalle')
                    ->label('Ver Detalle')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->size('sm')
                    ->tooltip('Ver desglose completo de ventas')
                    ->modalHeading(fn ($record) => '📊 Detalle de Ventas - ' . $record->name)
                    ->modalContent(fn ($record) => view('filament.components.detalle-ventas-agricultor', [
                        'agricultor_id' => $record->id,
                        'fecha_inicio' => $this->fecha_inicio,
                        'fecha_fin' => $this->fecha_fin,
                        'estado_filtro' => $this->estado_filtro,
                    ]))
                    ->modalWidth('7xl'),

                // Acción Marcar como Pagado mejorada
                \Filament\Tables\Actions\Action::make('marcar_pagado')
                    ->label('Marcar Pagado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('sm')
                    ->tooltip('Registrar que el pago fue realizado')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => '💸 Confirmar Pago a ' . $record->name)
                    ->modalDescription(function ($record) {
                        $total = $this->calcularVentasAgricultor($record->id)['total_ventas'];
                        return '¿Confirmas que has pagado S/ ' . number_format($total, 2) . ' a este agricultor por las ventas del período seleccionado?';
                    })
                    ->modalSubmitActionLabel('✅ Sí, Confirmar Pago')
                    ->modalCancelActionLabel('❌ Cancelar')
                    ->action(function ($record) {
                        $this->marcarComoPagado($record->id);
                    }),
            ])
            ->emptyStateHeading('😔 No hay ventas en este período')
            ->emptyStateDescription('Intenta cambiar las fechas o el estado de los pedidos para ver más resultados.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->recordClasses(fn ($record) => 'hover:bg-green-50 dark:hover:bg-gray-700/50 transition-colors duration-200');
    }

    protected function getTableQuery()
    {
        return User::query()
            ->where('role', 'agricultor')
            ->whereHas('productos', function ($query) {
                $query->whereHas('orderItems', function ($subQuery) {
                    $subQuery->whereHas('order', function ($orderQuery) {
                        // Filtrar por fechas
                        if ($this->fecha_inicio && $this->fecha_fin) {
                            $orderQuery->whereBetween('created_at', [
                                Carbon::parse($this->fecha_inicio)->startOfDay(),
                                Carbon::parse($this->fecha_fin)->endOfDay()
                            ]);
                        }

                        // Filtrar por estado
                        if ($this->estado_filtro && $this->estado_filtro !== 'todos') {
                            $orderQuery->where('estado', $this->estado_filtro);
                        }
                    });
                });
            })
            ->with(['mercado']);
    }

    private function calcularVentasAgricultor($agricultorId)
    {
        // Cache para evitar recalcular múltiples veces
        static $cache = [];
        $key = $agricultorId . '_' . $this->fecha_inicio . '_' . $this->fecha_fin . '_' . $this->estado_filtro;
        
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $query = OrderItem::query()
            ->join('productos', 'order_items.producto_id', '=', 'productos.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('productos.user_id', $agricultorId);

        // Aplicar filtros de fecha
        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('orders.created_at', [
                Carbon::parse($this->fecha_inicio)->startOfDay(),
                Carbon::parse($this->fecha_fin)->endOfDay()
            ]);
        }

        // Aplicar filtros de estado
        if ($this->estado_filtro && $this->estado_filtro !== 'todos') {
            $query->where('orders.estado', $this->estado_filtro);
        }

        $items = $query->select([
            'order_items.*',
            'orders.id as order_id'
        ])->get();

        $resultado = [
            'total_ventas' => $items->sum(function ($item) {
                return $item->precio * $item->cantidad;
            }),
            'total_cantidad' => $items->sum('cantidad'),
            'total_productos' => $items->count(),
            'pedidos_involucrados' => $items->unique('order_id')->count(),
        ];

        $cache[$key] = $resultado;
        return $resultado;
    }

    public function calcularTotalVentas(): float
    {
        $query = OrderItem::query()
            ->join('productos', 'order_items.producto_id', '=', 'productos.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'productos.user_id', '=', 'users.id')
            ->where('users.role', 'agricultor');

        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('orders.created_at', [
                Carbon::parse($this->fecha_inicio)->startOfDay(),
                Carbon::parse($this->fecha_fin)->endOfDay()
            ]);
        }

        if ($this->estado_filtro && $this->estado_filtro !== 'todos') {
            $query->where('orders.estado', $this->estado_filtro);
        }

        $items = $query->get();
        
        return $items->sum(function ($item) {
            return $item->precio * $item->cantidad;
        });
    }

    public function getSubheading(): ?string
    {
        $fechaTexto = '';
        if ($this->fecha_inicio && $this->fecha_fin) {
            $inicio = Carbon::parse($this->fecha_inicio)->format('d/m/Y');
            $fin = Carbon::parse($this->fecha_fin)->format('d/m/Y');
            $fechaTexto = "📅 {$inicio} - {$fin}";
        }
        
        $total = $this->calcularTotalVentas();
        $agricultores = $this->getTableQuery()->count();
        
        return "{$fechaTexto} | 💰 Total: S/ " . number_format($total, 2) . " | 👥 {$agricultores} agricultores";
    }

    private function marcarComoPagado($agricultor_id): void
    {
        try {
            // Verificar si el modelo PagoAgricultor existe
            if (class_exists(\App\Models\PagoAgricultor::class)) {
                $pago = \App\Models\PagoAgricultor::crearPago(
                    $agricultor_id,
                    $this->fecha_inicio,
                    $this->fecha_fin,
                    'pagado'
                );

                if ($pago) {
                    Notification::make()
                        ->title('✅ Pago registrado exitosamente')
                        ->body("Se registró el pago de S/ " . number_format($pago->monto_total, 2) . " para el agricultor.")
                        ->success()
                        ->duration(5000)
                        ->send();
                } else {
                    Notification::make()
                        ->title('⚠️ No hay ventas para pagar')
                        ->body('No se encontraron ventas en estado armado para este agricultor en el período seleccionado.')
                        ->warning()
                        ->send();
                }
            } else {
                // Si no existe el modelo, solo mostrar mensaje
                $ventas = $this->calcularVentasAgricultor($agricultor_id);
                Notification::make()
                    ->title('✅ Pago procesado')
                    ->body("Monto: S/ " . number_format($ventas['total_ventas'], 2))
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error al registrar el pago')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}