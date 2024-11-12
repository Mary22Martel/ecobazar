<?php

namespace App\Filament\Resources\MedidaResource\Pages;

use App\Filament\Resources\MedidaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedidas extends ListRecords
{
    protected static string $resource = MedidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
