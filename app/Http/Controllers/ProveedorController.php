<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Exports\ProveedoresExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProveedorController extends Controller
{
    /* ─────────────────────  CONSTRUCTOR  ───────────────────── */
    public function __construct()
    {
        /* Sólo usuarios con rol admin pueden acceder a todo el módulo */
        $this->middleware(['auth', 'role:admin']);
    }

    /* ─────────────────────  LISTADO  ───────────────────────── */
    public function index(Request $request)
    {
        /* filtros */
        $search  = $request->input('buscar');
        $area    = $request->input('area');
        $perPage = in_array($request->input('perPage'), [10, 25, 100])
                   ? (int) $request->input('perPage')
                   : 10;

        $query = Proveedor::orderByDesc('fechacarga');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre',      'like', "%{$search}%")
                  ->orWhere('producto',   'like', "%{$search}%")
                  ->orWhere('responsable','like', "%{$search}%");
            });
        }

        if ($area) {
            $query->where('area', $area);
        }

        $proveedores = $query->paginate($perPage)
                             ->appends($request->except('page'));

        /* áreas únicas para el <select> */
        $areas = Proveedor::whereNotNull('area')
                  ->where('area', '!=', '')     // ← filtra valores vacíos
                  ->distinct()
                  ->orderBy('area')
                  ->pluck('area');

        return view('proveedores.index', compact(
            'proveedores', 'areas', 'perPage', 'search', 'area'
        ));
    }

    /* ─────────────────────  FORMULARIO NUEVO  ──────────────── */
    public function create()
    {
        /* misma lista de áreas del índice para reutilizarla en el <select> */
        $areas = Proveedor::select('area')->distinct()->orderBy('area')->pluck('area');

        return view('proveedores.create', compact('areas'));
    }

    /* ─────────────────────  GUARDAR  ───────────────────────── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'area'        => 'required|string|max:255',
            'nombre'      => 'required|string|max:255',
            'datos'       => 'required|string',
            'producto'    => 'nullable|string',
            'responsable' => 'required|string|max:255',
            'detalle'     => 'required|string',
        ]);

        $data['creador']    = Auth::user()->name;
        $data['fechacarga'] = now()->toDateString();

        Proveedor::create($data);

        return redirect()
               ->route('proveedores.index')
               ->with('success', 'Proveedor creado correctamente');
    }

    /* ─────────────────────  SHOW  ──────────────────────────── */
    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    /* ─────────────────────  EDITAR  ───────────────────────── */
    public function edit(Proveedor $proveedor)
    {
        $areas = Proveedor::select('area')->distinct()->orderBy('area')->pluck('area');

        return view('proveedores.edit', compact('proveedor', 'areas'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $data = $request->validate([
            'area'        => 'required|string|max:255',
            'nombre'      => 'required|string|max:255',
            'datos'       => 'required|string',
            'producto'    => 'nullable|string',
            'responsable' => 'required|string|max:255',
            'detalle'     => 'required|string',
        ]);

        $proveedor->update($data);

        return redirect()
               ->route('proveedores.index')
               ->with('success', 'Proveedor actualizado');
    }

    /* ─────────────────────  ELIMINAR  ─────────────────────── */
    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return back()->with('success', 'Proveedor eliminado');
    }

    /* ─────────────────────  EXPORTAR EXCEL  ───────────────── */
    public function export()
    {
        return Excel::download(
            new ProveedoresExport,
            'proveedores-'.now()->format('Ymd').'.xlsx'
        );
    }
}
