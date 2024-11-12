<?php

namespace App\Filament\Resources\MedidaResource\Pages;

use App\Filament\Resources\MedidaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedida extends EditRecord
{
    protected static string $resource = MedidaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
