<?php

namespace App\Filament\Resources\ProyectoContratoResource\Pages;

use App\Filament\Resources\ProyectoContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProyectoContrato extends EditRecord
{
    protected static string $resource = ProyectoContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
