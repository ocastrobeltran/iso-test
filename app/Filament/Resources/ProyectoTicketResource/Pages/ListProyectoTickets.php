<?php

namespace App\Filament\Resources\ProyectoTicketResource\Pages;

use App\Filament\Resources\ProyectoTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProyectoTickets extends ListRecords
{
    protected static string $resource = ProyectoTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
