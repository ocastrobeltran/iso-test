<?php

namespace App\Filament\Resources\RecursosproyectoResource\Pages;

use App\Filament\Resources\RecursosproyectoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecursosproyecto extends EditRecord
{
    protected static string $resource = RecursosproyectoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
