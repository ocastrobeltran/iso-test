<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->visible(fn ($record) => strtolower(trim($record->estado)) !== 'cerrado')
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->url(fn () => $this->getResource()::getUrl('edit', ['record' => $this->record])),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Obtén el usuario asignado como empleado (si existe)
        $data['empleado_asignado_id'] = $this->record->usuarios()->wherePivot('rol', 'empleado')->first()?->id;
        return $data;
    }
}