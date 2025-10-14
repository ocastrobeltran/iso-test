<?php

namespace App\Filament\Resources\ProyectoResource\Pages;

use App\Filament\Resources\ProyectoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class EditProyecto extends EditRecord
{
    protected static string $resource = ProyectoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Ya no necesitas sincronizar contratos, solo servicios si aplica
        $servicios = $this->data['servicios'] ?? [];
        $this->record->servicios()->sync($servicios);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            isset($data['fecha_inicio'], $data['fecha_fin']) &&
            $data['fecha_inicio'] > $data['fecha_fin']
        ) {
            Notification::make()
                ->title('Error en las fechas')
                ->body('La fecha de inicio no puede ser mayor que la fecha de fin.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'fecha_inicio' => 'La fecha de inicio no puede ser mayor que la fecha de fin.',
                'fecha_fin' => 'La fecha de fin no puede ser menor que la fecha de inicio.',
            ]);
        }
        return $data;
    }
}