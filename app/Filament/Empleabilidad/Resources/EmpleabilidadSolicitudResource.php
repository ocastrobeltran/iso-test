<?php

namespace App\Filament\Empleabilidad\Resources;

use App\Filament\Empleabilidad\Resources\EmpleabilidadSolicitudResource\Pages;
use App\Models\EmpleabilidadSolicitud;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmpleabilidadSolicitudResource extends Resource
{
    protected static ?string $model = EmpleabilidadSolicitud::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationLabel = 'Nueva Solicitud';

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
                Tables\Columns\TextColumn::make('tipo_solicitud')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quien_solicito')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('fecha_creacion', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpleabilidadSolicituds::route('/'),
            'create' => Pages\CreateEmpleabilidadSolicitud::route('/create'),
        ];
    }
}