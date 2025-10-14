<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProyectoTicketResource\Pages;
use App\Filament\Resources\ProyectoTicketResource\RelationManagers;
use App\Models\ProyectoTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProyectoTicketResource extends Resource
{
    protected static ?string $model = ProyectoTicket::class;

       protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Proyecto Tickets';
        protected static ?int $navigationSort = 18;
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
                Forms\Components\Select::make('ticket_id')
                    ->relationship(name :'ticket', titleAttribute:'titulo')
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket.titulo')
                    ->label('Ticket')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListProyectoTickets::route('/'),
            'create' => Pages\CreateProyectoTicket::route('/create'),
            'edit' => Pages\EditProyectoTicket::route('/{record}/edit'),
        ];
    }
}
