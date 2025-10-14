<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProyectoContratoResource\Pages;
use App\Filament\Resources\ProyectoContratoResource\RelationManagers;
use App\Models\ProyectoContrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProyectoContratoResource extends Resource
{
    protected static ?string $model = ProyectoContrato::class;

     protected static ?string $navigationGroup = 'Servicios y Contratos';
    protected static ?string $navigationLabel = 'Proyecto Contrato';
    protected static ?int $navigationSort = 7;
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
                Forms\Components\Select::make('contrato_id')
                    ->label('Contrato')
                    ->options(
                        \App\Models\Contrato::with('usuario')->get()->mapWithKeys(function ($contrato) {
                            return [
                                $contrato->id => optional($contrato->usuario)->nombre
                                    ? $contrato->usuario->nombre . ' (Contrato #' . $contrato->id . ')'
                                    : 'Sin usuario (Contrato #' . $contrato->id . ')'
                            ];
                        })
                    )
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
                    ->label('Proyecto')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contrato.usuario.nombre')
                    ->label('Contrato')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListProyectoContratos::route('/'),
            'create' => Pages\CreateProyectoContrato::route('/create'),
            'edit' => Pages\EditProyectoContrato::route('/{record}/edit'),
        ];
    }
}
