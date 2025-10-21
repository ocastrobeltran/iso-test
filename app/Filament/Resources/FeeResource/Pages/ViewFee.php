<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewFee extends ViewRecord
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}