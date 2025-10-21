<?php

namespace App\Filament\Resources\ProyectoResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Table;

class MejorasRelationManager extends RelationManager
{
    protected static string $relationship = 'mejoras';
    protected static ?string $recordTitleAttribute = 'origen';

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('origen')
                ->label('Origen / Título')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3),

            Forms\Components\Select::make('prioridad')
                ->label('Prioridad')
                ->options([
                    'Baja' => 'Baja',
                    'Media' => 'Media',
                    'Alta' => 'Alta',
                ])
                ->default('Media'),

            Forms\Components\DatePicker::make('fecha_propuesta')->label('Fecha propuesta'),
            Forms\Components\DatePicker::make('fecha_implementacion_estimada')->label('Fecha estimada'),
            Forms\Components\DatePicker::make('fecha_implementacion_real')->label('Fecha real'),

            Forms\Components\Textarea::make('observaciones')->label('Observaciones'),

            Forms\Components\Select::make('estado')
                ->label('Estado')
                ->options([
                    'Propuesta' => 'Propuesta',
                    'En progreso' => 'En progreso',
                    'Completada' => 'Completada',
                    'Cancelada' => 'Cancelada',
                ])->default('Propuesta'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')->label('Descripción')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('prioridad')->label('Prioridad')->badge(),
                Tables\Columns\TextColumn::make('fecha_propuesta')->label('Propuesta')->date(),
                Tables\Columns\TextColumn::make('fecha_implementacion_estimada')->label('Estimado')->date(),
                Tables\Columns\TextColumn::make('fecha_implementacion_real')->label('Real')->date(),
                Tables\Columns\TextColumn::make('estado')->label('Estado')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('prioridad')->options([
                    'Baja' => 'Baja', 'Media' => 'Media', 'Alta' => 'Alta',
                ]),
                Tables\Filters\SelectFilter::make('estado')->options([
                    'Propuesta' => 'Propuesta', 'En progreso' => 'En progreso', 'Completada' => 'Completada', 'Cancelada' => 'Cancelada',
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}