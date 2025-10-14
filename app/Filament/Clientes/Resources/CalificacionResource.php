<?php

namespace App\Filament\Clientes\Resources;

use App\Filament\Clientes\Resources\CalificacionResource\Pages;
use App\Models\Calificacion;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CalificacionResource extends Resource
{
    protected static ?string $model = Calificacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Calidad del Servicio';
    protected static ?string $navigationLabel = 'Mis Calificaciones';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        // Solo mostrar calificaciones del cliente autenticado
        return parent::getEloquentQuery()->where('usuario_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->label('Ticket a Calificar')
                    ->options(function () {
                        // Solo mostrar tickets del cliente que están resueltos o cerrados y no tienen calificación
                        return \App\Models\Ticket::whereHas('users', function ($q) {
                                $q->where('ticket_users.user_id', auth()->id())
                                ->where('ticket_users.rol', 'cliente');
                            })
                            ->whereIn('estado', ['Resuelto', 'Cerrado'])
                            ->whereDoesntHave('calificacions', function ($q) {
                                $q->where('usuario_id', auth()->id());
                            })
                            ->pluck('titulo', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->placeholder('Selecciona un ticket para calificar')
                    ->helperText('Solo puedes calificar tickets resueltos o cerrados')
                    ->visible(fn ($context) => $context === 'create')
                    ->disabled(fn () => request()->query('ticket_id') !== null), // Deshabilitar si viene preseleccionado

                // Forms\Components\Placeholder::make('ticket_info')
                //     ->label('Ticket Calificado')
                //     ->content(function ($record, $get) {
                //         if ($record) {
                //             return $record?->ticket?->titulo ?? 'N/A';
                //         }
                        
                //         // En modo create, mostrar el ticket preseleccionado
                //         $ticketId = $get('ticket_id') ?? request()->query('ticket_id');
                //         if ($ticketId) {
                //             $ticket = \App\Models\Ticket::find($ticketId);
                //             return $ticket?->titulo ?? 'Ticket no encontrado';
                //         }
                        
                //         return 'No hay ticket seleccionado';
                //     })
                    // ->visible(fn ($context, $get) => 
                    //     in_array($context, ['edit', 'view']) || 
                    //     ($context === 'create' && (request()->query('ticket_id') || $get('ticket_id')))
                    // ),

                Forms\Components\Section::make('Calificación del Servicio')
                    ->schema([
                        Forms\Components\Radio::make('puntaje')
                            ->label('¿Qué tan satisfecho estás con la solución?')
                            ->options([
                                1 => '⭐ Muy insatisfecho',
                                2 => '⭐⭐ Insatisfecho', 
                                3 => '⭐⭐⭐ Neutral',
                                4 => '⭐⭐⭐⭐ Satisfecho',
                                5 => '⭐⭐⭐⭐⭐ Muy satisfecho',
                            ])
                            ->required()
                            ->inline(false)
                            ->descriptions([
                                1 => 'El problema no fue resuelto satisfactoriamente',
                                2 => 'El problema fue resuelto pero con dificultades',
                                3 => 'El problema fue resuelto de manera aceptable',
                                4 => 'El problema fue resuelto correctamente',
                                5 => 'Excelente servicio, superó mis expectativas',
                            ]),

                        Forms\Components\Textarea::make('comentario')
                            ->label('Comentarios adicionales (opcional)')
                            ->placeholder('Compártenos tu experiencia, sugerencias o comentarios...')
                            ->rows(4)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Campo oculto para el usuario (se llena automáticamente)
                Forms\Components\Hidden::make('usuario_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket.titulo')
                    ->label('Ticket Calificado')
                    ->searchable()
                    ->limit(40),
                
                Tables\Columns\TextColumn::make('puntaje')
                    ->label('Calificación')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . " ({$state}/5)")
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('comentario')
                    ->label('Comentario')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('puntaje')
                    ->label('Calificación')
                    ->options([
                        1 => '⭐ (1)',
                        2 => '⭐⭐ (2)',
                        3 => '⭐⭐⭐ (3)',
                        4 => '⭐⭐⭐⭐ (4)',
                        5 => '⭐⭐⭐⭐⭐ (5)',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make()
                //     ->visible(fn ($record) => $record->created_at?->diffInDays(now()) <= 7), // Solo editable por 7 días
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No tienes calificaciones')
            ->emptyStateDescription('Las calificaciones aparecerán aquí una vez que califiques tus tickets resueltos.')
            ->emptyStateIcon('heroicon-o-star');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalificacions::route('/'),
            'create' => Pages\CreateCalificacion::route('/create'),
            // 'edit' => Pages\EditCalificacion::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        // Verificar si hay tickets disponibles para calificar
        return Ticket::whereHas('users', function ($q) {
                $q->where('ticket_users.user_id', auth()->id())
                  ->where('ticket_users.rol', 'cliente'); // Especificar la tabla
            })
            ->whereIn('estado', ['Resuelto', 'Cerrado'])
            ->whereDoesntHave('calificacions', function ($q) {
                $q->where('usuario_id', auth()->id());
            })
            ->exists();
    }
}