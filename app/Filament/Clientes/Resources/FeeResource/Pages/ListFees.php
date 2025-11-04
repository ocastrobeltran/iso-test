<?php

namespace App\Filament\Clientes\Resources\FeeResource\Pages;

use App\Filament\Clientes\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFees extends ListRecords
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Sin crear para clientes
        ];
    }
}