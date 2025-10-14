<?php

namespace App\Filament\Resources\MejoraResource\Pages;

use App\Filament\Resources\MejoraResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewMejora extends ViewRecord
{
    protected static string $resource = MejoraResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(fn () => $this->getResource()::getUrl('edit', ['record' => $this->record])),
        ];
    }
}