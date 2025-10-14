<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetalleconsumoResource\Pages;
use App\Models\Detalleconsumo;
use App\Models\Usuario;
use App\Models\Ticket;
use App\Models\Proyecto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class DetalleconsumoResource extends Resource
{
    protected static ?string $model = Detalleconsumo::class;
    protected static ?string $navigationGroup = 'Recursos y Cronogramas';
    protected static ?string $navigationLabel = 'Control de Horas';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationBadge(): ?string
    {
        $pendientes = static::getModel()::where('estado', 'Pendiente Aprobación')->count();
        return $pendientes > 0 ? (string) $pendientes : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Trabajo')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha de Trabajo')
                            ->required()
                            ->default(today())
                            ->maxDate(today())
                            ->columnSpan(1),

                        Forms\Components\Select::make('usuario_id')
                            ->label('Usuario')
                            ->relationship('usuario', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(auth()->id())
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('actividad')
                            ->label('Actividad Realizada')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Desarrollo de módulo de usuarios')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('modulo')
                            ->label('Módulo/Componente')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Autenticación, Dashboard, etc.')
                            ->columnSpan(1),

                        Forms\Components\Select::make('tipo_trabajo')
                            ->label('Tipo de Trabajo')
                            ->options([
                                'Desarrollo' => 'Desarrollo',
                                'Testing' => 'Testing/Pruebas',
                                'Documentación' => 'Documentación',
                                'Reunión' => 'Reunión',
                                'Soporte' => 'Soporte Técnico',
                                'Análisis' => 'Análisis',
                                'Diseño' => 'Diseño',
                                'Capacitación' => 'Capacitación',
                                'Otros' => 'Otros',
                            ])
                            ->default('Desarrollo')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción Detallada')
                            ->rows(3)
                            ->placeholder('Describe el trabajo realizado...')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Control de Horas')
                    ->schema([
                        Forms\Components\TextInput::make('horas')
                            ->label('Horas Trabajadas')
                            ->numeric()
                            ->required()
                            ->step(0.25)
                            ->minValue(0.25)
                            ->maxValue(24)
                            ->suffix('hrs')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-calcular horas facturables
                                $set('horas_facturables', $state);
                            }),

                        Forms\Components\TextInput::make('horas_facturables')
                            ->label('Horas Facturables')
                            ->numeric()
                            ->step(0.25)
                            ->minValue(0)
                            ->suffix('hrs')
                            ->helperText('Horas que se facturarán'),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'Borrador' => 'Borrador',
                                'Pendiente Aprobación' => 'Pendiente Aprobación',
                                'Aprobado' => 'Aprobado',
                                'Rechazado' => 'Rechazado',
                            ])
                            ->default('Borrador')
                            ->required(),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(2)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Recursos utilizados')
                    ->schema([
                        Forms\Components\Select::make('recursos')
                            ->label('Recursos utilizados')
                            ->multiple()
                            ->relationship('recursos', 'tipo') // Puedes mostrar tipo o un campo más descriptivo
                            ->searchable()
                            ->preload()
                            ->helperText('Selecciona los recursos utilizados en esta actividad'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Asignación')
                    ->schema([
                        Forms\Components\Select::make('tipo_asociacion')
                            ->label('Asignar a')
                            ->options([
                                'proyecto' => 'Proyecto',
                                'ticket' => 'Ticket',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('proyectos', []);
                                $set('tickets', []);
                            }),

                        Forms\Components\Select::make('proyectos')
                            ->label('Proyecto(s)')
                            ->relationship('proyectos', 'nombre')
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->visible(fn ($get) => $get('tipo_asociacion') === 'proyecto'),

                        Forms\Components\Select::make('tickets')
                            ->label('Ticket(s)')
                            ->relationship('tickets', 'titulo')
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->visible(fn ($get) => $get('tipo_asociacion') === 'ticket'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('usuario.nombre')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('actividad')
                    ->label('Actividad')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 35 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('modulo')
                    ->label('Módulo')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('tipo_trabajo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Desarrollo' => 'primary',
                        'Testing' => 'warning',
                        'Documentación' => 'info',
                        'Reunión' => 'gray',
                        'Soporte' => 'danger',
                        'Análisis' => 'purple',
                        'Diseño' => 'pink',
                        'Capacitación' => 'green',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('horas')
                    ->label('Horas')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . 'h')
                    ->alignRight()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn ($state) => number_format($state, 2) . 'h'),
                    ]),

                Tables\Columns\TextColumn::make('horas_facturables')
                    ->label('Facturables')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . 'h' : '-')
                    ->alignRight()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->formatStateUsing(fn ($state) => number_format($state, 2) . 'h'),
                    ]),

                Tables\Columns\TextColumn::make('tipo_asociacion')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'proyecto' => 'Proyecto',
                        'ticket' => 'Ticket',
                        default => 'Sin asignar'
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'proyecto' => 'success',
                        'ticket' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('asociado')
                    ->label('Asignado a')
                    ->limit(30),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Borrador' => 'gray',
                        'Pendiente Aprobación' => 'warning',
                        'Aprobado' => 'success',
                        'Rechazado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Borrador' => 'Borrador',
                        'Pendiente Aprobación' => 'Pendiente Aprobación',
                        'Aprobado' => 'Aprobado',
                        'Rechazado' => 'Rechazado',
                    ]),

                Tables\Filters\SelectFilter::make('tipo_trabajo')
                    ->label('Tipo de Trabajo')
                    ->options([
                        'Desarrollo' => 'Desarrollo',
                        'Testing' => 'Testing',
                        'Documentación' => 'Documentación',
                        'Reunión' => 'Reunión',
                        'Soporte' => 'Soporte',
                        'Análisis' => 'Análisis',
                        'Diseño' => 'Diseño',
                        'Capacitación' => 'Capacitación',
                        'Otros' => 'Otros',
                    ]),

                Tables\Filters\SelectFilter::make('usuario_id')
                    ->label('Usuario')
                    ->relationship('usuario', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fecha_rango')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('fecha_hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn ($q, $date) => $q->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn ($q, $date) => $q->whereDate('fecha', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('esta_semana')
                    ->label('Esta semana')
                    ->query(fn ($query) => $query->whereBetween('fecha', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])),

                Tables\Filters\Filter::make('este_mes')
                    ->label('Este mes')
                    ->query(fn ($query) => $query->whereMonth('fecha', now()->month)
                                                  ->whereYear('fecha', now()->year)),
            ])
            ->actions([
                Tables\Actions\Action::make('enviar_aprobacion')
                    ->label('Enviar')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn ($record) => $record->estado === 'Borrador')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['estado' => 'Pendiente Aprobación']);
                        
                        Notification::make()
                            ->title('Enviado para aprobación')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado === 'Pendiente Aprobación')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'estado' => 'Aprobado',
                            'aprobado_por' => auth()->id(),
                            'fecha_aprobacion' => now(),
                        ]);
                        
                        Notification::make()
                            ->title('Registro aprobado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->estado === 'Pendiente Aprobación')
                    ->form([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Motivo del rechazo')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'estado' => 'Rechazado',
                            'observaciones' => $data['observaciones'],
                        ]);
                        
                        Notification::make()
                            ->title('Registro rechazado')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => in_array($record->estado, ['Borrador', 'Rechazado'])),

                Tables\Actions\Action::make('duplicar')
                    ->label('Duplicar')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function ($record) {
                        $nuevo = $record->replicate();
                        $nuevo->fecha = today();
                        $nuevo->estado = 'Borrador';
                        $nuevo->aprobado_por = null;
                        $nuevo->fecha_aprobacion = null;
                        $nuevo->observaciones = null;
                        $nuevo->save();

                        // Duplicar relaciones
                        if ($record->proyectos()->exists()) {
                            $nuevo->proyectos()->sync($record->proyectos->pluck('id'));
                        }
                        if ($record->tickets()->exists()) {
                            $nuevo->tickets()->sync($record->tickets->pluck('id'));
                        }

                        Notification::make()
                            ->title('Registro duplicado')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('registro_rapido')
                    ->label('Registro Rápido')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('actividad')
                            ->label('Actividad')
                            ->required(),
                        Forms\Components\TextInput::make('modulo')
                            ->label('Módulo')
                            ->required(),
                        Forms\Components\TextInput::make('horas')
                            ->label('Horas')
                            ->numeric()
                            ->step(0.25)
                            ->required(),
                        Forms\Components\Select::make('tipo_trabajo')
                            ->label('Tipo')
                            ->options([
                                'Desarrollo' => 'Desarrollo',
                                'Testing' => 'Testing',
                                'Documentación' => 'Documentación',
                                'Reunión' => 'Reunión',
                                'Soporte' => 'Soporte',
                                'Análisis' => 'Análisis',
                                'Diseño' => 'Diseño',
                                'Otros' => 'Otros',
                            ])
                            ->default('Desarrollo')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        Detalleconsumo::create([
                            'actividad' => $data['actividad'],
                            'modulo' => $data['modulo'],
                            'horas' => $data['horas'],
                            'horas_facturables' => $data['horas'],
                            'tipo_trabajo' => $data['tipo_trabajo'],
                            'fecha' => today(),
                            'usuario_id' => auth()->id(),
                            'estado' => 'Borrador',
                        ]);

                        Notification::make()
                            ->title('Registro creado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('mis_horas_hoy')
                    ->label('Mis Horas Hoy')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->badge(fn () => Detalleconsumo::where('usuario_id', auth()->id())
                                                   ->whereDate('fecha', today())
                                                   ->sum('horas') . 'h')
                    ->action(function () {
                        return redirect(request()->fullUrlWithQuery([
                            'tableFilters' => [
                                'usuario_id' => ['value' => auth()->id()],
                                'fecha_rango' => [
                                    'fecha_desde' => today()->format('Y-m-d'),
                                    'fecha_hasta' => today()->format('Y-m-d'),
                                ]
                            ]
                        ]));
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('enviar_aprobacion_bulk')
                        ->label('Enviar a Aprobación')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            $records->each(function ($record) use (&$count) {
                                if ($record->estado === 'Borrador') {
                                    $record->update(['estado' => 'Pendiente Aprobación']);
                                    $count++;
                                }
                            });
                            
                            Notification::make()
                                ->title("$count registros enviados")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('aprobar_bulk')
                        ->label('Aprobar Seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            $records->each(function ($record) use (&$count) {
                                if ($record->estado === 'Pendiente Aprobación') {
                                    $record->update([
                                        'estado' => 'Aprobado',
                                        'aprobado_por' => auth()->id(),
                                        'fecha_aprobacion' => now(),
                                    ]);
                                    $count++;
                                }
                            });
                            
                            Notification::make()
                                ->title("$count registros aprobados")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\ExportBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDetalleconsumos::route('/'),
            'create' => Pages\CreateDetalleconsumo::route('/create'),
            'edit' => Pages\EditDetalleconsumo::route('/{record}/edit'),
        ];
    }
}