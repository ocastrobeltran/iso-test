<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketServicioResource\Pages;
use App\Filament\Resources\TicketServicioResource\RelationManagers;
use App\Models\TicketServicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketServicioResource extends Resource
{
    protected static ?string $model = TicketServicio::class;

            protected static ?string $navigationGroup = 'Soporte Técnico';
    protected static ?string $navigationLabel = 'Tickets Servicios';
    protected static ?int $navigationSort = 20;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->relationship(name :'ticket', titleAttribute:'titulo')
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
                Tables\Columns\TextColumn::make('ticket.titulo')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('servicio.nombre')
                    ->label('Servicio')
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
            'index' => Pages\ListTicketServicios::route('/'),
            'create' => Pages\CreateTicketServicio::route('/create'),
            'edit' => Pages\EditTicketServicio::route('/{record}/edit'),
        ];
    }
}
