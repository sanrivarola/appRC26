<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horario;
use App\Notifications\HorarioNoCerrado;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HorarioController extends Controller
{
        public function card()
        {
            $user = Auth::user();
            $hoy = Carbon::now()->timezone('America/Argentina/Buenos_Aires')->toDateString();

            // Último horario cargado hoy
            $horario = Horario::where('user_id', $user->id)
                ->where('dia', $hoy)
                ->first();

            // Notificación si hay horario de hoy sin horariosalida
            if ($horario && !$horario->horariosalida && !session('notificado_horario_no_cerrado')) {
                $user->notify(new \App\Notifications\HorarioNoCerrado());
                session(['notificado_horario_no_cerrado' => true]);
            }

            return view('horarios.card', compact('horario', 'hoy'));
        }

    public function entrada(Request $request)
    {
        $user = Auth::user();
        $hoy = Carbon::now()->timezone('America/Argentina/Buenos_Aires')->toDateString();

        $horario = Horario::create([
            'user_id' => $user->id,
            'dia' => $hoy,
            'horarioentrada' => Carbon::now()->timezone('America/Argentina/Buenos_Aires'),
            'equipo' => $user->name, // O cualquier campo que represente "equipo"
        ]);

        return back()->with('success', 'Horario de entrada registrado');
    }

    public function salida(Request $request)
    {
        $user = Auth::user();
        $hoy = Carbon::now()->timezone('America/Argentina/Buenos_Aires')->toDateString();

        $horario = Horario::where('user_id', $user->id)->where('dia', $hoy)->first();
        if ($horario && !$horario->horariosalida) {
            $horario->update([
                'horariosalida' => Carbon::now()->timezone('America/Argentina/Buenos_Aires')
            ]);
        }

        return back()->with('success', 'Horario de salida registrado');
    }
}
