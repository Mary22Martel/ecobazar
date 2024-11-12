<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Log;

class PedidoDetalle extends ViewRecord
{
    protected static string $resource = PedidoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('actualizar_estado')
                ->label('Actualizar Estado')
                ->action('actualizarEstado')
                ->form([
                    Select::make('estado')
                        ->label('Estado')
                        ->options([
                            'pendiente' => 'Pendiente',
                            'listo' => 'Listo',
                            'enviando' => 'Enviando',
                            'entregado' => 'Entregado',
                            'cancelado' => 'Cancelado',
                        ])
                        ->required(),
                ])
                ->modalHeading('Actualizar Estado del Pedido')
                ->label('Actualizar'),
        ];
    }

    public function actualizarEstado(array $data): void
    {
        $this->record->update(['estado' => $data['estado']]);
        $this->notify('success', 'El estado del pedido se ha actualizado correctamente.');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        try {
            // Cargar las relaciones necesarias
            $this->record->load(['user', 'repartidor', 'items.product.usuario']); 

            // Mapeo de datos
            $data['user'] = $this->record->user->name ?? 'N/A';
            $data['email'] = $this->record->user->email ?? 'N/A';
            $data['direccion'] = $this->record->direccion ?? 'N/A';
            $data['repartidor'] = $this->record->repartidor->name ?? 'No asignado';
            $data['total'] = $this->record->total ?? '0.00';
            $data['observaciones'] = $this->record->observaciones ?? '';

            // Mapear los items del pedido
            $data['items'] = $this->record->items->map(function ($item) {
                return [
                    'nombre' => $item->product->nombre ?? 'N/A',
                    'cantidad' => $item->cantidad,
                    'precio' => $item->precio,
                    'agricultor' => $item->product->usuario->name ?? 'N/A',
                ];
            })->toArray();

            // Para depuración: muestra los datos en pantalla
            //dd($data);

            return $data;

        } catch (\Exception $e) {
            Log::error('Error en mutateFormDataBeforeFill: '.$e->getMessage());
            throw $e; // re-lanza el error para verlo en la interfaz también
        }
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('user')
                ->label('Cliente')
                ->disabled(),
            TextInput::make('email')
                ->label('Email')
                ->disabled(),
            TextInput::make('direccion')
                ->label('Dirección')
                ->disabled(),
            TextInput::make('repartidor')
                ->label('Repartidor')
                ->disabled(),
        ];
    }
    
}
