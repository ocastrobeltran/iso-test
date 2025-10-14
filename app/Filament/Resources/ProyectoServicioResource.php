<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProyectoServicioResource\Pages;
use App\Filament\Resources\ProyectoServicioResource\RelationManagers;
use App\Models\ProyectoServicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProyectoServicioResource extends Resource
{
    protected static ?string $model = ProyectoServicio::class;
     protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Proyecto Servicios';
        protected static ?int $navigationSort = 17;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('proyecto_id')
                    ->relationship(name :'proyecto', titleAttribute:'nombre')
                    ->searchable()
                    ->preload()
                    ->live()                    
                    ->required(),
                Forms\Components\Select::make('servicio_id')
                    ->relationship(name :'servicio', titleAttribute:'nombre')
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
                Tables\Columns\TextColumn::make('proyecto.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Proyecto'),
                Tables\Columns\TextColumn::make('servicio.nombre')
                    ->sortable()
                    ->searchable()
                    ->label('Servicio'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProyectoServicios::route('/'),
            'create' => Pages\CreateProyectoServicio::route('/create'),
            'edit' => Pages\EditProyectoServicio::route('/{record}/edit'),
        ];
    }
}
