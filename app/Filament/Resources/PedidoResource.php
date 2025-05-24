<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PedidoResource extends Resource
{
    protected static ?string $model = Order::class;
    //protected static ?string $slug = 'pedidos';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pedidos';

    public static function label(): string
    {
        return 'Pedido';
    }

    public static function pluralLabel(): string
    {
        return 'Pedidos';
    }


     // Usamos form() para mostrar detalles, pero como solo lectura
     public static function form(Forms\Form $form): Forms\Form
     {
         return $form
             ->schema([
                 // Cliente (solo lectura)
                 TextInput::make('nombre')
                     ->label('Nombre')
                     ->default(fn($record) => $record ? $record->nombre : '') // Usar valor vacío si no hay record
                     ->disabled(), 
                 TextInput::make('apellido')
                     ->label('Apellido')
                     ->default(fn($record) => $record ? $record->apellido : '')
                     ->disabled(), 
     
                 // Teléfono (solo lectura)
                 TextInput::make('telefono')
                     ->label('Teléfono')
                     ->default(fn($record) => $record ? $record->telefono : '')
                     ->disabled(), // Usamos disabled para hacerlo solo lectura
     
                 // Dirección (solo lectura)
                 TextInput::make('distrito')
                     ->label('Distrito')
                     ->default(fn($record) => $record ? $record->direccion : '')
                     ->disabled(), // Usamos disabled para hacerlo solo lectura
                
                 TextInput::make('direccion')
                     ->label('Dirección')
                     ->default(fn($record) => $record ? $record->direccion : '')
                     ->disabled(), // Usamos disabled para hacerlo solo lectura
     
                 // Repartidor asignado (solo lectura)
                 Select::make('repartidor_id')
                     ->label('Repartidor')
                     ->relationship('repartidor', 'name')
                     ->required(),
     
                 // Estado del pedido (solo lectura)
                 Select::make('estado')
                     ->label('Estado')
                     ->options([
                         'pagado' => 'Pagado',
                         'en_proceso' => 'En Proceso',
                         'entregado' => 'Entregado',
                         'cancelado' => 'Cancelado',
                     ])
                     ->default('pendiente')
                     ->required(),
     
                 Textarea::make('observaciones')
                     ->label('Observaciones')
                     ->nullable(),
             ]);
     }
     

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Cliente')->sortable()->searchable(),
                TextColumn::make('telefono')->label('Teléfono')->sortable()->searchable(),
                TextColumn::make('repartidor.name')->label('Repartidor')->sortable()->searchable(),
                TextColumn::make('estado')->label('Estado')->sortable(),
                TextColumn::make('total')->label('Total')->sortable(),
                TextColumn::make('created_at')->label('Fecha de Creación')->dateTime('d-m-Y H:i')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->modalHeading('Detalles del Pedido') // Título del modal
                    ->modalWidth('2xl') // Tamaño del modal
                    ->action(function ($record) {
                        // Pasamos la información completa del pedido a la vista
                        return view('admin.pedidos.detalle', ['pedido' => $record]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'pendientes' => Pages\PedidosPendientes::route('/pendientes'),
            'pagados' => Pages\PedidosPagados::route('/pagados'),
            'pagos'     => Pages\PagosAgricultores::route('/pagos'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}
