<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Horario;
use App\Models\User;
use App\Exports\HorariosExport;
use Maatwebsite\Excel\Facades\Excel;

class HorarioAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Horario::query();

        $limiteFecha = now()->subDays(60);
        $query->where('dia', '>=', $limiteFecha);

        if ($request->filled('fecha1') && $request->filled('fecha2')) {
            $query->whereBetween('dia', [
                max($limiteFecha, $request->fecha1),
                $request->fecha2
            ]);
        }

        if ($request->filled('equipo') && $request->equipo !== 'todos') {
            $query->where('equipo', $request->equipo);
        }


        $horarios = $query->orderBy('dia', 'desc')->paginate(20);

        $horasFormateadas = gmdate('H:i', $horarios->reduce(function ($carry, $horario) {
            if ($horario->horarioentrada && $horario->horariosalida) {
                $entrada = \Carbon\Carbon::parse($horario->horarioentrada);
                $salida = \Carbon\Carbon::parse($horario->horariosalida);
                $carry += $entrada->diffInSeconds($salida);
            }
            return $carry;
        }, 0));

        $usuarios = User::where('activo', true)
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'superadmin');
            })
            ->orderBy('name')
            ->get();

        $equipos = $usuarios->pluck('name');

        return view('horarios.index', [
            'horarios' => $horarios,
            'equipos' => $equipos,
            'usuarios' => $usuarios,
        ]);
    }


    public function show(Horario $horario)
    {
        return view('horarios.show', compact('horario'));
    }

        public function export(Request $request)
    {
        $equipo   = $request->equipo;
        $fecha1   = $request->fecha1;
        $fecha2   = $request->fecha2;

        $filename = 'horarios_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\HorariosExport($equipo, $fecha1, $fecha2),
            $filename
        );
    }
}
