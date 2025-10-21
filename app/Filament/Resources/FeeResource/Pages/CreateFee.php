<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFee extends CreateRecord
{
    protected static string $resource = FeeResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $contratoId = $this->record->contrato_id;
        if ($contratoId) {
            $this->record->contratos()->attach($contratoId);
        }
    }
}