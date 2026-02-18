<?php

namespace App\Http\Controllers;

use App\Models\AreaInventario;
use Illuminate\Http\Request;

class AreaInventarioController extends Controller
{
    /* index -------------------------------------------------------------- */
    public function index()
    {
        $areas = AreaInventario::with('subareas')   // trae subáreas
                ->orderBy('nombre')            // Áreas A-Z
                ->get();

    return view('areas.index', compact('areas'));
    }

    /* create ------------------------------------------------------------- */
    public function create()
    {
        return view('areas.create');
    }

    /* store -------------------------------------------------------------- */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        AreaInventario::create([
            'nombre'  => $validated['nombre'],
            'creador' => $request->user()->id,
        ]);

        return redirect()
            ->route('areas.index')
            ->with('success', 'Área creada correctamente.');
    }

    /* edit --------------------------------------------------------------- */
    public function edit(AreaInventario $area)
    {
        return view('areas.edit', compact('area'));
    }

    /* update ------------------------------------------------------------- */
    public function update(Request $request, AreaInventario $area)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $area->update($validated);

        return redirect()
            ->route('areas.index')
            ->with('success', 'Área actualizada correctamente.');
    }

    /* destroy ------------------------------------------------------------ */
    public function destroy(AreaInventario $area)
    {
        $area->delete();
        return redirect()
            ->route('areas.index')
            ->with('success', 'Área eliminada correctamente.');
    }
}
