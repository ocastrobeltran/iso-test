<?php

namespace App\Filament\Clientes\Resources\CalificacionResource\Pages;

use App\Filament\Clientes\Resources\CalificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalificacion extends EditRecord
{
    protected static string $resource = CalificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
