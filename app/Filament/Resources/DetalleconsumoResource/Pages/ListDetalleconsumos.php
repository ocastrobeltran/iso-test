<?php

namespace App\Filament\Resources\DetalleconsumoResource\Pages;

use App\Filament\Resources\DetalleconsumoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DetalleconsumoResource\Widgets\HorasClockifyTable;

class ListDetalleconsumos extends ListRecords
{
    protected static string $resource = DetalleconsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HorasClockifyTable::class,
        ];
    }
}
