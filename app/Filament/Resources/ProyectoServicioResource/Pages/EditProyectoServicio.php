<?php

namespace App\Filament\Resources\ProyectoServicioResource\Pages;

use App\Filament\Resources\ProyectoServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProyectoServicio extends EditRecord
{
    protected static string $resource = ProyectoServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
