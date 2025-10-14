<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CronogramaResource\Pages;
use App\Filament\Resources\CronogramaResource\RelationManagers;
use App\Models\Cronograma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CronogramaResource extends Resource
{
    protected static ?string $model = Cronograma::class;

    protected static ?string $navigationGroup = 'Recursos y Cronogramas';
    protected static ?string $navigationLabel = 'Cronogramas';
    protected static ?int $navigationSort = 15;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('fecha_inicio')
                    ->required(),
                Forms\Components\DatePicker::make('fecha_fin')
                    ->required()
                    ->after('fecha_inicio')
                    ->label('Fecha fin (debe ser igual o posterior a inicio)'),
                Forms\Components\Select::make('estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Progreso' => 'En Progreso',
                        'Completado' => 'Completado',
                        'Cancelado' => 'Cancelado',
                    ])
                    ->default('Pendiente')
                    ->required(),   
                Forms\Components\Select::make('proyecto_id')
                    ->relationship(name :'proyecto', titleAttribute:'nombre')
                    ->searchable()
                    ->preload()
                    ->live()                    
                    ->required(),
                Forms\Components\Select::make('usuario_id')
                    ->label('Cliente o empleado')
                    ->relationship(name :'usuario', titleAttribute:'nombre')
                    ->searchable()
                    ->preload()
                    ->live()                    
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario.nombre')
                    ->label('Cliente o empleado')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Progreso' => 'En Progreso',
                        'Completado' => 'Completado',
                        'Cancelado' => 'Cancelado',
                    ]),
                Tables\Filters\SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'nombre'),
                Tables\Filters\SelectFilter::make('usuario_id')
                    ->label('Cliente o empleado')
                    ->relationship('usuario', 'nombre'),
                Tables\Filters\Filter::make('rango_fechas')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($q, $date) => $q->whereDate('fecha_inicio', '>=', $date))
                            ->when($data['hasta'], fn ($q, $date) => $q->whereDate('fecha_fin', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('completar')
                    ->label('Completar')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->estado !== 'Completado')
                    ->action(fn ($record) => $record->update(['estado' => 'Completado'])),
                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record) => $record->estado !== 'Cancelado')
                    ->action(fn ($record) => $record->update(['estado' => 'Cancelado'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCronogramas::route('/'),
            'create' => Pages\CreateCronograma::route('/create'),
            'edit' => Pages\EditCronograma::route('/{record}/edit'),
        ];
    }
}
