<?php

namespace App\Filament\Resources\ConfiguracionResource\Pages;

use App\Filament\Resources\ConfiguracionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateConfiguracion extends CreateRecord
{
    protected static string $resource = ConfiguracionResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
