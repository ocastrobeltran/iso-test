<?php

namespace App\Filament\Resources\DetalleconsumoResource\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use App\Models\User;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;

class HorasClockifyTable extends BaseWidget
{
    public ?int $userId = null;
    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('project')
                ->label('Proyecto'),
            Tables\Columns\TextColumn::make('description')
                ->label('Descripción'),
            Tables\Columns\TextColumn::make('start')
                ->label('Inicio'),
            Tables\Columns\TextColumn::make('end')
                ->label('Fin'),
            Tables\Columns\TextColumn::make('duration')
                ->label('Duración (h)')
                ->formatStateUsing(fn ($state) => round($state / 3600, 2)),
        ];
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        // Retorna un query dummy, por ejemplo de User, solo para cumplir la interfaz
        return User::query()->whereNull('id');
    }

    public function getTableRecords(): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->userId || !$this->fechaInicio || !$this->fechaFin) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $user = User::find($this->userId);
        if (!$user || !$user->clockify_id) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $entries = $user->getHorasClockify(
            Carbon::parse($this->fechaInicio)->toIso8601String(),
            Carbon::parse($this->fechaFin)->toIso8601String()
        );

        return new \Illuminate\Database\Eloquent\Collection(
            collect($entries)->map(function ($entry) {
                return (object)[
                    'project' => $entry['project']['name'] ?? '',
                    'description' => $entry['description'] ?? '',
                    'start' => $entry['timeInterval']['start'] ?? '',
                    'end' => $entry['timeInterval']['end'] ?? '',
                    'duration' => $entry['timeInterval']['duration'] ?? 0,
                ];
            })->toArray()
        );
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('userId')
                ->label('Usuario')
                ->options(User::whereNotNull('clockify_id')->pluck('name', 'id'))
                ->query(function ($query, $value) {
                    $this->userId = $value;
                }),
            Tables\Filters\Filter::make('fechaInicio')
                ->form([
                    DatePicker::make('fechaInicio')
                        ->label('Desde')
                        ->required(),
                    DatePicker::make('fechaFin')
                        ->label('Hasta')
                        ->required(),
                ])
                ->query(function ($query, $data) {
                    $this->fechaInicio = $data['fechaInicio'];
                    $this->fechaFin = $data['fechaFin'];
                }),
        ];
    }
}