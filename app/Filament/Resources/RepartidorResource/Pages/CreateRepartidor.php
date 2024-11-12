<?php

namespace App\Filament\Resources\RepartidorResource\Pages;

use App\Filament\Resources\RepartidorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRepartidor extends CreateRecord
{
    protected static string $resource = RepartidorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'repartidor'; // Asegura que el rol sea siempre 'repartidor'
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}