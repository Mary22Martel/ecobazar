<?php
namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;

class PedidosPagados extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verTodos')
                ->label('Ver Todos')
                ->url(PedidoResource::getUrl())
                ->color('info'),
            Action::make('verPendientes')
                ->label('Ver Pendientes')
                ->url(PedidoResource::getUrl('pendientes'))
                ->color('primary'),
        ];
    }

    protected function getTableQuery(): Builder
{
    return PedidoResource::getModel()::query()->where('estado', 'pagado');
}

}
