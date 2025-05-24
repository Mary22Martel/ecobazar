<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\Action;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;

class PagosAgricultores extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    // Texto que aparece en el header de la página
    protected static ?string $title = 'Pagos por Agricultor';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verTodos')
                ->label('Ver Todos')
                ->url(PedidoResource::getUrl())
                ->color('secondary'),

            Action::make('verPendientes')
                ->label('Ver Pendientes')
                ->url(PedidoResource::getUrl('pendientes'))
                ->color('warning'),

            Action::make('verPagados')
                ->label('Ver Pagados')
                ->url(PedidoResource::getUrl('pagados'))
                ->color('success'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('fecha_sabado')
                ->form([
                    DatePicker::make('fecha')
                        ->label('Sábado')
                        ->default(fn () => Carbon::now()->previous(Carbon::SATURDAY))
                        ->required(),
                ])
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['fecha'])) {
                        $query->whereDate('orders.created_at', $data['fecha']);
                    }
                })
                ->label('Filtrar por fecha'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return OrderItem::query()
            ->join('orders',    'orders.id',       '=', 'order_items.order_id')
            ->join('productos', 'productos.id',    '=', 'order_items.producto_id')
            ->join('users',     'users.id',        '=', 'productos.user_id')
            ->where('orders.estado', 'pagado')
            ->selectRaw(/** agrupamos por agricultor **/ '
                productos.user_id   AS user_id,
                users.name          AS productor,
                SUM(order_items.cantidad)                         AS cantidad_vendida,
                SUM(order_items.cantidad * order_items.precio)    AS total_a_pagar
            ')
            ->groupBy('productos.user_id','users.name');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('productor')
                ->label('Agricultor')
                ->sortable(),

            Tables\Columns\TextColumn::make('cantidad_vendida')
                ->label('Cantidad Vendida')
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('total_a_pagar')
                ->label('Total a Pagar (S/)')
                ->money('pen', 'S/')
                ->sortable(),
        ];
    }
}
