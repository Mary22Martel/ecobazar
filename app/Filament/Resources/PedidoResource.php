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

    // Agregar esta función para crear items de navegación personalizados
    public static function getNavigationItems(): array
    {
        return [
            // Item principal de órdenes
            NavigationItem::make(static::getNavigationLabel())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.index'))
                ->sort(static::getNavigationSort())
                ->url(static::getUrl()),
            
            // Item directo para pagos a agricultores
            NavigationItem::make('Pagos Agricultores')
                ->icon('heroicon-o-banknotes')
                ->url(static::getUrl('pagos'))
                ->sort(static::getNavigationSort() + 1)
                ->badge(function () {
                    // Mostrar cuántos agricultores tienen ventas pendientes de pago esta semana
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
                // HEADER - Información del Cliente con Diseño Mejorado
                Infolists\Components\Section::make('')
                    ->schema([
                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('cliente_nombre')
                                ->label('')
                                ->state(function (Order $record) {
                                    return "{$record->nombre} {$record->apellido}";
                                })
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->weight(FontWeight::Bold)
                                ->color('gray')
                                ->icon('heroicon-m-user')
                                ->iconColor('primary'),
                            
                            Infolists\Components\TextEntry::make('telefono_info')
                                ->label('')
                                ->state(function (Order $record) {
                                    return $record->telefono;
                                })
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Medium)
                                ->color('gray')
                                ->icon('heroicon-m-phone')
                                ->iconColor('success')
                                ->badge()
                                ->copyable(),
                            
                            Infolists\Components\TextEntry::make('entrega_info')
                                ->label('')
                                ->state(function (Order $record) {
                                    return $record->delivery === 'delivery' 
                                        ? "Delivery: {$record->direccion}, {$record->distrito}"
                                        : "Recoger en Puesto";
                                })
                                ->badge()
                                ->color(fn (Order $record): string => $record->delivery === 'delivery' ? 'warning' : 'info')
                                ->icon(fn (Order $record): string => $record->delivery === 'delivery' ? 'heroicon-m-truck' : 'heroicon-m-building-storefront'),
                        ])->columns(3),
                    ])
                    ->compact()
                    ->extraAttributes([
                        'style' => 'background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; border: 1px solid #cbd5e1;'
                    ]),

                // PRODUCTOS POR AGRICULTOR - Diseño con Cards
                Infolists\Components\Section::make('Productos por Agricultor')
                    ->description('Lista de productos que cada agricultor debe entregar')
                    ->icon('heroicon-m-user-group')
                    ->schema([
                        Infolists\Components\TextEntry::make('productos_agricultores')
                            ->label('')
                            ->state(function (Order $record) {
                                try {
                                    $items = $record->items()->with(['product.user', 'product.medida'])->get();
                                    
                                    if ($items->isEmpty()) {
                                        return "Sin productos en este pedido";
                                    }
                                    
                                    $cards = [];
                                    
                                    // Agrupar por agricultor
                                    $porAgricultor = $items->groupBy(function ($item) {
                                        return $item->product->user->name ?? 'AGRICULTOR DESCONOCIDO';
                                    });
                                    
                                    foreach ($porAgricultor as $agricultor => $productos) {
                                        $productosTexto = [];
                                        foreach ($productos as $item) {
                                            $producto = $item->product->nombre ?? 'Producto no encontrado';
                                            $medida = $item->product->medida->nombre ?? 'unidad';
                                            $cantidad = $item->cantidad;
                                            $productosTexto[] = "• {$cantidad} {$medida} de {$producto}";
                                        }
                                        
                                        $cards[] = "
<div style='background: #fef3c7; border: 2px solid #f59e0b; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
    <div style='display: flex; align-items: center; margin-bottom: 0.75rem;'>
        <div style='background: #f59e0b; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; font-weight: bold;'>👨‍🌾</div>
        <h3 style='margin: 0; font-weight: bold; font-size: 1.1rem; color: #92400e;'>" . strtoupper($agricultor) . "</h3>
    </div>
    <div style='color: #92400e; line-height: 1.6; font-size: 0.95rem;'>" . implode("<br>", $productosTexto) . "</div>
</div>";
                                    }
                                    
                                    return implode("", $cards);
                                    
                                } catch (\Exception $e) {
                                    return "<div style='color: #dc2626; padding: 1rem;'>❌ ERROR: " . $e->getMessage() . "</div>";
                                }
                            })
                            ->columnSpanFull()
                            ->extraAttributes([
                                'class' => 'productos-agricultores'
                            ])
                            ->html(),
                    ])
                    ->collapsible(false),

                // CHECKLIST INTERACTIVO
                Infolists\Components\Section::make('Pasos para Armar el Pedido')
                    ->description('Sigue estos pasos para completar el pedido')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->schema([
                        Infolists\Components\TextEntry::make('checklist_interactivo')
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
                                    "Solicitar productos a: {$agricultores}",
                                    "Verificar que tienes {$totalProductos} productos completos",
                                    "Juntar todos los productos en una bolsa o caja",
                                    "Etiquetar el paquete con: \"{$cliente}\"",
                                    "Marcar como ARMADO cuando esté listo"
                                ];
                                
                                $html = "<div style='background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-radius: 12px; padding: 1.5rem; border: 2px solid #10b981;'>";
                                
                                foreach ($pasos as $index => $paso) {
                                    $numero = $index + 1;
                                    $html .= "
<div style='display: flex; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.7); border-radius: 8px; border-left: 4px solid #10b981;'>
    <div style='background: #10b981; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-weight: bold; font-size: 0.875rem;'>{$numero}</div>
    <span style='flex: 1; color: #065f46; font-weight: 500;'>{$paso}</span>
</div>";
                                }
                                
                                $html .= "</div>";
                                return $html;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(false),

                // RESUMEN FINANCIERO CON MEJOR DISEÑO
                Infolists\Components\Section::make('Resumen de Pagos')
                    ->description('Detalle de pagos para este pedido')
                    ->icon('heroicon-m-banknotes')
                    ->schema([
                        Infolists\Components\TextEntry::make('resumen_financiero')
                            ->label('')
                            ->state(function (Order $record) {
                                try {
                                    $items = $record->items()->with(['product.user'])->get();
                                    
                                    if ($items->isEmpty()) {
                                        return "<div style='text-align: center; color: #6b7280; padding: 2rem;'>Sin productos en este pedido</div>";
                                    }
                                    
                                    $html = "<div style='background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 12px; padding: 1.5rem; border: 2px solid #0ea5e9;'>";
                                    
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
                                        
                                        $html .= "
<div style='display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; margin-bottom: 0.5rem; background: rgba(255,255,255,0.8); border-radius: 8px; border-left: 4px solid #0ea5e9;'>
    <div style='display: flex; align-items: center;'>
        <div style='background: #0ea5e9; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 0.75rem; font-size: 0.75rem;'>👨‍🌾</div>
        <span style='font-weight: 600; color: #0c4a6e;'>{$agricultor}</span>
    </div>
    <span style='font-weight: bold; color: #0c4a6e; background: #bae6fd; padding: 0.25rem 0.75rem; border-radius: 20px;'>S/ " . number_format($totalAgricultor, 2) . "</span>
</div>";
                                    }
                                    
                                    $html .= "
<div style='border-top: 2px solid #0ea5e9; margin-top: 1rem; padding-top: 1rem;'>
    <div style='display: flex; justify-content: space-between; align-items: center; background: #0ea5e9; color: white; padding: 1rem; border-radius: 8px;'>
        <span style='font-weight: bold; font-size: 1.1rem;'>💰 TOTAL DEL PEDIDO</span>
        <span style='font-weight: bold; font-size: 1.25rem;'>S/ " . number_format($totalGeneral, 2) . "</span>
    </div>
</div>";
                                    
                                    $html .= "</div>";
                                    return $html;
                                    
                                } catch (\Exception $e) {
                                    return "<div style='color: #dc2626; padding: 1rem;'>❌ ERROR: " . $e->getMessage() . "</div>";
                                }
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->compact()
                    ->collapsible(),

                // ESTADO CON DISEÑO MEJORADO
                Infolists\Components\Section::make('Estado del Pedido')
                    ->schema([
                        Infolists\Components\TextEntry::make('estado_visual_mejorado')
                            ->label('')
                            ->state(function (Order $record) {
                                $estados = [
                                    'pagado' => ['texto' => 'Pagado - Listo para Armar', 'emoji' => '🎯', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
                                    'pendiente' => ['texto' => 'Pendiente', 'emoji' => '⏳', 'color' => '#6b7280', 'bg' => '#f3f4f6'],
                                    'armado' => ['texto' => 'Pedido Armado - Listo para Entregar', 'emoji' => '✅', 'color' => '#10b981', 'bg' => '#dcfce7'],
                                    'en_entrega' => ['texto' => 'En Camino al Cliente', 'emoji' => '🚚', 'color' => '#3b82f6', 'bg' => '#dbeafe'],
                                    'entregado' => ['texto' => 'Entregado al Cliente', 'emoji' => '🎉', 'color' => '#8b5cf6', 'bg' => '#e9d5ff'],
                                    'cancelado' => ['texto' => 'Pedido Cancelado', 'emoji' => '❌', 'color' => '#ef4444', 'bg' => '#fecaca'],
                                ];
                                
                                $estado = $estados[$record->estado] ?? ['texto' => ucfirst($record->estado), 'emoji' => '⚪', 'color' => '#6b7280', 'bg' => '#f3f4f6'];
                                
                                // Helper function for color adjustment
                                $adjustBrightness = function($hexColor, $percent) {
                                    $hexColor = ltrim($hexColor, '#');
                                    $r = hexdec(substr($hexColor, 0, 2));
                                    $g = hexdec(substr($hexColor, 2, 2));
                                    $b = hexdec(substr($hexColor, 4, 2));
                                    $r = max(0, min(255, $r + ($r * $percent / 100)));
                                    $g = max(0, min(255, $g + ($g * $percent / 100)));
                                    $b = max(0, min(255, $b + ($b * $percent / 100)));
                                    return sprintf("#%02x%02x%02x", $r, $g, $b);
                                };
                                
                                return "
<div style='background: linear-gradient(135deg, {$estado['bg']} 0%, " . $adjustBrightness($estado['bg'], -10) . " 100%); border: 2px solid {$estado['color']}; border-radius: 12px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
    <div style='font-size: 3rem; margin-bottom: 0.5rem;'>{$estado['emoji']}</div>
    <div style='font-size: 1.25rem; font-weight: bold; color: {$estado['color']}; margin-bottom: 0.5rem;'>{$estado['texto']}</div>
    <div style='font-size: 0.875rem; color: " . $adjustBrightness($estado['color'], -20) . ";'>Pedido #{$record->id}</div>
</div>";
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->compact(),
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
                        'armado' => 'success',
                        'en_entrega' => 'secondary',
                        'entregado' => 'primary',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pagado' => 'Pagado',
                        'pendiente' => 'Pendiente',
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
                    ->default('pagado'), // Por defecto mostrar pedidos pagados listos para armar
                    
                Tables\Filters\SelectFilter::make('delivery')
                    ->options([
                        'puesto' => 'Recoger en Puesto',
                        'delivery' => 'Delivery'
                    ]),
            ])
            ->actions([
                // Acción principal: ARMAR PEDIDO
                Tables\Actions\ViewAction::make()
                    ->label(fn (Order $record): string => match ($record->estado) {
                        'pagado' => '📋 Armar Pedido',
                        'armado' => '👁️ Ver Pedido',
                        default => '👁️ Revisar',
                    })
                    ->modalWidth('6xl')
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

                // Marcar como armado (acción más importante)
                Tables\Actions\Action::make('marcarArmado')
                    ->label('🎯 Pedido Armado')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->action(function (Order $record) {
                        $record->update(['estado' => 'armado']);
                    })
                    ->visible(fn (Order $record): bool => $record->estado === 'pagado')
                    ->requiresConfirmation()
                    ->modalHeading('¿Marcar pedido como armado?')
                    ->modalDescription('Confirma que verificaste todos los productos y armaste el pedido completo.')
                    ->modalSubmitActionLabel('Sí, está armado'),

                // Editar (solo icono para ahorrar espacio)  
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