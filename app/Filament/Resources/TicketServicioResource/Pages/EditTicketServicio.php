<?php

namespace App\Filament\Resources\TicketServicioResource\Pages;

use App\Filament\Resources\TicketServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicketServicio extends EditRecord
{
    protected static string $resource = TicketServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
