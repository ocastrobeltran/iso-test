<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;
use App\Policies\RolePolicy;
use BezhanSalleh\PanelSwitch\PanelSwitch;

use App\Models\Proyecto;
use App\Models\Calificacion;
use App\Models\Checklist;
use App\Models\Contrato;
use App\Models\Detalleconsumo;
use App\Models\Mejora;
use App\Models\Recursosproyecto;
use App\Models\Ticket;

use App\Observers\ProyectoObserver;
use App\Observers\CalificacionObserver;
use App\Observers\ChecklistObserver;
use App\Observers\ContratoObserver;
use App\Observers\DetalleconsumoObserver;
use App\Observers\MejoraObserver;
use App\Observers\RecursosproyectoObserver;
use App\Observers\TicketObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Configuración del PanelSwitch
        // funcionalidad para cambiar entre paneles de administracion y clientes
        // con el rol de admin o super_admin
        // PanelSwitch::configureUsing( function (PanelSwitch $panelSwitch) {
        //     $panelSwitch
        //      ->visible (fn (): bool => auth()->user()?->hasAnyRole(['admin', 'super_admin']))
        //      ->slideOver()
        //      ->modalWidth('sm')
        //      ->labels([
        //         'clientes' => 'Clientes',
        //         'dashboard' => 'Admin'
        //     ])
        //     ->icons([
        //         'clientes' => 'heroicon-o-users',
        //         'dashboard' => 'heroicon-o-chart-bar',
        //     ], $asImage = false);
        // });

        Schema::defaultStringLength(191);

        Proyecto::observe(ProyectoObserver::class);
        Calificacion::observe(CalificacionObserver::class);
        Checklist::observe(ChecklistObserver::class);
        Contrato::observe(ContratoObserver::class);
        Detalleconsumo::observe(DetalleconsumoObserver::class);
        Mejora::observe(MejoraObserver::class);
        Recursosproyecto::observe(RecursosproyectoObserver::class);
        Ticket::observe(TicketObserver::class);;

        // Forzar HTTPS en producción
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }

    protected $policies = [
    Role::class => RolePolicy::class,
    ];

}
