<?php

namespace App\Filament\Clientes\Resources;

use App\Filament\Clientes\Resources\ProyectoResource\Pages;
use App\Filament\Clientes\Resources\ProyectoResource\RelationManagers;
use App\Models\Proyecto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProyectoResource extends Resource
{
    protected static ?string $model = Proyecto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Proyectos';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Solo mostrar proyectos relacionados al cliente autenticado
        return parent::getEloquentQuery()
            ->whereHas('contratos', function ($q) {
                $q->where('cliente_id', auth()->id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->maxLength(255),
                Forms\Components\Select::make('estado')
                    ->options([
                        'Planificado' => 'Planificado',
                        'En Progreso' => 'En Progreso',
                        'Completado' => 'Completado',
                        'Stop' => 'Stop',
                        'Cancelado' => 'Cancelado',
                    ])
                    ->default('En Progreso')
                    ->required(),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('fecha_inicio'),
                Forms\Components\DatePicker::make('fecha_fin'),
                Forms\Components\Textarea::make('objetivos')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('riesgos')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getViewFormSchema(): array
    {
        return [
            TextEntry::make('nombre')
                ->label('Nombre del Proyecto'),
            TextEntry::make('descripcion')
                ->label('Descripción'),
        ];
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
            'index' => Pages\ListProyectos::route('/'),
            'create' => Pages\CreateProyecto::route('/create'),
            'edit' => Pages\EditProyecto::route('/{record}/edit'),
            'view' => Pages\ViewProyecto::route('/{record}'),
        ];
    }
}
