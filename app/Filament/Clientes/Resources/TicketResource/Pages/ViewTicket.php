<?php

namespace App\Filament\Clientes\Resources\TicketResource\Pages;

use App\Filament\Clientes\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

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
}