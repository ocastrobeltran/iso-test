<?php

namespace App\Filament\Resources\TicketServicioResource\Pages;

use App\Filament\Resources\TicketServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTicketServicios extends ListRecords
{
    protected static string $resource = TicketServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
