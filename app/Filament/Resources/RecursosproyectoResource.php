<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecursosproyectoResource\Pages;
use App\Filament\Resources\RecursosproyectoResource\RelationManagers;
use App\Models\Recursosproyecto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecursosproyectoResource extends Resource
{
    protected static ?string $model = Recursosproyecto::class;

    protected static ?string $navigationGroup = 'Recursos y Cronogramas';
    protected static ?string $navigationLabel = 'Recursos';
    protected static ?int $navigationSort = 16;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo')
                    ->label('Tipo de recurso')
                    ->options([
                        'Software' => 'Software',
                        'Hardware' => 'Hardware',
                        'Personal' => 'Personal',
                        'Financiero' => 'Financiero',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del recurso')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('ubicacion')
                    ->label('Ubicación')
                    ->columnSpanFull(),
                Forms\Components\Select::make('proyecto_id')
                    ->relationship(name: 'proyecto', titleAttribute: 'nombre')
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
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->searchable()
                    ->columnSpanFull(),
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
            'index' => Pages\ListRecursosproyectos::route('/'),
            'create' => Pages\CreateRecursosproyecto::route('/create'),
            'edit' => Pages\EditRecursosproyecto::route('/{record}/edit'),
        ];
    }
}
