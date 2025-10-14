<?php

namespace App\Filament\Clientes\Resources;

use App\Filament\Clientes\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\Proyecto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clientes\Resources\CalificacionResource;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationLabel = 'Mis Tickets';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getEloquentQuery(): Builder
    {
        // Solo mostrar tickets del cliente autenticado
        return parent::getEloquentQuery()->forCliente(auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Crear Ticket
                Forms\Components\Section::make('Crear Ticket')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->visible(fn ($context) => $context === 'create'),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->visible(fn ($context) => $context === 'create'),
                        Forms\Components\Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->options(
                                \App\Models\Proyecto::whereHas('contratos', function ($q) {
                                    $q->where('cliente_id', auth()->id());
                                })->pluck('nombre', 'id')
                            )
                            ->required()
                            ->visible(fn ($context) => $context === 'create'),
                    ])->columns(2)
                    ->visible(fn ($context) => $context === 'create'),

                // Información del Ticket (solo view/edit)
                Forms\Components\Section::make('Información del Ticket')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Título')
                            ->disabled()
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->disabled()
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                        Forms\Components\TextInput::make('proyecto_nombre')
                            ->label('Proyecto')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, $record) => $record?->proyecto_nombre)
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                    ])->columns(2)
                    ->visible(fn ($context) => in_array($context, ['edit', 'view'])),

                // Estado y fechas (solo edit/view)
                Forms\Components\Section::make('Estado del Ticket')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Abierto' => 'Abierto',
                                'En Progreso' => 'En Progreso',
                                'Resuelto' => 'Resuelto',
                                'Devuelto' => 'Devuelto',
                                'Cerrado' => 'Cerrado',
                            ])
                            ->disableOptionWhen(fn ($value) => $value === 'Resuelto' || $value === 'Abierto')
                            ->required()
                            ->reactive()
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                        Forms\Components\DateTimePicker::make('fecha_creacion')
                            ->label('Fecha de Creación')
                            ->disabled()
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                        Forms\Components\DateTimePicker::make('fecha_resolucion')
                            ->label('Fecha de Resolución')
                            ->disabled()
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                        Forms\Components\DateTimePicker::make('fecha_cierre')
                            ->label('Fecha de Cierre')
                            ->disabled()
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                    ])->columns(2)
                    ->visible(fn ($context) => in_array($context, ['edit', 'view'])),

                // Solución y comentarios (solo edit/view)
                Forms\Components\Section::make('Solución y Comentarios')
                    ->schema([
                        Forms\Components\Textarea::make('solucion')
                            ->label('Solución')
                            ->disabled()
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                        Forms\Components\Textarea::make('nuevo_comentario')
                            ->label('Agregar comentario')
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->visible(fn ($context) => $context === 'edit'),
                        Forms\Components\ViewField::make('comentarios')
                            ->label('Comentarios')
                            ->view('filament.components.comentarios-ticket', fn ($record) => [
                                'comentarios' => $record->comentarios,
                            ])
                            ->visible(fn ($context) => in_array($context, ['edit', 'view']))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'danger' => 'Abierto',
                        'danger' => 'Devuelto',
                        'warning' => 'En Progreso',
                        'success' => 'Resuelto',
                        'secondary' => 'cerrado',
                    ]),
                Tables\Columns\TextColumn::make('proyecto_nombre')
                    ->label('Proyecto'),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_resolucion')
                    ->label('Resuelto')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_cierre')
                    ->label('Cerrado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Abierto' => 'Abierto',
                        'En Progreso' => 'En Progreso',
                        'Resuelto' => 'Resuelto',
                        'Cerrado' => 'Cerrado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => strtolower(trim($record->estado)) !== 'cerrado'),
                Tables\Actions\Action::make('calificar')
                    ->label('Calificar')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(function ($record) {
                        return in_array($record->estado, ['Resuelto', 'Cerrado']) && 
                            !$record->calificacions()->where('usuario_id', auth()->id())->exists();
                    })
                    ->url(fn ($record) => CalificacionResource::getUrl('create', ['ticket_id' => $record->id])),
            ]);
    }

    public static function getViewFormSchema(): array
    {
        return [
            \Filament\Forms\Components\TextEntry::make('titulo')->label('Título'),
            \Filament\Forms\Components\TextEntry::make('descripcion')->label('Descripción'),
            \Filament\Forms\Components\TextEntry::make('proyecto_nombre')->label('Proyecto'),
            \Filament\Forms\Components\TextEntry::make('estado')->label('Estado'),
            \Filament\Forms\Components\TextEntry::make('fecha_creacion')->label('Fecha de Creación'),
            \Filament\Forms\Components\TextEntry::make('fecha_resolucion')->label('Fecha de Resolución'),
            \Filament\Forms\Components\TextEntry::make('fecha_cierre')->label('Fecha de Cierre'),
            \Filament\Forms\Components\Textarea::make('solucion')->label('Solución'),
            \Filament\Forms\Components\Textarea::make('comentarios')->label('Comentarios'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}