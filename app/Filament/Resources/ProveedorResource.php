<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Filament\Resources\ProveedorResource\RelationManagers;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationGroup = 'Servicios y Contratos';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->maxLength(255),
                Forms\Components\Select::make('tipo_servicio')
                    ->options([
                        'Consultoría' => 'Consultoría',
                        'Desarrollo' => 'Desarrollo',
                        'Soporte' => 'Soporte',
                        'Mantenimiento' => 'Mantenimiento',
                    ])
                    ->default('Consultoría')
                    ->required(),
                Forms\Components\Textarea::make('contacto')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('tipo_servicio')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contacto')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('nombre')
                    ->form([
                        Forms\Components\TextInput::make('nombre')->label('Nombre'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['nombre']) {
                            $query->where('nombre', 'like', '%' . $data['nombre'] . '%');
                        }
                    }),

                Tables\Filters\SelectFilter::make('tipo_servicio')
                    ->label('Tipo de servicio')
                    ->options([
                        'Consultoría' => 'Consultoría',
                        'Desarrollo' => 'Desarrollo',
                        'Soporte' => 'Soporte',
                        'Mantenimiento' => 'Mantenimiento',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ;
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
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
            
        ];
    }
}
