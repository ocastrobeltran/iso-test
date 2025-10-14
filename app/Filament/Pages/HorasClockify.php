<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Carbon;

class HorasClockify extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.pages.horas-clockify';

    public ?int $userId = null;
    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;
    public array $horas = [];

    public function mount(): void
    {
        $this->fechaInicio = now()->startOfMonth()->toDateString();
        $this->fechaFin = now()->endOfMonth()->toDateString();
    }

    public function updated($property)
    {
        $this->consultarHoras();
    }

    public function consultarHoras()
    {
        $this->horas = [];

        if (!$this->userId || !$this->fechaInicio || !$this->fechaFin) {
            return;
        }

        $user = User::find($this->userId);
        if (!$user || !$user->clockify_id) {
            return;
        }

        $entries = $user->getHorasClockify(
            Carbon::parse($this->fechaInicio)->toIso8601String(),
            Carbon::parse($this->fechaFin)->toIso8601String()
        );

        // Obtener todos los projectId únicos
        $projectIds = collect($entries)
            ->pluck('projectId')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Obtener los proyectos de Clockify
        $clockify = app(\App\Services\ClockifyService::class);
        $projects = $clockify->getProjects($projectIds);

        // Mapear projectId => [name, clientName]
        $projectsMap = [];
        foreach ($projects as $project) {
            $projectsMap[$project['id']] = [
                'name' => $project['name'] ?? 'Sin nombre',
                'clientName' => $project['clientName'] ?? 'Sin cliente',
            ];
        }

        $this->horas = collect($entries)->map(function ($entry) use ($projectsMap) {
            $projectId = $entry['projectId'] ?? null;
            $projectName = $projectId && isset($projectsMap[$projectId]) ? $projectsMap[$projectId]['name'] : 'Sin proyecto';
            $clientName = $projectId && isset($projectsMap[$projectId]) ? $projectsMap[$projectId]['clientName'] : 'Sin cliente';

            return [
                'project' => $projectName,
                'client' => $clientName,
                'description' => $entry['description'] ?? '',
                'start' => $entry['timeInterval']['start'] ?? '',
                'end' => $entry['timeInterval']['end'] ?? '',
                'duration' => isset($entry['timeInterval']['duration'])
                    ? round($this->iso8601ToSeconds($entry['timeInterval']['duration']) / 3600, 2)
                    : 0,
            ];
        })->toArray();
    }

    protected function iso8601ToSeconds($duration)
    {
        if (!$duration) return 0;
        try {
            $interval = new \DateInterval($duration);
            return ($interval->d * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getViewData(): array
    {
        return [
            'users' => User::whereNotNull('clockify_id')->pluck('name', 'id'),
            'horas' => $this->horas,
            'userId' => $this->userId,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
        ];
    }
}