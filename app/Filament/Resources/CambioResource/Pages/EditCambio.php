<?php

namespace App\Filament\Resources\CambioResource\Pages;

use App\Filament\Resources\CambioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCambio extends EditRecord
{
    protected static string $resource = CambioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Eliminar cualquier referencia a widgets no existentes
    protected function getHeaderWidgets(): array
    {
        return [];
    }
}