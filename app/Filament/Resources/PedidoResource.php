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
    protected static ?string $slug = 'pedidos';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pedidos';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Cliente')
                    ->relationship('user', 'name')
                    ->required(),

                Select::make('repartidor_id')
                    ->label('Repartidor')
                    ->relationship('repartidor', 'name')
                    ->required(),

                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('pendiente')
                    ->required(),

                TextInput::make('total')
                    ->label('Total del Pedido')
                    ->numeric()
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
            TextColumn::make('user.name')
                ->label('Cliente')
                ->sortable()
                ->searchable(),

            TextColumn::make('repartidor.name')
                ->label('Repartidor')
                ->sortable()
                ->searchable(),

            TextColumn::make('estado')
                ->label('Estado')
                ->sortable(),

            TextColumn::make('total')
                ->label('Total')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Fecha de Creación')
                ->dateTime('d-m-Y H:i')
                ->sortable(),
        ])
        ->filters([
            // Agrega filtros aquí si es necesario, como por estado de pedido
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\ViewAction::make()->url(fn ($record) => PedidoResource::getUrl('view', ['record' => $record->getKey()])),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}


public static function getPages(): array
{
    return [
        'index' => Pages\ListPedidos::route('/'),
        'view' => Pages\PedidoDetalle::route('/{record}'),
        'pendientes' => Pages\PedidosPendientes::route('/pendientes'),
        'pagados' => Pages\PedidosPagados::route('/pagados'),
        'create' => Pages\CreatePedido::route('/create'),
        'edit' => Pages\EditPedido::route('/{record}/edit'),
    ];
}

}
