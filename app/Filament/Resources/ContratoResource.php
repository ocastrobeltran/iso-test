<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContratoResource\Pages;
use App\Filament\Resources\ContratoResource\RelationManagers;
use App\Models\Contrato;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;


class ContratoResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static ?string $navigationGroup = 'Servicios y Contratos';
    protected static ?string $navigationLabel = 'Contratos';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->label('Título')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('siglas')
                    ->label('Siglas')
                    ->maxLength(20)
                    ->required(),
                Forms\Components\TextInput::make('total_horas')
                    ->numeric()
                    ->label('Total de horas')
                    ->required()
                    ->minValue(fn ($context, $record) => $context === 'edit' ? $record?->total_horas : 0)
                    ->helperText(fn ($context, $record) =>
                        $context === 'edit'
                            ? "Solo puedes aumentar el número de horas. El valor mínimo es {$record?->total_horas}."
                            : null
                    ),
                Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship(
                        name: 'cliente',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('rol', 'cliente')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\Repeater::make('recursos')
                    ->label('Recursos asignados')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Recurso')
                            ->options(
                                User::where('rol', '!=', 'cliente')->pluck('name', 'id')
                            )
                            ->required(),
                        Forms\Components\TextInput::make('horas_asignadas')
                            ->label('Horas asignadas')
                            ->numeric()
                            ->required(),
                    ])
                    ->helperText(function (callable $get) {
                        $recursos = $get('recursos') ?? [];
                        $total = $get('total_horas') ?? 0;
                        $suma = collect($recursos)->sum('horas_asignadas');
                        if ($suma > $total) {
                            return '⚠️ La suma de horas asignadas supera el total de horas del contrato.';
                        }
                        return null;
                    })
                    ->createItemButtonLabel('Agregar recurso')
                    ->visible(fn ($context) => $context != 'view'),

                Forms\Components\Placeholder::make('recursos_view')
                    ->label('Recursos asignados')
                    ->content(fn ($record) =>
                        $record && $record->recursos && $record->recursos->count()
                            ? $record->recursos->map(fn($recurso) =>
                                $recurso->name . ' (' . ($recurso->pivot->horas_asignadas ?? 0) . 'h)'
                            )->implode(', ')
                            : 'Sin recursos asignados'
                    )
                    ->visible(fn ($context) => $context === 'view')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('estado')
                    ->maxLength(255)
                    ->label('Estado')
                    ->required(),
                // Forms\Components\Select::make('tipo_usuario')
                //     ->label('Tipo de usuario')
                //     ->options([
                //         'cliente' => 'Cliente',
                //         'proveedor' => 'Proveedor',
                //     ])
                //     ->required()
                //     ->live()
                //     ->helperText('Recuerde: El cliente o proveedor debe estar registado previamente.')
                //     ->visible(fn ($context) => $context === 'create'),

                // Forms\Components\Select::make('cliente_id')
                //     ->label('Cliente')
                //     ->relationship(
                //         name: 'cliente',
                //         titleAttribute: 'name',
                //         modifyQueryUsing: function ($query, $get) {
                //             $tipo = $get('tipo_usuario');
                //             if ($tipo === 'cliente') {
                //                 $query->where('rol', 'cliente');
                //             }
                //         }
                //     )
                //     ->searchable()
                //     ->preload()
                //     ->live()
                //     ->required(fn ($get) => $get('tipo_usuario') === 'cliente')
                //     ->visible(fn ($get, $context) => $get('tipo_usuario') === 'cliente' && $context === 'create')
                //     ->disabled(fn ($get, $context) => !$get('tipo_usuario') || $context !== 'create'),

                // Forms\Components\Select::make('proveedors')
                //     ->label('Proveedor')
                //     // ->multiple()
                //     ->relationship('proveedors', 'nombre')
                //     ->preload()
                //     ->searchable()
                //     ->nullable()
                //     ->visible(fn ($get) => $get('tipo_usuario') === 'proveedor'),

                // Forms\Components\Placeholder::make('cliente_view')
                //     ->label('Cliente')
                //     ->content(fn ($record) =>
                //         $record->cliente?->name ?? 'Sin cliente'
                //     )
                //     ->visible(fn ($context, $record) =>
                //         $context === 'view' && $record->cliente
                //     ),

                // Forms\Components\Placeholder::make('proveedor_view')
                //     ->label('Proveedor')
                //     ->content(fn ($record) =>
                //         $record->proveedors?->pluck('nombre')->implode(', ') ?: 'Sin proveedor'
                //     )
                //     ->visible(fn ($context, $record) =>
                //         $context === 'view' && $record->proveedors && $record->proveedors->count() > 0
                //     ),
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

                Tables\Columns\TextColumn::make('siglas')
                    ->label('Siglas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_horas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente.name') 
                    ->label('Cliente')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recursos')
                    ->label('Recursos')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->recursos
                            ? $record->recursos->map(function ($recurso) {
                                return $recurso->name . ' (' . ($recurso->pivot->horas_asignadas ?? 0) . 'h)';
                            })->implode(', ')
                            : null;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(
                        Contrato::query()->distinct()->pluck('estado', 'estado')->toArray()
                    ),
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->searchable()
                    ->options(
                        \App\Models\User::where('rol', 'cliente')->orWhere('rol', 'Proveedor')->pluck('name', 'id')->toArray()
                    ),
                Tables\Filters\SelectFilter::make('proveedors')
                    ->label('Proveedor')
                    ->searchable()
                    ->relationship('proveedors', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContratos::route('/'),
            'create' => Pages\CreateContrato::route('/create'),
            'edit' => Pages\EditContrato::route('/{record}/edit'),
            'view' => Pages\ViewContrato::route('/{record}'),
        ];
    }
}
