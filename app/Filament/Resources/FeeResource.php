<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
use App\Filament\Resources\FeeResource\RelationManagers;
use App\Models\Fee;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Contrato;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Fees';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('nombre')->required()->maxLength(255),
                    Select::make('estado')
                        ->options([
                            'Activo' => 'Activo',
                            'Pausado' => 'Pausado',
                            'Finalizado' => 'Finalizado',
                            'Cancelado' => 'Cancelado',
                        ])
                        ->default('Activo')
                        ->required(),
                    Textarea::make('descripcion')->rows(3),
                    Select::make('contrato_id')
                        ->label('Contrato asociado')
                        ->options(\App\Models\Contrato::pluck('siglas', 'id')
                            ->map(fn($siglas, $id) => (string) ($siglas ?? "Contrato #{$id}"))
                            ->toArray())
                        ->searchable()
                        ->nullable()
                        ->helperText('Recuerde: El Fee se puede crear sin contrato.'),
                    // Select::make('cliente_id')
                    //     ->label('Cliente')
                    //     ->options(User::where('rol', 'cliente')->pluck('name', 'id'))
                    //     ->searchable()
                    //     ->required(),
                    Select::make('pm_responsable_id')
                        ->label('Responsable del Fee')
                        ->options(\App\Models\User::whereIn('rol', ['PM', 'Agencia'])->pluck('name','id')
                            ->map(fn($name, $id) => (string) ($name ?? "Usuario #{$id}"))
                            ->toArray())
                        ->searchable()
                        ->nullable(),
                    Select::make('servicios')
                        ->label('Servicios asociados')
                        ->multiple()
                        ->options(\App\Models\Servicio::pluck('nombre','id')
                            ->map(fn($nombre, $id) => (string) ($nombre ?? "Servicio #{$id}"))
                            ->toArray())
                        ->preload()
                        ->searchable(),
                ])->columns(2),

                Card::make()->schema([
                    Grid::make(2)->schema([
                        TextInput::make('mes')
                            ->label('Mes')
                            ->placeholder('Ej: 2025-10')
                            ->helperText('Formato YYYY-MM o nombre del mes')
                            ->required(),
                        Toggle::make('is_demanda')
                            ->label('Es a demanda')
                            ->reactive()
                            ->helperText('Si es a demanda no se asignan horas contratadas'),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('horas_contratadas')
                            ->label('Horas contratadas')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn ($get) => ! (bool) $get('is_demanda'))
                            ->disabled(fn ($get) => (bool) $get('is_demanda'))
                            ->required(fn ($get) => ! (bool) $get('is_demanda')),

                        TextInput::make('horas_ejecutadas')
                            ->label('Horas ejecutadas (Clockify)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Puede sincronizarse con Clockify')
                            ->visible(fn ($get) => $get('id') !== null),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('valor_mensual')
                            ->label('Valor mensual')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn ($get) => !$get('is_demanda')),
                        TextInput::make('horas_restantes')
                            ->label('Horas restantes')
                            ->disabled()
                            ->reactive()
                            ->dehydrated(false)
                            ->default(fn($record, $get) => $record?->horas_restantes ?? null)
                            ->visible(fn ($get) => $get('id') !== null),
                    ]),
                    Textarea::make('notas')->rows(3)->columnSpanFull(),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->searchable()->sortable(),
                TextColumn::make('cliente.name')->label('Cliente')->sortable(),
                TextColumn::make('contrato.titulo')->label('Contrato')->sortable(),
                TextColumn::make('contrato.cliente.name')
                    ->label('Cliente')
                    ->default('Sin cliente'),
                TextColumn::make('pmResponsable.name')->label('PM')->sortable(),
                TextColumn::make('mes')->label('Mes')->sortable(),
                BooleanColumn::make('is_demanda')->label('A Demanda'),
                TextColumn::make('horas_contratadas')->label('Horas CTD'),
                TextColumn::make('horas_ejecutadas')->label('Horas EJ'),
                TextColumn::make('valor_mensual')->money('COP'),
                TextColumn::make('estado')->label('Estado')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_demanda')->label('A Demanda')->options([
                    1 => 'Sí',
                    0 => 'No',
                ]),
                Tables\Filters\SelectFilter::make('cliente_id')->label('Cliente')->options(User::where('rol','cliente')->pluck('name','id')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TicketsRelationManager::class,
            // puede agregarse RelationManager::class para recursos, etc.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
            'view' => Pages\ViewFee::route('/{record}'),
        ];
    }
}