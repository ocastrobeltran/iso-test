<?php

namespace App\Filament\Resources\DiagnosticoResource\Pages;

use App\Filament\Resources\DiagnosticoResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewDiagnostico extends ViewRecord
{
    protected static string $resource = DiagnosticoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->url(fn () => $this->getResource()::getUrl('edit', ['record' => $this->record])),
        ];
    }
}
