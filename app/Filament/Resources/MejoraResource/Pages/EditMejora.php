<?php

namespace App\Filament\Resources\MejoraResource\Pages;

use App\Filament\Resources\MejoraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMejora extends EditRecord
{
    protected static string $resource = MejoraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
