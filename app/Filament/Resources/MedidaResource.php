<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedidaResource\Pages;
use App\Filament\Resources\MedidaResource\RelationManagers;
use App\Models\Medida;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedidaResource extends Resource
{
    protected static ?string $model = Medida::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Medidas';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre de la Medida')
                    ->required()
                    ->unique(ignoreRecord: true), // Hace que el nombre sea único
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedidas::route('/'),
            'create' => Pages\CreateMedida::route('/create'),
            'edit' => Pages\EditMedida::route('/{record}/edit'),
        ];
    }
}
