<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CambioResource\Pages;
use App\Models\Cambio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CambioResource extends Resource
{
    protected static ?string $model = Cambio::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Gestión de Cambios';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cambio')
                    ->schema([
                        Forms\Components\Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->relationship('proyecto', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->helperText('Selecciona el proyecto al que afecta este cambio'),

                        Forms\Components\Select::make('usuario_id')
                            ->label('Solicitado por')
                            ->relationship('usuario', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Usuario que solicita el cambio'),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción del Cambio')
                            ->required()
                            ->rows(4)
                            ->placeholder('Describe detalladamente el cambio solicitado...')
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha de Solicitud')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'Pendiente' => 'Pendiente de Revisión',
                                'En Análisis' => 'En Análisis',
                                'Aprobado' => 'Aprobado',
                                'Rechazado' => 'Rechazado',
                                'En Implementación' => 'En Implementación',
                                'Implementado' => 'Implementado',
                                'Cerrado' => 'Cerrado',
                            ])
                            ->default('Pendiente')
                            ->required()
                            ->reactive()
                            ->helperText(fn ($get) => match ($get('estado')) {
                                'Pendiente' => 'Solicitud pendiente de revisión técnica',
                                'En Análisis' => 'Se está evaluando el impacto del cambio',
                                'Aprobado' => 'Cambio aprobado, listo para implementar',
                                'Rechazado' => 'Cambio rechazado con justificación',
                                'En Implementación' => 'Cambio en proceso de implementación',
                                'Implementado' => 'Cambio completado exitosamente',
                                'Cerrado' => 'Solicitud cerrada',
                                default => null,
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Trazabilidad y Seguimiento')
                    ->schema([
                        Forms\Components\Textarea::make('justificacion')
                            ->label('Justificación/Notas')
                            ->placeholder('Justificación para aprobación/rechazo o notas del proceso...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('fecha_aprobacion')
                            ->label('Fecha de Aprobación')
                            ->visible(fn ($get) => in_array($get('estado'), ['Aprobado', 'En Implementación', 'Implementado'])),

                        Forms\Components\DatePicker::make('fecha_implementacion')
                            ->label('Fecha de Implementación')
                            ->visible(fn ($get) => $get('estado') === 'Implementado'),

                        Forms\Components\Select::make('prioridad')
                            ->label('Prioridad')
                            ->options([
                                'Baja' => 'Baja',
                                'Media' => 'Media',
                                'Alta' => 'Alta',
                                'Crítica' => 'Crítica',
                            ])
                            ->default('Media')
                            ->required(),

                        Forms\Components\Select::make('impacto')
                            ->label('Impacto Estimado')
                            ->options([
                                'Bajo' => 'Bajo - Sin afectación mayor',
                                'Medio' => 'Medio - Requiere coordinación',
                                'Alto' => 'Alto - Afecta cronograma/recursos',
                                'Crítico' => 'Crítico - Impacto significativo',
                            ])
                            ->default('Medio'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Solicitado por')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'En Análisis' => 'info',
                        'Aprobado' => 'success',
                        'Rechazado' => 'danger',
                        'En Implementación' => 'warning',
                        'Implementado' => 'success',
                        'Cerrado' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baja' => 'gray',
                        'Media' => 'warning',
                        'Alta' => 'danger',
                        'Crítica' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha Solicitud')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_implementacion')
                    ->label('Implementado')
                    ->date()
                    ->sortable()
                    ->placeholder('N/A'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->multiple()
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Análisis' => 'En Análisis',
                        'Aprobado' => 'Aprobado',
                        'Rechazado' => 'Rechazado',
                        'En Implementación' => 'En Implementación',
                        'Implementado' => 'Implementado',
                        'Cerrado' => 'Cerrado',
                    ]),

                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'Baja' => 'Baja',
                        'Media' => 'Media',
                        'Alta' => 'Alta',
                        'Crítica' => 'Crítica',
                    ]),

                Tables\Filters\SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'nombre')
                    ->searchable(),

                Tables\Filters\Filter::make('fecha_rango')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($q, $date) => $q->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($q, $date) => $q->whereDate('fecha', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => !in_array($record->estado, ['Aprobado', 'Rechazado'])),
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado === 'Pendiente')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'estado' => 'Aprobado',
                            'fecha_aprobacion' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->estado === 'Pendiente')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['estado' => 'Rechazado']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCambios::route('/'),
            'create' => Pages\CreateCambio::route('/create'),
            'edit' => Pages\EditCambio::route('/{record}/edit'),
            'view' => Pages\ViewCambio::route('/{record}'),
        ];
    }
}