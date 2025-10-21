<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Resources\Pages\EditRecord;

class EditFee extends EditRecord
{
    protected static string $resource = FeeResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $contratoId = $this->record->contrato_id;
        // Sync: elimina asociaciones previas y añade la nueva (o ninguna si null)
        $this->record->contratos()->sync($contratoId ? [$contratoId] : []);
    }
}