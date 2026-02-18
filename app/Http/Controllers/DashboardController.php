<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Mantenimiento;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $rol = $user->getRoleNames()->first(); // admin, editor, colaborador
        $hoy = Carbon::today();

        $query = Mantenimiento::where('archivado', 0)
                              ->whereNotNull('next_due_at');

        if ($rol === 'colaborador') {
            $query->where('equipo', $user->name);
        }

        $vencidos = (clone $query)->whereDate('next_due_at', '<', $hoy)->count();
        $porVencer = (clone $query)
            ->whereDate('next_due_at', '>=', $hoy)
            ->whereDate('next_due_at', '<=', $hoy->copy()->addDays(7))
            ->count();

        $vista = match($rol) {
            'editor'      => 'dashboard.editor',
            'colaborador' => 'dashboard.colaborador',
            default       => 'dashboard', // admin u otros
        };

        return view($vista, compact('vencidos', 'porVencer'));
    }
}
