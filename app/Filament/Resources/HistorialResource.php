<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialResource\Pages;
use App\Filament\Resources\HistorialResource\RelationManagers;
use App\Models\Historial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistorialResource extends Resource
{
    protected static ?string $model = Historial::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Historial';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('fecha')
                    ->label('Fecha')
                    ->required(),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Select::make('usuario_id')
                    ->label('Usuario')
                    ->relationship('usuario', 'name') // Asegúrate que tu modelo Historial tenga la relación correcta
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(80)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('historialable_type')
                    ->label('Tipo de Entidad')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('historialable_nombre')
                    ->label('Entidad Relacionada')
                    ->getStateUsing(function ($record) {
                        // Si existe la relación, intenta mostrar el campo más representativo
                        if ($record->historialable) {
                            // Puedes personalizar los campos según tus modelos
                            foreach (['nombre', 'titulo', 'name', 'descripcion', 'detalle'] as $campo) {
                                if (isset($record->historialable->$campo)) {
                                    return $record->historialable->$campo;
                                }
                            }
                            // Si no hay campo representativo, muestra el ID
                            return 'ID: ' . $record->historialable->id;
                        }
                        return '-';
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Puedes agregar filtros por tipo de entidad, usuario, fecha, etc.
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListHistorials::route('/'),
            // 'create' => Pages\CreateHistorial::route('/create'),
            // 'edit' => Pages\EditHistorial::route('/{record}/edit'),
        ];
    }
}
