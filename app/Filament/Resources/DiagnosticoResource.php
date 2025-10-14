<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiagnosticoResource\Pages;
use App\Filament\Resources\DiagnosticoResource\RelationManagers;
use App\Models\Diagnostico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiagnosticoResource extends Resource
{
    protected static ?string $model = Diagnostico::class;

    protected static ?string $navigationGroup = 'Soporte Técnico';
    protected static ?string $navigationLabel = 'Diagnósticos';
    protected static ?int $navigationSort = 13;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Textarea::make('resultado')
                    ->label('Resultado')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Toggle::make('es_recurrente')
                    ->label('¿Es recurrente?'),
                Forms\Components\Select::make('ticket_id')
                    ->label('Ticket')
                    ->relationship(name: 'ticket', titleAttribute: 'titulo')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('usuario_id')
                    ->label('Empleado')
                    ->relationship(
                        name: 'usuario',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->whereIn('rol', ['Técnico', 'Soporte', 'QA']),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('es_recurrente')
                    ->label('Recurrente')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ticket.titulo')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Empleado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resultado')
                    ->label('Resultado')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // 
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDiagnosticos::route('/'),
            'create' => Pages\CreateDiagnostico::route('/create'),
            'edit' => Pages\EditDiagnostico::route('/{record}/edit'),
            'view' => Pages\ViewDiagnostico::route('/{record}'),
        ];
    }
}
