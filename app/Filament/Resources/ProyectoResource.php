<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProyectoResource\Pages;
use App\Filament\Resources\ProyectoResource\RelationManagers;
use App\Models\Proyecto;
use App\Models\Contrato;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProyectoResource\RelationManagers\HistorialesRelationManager;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ProyectoResource extends Resource
{
    protected static ?string $model = Proyecto::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Proyectos';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->maxLength(255)
                    ->required()
                    ->disabled(fn ($context) => $context === 'edit'),
                Forms\Components\Select::make('estado')
                    ->options([
                        'Inicio' => 'Inicio',
                        'Planeación' => 'Planeación',
                        'En ejecución' => 'En ejecución',
                        'QA' => 'QA',
                        'Finalizado' => 'Finalizado',
                        'Suspendido' => 'Suspendido',
                        'Cancelado' => 'Cancelado',
                    ])
                    ->default('Inicio')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // Si el estado es Finalizado, sugerir poner la fecha de fin real como hoy
                        if ($state === 'Finalizado') {
                            $set('fecha_fin_real', now());
                        }
                    }),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción del proyecto')
                    ->columnSpanFull()
                    ->required()
                    ->disabled(fn ($context) => $context === 'edit'),
                // Forms\Components\Select::make('contratos')
                //     ->label('Contrato asociado')
                //     // ->multiple()
                //     ->options(
                //         \App\Models\Contrato::with('cliente')->get()->mapWithKeys(function ($contrato) {
                //             $cliente = $contrato->cliente ? $contrato->cliente->name : 'Sin cliente';
                //             return [
                //                 $contrato->id => "Contrato #{$contrato->id} (Cliente: {$cliente}, Horas: {$contrato->total_horas})"
                //             ];
                //         })
                //     )
                //     ->searchable()
                //     ->nullable()
                //     ->helperText('Recuerde: El proyecto se puede crear sin contrato. pero si el proyecto no se asocia a un contrato, el cliente no podrá visualizarlo.') // Tooltip/ayuda
                //     ->visible(fn ($context) => in_array($context, ['create', 'edit'])),
               Forms\Components\Placeholder::make('contrato_view')
                    ->label('Contrato asociado')
                    ->content(function ($record) {
                        if (!$record || !$record->contrato) {
                            return 'Sin contrato asociado';
                        }
                        $contrato = $record->contrato;
                        $cliente = $contrato->cliente ? $contrato->cliente->name : 'Sin cliente';
                        return "Contrato #{$contrato->id} (Cliente: {$cliente}, Horas: {$contrato->total_horas})";
                    })
                    ->visible(fn ($context) => $context === 'view')
                    ->columnSpanFull(),
                Forms\Components\Select::make('contrato_id')
                    ->label('Contrato asociado')
                    ->options(
                        Contrato::with('cliente')->get()->mapWithKeys(function ($contrato) {
                            $cliente = $contrato->cliente ? $contrato->cliente->name : 'Sin cliente';
                            return [
                                $contrato->id => "Contrato #{$contrato->id} (Cliente: {$cliente}, Horas: {$contrato->total_horas})"
                            ];
                        })
                    )
                    ->searchable()
                    ->nullable()
                    ->helperText('Recuerde: El proyecto se puede crear sin contrato. Pero si el proyecto no se asocia a un contrato, el cliente no podrá visualizarlo.'),
                Forms\Components\Select::make('pm_responsable_id')
                    ->label('PM Responsable')
                    ->options(
                        User::where('rol', 'PMO')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('servicios')
                    ->label('Servicios asociados')
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
                        return $record->servicios->pluck('nombre')->implode(', ');
                    })
                    ->visible(fn ($context) => $context === 'view')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('fecha_inicio')
                    ->required()
                    ->default(now())
                    ->helperText('Fecha de inicio real del proyecto')
                    ->visible(fn ($context) => $context !== 'create'),
                Forms\Components\DatePicker::make('fecha_inicio_planificada')
                    ->required()
                    ->default(now())
                    ->disabled(fn ($context) => $context === 'edit')
                    ->helperText('Fecha de inicio planificada del proyecto'),
                Forms\Components\DatePicker::make('fecha_fin_planificada')
                    ->label('Fecha de Fin Planificada')
                    ->helperText('Fecha de fin planificada del proyecto')
                    ->required()
                    ->disabled(fn ($context) => $context === 'edit'),
                Forms\Components\DatePicker::make('fecha_fin_real')
                    ->label('Fecha de Fin Real (Automática)')
                    ->helperText('Se llena automáticamente al cambiar el estado a Finalizado.')
                    ->disabled(),
                Forms\Components\Textarea::make('objetivos')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('duracion_estimada')
                ->label('Duración Estimada (días)')
                ->numeric()
                ->minValue(1)
                ->required(),

                Forms\Components\TextInput::make('duracion_real')
                    ->label('Duración Real (días)')
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Se puede calcular automáticamente al finalizar el proyecto.'),

                Forms\Components\TextInput::make('horas_estimadas')
                    ->label('Horas Estimadas')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TextInput::make('horas_ejecutadas')
                    ->label('Horas Ejecutadas')
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Puede integrarse con Clockify.'),

                // Avances
                Forms\Components\TextInput::make('porcentaje_avance_real')
                    ->label('Avance Real (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.1)
                    ->required(),

                Forms\Components\TextInput::make('porcentaje_avance_planeado')
                    ->label('Avance Planeado (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.1)
                    ->required(),

                // Riesgos y Fases
                Forms\Components\TextInput::make('riesgos_identificados')
                    ->label('Riesgos Identificados')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TextInput::make('riesgos_mitigados')
                    ->label('Riesgos Mitigados')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TextInput::make('fases_planeadas')
                    ->label('Fases Planeadas')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TextInput::make('fases_entregadas')
                    ->label('Fases Entregadas')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                // Notas
                Forms\Components\Textarea::make('notas')
                    ->label('Notas / Observaciones')
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
                Tables\Columns\TextColumn::make('contrato.cliente.name')
                    ->label('Cliente')
                    ->default('Sin cliente'),
                Tables\Columns\TextColumn::make('pmResponsable.name')
                    ->label('PM Responsable')
                    ->default('Sin PM'),
                // Tables\Columns\TextColumn::make('contratos')
                //     ->label('Cliente')
                //     ->formatStateUsing(fn($state, $record) =>
                //         $record->contratos->map(function($contrato) {
                //             $cliente = $contrato->cliente ? $contrato->cliente->name : 'Sin cliente';
                //             return "{$cliente}";
                //         })->implode(', ')
                //     )
                //     ->limit(50),
                // Tables\Columns\TextColumn::make('servicios')
                //     ->label('Servicios')
                //     ->formatStateUsing(fn($state, $record) =>
                //         $record->servicios?->pluck('nombre')->implode(', ') ?: 'Sin servicios'
                //     )
                //     ->limit(50),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin_planificada')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                // Filtro por estado
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Inicio' => 'Inicio',
                        'Planeación' => 'Planeación',
                        'En ejecución' => 'En ejecución',
                        'QA' => 'QA',
                        'Finalizado' => 'Finalizado',
                        'Suspendido' => 'Suspendido',
                        'Cancelado' => 'Cancelado',
                    ]),
                // Filtro por fecha de creación
                Tables\Filters\Filter::make('fecha_creacion')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('fecha_inicio', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('fecha_inicio', '<=', $date));
                    }),
                Tables\Filters\SelectFilter::make('contrato.cliente_id')
                    ->label('Cliente')
                    ->options(
                        \App\Models\User::where('rol', 'cliente')->pluck('name', 'id')->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => strtolower(trim($record->estado)) !== 'completado' && strtolower(trim($record->estado)) !== 'cancelado'),
                Tables\Actions\ViewAction::make(),
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ;
    }

    public static function getRelations(): array
    {
        return [
            HistorialesRelationManager::class,
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

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->schema([
                        TextEntry::make('nombre')->label('Nombre del Proyecto')->weight('bold'),
                        TextEntry::make('estado')->label('Estado'),
                        TextEntry::make('descripcion')->label('Descripción')->columnSpanFull(),
                        TextEntry::make('contrato.cliente.name')->label('Cliente')->default('Sin cliente'),
                        TextEntry::make('pmResponsable.name')->label('PM Responsable')->default('Sin PM'),
                        TextEntry::make('servicios')
                            ->label('Servicios asociados')
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record->servicios->count()) {
                                    return 'Sin servicios asociados';
                                }
                                return $record->servicios->pluck('nombre')->implode(', ');
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Fechas y Duración')
                    ->schema([
                        TextEntry::make('fecha_inicio')->label('Fecha de Inicio')->date(),
                        TextEntry::make('fecha_fin')->label('Fecha de Fin')->date(),
                        TextEntry::make('duracion_estimada')->label('Duración Estimada (días)'),
                        TextEntry::make('duracion_real')->label('Duración Real (días)'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Avances y Métricas')
                    ->schema([
                        TextEntry::make('horas_estimadas')->label('Horas Estimadas'),
                        TextEntry::make('horas_ejecutadas')->label('Horas Ejecutadas'),
                        TextEntry::make('porcentaje_avance_real')->label('Avance Real (%)'),
                        TextEntry::make('porcentaje_avance_planeado')->label('Avance Planeado (%)'),
                        TextEntry::make('avance_documentacion')
                            ->label('Avance de documentos Checklists (%)')
                            ->formatStateUsing(fn ($state) => is_null($state) ? 'Sin checklists' : $state . '%')
                            ->badge()
                            ->color(fn ($state) => is_null($state) ? 'gray' : ($state >= 100 ? 'success' : ($state >= 75 ? 'info' : ($state >= 50 ? 'warning' : 'danger')))),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Mejoras y Origen')
                    ->schema([
                        TextEntry::make('mejoras_continuas')->label('Mejoras Continuas')->columnSpanFull(),
                        TextEntry::make('origen')->label('Origen del Proyecto'),
                        TextEntry::make('nps_cliente')->label('NPS Cliente'),
                        TextEntry::make('riesgos_identificados')->label('Riesgos Identificados'),
                        TextEntry::make('riesgos_mitigados')->label('Riesgos Mitigados'),
                        TextEntry::make('fases_planeadas')->label('Fases Planeadas'),
                        TextEntry::make('fases_entregadas')->label('Fases Entregadas'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Riesgos y Fases')
                    ->schema([
                        TextEntry::make('riesgos_identificados')->label('Riesgos Identificados'),
                        TextEntry::make('riesgos_mitigados')->label('Riesgos Mitigados'),
                        TextEntry::make('porcentaje_riesgos_mitigados')
                            ->label('% Riesgos Mitigados')
                            ->formatStateUsing(fn($state) => is_null($state) ? 'N/A' : $state . '%')
                            ->badge()
                            ->color(fn($state) => is_null($state) ? 'gray' : ($state >= 100 ? 'success' : ($state >= 75 ? 'info' : ($state >= 50 ? 'warning' : 'danger')))),
                        TextEntry::make('fases_planeadas')->label('Fases Planeadas'),
                        TextEntry::make('fases_entregadas')->label('Fases Entregadas'),
                        TextEntry::make('porcentaje_fases_entregadas')
                            ->label('% Fases Entregadas')
                            ->formatStateUsing(fn($state) => is_null($state) ? 'N/A' : $state . '%')
                            ->badge()
                            ->color(fn($state) => is_null($state) ? 'gray' : ($state >= 100 ? 'success' : ($state >= 75 ? 'info' : ($state >= 50 ? 'warning' : 'danger')))),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Notas / Observaciones')
                    ->schema([
                        TextEntry::make('notas')->label('Notas / Observaciones')->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
