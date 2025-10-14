<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChecklistResource\Pages;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ChecklistResource extends Resource
{
    protected static ?string $model = Checklist::class;

    protected static ?string $navigationGroup = 'Calidad del Servicio';
    protected static ?string $navigationLabel = 'Checklists de Proyecto';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del Checklist')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Checklist de Pre-entrega'),

                        Forms\Components\Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->relationship('proyecto', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Describe el propósito de este checklist...')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('categoria')
                            ->options([
                                'Planificación' => 'Planificación',
                                'Desarrollo' => 'Desarrollo',
                                'Testing' => 'Testing',
                                'Entrega' => 'Entrega',
                                'Post-entrega' => 'Post-entrega',
                                'Calidad' => 'Calidad',
                                'Seguridad' => 'Seguridad',
                            ])
                            ->default('Desarrollo')
                            ->required(),

                        Forms\Components\Select::make('prioridad')
                            ->options([
                                'Baja' => 'Baja',
                                'Media' => 'Media',
                                'Alta' => 'Alta',
                                'Crítica' => 'Crítica',
                            ])
                            ->default('Media')
                            ->required(),

                        Forms\Components\Select::make('responsable_id')
                            ->label('Responsable')
                            ->relationship('responsable', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Usuario responsable de completar este checklist'),

                        Forms\Components\DatePicker::make('fecha_vencimiento')
                            ->label('Fecha Límite')
                            ->nullable()
                            ->helperText('Fecha límite para completar este checklist'),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'Completado' => 'Completado',
                                'Cancelado' => 'Cancelado',
                            ])
                            ->default('Activo')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Items del Checklist')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('titulo')
                                    ->label('Título del Item')
                                    ->required()
                                    ->placeholder('Ej: Verificar configuración de base de datos'),

                                Forms\Components\Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->rows(2)
                                    ->placeholder('Descripción detallada del item...')
                                    ->columnSpanFull(),

                                Forms\Components\Hidden::make('orden')
                                    ->default(fn ($get) => $get('../../items') ? count($get('../../items')) + 1 : 1),

                                Forms\Components\Toggle::make('completado')
                                    ->label('Completado')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $set('fecha_completado', now());
                                            $set('completado_por', auth()->id());
                                        } else {
                                            $set('fecha_completado', null);
                                            $set('completado_por', null);
                                        }
                                    }),

                                Forms\Components\DateTimePicker::make('fecha_completado')
                                    ->label('Fecha de Completado')
                                    ->visible(fn ($get) => $get('completado'))
                                    ->disabled(),

                                Forms\Components\Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->rows(2)
                                    ->visible(fn ($get) => $get('completado'))
                                    ->placeholder('Observaciones sobre la completación...')
                                    ->columnSpanFull(),
                            ])
                            ->orderColumn('orden')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['titulo'] ?? null)
                            ->addActionLabel('Agregar Item')
                            ->columnSpanFull()
                            ->minItems(1),
                    ]),

                Forms\Components\Section::make('Notas Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas del Checklist')
                            ->rows(3)
                            ->placeholder('Notas adicionales sobre este checklist...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Checklist')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('categoria')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Planificación' => 'info',
                        'Desarrollo' => 'warning',
                        'Testing' => 'purple',
                        'Entrega' => 'success',
                        'Post-entrega' => 'gray',
                        'Calidad' => 'indigo',
                        'Seguridad' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('porcentaje_completado')
                    ->label('Progreso')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 75 => 'warning',
                        $state >= 50 => 'info',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->limit(20)
                    ->placeholder('Sin asignar'),

                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('Vence')
                    ->formatStateUsing(function ($record) {
                        if (!$record->fecha_vencimiento) return 'Sin fecha';
                        
                        $fecha = $record->fecha_vencimiento->format('d/m/Y');
                        
                        if ($record->porcentaje_completado >= 100) {
                            return "✅ {$fecha}";
                        }
                        
                        if ($record->fecha_vencimiento < now()) {
                            $diasVencido = now()->diffInDays($record->fecha_vencimiento);
                            return "⚠️ {$fecha} (vencido {$diasVencido}d)";
                        }
                        
                        if ($record->fecha_vencimiento <= now()->addDays(3)) {
                            $diasRestantes = now()->diffInDays($record->fecha_vencimiento);
                            return "🔔 {$fecha} ({$diasRestantes}d)";
                        }
                        
                        return $fecha;
                    })
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->fecha_vencimiento) return 'gray';
                        if ($record->porcentaje_completado >= 100) return 'success';
                        if ($record->fecha_vencimiento < now()) return 'danger';
                        if ($record->fecha_vencimiento <= now()->addDays(3)) return 'warning';
                        return 'primary';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Completado' => 'info',
                        'Inactivo' => 'warning',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Completado' => 'Completado',
                        'Inactivo' => 'Inactivo',
                        'Cancelado' => 'Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('categoria')
                    ->options([
                        'Planificación' => 'Planificación',
                        'Desarrollo' => 'Desarrollo',
                        'Testing' => 'Testing',
                        'Entrega' => 'Entrega',
                        'Post-entrega' => 'Post-entrega',
                        'Calidad' => 'Calidad',
                        'Seguridad' => 'Seguridad',
                    ]),

                Tables\Filters\SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'nombre')
                    ->searchable(),

                Tables\Filters\Filter::make('por_vencer')
                    ->label('Por Vencer (7 días)')
                    ->query(fn ($query) => $query->where('fecha_vencimiento', '<=', now()->addDays(7))
                                                ->where('estado', 'Activo')
                                                ->where('porcentaje_completado', '<', 100)),

                Tables\Filters\Filter::make('vencidos')
                    ->label('Vencidos')
                    ->query(fn ($query) => $query->where('fecha_vencimiento', '<', now())
                                                ->where('estado', 'Activo')
                                                ->where('porcentaje_completado', '<', 100)),

                Tables\Filters\Filter::make('incompletos')
                    ->label('Incompletos')
                    ->query(fn ($query) => $query->where('porcentaje_completado', '<', 100)
                                                ->where('estado', 'Activo')),
            ])
            ->actions([
                Tables\Actions\Action::make('completar_item')
                    ->label('Marcar Item')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado === 'Activo' && $record->porcentaje_completado < 100)
                    ->form([
                        Forms\Components\Select::make('item_id')
                            ->label('Seleccionar Item')
                            ->options(fn ($record) => $record->items()
                                ->where('completado', false)
                                ->pluck('titulo', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(2)
                            ->placeholder('Observaciones sobre la completación...'),
                    ])
                    ->action(function ($record, array $data) {
                        $item = \App\Models\ChecklistItem::find($data['item_id']);
                        $item->update([
                            'completado' => true,
                            'fecha_completado' => now(),
                            'completado_por' => auth()->id(),
                            'observaciones' => $data['observaciones'] ?? null,
                        ]);
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('crear_desde_template')
                    ->label('Crear desde Template')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('template')
                            ->label('Seleccionar Template')
                            ->options([
                                'pre_entrega' => 'Pre-entrega',
                                'testing' => 'Testing',
                                'seguridad' => 'Seguridad',
                            ])
                            ->required(),
                        Forms\Components\Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->relationship('proyecto', 'nombre')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('responsable_id')
                            ->label('Responsable')
                            ->relationship('responsable', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (array $data) {
                        \App\Services\ChecklistTemplateService::crearTemplate(
                            $data['template'],
                            $data['proyecto_id'],
                            $data['responsable_id'] ?? null
                        );
                    }),

                Tables\Actions\Action::make('duplicar')
                    ->label('Duplicar')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function ($record) {
                        $nuevoChecklist = $record->replicate();
                        $nuevoChecklist->nombre = $record->nombre . ' (Copia)';
                        $nuevoChecklist->estado = 'Activo';
                        $nuevoChecklist->porcentaje_completado = 0;
                        $nuevoChecklist->fecha_completado = null;
                        $nuevoChecklist->save();

                        foreach ($record->items as $item) {
                            $nuevoItem = $item->replicate();
                            $nuevoItem->checklist_id = $nuevoChecklist->id;
                            $nuevoItem->completado = false;
                            $nuevoItem->fecha_completado = null;
                            $nuevoItem->completado_por = null;
                            $nuevoItem->observaciones = null;
                            $nuevoItem->save();
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('crear_desde_template')
                    ->label('Crear desde Template')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('area')
                            ->label('Área')
                            ->options([
                                'proyectos' => 'Gestión de Proyectos',
                                'arquitectura' => 'Arquitectura de Software',
                                'ux_ui' => 'UX/UI Design',
                                'testing' => 'Testing y QA',
                                'desarrollo' => 'Desarrollo',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($set) => $set('template', null)),

                        Forms\Components\Select::make('template')
                            ->label('Template')
                            ->options(function ($get) {
                                return match($get('area')) {
                                    'proyectos' => [
                                        'proyecto_inicio' => 'Inicio de Proyecto',
                                        'proyecto_seguimiento' => 'Seguimiento de Proyecto',
                                        'proyecto_cierre' => 'Cierre de Proyecto',
                                        'pre_entrega' => 'Pre-entrega de Proyecto',
                                        'deployment' => 'Deployment y Go-Live',
                                    ],
                                    'arquitectura' => [
                                        'arquitectura_diseno' => 'Diseño de Arquitectura',
                                        'arquitectura_revision' => 'Revisión de Arquitectura',
                                        'arquitectura_microservicios' => 'Arquitectura de Microservicios',
                                    ],
                                    'ux_ui' => [
                                        'ux_investigacion' => 'Investigación UX',
                                        'ux_diseno' => 'Diseño UX/UI',
                                        'ux_testing' => 'Testing de UX',
                                    ],
                                    'testing' => [
                                        'testing_funcional' => 'Testing Funcional',
                                        'testing_performance' => 'Testing de Performance',
                                        'testing_seguridad' => 'Testing de Seguridad',
                                        'testing_automatizado' => 'Testing Automatizado',
                                        'seguridad' => 'Auditoría de Seguridad',
                                    ],
                                    'desarrollo' => [
                                        'calidad_codigo' => 'Calidad de Código',
                                    ],
                                    default => []
                                };
                            })
                            ->required()
                            ->reactive()
                            ->helperText(function ($get) {
                                $descriptions = [
                                    'proyecto_inicio' => 'Verificaciones esenciales para iniciar un proyecto exitosamente',
                                    'proyecto_seguimiento' => 'Checklist semanal para seguimiento de proyectos en ejecución',
                                    'proyecto_cierre' => 'Actividades necesarias para cerrar exitosamente un proyecto',
                                    'arquitectura_diseno' => 'Checklist para el diseño de arquitectura de software',
                                    'arquitectura_revision' => 'Revisión y validación de la arquitectura propuesta',
                                    'arquitectura_microservicios' => 'Checklist específico para arquitecturas basadas en microservicios',
                                    'ux_investigacion' => 'Proceso de investigación y análisis de experiencia de usuario',
                                    'ux_diseno' => 'Proceso de diseño de interfaz y experiencia de usuario',
                                    'ux_testing' => 'Pruebas de usabilidad y experiencia de usuario',
                                    'testing_funcional' => 'Verificaciones completas de funcionalidad del sistema',
                                    'testing_performance' => 'Pruebas de rendimiento y escalabilidad',
                                    'testing_seguridad' => 'Auditoría completa de seguridad de la aplicación',
                                    'testing_automatizado' => 'Implementación de suite de pruebas automatizadas',
                                    'pre_entrega' => 'Verificaciones esenciales antes de entregar un proyecto',
                                    'seguridad' => 'Checklist completo de verificaciones de seguridad',
                                    'calidad_codigo' => 'Verificaciones de calidad y estándares de código',
                                    'deployment' => 'Checklist para deployment seguro a producción',
                                ];
                                return $descriptions[$get('template')] ?? 'Selecciona un template para ver su descripción';
                            }),
                            
                        Forms\Components\Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->options(\App\Models\Proyecto::pluck('nombre', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('responsable_id')
                            ->label('Responsable (opcional)')
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccionar responsable...'),
                    ])
                    ->action(function (array $data) {
                        $checklist = \App\Services\ChecklistTemplateService::crearTemplate(
                            $data['template'],
                            $data['proyecto_id'],
                            $data['responsable_id'] ?? null
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Checklist creado')
                            ->body("El checklist '{$checklist->nombre}' fue creado exitosamente.")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChecklists::route('/'),
            'create' => Pages\CreateChecklist::route('/create'),
            'edit' => Pages\EditChecklist::route('/{record}/edit'),
            'view' => Pages\ViewChecklist::route('/{record}'),
        ];
    }
}