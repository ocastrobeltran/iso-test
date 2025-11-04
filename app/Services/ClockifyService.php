<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ClockifyService
{
    protected $client;
    protected $apiKey;
    protected $workspaceId;

    public function __construct()
    {
        $this->apiKey = config('services.clockify.api_key');
        $this->workspaceId = config('services.clockify.workspace_id');
        $this->client = new Client([
            'base_uri' => 'https://api.clockify.me/api/v1/',
            'headers' => [
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    // Entradas por usuario, filtradas por proyecto (paginado)
    public function getUserTimeEntries(string $clockifyUserId, string $start, string $end, ?string $projectId = null): array
    {
        try {
            $entries = [];
            $page = 1;
            $pageSize = 1000;

            do {
                $query = [
                    'start' => $start,
                    'end' => $end,
                    'page' => $page,
                    'page-size' => $pageSize,
                ];
                if ($projectId) {
                    // El endpoint soporta 'project' como filtro
                    $query['project'] = $projectId;
                }

                $response = $this->client->get("workspaces/{$this->workspaceId}/user/{$clockifyUserId}/time-entries", [
                    'query' => $query,
                ]);

                $chunk = json_decode($response->getBody(), true) ?? [];
                $entries = array_merge($entries, $chunk);
                $page++;
            } while (is_array($chunk) && count($chunk) === $pageSize);

            return $entries;
        } catch (\Throwable $e) {
            logger()->error('Clockify getUserTimeEntries error: ' . $e->getMessage());
            return [];
        }
    }

    // Mantén tu método existente si lo usas en otros lugares
    public function getProjects(array $projectIds)
    {
        $all = $this->fetchAllProjectsWithClients();
        return collect($all)
            ->whereIn('id', $projectIds)
            ->values()
            ->all();
    }

    // Nuevo: obtener TODOS los proyectos (con nombre de cliente)
    public function getAllProjects(): array
    {
        return $this->fetchAllProjectsWithClients();
    }

    // Nuevo: encontrar proyecto por nombre exacto
    public function findProjectByName(string $name): ?array
    {
        $all = $this->fetchAllProjectsWithClients();
        return collect($all)->firstWhere('name', $name);
    }

    // Core para traer proyectos y clientes
    protected function fetchAllProjectsWithClients(): array
    {
        $cacheKey = "clockify_all_projects_{$this->workspaceId}";

        return Cache::remember($cacheKey, 3600, function () {
            $allProjects = [];
            $page = 1;
            $pageSize = 100;

            do {
                $response = $this->client->get("workspaces/{$this->workspaceId}/projects", [
                    'query' => [
                        'page' => $page,
                        'page-size' => $pageSize,
                        'archived' => 'false',
                    ],
                ]);
                $projects = json_decode($response->getBody(), true);
                $allProjects = array_merge($allProjects, $projects);
                $page++;
            } while (is_array($projects) && count($projects) === $pageSize);

            $clientsResponse = $this->client->get("workspaces/{$this->workspaceId}/clients");
            $allClients = collect(json_decode($clientsResponse->getBody(), true))->keyBy('id');

            return collect($allProjects)
                ->map(function ($project) use ($allClients) {
                    $clientName = isset($project['clientId']) && $allClients->has($project['clientId'])
                        ? $allClients[$project['clientId']]['name']
                        : 'Sin cliente';
                    return array_merge($project, ['clientName' => $clientName]);
                })
                ->values()
                ->all();
        });
    }

    // CORREGIDO: usar Guzzle y endpoint de reportes detallados
    public function getTimeEntriesByProject(string $projectId, string $start, string $end): array
    {
        try {
            $response = $this->client->post("workspaces/{$this->workspaceId}/reports/detailed", [
                'json' => [
                    'dateRangeStart' => $start,
                    'dateRangeEnd' => $end,
                    'projects' => [$projectId],
                    'page' => 1,
                    'pageSize' => 1000,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            // Clockify puede devolver 'timeentries' o 'timeEntries' según versión
            return $data['timeentries'] ?? $data['timeEntries'] ?? [];
        } catch (\Throwable $e) {
            logger()->error('Clockify getTimeEntriesByProject error: ' . $e->getMessage());
            return [];
        }
    }

    // CORREGIDO: usar Guzzle client
    public function getUser(string $userId): array
    {
        $cacheKey = "clockify_user_{$this->workspaceId}_{$userId}";

        return Cache::remember($cacheKey, 3600, function () use ($userId) {
            try {
                $response = $this->client->get("workspaces/{$this->workspaceId}/users/{$userId}");
                return json_decode($response->getBody(), true) ?? [];
            } catch (\Throwable $e) {
                logger()->error('Clockify getUser error: ' . $e->getMessage());
                return [];
            }
        });
    }

    public function getUsers(): array
    {
        $cacheKey = "clockify_users_{$this->workspaceId}";

        return Cache::remember($cacheKey, 3600, function () {
            try {
                $all = [];
                $page = 1;
                $pageSize = 200;

                do {
                    $response = $this->client->get("workspaces/{$this->workspaceId}/users", [
                        'query' => [
                            'page' => $page,
                            'page-size' => $pageSize,
                            'status' => 'ACTIVE',
                        ],
                    ]);
                    $chunk = json_decode($response->getBody(), true) ?? [];
                    $all = array_merge($all, $chunk);
                    $page++;
                } while (is_array($chunk) && count($chunk) === $pageSize);

                return $all;
            } catch (\Throwable $e) {
                logger()->error('Clockify getUsers error: ' . $e->getMessage());
                return [];
            }
        });
    }
}