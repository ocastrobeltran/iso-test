<?php

namespace App\Filament\Resources\ContratoResource\Widgets;

use Filament\Widgets\Widget;

class ContratoRecursosWidget extends Widget
{
    protected static string $view = 'filament.resources.contrato-resource.widgets.recursos-asignados';

    public $record;

    protected function setUp(): void
    {
        parent::setUp();
        $this->record = $this->getRecord();
    }

    protected function getViewData(): array
    {
        return [
            'recursos' => $this->record?->recursos ?? [],
        ];
    }
}