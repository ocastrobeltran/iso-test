<?php

namespace App\Filament\Clientes\Resources;

use App\Filament\Clientes\Resources\FeeResource\Pages;
use App\Models\Fee;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationGroup = 'Gestión de Proyectos';
    protected static ?string $navigationLabel = 'Fees';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        return parent::getEloquentQuery()
            ->where(function ($q) use ($userId) {
                $q->where('cliente_id', $userId)
                    ->orWhereHas('contrato', fn ($cq) => $cq->where('cliente_id', $userId))
                    ->orWhereHas('contratos', fn ($cq) => $cq->where('cliente_id', $userId));
            })
            ->with(['contrato.cliente', 'pmResponsable']);
    }

    public static function getNavigationBadge(): ?string
    {
        $userId = auth()->id();
        if (!$userId) return null;

        $count = static::getModel()::query()
            ->where(function ($q) use ($userId) {
                $q->where('cliente_id', $userId)
                    ->orWhereHas('contrato', fn ($cq) => $cq->where('cliente_id', $userId))
                    ->orWhereHas('contratos', fn ($cq) => $cq->where('cliente_id', $userId));
            })
            ->count();

        return (string) $count;
    }

    // No formulario para clientes (solo lectura)
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contrato.cotizacion')
                    ->label('Contrato')->default('Sin contrato')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('pmResponsable.name')
                    ->label('PM')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mes')
                    ->label('Mes')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_demanda')
                    ->label('A Demanda')
                    ->boolean(),
                Tables\Columns\TextColumn::make('horas_contratadas')
                    ->label('Horas CTD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('horas_ejecutadas')
                    ->label('Horas EJ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('horas_restantes')
                    ->label('Horas Restantes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_mensual')
                    ->money('COP')
                    ->label('Valor'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Pausado' => 'Pausado',
                        'Finalizado' => 'Finalizado',
                        'Cancelado' => 'Cancelado',
                    ]),
                Tables\Filters\TernaryFilter::make('is_demanda')
                    ->label('A Demanda'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]); // sin acciones masivas para clientes
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Resumen')
                    ->schema([
                        InfoGrid::make(2)
                            ->schema([
                                TextEntry::make('nombre')->label('Nombre'),
                                TextEntry::make('estado')->badge(),
                                TextEntry::make('mes')->label('Mes'),
                                TextEntry::make('is_demanda')
                                    ->label('A Demanda')
                                    ->formatStateUsing(fn (?bool $state): string => $state ? 'Sí' : 'No'),
                            ]),
                    ]),
                InfoSection::make('Contrato y Responsables')
                    ->schema([
                        InfoGrid::make(2)->schema([
                            TextEntry::make('contrato.cotizacion')
                                ->label('Contrato')
                                ->default('Sin contrato'),
                            TextEntry::make('pmResponsable.name')
                                ->label('PM Responsable')
                                ->default('—'),
                        ]),
                    ]),
                InfoSection::make('Métricas')
                    ->schema([
                        InfoGrid::make(3)
                            ->schema([
                                TextEntry::make('horas_contratadas')
                                    ->label('Horas contratadas'),
                                TextEntry::make('horas_ejecutadas')
                                    ->label('Horas ejecutadas'),
                                TextEntry::make('horas_restantes')
                                    ->label('Horas restantes')->default('—'),
                            ]),
                        TextEntry::make('valor_mensual')
                            ->label('Valor mensual')->money('COP'),
                    ]),
                InfoSection::make('Descripción')
                    ->schema([
                        TextEntry::make('descripcion')
                            ->default('—')->columnSpanFull(),
                    ]),
                InfoSection::make('Notas')
                    ->schema([
                        TextEntry::make('notas')
                            ->default('—')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFees::route('/'),
            'view' => Pages\ViewFee::route('/{record}'),
        ];
    }
}