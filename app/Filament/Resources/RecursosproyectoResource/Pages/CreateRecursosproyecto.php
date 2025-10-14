<?php

namespace App\Filament\Resources\RecursosproyectoResource\Pages;

use App\Filament\Resources\RecursosproyectoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRecursosproyecto extends CreateRecord
{
    protected static string $resource = RecursosproyectoResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
