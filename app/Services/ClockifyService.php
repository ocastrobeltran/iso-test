<?php

namespace App\Services;

use GuzzleHttp\Client;

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
            'verify' => false, // <-- Agrega esto solo para desarrollo
        ]);
    }

    // Obtener horas trabajadas por un usuario en un rango de fechas
    public function getUserTimeEntries($clockifyUserId, $start, $end)
    {
        $response = $this->client->get("workspaces/{$this->workspaceId}/user/{$clockifyUserId}/time-entries", [
            'query' => [
                'start' => $start,
                'end' => $end,
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    public function getProjects(array $projectIds)
    {
        $allProjects = [];
        $page = 1;
        $pageSize = 100; // máximo permitido por Clockify

        do {
            $response = $this->client->get("workspaces/{$this->workspaceId}/projects", [
                'query' => [
                    'page' => $page,
                    'page-size' => $pageSize,
                    'archived' => 'false', // opcional: solo activos
                ],
            ]);
            $projects = json_decode($response->getBody(), true);
            $allProjects = array_merge($allProjects, $projects);
            $page++;
        } while (count($projects) === $pageSize);

        // Obtener todos los clientes del workspace
        $clientsResponse = $this->client->get("workspaces/{$this->workspaceId}/clients");
        $allClients = collect(json_decode($clientsResponse->getBody(), true))->keyBy('id');

        // Filtrar solo los proyectos necesarios y agregar el nombre del cliente
        return collect($allProjects)
            ->whereIn('id', $projectIds)
            ->map(function ($project) use ($allClients) {
                $clientName = isset($project['clientId']) && $allClients->has($project['clientId'])
                    ? $allClients[$project['clientId']]['name']
                    : 'Sin cliente';
                return array_merge($project, ['clientName' => $clientName]);
            })
            ->values()
            ->all();
    }

    /**
     * (Opcional) Obtener todos los clientes del workspace
     */
    public function getClients()
    {
        $response = $this->client->get("workspaces/{$this->workspaceId}/clients");
        return json_decode($response->getBody(), true);
    }
}