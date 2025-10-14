<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MejoraResource\Pages;
use App\Models\Mejora;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MejoraResource extends Resource
{
    protected static ?string $model = Mejora::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Mejoras Continuas';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Mejora')
                    ->schema([
                        Forms\Components\Select::make('proyecto_id')
                            ->label('Proyecto')
                            ->relationship('proyecto', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Proyecto al que se aplicará la mejora'),

                        Forms\Components\Select::make('origen')
                            ->label('Propuesta por')
                            ->relationship('usuario', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Usuario que propone la mejora'),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción de la Mejora')
                            ->required()
                            ->rows(4)
                            ->placeholder('Describe la mejora propuesta y sus beneficios...')
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('fecha_propuesta')
                            ->label('Fecha de Propuesta')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),

                        Forms\Components\Select::make('estado')
                            ->options([
                                'Propuesta' => 'Propuesta',
                                'En evaluación' => 'En Evaluación',
                                'Aprobada' => 'Aprobada',
                                'Rechazada' => 'Rechazada',
                                'En Implementación' => 'En Implementación',
                                'Implementada' => 'Implementada',
                                'Verificada' => 'Verificada',
                            ])
                            ->default('Propuesta')
                            ->required()
                            ->reactive(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Evaluación y Seguimiento')
                    ->schema([
                        Forms\Components\Textarea::make('beneficios_esperados')
                            ->label('Beneficios Esperados')
                            ->placeholder('¿Qué beneficios se esperan de esta mejora?')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('recursos_necesarios')
                            ->label('Recursos Necesarios')
                            ->placeholder('¿Qué recursos se requieren para implementar?')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('prioridad')
                            ->label('Prioridad')
                            ->options([
                                'Baja' => 'Baja',
                                'Media' => 'Media',
                                'Alta' => 'Alta',
                            ])
                            ->default('Media'),

                        Forms\Components\DatePicker::make('fecha_implementacion_estimada')
                            ->label('Fecha Implementación Estimada')
                            ->visible(fn ($get) => in_array($get('estado'), ['Aprobada', 'En Implementación'])),

                        Forms\Components\DatePicker::make('fecha_implementacion_real')
                            ->label('Fecha Implementación Real')
                            ->visible(fn ($get) => in_array($get('estado'), ['Implementada', 'Verificada'])),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->placeholder('Notas adicionales sobre la mejora...')
                            ->rows(2)
                            ->columnSpanFull(),
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
                    ->label('Propuesta por')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Propuesta' => 'gray',
                        'En evaluación' => 'warning',
                        'Aprobada' => 'success',
                        'Rechazada' => 'danger',
                        'En Implementación' => 'info',
                        'Implementada' => 'success',
                        'Verificada' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baja' => 'gray',
                        'Media' => 'warning',
                        'Alta' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('fecha_propuesta')
                    ->label('Fecha Propuesta')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_implementacion_real')
                    ->label('Implementada')
                    ->date()
                    ->sortable()
                    ->placeholder('N/A'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->multiple()
                    ->options([
                        'Propuesta' => 'Propuesta',
                        'En evaluación' => 'En Evaluación',
                        'Aprobada' => 'Aprobada',
                        'Rechazada' => 'Rechazada',
                        'En Implementación' => 'En Implementación',
                        'Implementada' => 'Implementada',
                        'Verificada' => 'Verificada',
                    ]),

                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'Baja' => 'Baja',
                        'Media' => 'Media',
                        'Alta' => 'Alta',
                    ]),

                Tables\Filters\SelectFilter::make('proyecto_id')
                    ->label('Proyecto')
                    ->relationship('proyecto', 'nombre')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->estado === 'En evaluación')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['estado' => 'Aprobada']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_propuesta', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMejoras::route('/'),
            'create' => Pages\CreateMejora::route('/create'),
            'edit' => Pages\EditMejora::route('/{record}/edit'),
            'view' => Pages\ViewMejora::route('/{record}'),
        ];
    }
}