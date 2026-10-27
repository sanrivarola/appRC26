<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResidenciaHorario;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ResidenciaHorarioController extends Controller
{
    /**
     * Muestra la card de residencia para el usuario logueado.
     */
    public function card()
    {
        $user = Auth::user();

        // Residencia activa = tiene ingreso pero no tiene egreso
        $residenciaActiva = ResidenciaHorario::where('user_id', $user->id)
            ->whereNull('horario_egreso')
            ->latest('horario_ingreso')
            ->first();

        // Última residencia cerrada
        $ultimaResidencia = ResidenciaHorario::where('user_id', $user->id)
            ->whereNotNull('horario_egreso')
            ->latest('horario_egreso')
            ->first();

        return view('residencia_horarios.card', compact('residenciaActiva', 'ultimaResidencia'));
    }

    /**
     * Registra el ingreso a residencia.
     */
    public function ingreso(Request $request)
    {
        $user = Auth::user();

        // Verificar que no haya una residencia activa
        $activa = ResidenciaHorario::where('user_id', $user->id)
            ->whereNull('horario_egreso')
            ->exists();

        if ($activa) {
            return back()->with('error', 'Ya tenés una residencia activa. Registrá el egreso primero.');
        }

        $request->validate([
            'horario_ingreso' => 'required|date',
            'observaciones'   => 'nullable|string|max:500',
        ]);

        ResidenciaHorario::create([
            'user_id'         => $user->id,
            'horario_ingreso' => Carbon::parse($request->horario_ingreso)->timezone('America/Argentina/Buenos_Aires'),
            'observaciones'   => $request->observaciones,
        ]);

        return back()->with('success', 'Ingreso a residencia registrado correctamente.');
    }

    /**
     * Registra el egreso de residencia y calcula los días.
     */
    public function egreso(Request $request)
    {
        $user = Auth::user();

        $residencia = ResidenciaHorario::where('user_id', $user->id)
            ->whereNull('horario_egreso')
            ->latest('horario_ingreso')
            ->first();

        if (!$residencia) {
            return back()->with('error', 'No hay residencia activa para cerrar.');
        }

        $request->validate([
            'horario_egreso' => 'required|date|after_or_equal:' . $residencia->horario_ingreso->toDateTimeString(),
        ], [
            'horario_egreso.after_or_equal' => 'La fecha de egreso debe ser igual o posterior al ingreso.',
        ]);

        $horarioEgreso = Carbon::parse($request->horario_egreso)->timezone('America/Argentina/Buenos_Aires');
        $ingreso = Carbon::parse($residencia->horario_ingreso);

        $dias  = (int) $ingreso->diffInDays($horarioEgreso);
        $totalMinutos = $ingreso->diffInMinutes($horarioEgreso);
        $horas = (int) ($totalMinutos / 60);
        $minutos = $totalMinutos % 60;
        $horasTexto = "{$horas}h {$minutos}m";

        $residencia->update([
            'horario_egreso'      => $horarioEgreso,
            'dias_transcurridos'  => $dias,
            'horas_transcurridas' => $horasTexto,
        ]);

        return back()->with('success', "Egreso registrado. La residencia duró {$dias} día(s) — {$horasTexto}.");
    }


public function export(Request $request): StreamedResponse
{
    $query = ResidenciaHorario::with('usuario')
        ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
        ->when($request->estado === 'activa',  fn($q) => $q->whereNull('horario_egreso'))
        ->when($request->estado === 'cerrada', fn($q) => $q->whereNotNull('horario_egreso'))
        ->orderByDesc('horario_ingreso');

    $residencias = $query->get();

    $filename = 'residencias_' . now()->format('Ymd_His') . '.csv';

    return response()->streamDownload(function () use ($residencias) {
        $handle = fopen('php://output', 'w');

        // BOM para que Excel abra bien el UTF-8
        fputs($handle, "\xEF\xBB\xBF");

        // Cabecera
        fputcsv($handle, ['Usuario', 'Ingreso', 'Egreso', 'Días', 'Horas', 'Estado', 'Observaciones'], ';');

        foreach ($residencias as $r) {
            $ingreso = \Carbon\Carbon::parse($r->horario_ingreso)->format('d/m/Y H:i');
            $egreso  = $r->horario_egreso
                ? \Carbon\Carbon::parse($r->horario_egreso)->format('d/m/Y H:i')
                : 'Sin egreso';

            if ($r->horario_egreso) {
                $dias  = $r->dias_transcurridos;
                $horas = $r->horas_transcurridas ?? '—';
                $estado = 'Cerrada';
            } else {
                $minTotales = \Carbon\Carbon::parse($r->horario_ingreso)->diffInMinutes(now());
                $dias  = (int) ($minTotales / 1440) . '+';
                $horas = ((int)($minTotales / 60)) . 'h ' . ($minTotales % 60) . 'm+';
                $estado = 'Activa';
            }

            fputcsv($handle, [
                $r->usuario->name ?? '—',
                $ingreso,
                $egreso,
                $dias,
                $horas,
                $estado,
                $r->observaciones ?? '',
            ], ';');
        }

        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}


    /**
     * Vista admin: listado de todas las residencias.
     */
    public function index(Request $request)
    {
        $query = ResidenciaHorario::with('usuario')->latest('horario_ingreso');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'activa') {
                $query->whereNull('horario_egreso');
            } elseif ($request->estado === 'cerrada') {
                $query->whereNotNull('horario_egreso');
            }
        }

        $residencias = $query->paginate(20)->withQueryString();

        $usuarios = \App\Models\User::orderBy('name')->get();

        return view('residencia_horarios.index', compact('residencias', 'usuarios'));
    }
}
