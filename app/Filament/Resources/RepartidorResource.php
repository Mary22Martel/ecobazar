<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepartidorResource\Pages;
use App\Filament\Resources\RepartidorResource\RelationManagers;
use App\Models\User;
use App\Models\Repartidor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RepartidorResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $slug = 'repartidores'; // Define la URL en el panel de administración
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Repartidores';

    public static function getEloquentQuery(): Builder
    {
        // Filtra para mostrar solo los usuarios con rol de repartidor
        return parent::getEloquentQuery()->where('role', 'repartidor');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre del Repartidor')
                    ->required(),
                
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required()
                    ->visibleOn('create'), // Visible solo al crear un nuevo repartidor

                Select::make('role')
                    ->label('Rol')
                    ->options([
                        'repartidor' => 'Repartidor',
                    ])
                    ->default('repartidor') // Valor predeterminado de "repartidor"
                    ->disabled(), // Bloquea el campo para que no pueda ser cambiado
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
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
            'index' => Pages\ListRepartidors::route('/'),
            'create' => Pages\CreateRepartidor::route('/create'),
            'edit' => Pages\EditRepartidor::route('/{record}/edit'),
        ];
    }
}
