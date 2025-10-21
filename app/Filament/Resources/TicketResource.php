<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\Proyecto;
use App\Models\Usuario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TicketResource\RelationManagers\HistorialesRelationManager;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Models\Contrato;
use Filament\Forms\Components\Select;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationGroup = 'Soporte Técnico';
    protected static ?string $navigationLabel = 'Tickets';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'Abierto')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Ticket')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Título')
                            ->maxLength(255)
                            ->disabled(fn ($context) => $context === 'edit'),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->disabled(fn ($context) => $context === 'edit')
                            ->columnSpanFull(),
                        Select::make('contrato_id')
                            ->label('Contrato')
                            ->options(Contrato::pluck('siglas','id')
                                ->map(fn($siglas, $id) => (string) ($siglas ?? "Contrato #{$id}"))
                                ->filter(fn($label) => $label !== null)
                                ->toArray())
                            ->searchable()
                            ->required()
                            ->reactive()  // Ensure reactivity for dependent fields if needed
                            ->visible(fn ($context) => in_array($context, ['create', 'edit'])),
                    ])->columns(2),

                Forms\Components\Section::make('Gestión del Ticket')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->options([
                                'Abierto' => 'Abierto',
                                'En Progreso' => 'En Progreso',
                                'Resuelto' => 'Resuelto',
                            ])
                            ->default('Abierto')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === 'Resuelto') {
                                    $set('fecha_resolucion', now());
                                }
                            }),
                        Forms\Components\TextInput::make('tiempo_resolucion_estimada')
                            ->label('Tiempo de resolución estimada (h)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->required(false),
                        Forms\Components\Select::make('empleado_asignado_id')
                            ->label('Empleado Asignado')
                            ->options(
                                \App\Models\Usuario::whereIn('rol', [
                                    'Adm. Finanzas',
                                    'Comercial',
                                    'Técnico',
                                    'Soporte',
                                    'PM',
                                    'G. Humanas',
                                    'QA',
                                    'UX',
                                    'Agencia',
                                    'Calidad',
                                ])->pluck('nombre', 'id')
                            )
                            ->dehydrated(false)
                            ->reactive()
                            ->visible(fn ($context) => in_array($context, ['create', 'edit', 'view']))
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('fecha_asignacion', now());
                                }
                            }),
                        Forms\Components\Placeholder::make('empleado_asignado_nombre')
                            ->label('Empleado Asignado')
                            ->content(fn ($record) => $record?->empleado_asignado_nombre)
                            ->visible(fn ($context) => $context === 'view'),
                        Forms\Components\DateTimePicker::make('fecha_asignacion')
                            ->label('Fecha de Asignación')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('fecha_resolucion')
                            ->label('Fecha de Resolución')
                            ->disabled(),
                    ])->columns(2),
                Forms\Components\Section::make('Servicios Asociados')
                    ->schema([
                        Forms\Components\Select::make('servicios')
                            ->label('Servicios')
                            ->multiple()
                            ->relationship('servicios', 'nombre')
                            ->preload()
                            ->searchable()
                            ->required(false)
                            ->visible(fn ($context) => in_array($context, ['create', 'edit'])),
                        Forms\Components\Placeholder::make('servicios_view')
                            ->label('Servicios asociados')
                            ->content(function ($record) {
                                if (!$record || !$record->servicios->count()) {
                                    return 'Sin servicios asociados';
                                }
                                return $record->servicios->pluck('nombre')->implode('<br>');
                            })
                            ->visible(fn ($context) => $context === 'view'),
                    ])->columns(2),
                Forms\Components\Section::make('Solución y Comentarios')
                    ->schema([
                        Forms\Components\Textarea::make('solucion')
                            ->label('Solución')
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
                    ]),

                Forms\Components\Section::make('Fechas del Sistema')
                    ->schema([
                        Forms\Components\DateTimePicker::make('fecha_creacion')
                            ->label('Fecha de Creación')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('fecha_cierre')
                            ->label('Fecha de Cierre')
                            ->disabled(),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('contrato.siglas')
                    ->label('Contrato')
                    ->searchable()
                    ->sortable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('contrato.proyectos.first.nombre')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->default('—')
                    ->visible(fn ($record) => $record && $record->contrato && $record->contrato->fees->isEmpty()),  // Verifica $record
                Tables\Columns\TextColumn::make('contrato.fees.first.nombre')
                    ->label('Fee')
                    ->searchable()
                    ->sortable()
                    ->default('—')
                    ->visible(fn ($record) => $record && $record->contrato && $record->contrato->fees->isNotEmpty()),  // Verifica $record
                Tables\Columns\TextColumn::make('empleado_asignado_nombre')
                    ->label('Asignado a')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_resolucion')
                    ->label('Resuelto')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tiempo_resolucion_humano')
                    ->label('Tiempo de Resolución')
                    ->sortable()
                    ->tooltip(fn ($record) => $record->tiempo_resolucion_horas ? $record->tiempo_resolucion_horas . ' horas' : null),
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
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ;
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

    public static function getRelations(): array
    {
        return [
            HistorialesRelationManager::class,
        ];
    }

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->schema([
                        TextEntry::make('titulo')->label('Título'),
                        TextEntry::make('descripcion')->label('Descripción')->columnSpanFull(),
                        TextEntry::make('estado')->label('Estado'),
                        TextEntry::make('contrato.titulo')
                            ->label('Contrato')
                            ->formatStateUsing(function ($state, $record) {
                                $link = $record->contrato ? url("/admin/resources/contrato/{$record->contrato->id}/view") : null;
                                if ($link) {
                                    return "<a href=\"{$link}\" class=\"filament-link\">".e($record->contrato->titulo)."</a>";
                                }
                                return $record->contrato->titulo ?? 'Sin contrato';
                            })
                            ->html(),
                        TextEntry::make('contrato.proyectos.nombre')
                            ->label('Proyecto')
                            ->default('Sin proyecto')
                            ->visible(fn ($record) => $record && $record->contrato && $record->contrato->fees->isEmpty()),  // Verifica $record
                        TextEntry::make('contrato.fees.nombre')
                            ->label('Fee')
                            ->default('Sin fee')
                            ->visible(fn ($record) => $record && $record->contrato && $record->contrato->fees->isNotEmpty()),  // Verifica $record
                        TextEntry::make('empleado_asignado_nombre')->label('Asignado a'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Gestión y Fechas')
                    ->schema([
                        TextEntry::make('fecha_creacion')->label('Fecha de Creación')->dateTime(),
                        TextEntry::make('fecha_asignacion')->label('Fecha de Asignación')->dateTime(),
                        TextEntry::make('fecha_resolucion')->label('Fecha de Resolución')->dateTime(),
                        TextEntry::make('fecha_cierre')->label('Fecha de Cierre')->dateTime(),
                        TextEntry::make('tiempo_resolucion_estimada')->label('Tiempo de resolución estimada (h)'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Servicios Asociados')
                    ->schema([
                        TextEntry::make('servicios')
                            ->label('Servicios')
                            ->formatStateUsing(function ($state, $record) {
                                // Si es relación Eloquent
                                if (method_exists($record, 'servicios') && $record->servicios instanceof \Illuminate\Database\Eloquent\Collection) {
                                    if (!$record->servicios->count()) {
                                        return 'Sin servicios asociados';
                                    }
                                    return $record->servicios->pluck('nombre')->implode(', ');
                                }
                                // Si es colección Laravel
                                if ($state instanceof \Illuminate\Support\Collection) {
                                    $state = $state->all();
                                }
                                // Si es array
                                if (is_array($state)) {
                                    if (empty($state)) {
                                        return 'Sin servicios asociados';
                                    }
                                    // Si es array de arrays/objetos
                                    if (is_array(reset($state)) || is_object(reset($state))) {
                                        $nombres = [];
                                        foreach ($state as $item) {
                                            if (is_array($item) && isset($item['nombre'])) {
                                                $nombres[] = $item['nombre'];
                                            } elseif (is_object($item) && isset($item->nombre)) {
                                                $nombres[] = $item->nombre;
                                            } elseif (is_scalar($item)) {
                                                $nombres[] = $item;
                                            } else {
                                                $nombres[] = json_encode($item);
                                            }
                                        }
                                        return count($nombres) ? implode(', ', $nombres) : 'Sin servicios asociados';
                                    }
                                    // Si es array plano
                                    return implode(', ', array_map('strval', $state));
                                }
                                // Si es string o null
                                if (is_string($state)) {
                                    return $state ?: 'Sin servicios asociados';
                                }
                                // Si es cualquier otro tipo (objeto, etc)
                                return is_scalar($state) ? (string) $state : 'Sin servicios asociados';
                            }),
                    ])
                    ->collapsible(),

                Section::make('Solución y Comentarios')
                    ->schema([
                        TextEntry::make('solucion')->label('Solución')->columnSpanFull(),
                        TextEntry::make('comentarios')
                            ->label('Comentarios')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Tiempos y Cumplimiento')
                    ->schema([
                        TextEntry::make('tiempo_resolucion_humano')
                            ->label('Tiempo de resolución real')
                            ->helperText('Tiempo transcurrido entre la creación y la resolución del ticket, considerando días laborales de 8 horas.'),
                        TextEntry::make('tiempo_resolucion_estimada')
                            ->label('Tiempo de resolución estimada')
                            ->helperText('Tiempo estimado por el PM o responsable para resolver el ticket (en horas).'),
                        TextEntry::make('cumplimiento_resolucion')
                            ->label('Cumplimiento de resolución')
                            ->helperText('Porcentaje de cumplimiento entre el tiempo real y el estimado. Si es mayor a 100%, se excedió el tiempo estimado.'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['contrato.fees', 'contrato.proyectos']);
    }
}