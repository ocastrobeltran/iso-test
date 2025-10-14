<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpleabilidadSolicitudAdminResource\Pages;
use App\Models\EmpleabilidadSolicitud;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;

class EmpleabilidadSolicitudAdminResource extends Resource
{
    protected static ?string $model = EmpleabilidadSolicitud::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Formulario de Empleabilidad';
    protected static ?string $navigationGroup = 'Empleabilidad';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo_solicitud')
                    ->label('Tipo de Solicitud')
                    ->options([
                        'Nuevas implementaciones' => 'Nuevas implementaciones',
                        'Mejoras' => 'Mejoras',
                        'Consultas' => 'Consultas',
                        'Requerimientos' => 'Requerimientos',
                        'Otros' => 'Otros',
                    ])
                    ->required(),

                Forms\Components\Select::make('herramienta')
                    ->label('Herramienta')
                    ->options([
                        'Salesforce Marketing Cloud' => 'Salesforce Marketing Cloud',
                        'HubSpot' => 'HubSpot',
                        'Mailchimp' => 'Mailchimp',
                        'WordPress' => 'WordPress',
                        'Google Analytics' => 'Google Analytics',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('canal')
                    ->label('Canal')
                    ->maxLength(10)
                    ->minLength(9)
                    ->required()
                    ->helperText('Código alfanumérico de 9-10 caracteres'),

                Forms\Components\TextInput::make('quien_solicito')
                    ->label('Quién lo Solicitó')
                    ->email()
                    ->maxLength(255)
                    ->required()
                    ->helperText('Ingrese el correo electrónico'),

                Forms\Components\Textarea::make('descripcion_requerimiento')
                    ->label('Descripción del Requerimiento')
                    ->maxLength(255)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('solucion')
                    ->label('Solución')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('landing')
                    ->label('Landing, web o portal asociado')
                    ->maxLength(255)
                    ->url()
                    ->helperText('URL de la landing page'),

                Forms\Components\TextInput::make('responsable_ejecucion')
                    ->label('Responsable de Ejecución')
                    ->email()
                    ->maxLength(255)
                    ->helperText('Correo del responsable'),

                Forms\Components\DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->default(now()),

                Forms\Components\Select::make('mes')
                    ->label('Mes')
                    ->options([
                        'Enero' => 'Enero',
                        'Febrero' => 'Febrero',
                        'Marzo' => 'Marzo',
                        'Abril' => 'Abril',
                        'Mayo' => 'Mayo',
                        'Junio' => 'Junio',
                        'Julio' => 'Julio',
                        'Agosto' => 'Agosto',
                        'Septiembre' => 'Septiembre',
                        'Octubre' => 'Octubre',
                        'Noviembre' => 'Noviembre',
                        'Diciembre' => 'Diciembre',
                    ])
                    ->default(now()->format('F')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tipo_solicitud')
                    ->label('Tipo de Solicitud')
                    ->searchable()
                    ->sortable(),
                
                // Tables\Columns\TextColumn::make('herramienta')
                //     ->label('Herramienta')
                //     ->searchable()
                //     ->sortable(),
                
                Tables\Columns\TextColumn::make('canal')
                    ->label('Canal')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quien_solicito')
                    ->label('Solicitante')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                // Tables\Columns\TextColumn::make('descripcion_requerimiento')
                //     ->label('Descripción')
                //     ->limit(50)
                //     ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                //         $state = $column->getState();
                //         if (strlen($state) <= 50) {
                //             return null;
                //         }
                //         return $state;
                //     }),
                
                // Tables\Columns\TextColumn::make('responsable_ejecucion')
                //     ->label('Responsable')
                //     ->searchable()
                //     ->limit(30),
                
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha Inicio')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->label('Fecha Creación')
                    ->dateTime()
                    ->sortable(),
                
                // Tables\Columns\TextColumn::make('mes')
                //     ->label('Mes')
                //     ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_solicitud')
                    ->label('Tipo de Solicitud')
                    ->options([
                        'Nuevas implementaciones' => 'Nuevas implementaciones',
                        'Mejoras' => 'Mejoras',
                        'Consultas' => 'Consultas',
                        'Requerimientos' => 'Requerimientos',
                        'Otros' => 'Otros',
                    ]),
                
                Tables\Filters\SelectFilter::make('herramienta')
                    ->label('Herramienta')
                    ->options([
                        'Salesforce Marketing Cloud' => 'Salesforce Marketing Cloud',
                        'HubSpot' => 'HubSpot',
                        'Mailchimp' => 'Mailchimp',
                        'WordPress' => 'WordPress',
                        'Google Analytics' => 'Google Analytics',
                    ]),
                
                Tables\Filters\Filter::make('fecha_creacion')
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
                                fn ($query, $date) => $query->whereDate('fecha_creacion', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('fecha_creacion', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                //     Tables\Actions\ExportBulkAction::make()
                //         ->label('Exportar seleccionados')
                //         ->exporter(\App\Filament\Exports\EmpleabilidadSolicitudExporter::class),
                // ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_csv')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $filename = 'solicitudes_empleabilidad_' . now()->format('Y-m-d_H-i-s') . '.csv';
                        
                        return response()->streamDownload(function () {
                            $handle = fopen('php://output', 'w');
                            
                            // Encabezados del CSV con BOM para UTF-8
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                            
                            // Headers del CSV
                            fputcsv($handle, [
                                'ID',
                                'Tipo de Solicitud',
                                'Herramienta',
                                'Canal',
                                'Quién lo Solicitó',
                                'Descripción del Requerimiento',
                                'Solución',
                                'Landing',
                                'Responsable de Ejecución',
                                'Fecha de Inicio',
                                'Fecha de Creación',
                                'Mes'
                            ]);

                            // Obtener y exportar datos
                            \App\Models\EmpleabilidadSolicitud::all()->each(function ($solicitud) use ($handle) {
                                fputcsv($handle, [
                                    $solicitud->id,
                                    $solicitud->tipo_solicitud,
                                    $solicitud->herramienta,
                                    $solicitud->canal,
                                    $solicitud->quien_solicito,
                                    $solicitud->descripcion_requerimiento,
                                    $solicitud->solucion,
                                    $solicitud->landing,
                                    $solicitud->responsable_ejecucion,
                                    $solicitud->fecha_inicio ? $solicitud->fecha_inicio->format('Y-m-d') : '',
                                    $solicitud->fecha_creacion ? $solicitud->fecha_creacion->format('Y-m-d H:i:s') : '',
                                    $solicitud->mes,
                                ]);
                            });

                            fclose($handle);
                        }, $filename, [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),

            ])
            ->defaultSort('fecha_creacion', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpleabilidadSolicitudAdmins::route('/'),
            'create' => Pages\CreateEmpleabilidadSolicitudAdmin::route('/create'),
            // 'view' => Pages\ViewEmpleabilidadSolicitudAdmin::route('/{record}'),
            'edit' => Pages\EditEmpleabilidadSolicitudAdmin::route('/{record}/edit'),
        ];
    }
}