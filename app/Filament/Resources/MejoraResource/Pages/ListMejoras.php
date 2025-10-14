<?php

namespace App\Filament\Resources\MejoraResource\Pages;

use App\Filament\Resources\MejoraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMejoras extends ListRecords
{
    protected static string $resource = MejoraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
