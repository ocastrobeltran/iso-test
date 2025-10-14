<?php

namespace App\Filament\Resources\MejoraResource\Pages;

use App\Filament\Resources\MejoraResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMejora extends CreateRecord
{
    protected static string $resource = MejoraResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
