<?php

namespace App\Http\Controllers;

use App\Models\SubareaInventario;
use App\Models\AreaInventario;
use Illuminate\Http\Request;

class SubareaInventarioController extends Controller
{
    
    public function index()
    {
        return view('subareas.index');
    }

    /* formulario de alta */
    public function create(Request $request)
    {
        // si llega area_id por query, lo pasamos a la vista para precargar
        $areaId = $request->input('area_id');
        return view('subareas.create', compact('areaId'));
    }

    /* guardar subárea */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'areapadre' => 'required|exists:area_inventarios,id_areainventario',
        ]);

        SubareaInventario::create($data);

        // redirige al listado y abre automáticamente la fila del área
        return redirect()
            ->route('areas.index', ['open' => $data['areapadre']])
            ->with('success', 'Subárea creada.');
    }

    /* formulario de edición */
    public function edit(SubareaInventario $subarea)
    {
        return view('subareas.edit', compact('subarea'));
    }

    /* actualizar subárea */
    public function update(Request $request, SubareaInventario $subarea)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
    
        $subarea->update($data);
    
        return redirect()
            ->route('areas.index', ['open' => $subarea->areapadre])
            ->with('success', 'Subárea actualizada.');
    }

    /* eliminar subárea */
    public function destroy(SubareaInventario $subarea)
    {
        $subarea->delete();
        return back()->with('success', 'Subárea eliminada.');
    }
}
