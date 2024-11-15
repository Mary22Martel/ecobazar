<?php
namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions\Action;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    public static function label(): string
    {
        return 'Pedido';
    }

    public static function pluralLabel(): string
    {
        return 'Pedidos';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('verPendientes')
                ->label('Ver Pendientes')
                ->url(PedidoResource::getUrl('pendientes'))
                ->color('primary'),
            Action::make('verPagados')
                ->label('Ver Pagados')
                ->url(PedidoResource::getUrl('pagados'))
                ->color('success'),
        ];
    }
}
