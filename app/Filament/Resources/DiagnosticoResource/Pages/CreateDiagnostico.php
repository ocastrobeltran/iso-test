<?php

namespace App\Filament\Resources\DiagnosticoResource\Pages;

use App\Filament\Resources\DiagnosticoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDiagnostico extends CreateRecord
{
    protected static string $resource = DiagnosticoResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
