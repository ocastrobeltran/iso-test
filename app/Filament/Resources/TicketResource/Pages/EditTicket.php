<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si se asigna empleado y no tenía fecha de asignación
        if (isset($data['empleado_asignado_id']) && 
            $data['empleado_asignado_id'] && 
            !$this->record->fecha_asignacion) {
            $data['fecha_asignacion'] = now();
        }

        // Si se marca como resuelto y no tenía fecha de resolución
        if (isset($data['estado']) && 
            $data['estado'] === 'Resuelto' && 
            !$this->record->fecha_resolucion) {
            $data['fecha_resolucion'] = now();
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Obtén el primer proyecto asociado al ticket
        $data['proyecto_id'] = $this->record->proyectos()->first()?->id;

        // Obtén el usuario asignado como empleado (si existe)
        $data['empleado_asignado_id'] = $this->record->usuarios()->wherePivot('rol', 'empleado')->first()?->id;

        return $data;
    }

    protected function afterSave(): void
    {
        // Asociar proyecto desde contrato (si existe)
        $contrato = $this->record->contrato;
        if ($contrato && $contrato->proyecto) {
            $this->record->proyectos()->sync([$contrato->proyecto->id]);
        } else {
            $this->record->proyectos()->detach();
        }

        $empleadoId = $this->data['empleado_asignado_id'] ?? null;
        if ($empleadoId) {
            $this->record->usuarios()->sync([$empleadoId => ['rol' => 'empleado']]);
            // Si no tiene fecha de asignación, la establecemos
            if (!$this->record->fecha_asignacion) {
                $this->record->fecha_asignacion = now();
                $this->record->save();
            }
        } else {
            // Si se desasigna, elimina la relación y la fecha
            $this->record->usuarios()->detach();
            $this->record->fecha_asignacion = null;
            $this->record->save();
        }

        $nuevoComentario = $this->data['nuevo_comentario'] ?? null;
        if ($nuevoComentario) {
            $comentarios = $this->record->comentarios ?? [];
            $comentarios[] = [
                'usuario_id' => auth()->user()->id,
                'contenido' => $nuevoComentario,
                'fecha' => now()->toDateTimeString(),
            ];
            $this->record->comentarios = $comentarios;
            $this->record->save();
        }

        // Sincronizar servicios seleccionados
        $servicios = $this->data['servicios'] ?? [];
        $this->record->servicios()->sync($servicios);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function resolveRecord($key): Model
    {
        return static::getModel()::with(['contrato.fees', 'contrato.proyectos'])->findOrFail($key);
    }
}