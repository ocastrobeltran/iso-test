<?php

namespace App\Filament\Resources\ProyectoContratoResource\Pages;

use App\Filament\Resources\ProyectoContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProyectoContratos extends ListRecords
{
    protected static string $resource = ProyectoContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
