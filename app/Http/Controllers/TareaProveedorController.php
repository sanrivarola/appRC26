<?php

namespace App\Http\Controllers;

use App\Models\TareaProveedor;
use App\Models\DevolucionTareaProveedor;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TareaProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|editor']);
    }

    /*──────────────────────────────────────────────
    |  INDEX  — todas las tareas (filtrable)
    ──────────────────────────────────────────────*/
    public function index(Request $request)
    {
        $query = TareaProveedor::with('proveedor')
            ->when($request->proveedor_id, fn($q,$v) => $q->where('proveedor_id', $v))
            ->when($request->tipo,         fn($q,$v) => $q->where('tipo', $v))
            ->when($request->estado,       fn($q,$v) => $q->where('estado', $v))
            ->when($request->prioridad,    fn($q,$v) => $q->where('prioridad', $v))
            ->when($request->search,       fn($q,$v) => $q->where('titulo','like',"%{$v}%"))
            ->orderByRaw("FIELD(estado, 'Pendiente','En curso','Cerrada')")
            ->orderByRaw("FIELD(prioridad,'Alta','Media','Baja')")
            ->orderByDesc('fecha_carga');

        $tareas      = $query->paginate(20)->appends($request->query());
        $proveedores = Proveedor::orderBy('nombre')->pluck('nombre','id_proveedores');

        // Stats globales
        $stats = [
            'total'     => TareaProveedor::count(),
            'pendiente' => TareaProveedor::where('estado','Pendiente')->count(),
            'en_curso'  => TareaProveedor::where('estado','En curso')->count(),
            'cerradas'  => TareaProveedor::where('estado','Cerrada')->count(),
        ];

        return view('tareas_proveedores.index', compact('tareas','proveedores','stats'));
    }

    /*──────────────────────────────────────────────
    |  CREATE
    ──────────────────────────────────────────────*/
    public function create(Request $request)
    {
        $proveedores = Proveedor::orderBy('nombre')->pluck('nombre','id_proveedores');
        $preselect   = $request->proveedor_id;

        return view('tareas_proveedores.create', compact('proveedores','preselect'));
    }

    /*──────────────────────────────────────────────
    |  STORE
    ──────────────────────────────────────────────*/
    public function store(Request $request)
    {
        $data = $request->validate([
            'proveedor_id' => 'required|exists:proveedores_admin,id_proveedores',
            'titulo'       => 'required|string|max:255',
            'detalle'      => 'nullable|string',
            'tipo'         => 'required|in:Reclamo,Consulta,Seguimiento,Renovación',
            'prioridad'    => 'required|in:Alta,Media,Baja',
            'fecha_limite' => 'nullable|date',
        ]);

        $data['creador']    = Auth::user()->name;
        $data['estado']     = 'Pendiente';
        $data['fecha_carga']= Carbon::now('America/Argentina/Buenos_Aires')->toDateString();

        TareaProveedor::create($data);

        return redirect()->route('tareas-proveedores.index')
            ->with('success', 'Tarea creada correctamente.');
    }

    /*──────────────────────────────────────────────
    |  SHOW — detalle + devoluciones
    ──────────────────────────────────────────────*/
    public function show(TareaProveedor $tareasProveedor)
    {
        $tarea = $tareasProveedor->load(['proveedor','devoluciones.user']);
        return view('tareas_proveedores.show', compact('tarea'));
    }

    /*──────────────────────────────────────────────
    |  EDIT
    ──────────────────────────────────────────────*/
    public function edit(TareaProveedor $tareasProveedor)
    {
        $tarea       = $tareasProveedor;
        $proveedores = Proveedor::orderBy('nombre')->pluck('nombre','id_proveedores');
        return view('tareas_proveedores.edit', compact('tarea','proveedores'));
    }

    /*──────────────────────────────────────────────
    |  UPDATE
    ──────────────────────────────────────────────*/
    public function update(Request $request, TareaProveedor $tareasProveedor)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'detalle'      => 'nullable|string',
            'tipo'         => 'required|in:Reclamo,Consulta,Seguimiento,Renovación',
            'prioridad'    => 'required|in:Alta,Media,Baja',
            'estado'       => 'required|in:Pendiente,En curso,Cerrada',
            'fecha_limite' => 'nullable|date',
        ]);

        $tareasProveedor->update($data);

        return redirect()->route('tareas-proveedores.show', $tareasProveedor)
            ->with('success', 'Tarea actualizada.');
    }

    /*──────────────────────────────────────────────
    |  DESTROY
    ──────────────────────────────────────────────*/
    public function destroy(TareaProveedor $tareasProveedor)
    {
        $tareasProveedor->delete();
        return redirect()->route('tareas-proveedores.index')
            ->with('success', 'Tarea eliminada.');
    }

    /*──────────────────────────────────────────────
    |  GUARDAR DEVOLUCIÓN (seguimiento)
    ──────────────────────────────────────────────*/
    public function guardarDevolucion(Request $request, TareaProveedor $tareasProveedor)
    {
        $request->validate([
            'devolucion' => 'required|string|max:2000',
        ]);

        // Pasar a "En curso" si estaba Pendiente
        if ($tareasProveedor->estado === 'Pendiente') {
            $tareasProveedor->update(['estado' => 'En curso']);
        }

        DevolucionTareaProveedor::create([
            'tarea_id'   => $tareasProveedor->id,
            'user_id'    => Auth::id(),
            'devolucion' => $request->devolucion,
        ]);

        return back()->with('success', 'Seguimiento agregado.');
    }

    /*──────────────────────────────────────────────
    |  CERRAR TAREA — resultado + calificación
    ──────────────────────────────────────────────*/
    public function cerrar(Request $request, TareaProveedor $tareasProveedor)
    {
        $data = $request->validate([
            'resultado'    => 'required|in:Resuelto,Parcialmente resuelto,Sin respuesta,Pendiente',
            'calificacion' => 'nullable|integer|min:1|max:5',
            'devolucion'   => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($tareasProveedor, $data) {
            $tareasProveedor->update([
                'estado'       => 'Cerrada',
                'resultado'    => $data['resultado'],
                'calificacion' => $data['calificacion'],
                'fecha_cierre' => Carbon::now('America/Argentina/Buenos_Aires')->toDateString(),
            ]);

            // Guardar devolución de cierre si viene con texto
            if (!empty($data['devolucion'])) {
                DevolucionTareaProveedor::create([
                    'tarea_id'   => $tareasProveedor->id,
                    'user_id'    => Auth::id(),
                    'devolucion' => '[Cierre] ' . $data['devolucion'],
                ]);
            }
        });

        return back()->with('success', 'Tarea cerrada correctamente.');
    }
}
