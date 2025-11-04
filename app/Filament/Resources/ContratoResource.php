<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContratoResource\Pages;
use App\Models\Contrato;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

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
                Forms\Components\TextInput::make('cotizacion')
                    ->label('Cotización')
                    ->placeholder('L3429')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Formato: L + número correlativo (ej. L3429). Debe iniciar con la letra L en mayúscula y continuar solo con dígitos; sin espacios, guiones ni letras adicionales. Es el identificador de la cotización en Legger y debe ser único.')
                    ->rule('regex:/^L\\d+$/')
                    ->unique(ignoreRecord: true),
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

                Forms\Components\TextInput::make('valor')
                    ->label('Valor')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('etapa')
                    ->label('Etapa')
                    ->maxLength(255),

                Forms\Components\Select::make('estado_factura')
                    ->label('Estado factura')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'OK para facturar' => 'OK para facturar',
                    ])
                    ->required(),
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
                Tables\Columns\TextColumn::make('cotizacion')
                    ->label('Cotización')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('etapa')
                    ->label('Etapa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado_factura')
                    ->label('Estado factura')
                    ->sortable(),
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
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Resumen')
                    ->schema([
                        InfoGrid::make(2)->schema([
                            TextEntry::make('titulo')->label('Título')->icon('heroicon-o-document-text'),
                            TextEntry::make('cotizacion')->label('Cotización')->icon('heroicon-o-tag'),
                            TextEntry::make('cliente.name')->label('Cliente')->default('—')->icon('heroicon-o-user'),
                            TextEntry::make('total_horas')->label('Total de horas')->icon('heroicon-o-clock'),
                            TextEntry::make('valor')->label('Valor')->money('COP')->icon('heroicon-o-cash'),
                            TextEntry::make('estado_factura')->label('Estado Factura')->badge()->icon('heroicon-o-check-circle'),
                        ]),
                        TextEntry::make('recursos_list')
                            ->label('Recursos asignados')
                            ->getStateUsing(fn ($record) =>
                                $record->recursos && $record->recursos->count()
                                    ? $record->recursos->map(fn($r) => $r->name . ' (' . ($r->pivot->horas_asignadas ?? 0) . 'h)')->implode(', ')
                                    : 'Sin recursos asignados'
                            )
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Métricas de Horas (Clockify)')
                    ->schema([
                        ViewEntry::make('metricas_clockify')
                            ->view('filament.infolists.contrato-metricas-clockify')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
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