<?php

namespace App\Filament\Clientes\Resources;

use App\Filament\Clientes\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\Contrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clientes\Resources\CalificacionResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Mis Tickets';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'Abierto')->count();
    }

    public static function getEloquentQuery(): Builder
    {
        // Solo mostrar tickets donde el contrato pertenezca al cliente autenticado
        return parent::getEloquentQuery()
            ->whereHas('contrato', function ($q) {
                $q->where('cliente_id', auth()->id());
            })
            ->with(['contrato.fees', 'contrato.proyectos']);
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
                            ->required()
                            ->visible(fn ($context) => $context === 'create')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('contrato_id')
                            ->label('Contrato')
                            ->options(
                                Contrato::where('cliente_id', auth()->id())
                                    ->get()
                                    ->mapWithKeys(fn ($c) => [$c->id => $c->cotizacion ?? "Contrato #{$c->id}"])
                            )
                            ->searchable()
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
                            ->visible(fn ($context) => in_array($context, ['edit', 'view']))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('contrato.cotizacion')
                            ->label('Contrato')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, $record) => $record?->contrato?->cotizacion ?? 'Sin contrato')
                            ->visible(fn ($context) => in_array($context, ['edit', 'view'])),
                        Forms\Components\TextInput::make('proyecto_fee_nombre')
                            ->label('Proyecto / Fee')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record?->contrato) return 'Sin asignar';
                                // Prioriza fee si existe
                                if ($record->contrato->fees->isNotEmpty()) {
                                    return 'Fee: ' . $record->contrato->fees->first()->nombre;
                                }
                                if ($record->contrato->proyectos->isNotEmpty()) {
                                    return 'Proyecto: ' . $record->contrato->proyectos->first()->nombre;
                                }
                                return 'Sin proyecto/fee';
                            })
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
                            ->disableOptionWhen(fn ($value) => in_array($value, ['Resuelto', 'Abierto', 'En Progreso']))
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
                            ->visible(fn ($context) => in_array($context, ['edit', 'view']))
                            ->columnSpanFull(),
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
                        'danger' => fn ($state) => in_array($state, ['Abierto', 'Devuelto']),
                        'warning' => 'En Progreso',
                        'success' => 'Resuelto',
                        'secondary' => 'Cerrado',
                    ]),
                Tables\Columns\TextColumn::make('contrato.cotizacion')
                    ->label('Contrato')
                    ->default('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proyecto_fee_nombre')
                    ->label('Proyecto / Fee')
                    ->getStateUsing(function ($record) {
                        if (!$record->contrato) return '—';
                        if ($record->contrato->fees->isNotEmpty()) {
                            return 'Fee: ' . $record->contrato->fees->first()->nombre;
                        }
                        if ($record->contrato->proyectos->isNotEmpty()) {
                            return 'Proyecto: ' . $record->contrato->proyectos->first()->nombre;
                        }
                        return '—';
                    }),
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
                        'Devuelto' => 'Devuelto',
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

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->schema([
                        TextEntry::make('titulo')->label('Título'),
                        TextEntry::make('descripcion')->label('Descripción')->columnSpanFull(),
                        TextEntry::make('estado')
                            ->label('Estado')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'Abierto' => 'danger',
                                'En Progreso' => 'warning',
                                'Resuelto' => 'success',
                                'Devuelto' => 'warning',
                                'Cerrado' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('contrato.cotizacion')->label('Contrato')->default('Sin contrato'),
                        TextEntry::make('proyecto_fee')
                            ->label('Proyecto / Fee')
                            ->getStateUsing(function ($record) {
                                if (!$record->contrato) return 'Sin asignar';
                                if ($record->contrato->fees->isNotEmpty()) {
                                    return 'Fee: ' . $record->contrato->fees->first()->nombre;
                                }
                                if ($record->contrato->proyectos->isNotEmpty()) {
                                    return 'Proyecto: ' . $record->contrato->proyectos->first()->nombre;
                                }
                                return 'Sin proyecto/fee';
                            }),
                        TextEntry::make('fecha_creacion')->label('Fecha de Creación')->dateTime(),
                        TextEntry::make('fecha_resolucion')->label('Fecha de Resolución')->dateTime(),
                        TextEntry::make('fecha_cierre')->label('Fecha de Cierre')->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Solución')
                    ->schema([
                        TextEntry::make('solucion')->label('Solución')->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Comentarios')
                    ->schema([
                        TextEntry::make('comentarios_formateados')
                            ->label('Comentarios')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
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