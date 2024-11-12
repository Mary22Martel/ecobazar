<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class PedidosPendientes extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verTodos')
                ->label('Ver Todos')
                ->url(PedidoResource::getUrl())
                ->color('info'),
            Action::make('verPagados')
                ->label('Ver Pagados')
                ->url(PedidoResource::getUrl('pagados'))
                ->color('success'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return PedidoResource::getModel()::query()->where('estado', 'pendiente de pago');
    }
}
