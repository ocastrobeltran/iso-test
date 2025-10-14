<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateContrato extends CreateRecord
{
    protected static string $resource = ContratoResource::class;

    protected function afterCreate(): void
    {
        $recursos = $this->data['recursos'] ?? [];
        $pivotData = [];
        foreach ($recursos as $recurso) {
            if (isset($recurso['user_id'])) {
                $pivotData[$recurso['user_id']] = ['horas_asignadas' => $recurso['horas_asignadas'] ?? 0];
            }
        }
        $this->record->recursos()->sync($pivotData);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $totalHoras = $data['total_horas'] ?? 0;
        $suma = collect($data['recursos'] ?? [])->sum('horas_asignadas');
        if ($suma > $totalHoras) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'recursos.*.horas_asignadas' => 'La suma de horas asignadas a los recursos no puede superar el total de horas del contrato.',
            ]);
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $totalHoras = $data['total_horas'] ?? 0;
        $suma = collect($data['recursos'] ?? [])->sum('horas_asignadas');
        if ($suma > $totalHoras) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'recursos.*.horas_asignadas' => 'La suma de horas asignadas a los recursos no puede superar el total de horas del contrato.',
            ]);
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}