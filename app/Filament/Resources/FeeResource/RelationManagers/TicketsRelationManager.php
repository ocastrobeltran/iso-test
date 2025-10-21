<?php

namespace App\Filament\Resources\FeeResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table as TablesTable; // para la firma
use Filament\Forms\Form as FormsForm; // para la firma

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';
    protected static ?string $recordTitleAttribute = 'titulo';

    public function form(FormsForm $form): FormsForm
    {
        return $form->schema([
            Forms\Components\TextInput::make('titulo')->required(),
            Forms\Components\Textarea::make('descripcion')->rows(3),
            Forms\Components\Select::make('estado')->options([
                'Abierto' => 'Abierto',
                'En progreso' => 'En progreso',
                'Resuelto' => 'Resuelto',
                'Cerrado' => 'Cerrado',
            ])->default('Abierto'),
            Forms\Components\Select::make('prioridad')->options([
                'Baja' => 'Baja', 'Media' => 'Media', 'Alta' => 'Alta',
            ])->default('Media'),
        ]);
    }

    public function table(TablesTable $table): TablesTable
    {
        return $table
            ->columns([
                TextColumn::make('titulo')->label('Título')->searchable()->sortable(),
                BadgeColumn::make('estado')->label('Estado'),
                BadgeColumn::make('prioridad')->label('Prioridad'),
                TextColumn::make('created_at')->label('Creado')->dateTime(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}