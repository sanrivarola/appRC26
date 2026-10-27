<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RestrictVisitanteRole
{
    /**
     * Nombres de ruta (admite comodín '*') a los que un usuario que
     * ÚNICAMENTE tiene el rol 'visitante' tiene permitido acceder.
     *
     * Si el usuario visitante también tiene otro rol combinado
     * (admin/editor/colaborador), este middleware no restringe nada:
     * se asume que ese caso ya se maneja con roles "normales".
     */
    protected array $allowed = [
        'residencia.*',   // card, ingreso, egreso (index/export son admin y ya están protegidas aparte)
        'profile.*',      // ver/editar su propio perfil
        'dashboard',      // se permite para poder redirigir desde ahí (ver DashboardController)
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('visitante') && $user->roles->count() === 1) {
            $routeName = $request->route()?->getName();

            $tienePermiso = $routeName && collect($this->allowed)
                ->contains(fn (string $pattern) => Str::is($pattern, $routeName));

            if (! $tienePermiso) {
                abort(403, 'Tu usuario solo tiene acceso a la sección de Residencias.');
            }
        }

        return $next($request);
    }
}