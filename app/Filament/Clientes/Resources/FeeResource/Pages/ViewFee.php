<?php

namespace App\Filament\Clientes\Resources\FeeResource\Pages;

use App\Filament\Clientes\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFee extends ViewRecord
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Solo lectura para clientes
        ];
    }
}