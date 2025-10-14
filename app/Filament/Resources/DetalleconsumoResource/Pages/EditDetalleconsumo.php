<?php

namespace App\Filament\Resources\DetalleconsumoResource\Pages;

use App\Filament\Resources\DetalleconsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetalleconsumo extends EditRecord
{
    protected static string $resource = DetalleconsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->recursos()->sync($this->data['recursos'] ?? []);
    }
}
