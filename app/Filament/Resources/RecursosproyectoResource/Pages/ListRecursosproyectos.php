<?php

namespace App\Filament\Resources\RecursosproyectoResource\Pages;

use App\Filament\Resources\RecursosproyectoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecursosproyectos extends ListRecords
{
    protected static string $resource = RecursosproyectoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
