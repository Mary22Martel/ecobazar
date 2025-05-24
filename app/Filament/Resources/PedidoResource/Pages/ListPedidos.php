<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions\Action;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Botón de crear
            Actions\CreateAction::make(),

            // Ver pendientes
            Action::make('verPendientes')
                ->label('Ver Pendientes')
                ->url(PedidoResource::getUrl('pendientes'))
                ->color('primary'),

            // Ver pagados
            Action::make('verPagados')
                ->label('Ver Pagados')
                ->url(PedidoResource::getUrl('pagados'))
                ->color('success'),

            // ← Nuevo: Ver pagos por agricultor
            Action::make('verPagos')
                ->label('Ver Pagos')
                ->url(PedidoResource::getUrl('pagos'))
                ->color('secondary'),
        ];
    }
}
