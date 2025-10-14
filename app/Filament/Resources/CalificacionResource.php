<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalificacionResource\Pages;
use App\Models\Calificacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CalificacionResource extends Resource
{
    protected static ?string $model = Calificacion::class;

    protected static ?string $navigationGroup = 'Calidad del Servicio';
    protected static ?string $navigationLabel = 'Calificaciones';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationIcon = 'heroicon-o-star';

    // Solo mostrar vista - sin formularios editables
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('puntaje_display')
                    ->label('Calificación')
                    ->content(fn ($record) => $record ? str_repeat('⭐', $record->puntaje) . " ({$record->puntaje}/5)" : 'N/A'),

                Forms\Components\Placeholder::make('comentario_display')
                    ->label('Comentario')
                    ->content(fn ($record) => $record?->comentario ?: 'Sin comentarios'),

                Forms\Components\Placeholder::make('cliente_display')
                    ->label('Cliente')
                    ->content(fn ($record) => $record?->usuario?->nombre ?: 'N/A'),

                Forms\Components\Placeholder::make('ticket_display')
                    ->label('Ticket')
                    ->content(fn ($record) => $record?->ticket?->titulo ?: 'N/A'),

                Forms\Components\Placeholder::make('fecha_display')
                    ->label('Fecha de Calificación')
                    ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i') ?: 'N/A'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.nombre')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('ticket.titulo')
                    ->label('Ticket')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('puntaje')
                    ->label('Calificación')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . " ({$state}/5)")
                    ->sortable()
                    ->alignCenter(),

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

                Tables\Columns\TextColumn::make('ticket.estado')
                    ->label('Estado del Ticket')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Abierto' => 'danger',
                        'En Progreso' => 'warning', 
                        'Resuelto' => 'success',
                        'Cerrado' => 'gray',
                        default => 'gray',
                    }),
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

                Tables\Filters\Filter::make('fecha_calificacion')
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
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('usuario_id')
                    ->label('Cliente')
                    ->relationship('usuario', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalles')
                    ->modalHeading('Detalles de la Calificación')
                    ->modalWidth('md'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Exportar seleccionadas')
                        ->exporter(\App\Filament\Exports\CalificacionExporter::class),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Exportar todas')
                    ->exporter(\App\Filament\Exports\CalificacionExporter::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalificacions::route('/'),
            // 'view' => Pages\ViewCalificacion::route('/{record}'),
        ];
    }

    // Deshabilitar creación y edición
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}