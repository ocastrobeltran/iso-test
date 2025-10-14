<?php

namespace App\Filament\Resources\DetalleconsumoResource\Pages;

use App\Filament\Resources\DetalleconsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDetalleconsumo extends CreateRecord
{
    protected static string $resource = DetalleconsumoResource::class;

    protected function afterCreate(): void
    {
        $this->record->recursos()->sync($this->data['recursos'] ?? []);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
