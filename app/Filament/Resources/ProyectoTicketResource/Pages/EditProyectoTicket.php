<?php

namespace App\Filament\Resources\ProyectoTicketResource\Pages;

use App\Filament\Resources\ProyectoTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProyectoTicket extends EditRecord
{
    protected static string $resource = ProyectoTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
