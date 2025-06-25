<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Navigation\NavigationItem;
use Carbon\Carbon;

class PedidoResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Órdenes';

    public static function label(): string
    {
        return 'Orden';
    }

    public static function pluralLabel(): string
    {
        return 'Órdenes';
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.index'))
                ->sort(static::getNavigationSort())
                ->url(static::getUrl()),
            
            NavigationItem::make('Pagos Agricultores')
                ->icon('heroicon-o-banknotes')
                ->url(static::getUrl('pagos'))
                ->sort(static::getNavigationSort() + 1)
                ->badge(function () {
                    try {
                        $count = User::where('role', 'agricultor')
                            ->whereHas('productos.orderItems.order', function ($query) {
                                $query->where('estado', 'armado')
                                    ->whereBetween('created_at', [
                                        Carbon::now()->startOfWeek(),
                                        Carbon::now()->endOfWeek()
                                    ]);
                            })->count();
                        
                        return $count > 0 ? $count : null;
                    } catch (\Exception $e) {
                        return null;
                    }
                }, 'warning'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // INFORMACIÓN DEL CLIENTE - Diseño limpio
                Infolists\Components\Section::make('Información del Cliente')
                    ->icon('heroicon-m-user')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('nombre_completo')
                                    ->label('Cliente')
                                    ->state(function (Order $record) {
                                        return $record->nombre . ' ' . $record->apellido;
                                    })
                                    ->weight(FontWeight::SemiBold)
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                
                                Infolists\Components\TextEntry::make('telefono')
                                    ->label('Teléfono')
                                    ->copyable()
                                    ->copyMessage('Teléfono copiado')
                                    ->weight(FontWeight::Medium),
                                
                                Infolists\Components\TextEntry::make('tipo_entrega')
                                    ->label('Tipo de entrega')
                                    ->state(function (Order $record) {
                                        if ($record->delivery === 'delivery') {
                                            return 'Delivery: ' . $record->direccion . ', ' . $record->distrito;
                                        }
                                        return 'Recoger en puesto';
                                    })
                                    ->badge()
                                    ->color(fn (Order $record): string => $record->delivery === 'delivery' ? 'warning' : 'success'),
                            ]),
                    ])
                    ->compact(),

                // PRODUCTOS POR AGRICULTOR - Diseño simplificado
                Infolists\Components\Section::make('Lista de Productos')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->description('Productos que debe entregar cada agricultor')
                    ->schema([
                        Infolists\Components\TextEntry::make('productos_lista')
                            ->label('')
                            ->state(function (Order $record) {
                                try {
                                    $items = $record->items()->with(['product.user', 'product.medida'])->get();
                                    
                                    if ($items->isEmpty()) {
                                        return 'Sin productos en este pedido';
                                    }
                                    
                                    $html = '<div class="space-y-4">';
                                    
                                    // Agrupar por agricultor
                                    $porAgricultor = $items->groupBy(function ($item) {
                                        return $item->product->user->name ?? 'AGRICULTOR DESCONOCIDO';
                                    });
                                    
                                    foreach ($porAgricultor as $agricultor => $productos) {
                                        $html .= '<div class="border border-gray-200 rounded-lg p-4 bg-gray-50">';
                                        $html .= '<h4 class="font-semibold text-gray-900 mb-3 text-sm uppercase tracking-wide">' . $agricultor . '</h4>';
                                        
                                        $html .= '<div class="space-y-2">';
                                        foreach ($productos as $item) {
                                            $producto = $item->product->nombre ?? 'Producto no encontrado';
                                            $medida = $item->product->medida->nombre ?? 'unidad';
                                            $cantidad = $item->cantidad;
                                            
                                            $html .= '<div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">';
                                            $html .= '<div class="flex-1">';
                                            $html .= '<span class="text-gray-900 font-medium">' . $producto . '</span>';
                                            $html .= '</div>';
                                            $html .= '<div class="text-right">';
                                            $html .= '<span class="font-semibold text-gray-900">' . $cantidad . ' ' . $medida . '</span>';
                                            $html .= '</div>';
                                            $html .= '</div>';
                                        }
                                        $html .= '</div>';
                                        $html .= '</div>';
                                    }
                                    
                                    $html .= '</div>';
                                    return $html;
                                    
                                } catch (\Exception $e) {
                                    return 'Error al cargar productos: ' . $e->getMessage();
                                }
                            })
                            ->html()
                            ->columnSpanFull(),
                    ]),

                // INSTRUCCIONES PARA ARMAR - Diseño simplificado
                Infolists\Components\Section::make('Instrucciones para Armar')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->schema([
                        Infolists\Components\TextEntry::make('instrucciones')
                            ->label('')
                            ->state(function (Order $record) {
                                $cliente = $record->nombre . ' ' . $record->apellido;
                                $totalProductos = $record->items->count();
                                
                                $agricultores = $record->items()->with('product.user')->get()
                                    ->groupBy('product.user_id')
                                    ->map(function ($items) {
                                        return $items->first()->product->user->name ?? 'Desconocido';
                                    })
                                    ->values()
                                    ->implode(', ');
                                
                                $pasos = [
                                    'Solicitar productos a: ' . $agricultores,
                                    'Verificar que tienes ' . $totalProductos . ' productos completos',
                                    'Juntar todos los productos en una bolsa o caja',
                                    'Etiquetar el paquete con: "' . $cliente . '"',
                                    'Marcar como ARMADO cuando esté listo'
                                ];
                                
                                $html = '<ol class="space-y-2">';
                                foreach ($pasos as $index => $paso) {
                                    $numero = $index + 1;
                                    $html .= '<li class="flex items-start gap-3">';
                                    $html .= '<span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center text-sm font-medium">' . $numero . '</span>';
                                    $html .= '<span class="text-gray-700">' . $paso . '</span>';
                                    $html .= '</li>';
                                }
                                $html .= '</ol>';
                                
                                return $html;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // RESUMEN DE PAGOS - Diseño limpio
                Infolists\Components\Section::make('Resumen de Pagos')
                    ->icon('heroicon-m-banknotes')
                    ->schema([
                        Infolists\Components\TextEntry::make('resumen_pagos')
                            ->label('')
                            ->state(function (Order $record) {
                                try {
                                    $items = $record->items()->with(['product.user'])->get();
                                    
                                    if ($items->isEmpty()) {
                                        return 'Sin productos en este pedido';
                                    }
                                    
                                    $html = '<div class="space-y-3">';
                                    $totalGeneral = 0;
                                    
                                    // Agrupar por agricultor y calcular totales
                                    $porAgricultor = $items->groupBy(function ($item) {
                                        return $item->product->user->name ?? 'AGRICULTOR DESCONOCIDO';
                                    });
                                    
                                    foreach ($porAgricultor as $agricultor => $productos) {
                                        $totalAgricultor = $productos->sum(function ($item) {
                                            return $item->cantidad * $item->precio;
                                        });
                                        $totalGeneral += $totalAgricultor;
                                        
                                        $html .= '<div class="flex justify-between items-center py-2 border-b border-gray-200">';
                                        $html .= '<span class="font-medium text-gray-900">' . $agricultor . '</span>';
                                        $html .= '<span class="font-semibold text-gray-900">S/ ' . number_format($totalAgricultor, 2) . '</span>';
                                        $html .= '</div>';
                                    }
                                    
                                    $html .= '<div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">';
                                    $html .= '<span class="text-lg font-bold text-gray-900">TOTAL DEL PEDIDO</span>';
                                    $html .= '<span class="text-lg font-bold text-blue-600">S/ ' . number_format($totalGeneral, 2) . '</span>';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                    
                                    return $html;
                                    
                                } catch (\Exception $e) {
                                    return 'Error al calcular totales: ' . $e->getMessage();
                                }
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // ESTADO DEL PEDIDO - Diseño minimalista
                Infolists\Components\Section::make('Estado del Pedido')
                    ->schema([
                        Infolists\Components\TextEntry::make('estado')
                            ->label('')
                            ->state(function (Order $record) {
                                $estados = [
                                    'pagado' => ['texto' => 'Pagado - Listo para Armar', 'color' => 'warning'],
                                    'pendiente' => ['texto' => 'Pendiente de Pago', 'color' => 'gray'],
                                    'listo' => ['texto' => 'Listo - Preparado por Agricultor', 'color' => 'info'],
                                    'armado' => ['texto' => 'Pedido Armado - Listo para Entregar', 'color' => 'success'],
                                    'en_entrega' => ['texto' => 'En Camino al Cliente', 'color' => 'info'],
                                    'entregado' => ['texto' => 'Entregado al Cliente', 'color' => 'primary'],
                                    'cancelado' => ['texto' => 'Pedido Cancelado', 'color' => 'danger'],
                                ];
                                
                                $estado = $estados[$record->estado] ?? ['texto' => ucfirst($record->estado), 'color' => 'gray'];
                                
                                return $estado['texto'] . ' (Pedido #' . $record->id . ')';
                            })
                            ->badge()
                            ->color(function (Order $record) {
                                $colores = [
                                    'pagado' => 'warning',
                                    'pendiente' => 'gray',
                                    'armado' => 'success',
                                    'en_entrega' => 'info',
                                    'entregado' => 'primary',
                                    'cancelado' => 'danger',
                                ];
                                
                                return $colores[$record->estado] ?? 'gray';
                            })
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cliente')
                    ->schema([
                        TextInput::make('nombre')->label('Nombre')->disabled(),
                        TextInput::make('apellido')->label('Apellido')->disabled(),
                        TextInput::make('telefono')->label('Teléfono')->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Gestión del Pedido')
                    ->schema([
                        Select::make('repartidor_id')
                            ->label('Repartidor Asignado')
                            ->relationship('repartidor', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'pagado' => 'Pagado - Listo para Armar',
                                'pendiente' => 'Pendiente',
                                'armado' => 'Pedido Armado',
                                'en_entrega' => 'En Entrega',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required()
                            ->helperText('Cambia el estado según el progreso del pedido'),
                        TextInput::make('total')->label('Total')->prefix('S/')->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => "#{$state}")
                    ->size(TextColumn\TextColumnSize::ExtraSmall),
                
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Order $record): string => $record->telefono),
                
                TextColumn::make('delivery')
                    ->label('Entrega')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'puesto' => 'info',
                        'delivery' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'puesto' => '🏪 Puesto',
                        'delivery' => '🚚 Delivery',
                    }),
                
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pagado' => 'warning',
                        'pendiente' => 'info',
                        'listo' => 'success',
                        'armado' => 'success',
                        'en_entrega' => 'secondary',
                        'entregado' => 'primary',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pagado' => 'Pagado',
                        'pendiente' => 'Pendiente',
                        'listo' => 'Listo',  
                        'armado' => 'Armado',
                        'en_entrega' => 'En Entrega',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                        default => ucfirst($state),
                    }),
                
                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),
                
                TextColumn::make('items_count')
                    ->label('Productos')
                    ->badge()
                    ->color('info')
                    ->counts('items'),
                
                TextColumn::make('agricultores_count')
                    ->label('Agricultores')
                    ->badge()
                    ->color('warning')
                    ->state(function (Order $record) {
                        return $record->items()->with('product.user')->get()->groupBy('product.user_id')->count();
                    }),
                
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->size(TextColumn\TextColumnSize::ExtraSmall),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pagado' => 'Pagado - Listo para Armar',
                        'pendiente' => 'Pendiente',
                        'armado' => 'Armado',
                        'en_entrega' => 'En Entrega',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('pagado'),
                    
                Tables\Filters\SelectFilter::make('delivery')
                    ->options([
                        'puesto' => 'Recoger en Puesto',
                        'delivery' => 'Delivery'
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(fn (Order $record): string => match ($record->estado) {
                        'pagado' => 'Armar Pedido',
                        'armado' => 'Ver Pedido',
                        default => 'Revisar',
                    })
                    ->modalWidth('5xl')
                    ->color(fn (Order $record): string => match ($record->estado) {
                        'pagado' => 'success',
                        'armado' => 'primary',
                        default => 'gray',
                    })
                    ->icon(fn (Order $record): string => match ($record->estado) {
                        'pagado' => 'heroicon-o-clipboard-document-list',
                        'armado' => 'heroicon-o-eye',
                        default => 'heroicon-o-eye',
                    }),

                Tables\Actions\Action::make('marcarArmado')
                    ->label('Pedido Armado')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->action(function (Order $record) {
                        $record->update(['estado' => 'armado']);
                    })
                    ->visible(fn (Order $record): bool => $record->estado === 'listo')
                    ->requiresConfirmation()
                    ->modalHeading('¿Marcar pedido como armado?')
                    ->modalDescription('Confirma que verificaste todos los productos y armaste el pedido completo.')
                    ->modalSubmitActionLabel('Sí, está armado'),

                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Editar pedido')
                    ->size('sm')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay pedidos')
            ->emptyStateDescription('Los pedidos aparecerán aquí cuando los clientes hagan compras.')
            ->emptyStateIcon('heroicon-o-shopping-cart');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'pagados' => Pages\PedidosPagados::route('/pagados'),
            'pagos' => Pages\PagosAgricultores::route('/pagos'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}