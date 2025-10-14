<?php

namespace App\Filament\Resources\ProyectoResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistorialesRelationManager extends RelationManager
{
    protected static string $relationship = 'historiales'; // nombre del método en el modelo

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->label('Fecha')->dateTime(),
                Tables\Columns\TextColumn::make('descripcion')->label('Descripción'),
                Tables\Columns\TextColumn::make('usuario.name')->label('Usuario'),
            ])
            ->defaultSort('fecha', 'desc');
    }
}