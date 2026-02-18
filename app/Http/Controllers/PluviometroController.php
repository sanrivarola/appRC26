<?php

namespace App\Http\Controllers;

use App\Models\Pluviometro;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PluviometroController extends Controller
{
    /* ---------- LISTAR ---------- */
public function index(Request $request)
{
    /* ------------- ① Agrupación solicitada ------------- */
    $agrupado = $request->input('group', 'dias');   // 'dias' (default) | 'meses'

    /* ------------- ② Filtros y colección completa ------ */
    $query = Pluviometro::query();

    if ($busca = $request->input('search')) {
        $query->where('medicion','like',"%{$busca}%");
    }

    // Traemos TODAS las filas (sin paginar) para construir el gráfico
    $raw = $query->orderBy('fechacarga')->get(['fechacarga','medicion']);

    /* ------------- ③ Agregamos si es por meses ---------- */
    $datosGraf = $agrupado === 'meses'
        ? $raw->groupBy(fn($r) => \Carbon\Carbon::parse($r->fechacarga)->format('Y-m'))
              ->map(fn($g) => $g->sum('medicion'))   // suma mensual
        : $raw->keyBy('fechacarga')                 // serie diaria
              ->map(fn($r) => $r->medicion);

    /* ------------- ④ Tabla paginada --------------------- */
    $perPage   = $request->input('perPage', 25);
    $registros = $query->latest('fechacarga')
                       ->paginate($perPage)
                       ->appends($request->query());

    return view('pluviometro.index',
                compact('registros','agrupado','datosGraf','perPage'));
}


    /* ---------- FORM NUEVO ---------- */
    public function create()
    {
        return view('pluviometro.create');
    }

    /* ---------- GUARDAR ---------- */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fechacarga'    => 'required|date',
            'medicion'      => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $data['equipo']   = Auth::user()->name;  // responsable
        $data['creador']  = Auth::user()->name;

        Pluviometro::create($data);

        return redirect()->route('pluviometro.index')
                         ->with('success','Registro guardado');
    }

    /* ---------- EDIT / UPDATE ---------- */
    public function edit(Pluviometro $pluviometro)
    {
        return view('pluviometro.edit', compact('pluviometro'));
    }

    public function update(Request $request, Pluviometro $pluviometro)
    {
        $data = $request->validate([
            'fechacarga'    => 'required|date',
            'medicion'      => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        $pluviometro->update($data);

        return back()->with('success','Registro actualizado');
    }

    /* ---------- BORRAR (solo admin) ---------- */
    public function destroy(Pluviometro $pluviometro)
    {
        $pluviometro->delete();
        return back()->with('success','Registro eliminado');
    }
}
