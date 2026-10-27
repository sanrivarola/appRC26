<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\User;
use App\Models\DevolucionTarea;
use App\Exports\TareasExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DevolucionesExport;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TareaController extends Controller
{
    public function index(Request $request)
    {
        $user       = auth()->user();
        $buscar     = $request->input('buscar');
        $diaFiltro  = $request->input('seleccionardia');

        $query = Tarea::query();

        // Colaborador: ve solo sus tareas con reglas de no-repetición
        if ($user->hasRole('colaborador')) {
            $query->where('equipo', $user->name)
                  ->where('estado', 1)
                  ->where('id_tarea', '!=', 94)
                  ->where(function ($q) use ($user) {
                      $q->where('norepite', 'no')
                        ->orWhereNotIn('id_tarea', function ($sub) use ($user) {
                            $sub->select('id_tarea')
                                ->from('devoluciontareas')
                                ->where('equipo', $user->name)
                                ->where('finalizada', 'si');
                        });
                  });
        } else {
            // Admin/Editor: ven activas
            $query->where('estado', 1)->where('id_tarea', '!=', 94);
        }

        // Filtro por día
        if ($diaFiltro) {
            $query->where($diaFiltro, 'si');
            $mensajeday = 'Mostrando tareas de: ' . ucfirst($diaFiltro);
        } else {
            $mensajeday = 'Mostrando toda la semana';
        }

        // Búsqueda
        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('tarea', 'like', "%{$buscar}%")
                  ->orWhere('equipo', 'like', "%{$buscar}%");
            });
        }

        $tareas = $query->orderByDesc('fecha_carga')->paginate(15);

        // Extras del día (realizadas y no realizadas) para el usuario actual
        $hoy    = Carbon::today()->toDateString();
        $nombre = $user->name;

        $extrasRealizadas = DB::table('devoluciontareas as d')
            ->join('tareas as t', 'd.id_tarea', '=', 't.id_tarea')
            ->where('d.finalizada', 'si')
            ->where('d.equipo', $nombre)
            ->whereDate('d.fecha', $hoy)
            ->select('t.tarea', 'd.devolucion', 'd.fecha')
            ->get();

        $extrasNoRealizadas = DB::table('devoluciontareas as d')
            ->join('tareas as t', 'd.id_tarea', '=', 't.id_tarea')
            ->where('d.finalizada', 'no')
            ->where('d.equipo', $nombre)
            ->whereDate('d.fecha', $hoy)
            ->select('t.tarea', 'd.devolucion', 'd.fecha')
            ->get();

        // Lista de equipos para algún uso en index si lo necesitás
        $equipos = $this->equiposActivos();

        return view('tareas.index', compact(
            'tareas', 'diaFiltro', 'mensajeday', 'extrasRealizadas', 'extrasNoRealizadas', 'equipos'
        ));
    }

    /**
     * Formulario de export: pasa $equipos a la vista.
     */
    public function exportForm(Request $request)
    {
        // Combo de equipos desde devoluciontareas (incluye históricos)
        $equipos = DevolucionTarea::query()
            ->whereNotNull('equipo')
            ->where('equipo', '!=', '')
            ->distinct()
            ->orderBy('equipo')
            ->pluck('equipo');

        // Si viene búsqueda (preview), validamos y devolvemos resultados paginados
        $preview = null;
        $total   = 0;

        if ($request->filled(['fecha_desde', 'fecha_hasta'])) {
            $data = $request->validate([
                'fecha_desde' => 'required|date',
                'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
                'equipo'      => 'nullable|string',
            ]);

            $desde = $data['fecha_desde'];
            $hasta = $data['fecha_hasta'];

            $equipo = $request->input('equipo', 'todos');
            if ($equipo === null || $equipo === '') $equipo = 'todos';

            $q = $this->baseExportQuery($desde, $hasta, $equipo);

            // Totales (sin paginar)
            $total = (clone $q)->count();

            // Preview paginado
            $preview = $q->paginate(25)->withQueryString();
        }

        return view('tareas.export', compact('equipos', 'preview', 'total'));
    }

    /**
     * Descarga Excel con exactamente la MISMA query que el preview
     * POST /tareas/export
     */
    public function export(Request $request)
    {
        $data = $request->validate([
            'fecha_desde'      => 'required|date',
            'fecha_hasta'      => 'required|date|after_or_equal:fecha_desde',
            'equipo'           => 'nullable|string',
            'solo_finalizadas' => 'nullable|boolean',
        ]);

        $desde  = $data['fecha_desde'];   // formato YYYY-MM-DD
        $hasta  = $data['fecha_hasta'];   // formato YYYY-MM-DD
        $equipo = $request->input('equipo', 'todos') ?: 'todos';
        $solo   = $request->boolean('solo_finalizadas');

        // Partes del nombre de archivo
        $equipoSlug = $equipo !== 'todos' ? '_' . Str::slug($equipo, '_') : '';
        $soloSlug   = $solo ? '_solo_finalizadas' : '';

        $filename = "devoluciones_{$desde}_{$hasta}{$equipoSlug}{$soloSlug}.xlsx";

        return Excel::download(
            new DevolucionesExport($desde, $hasta, $equipo, $solo),
            $filename
        );
    }

    /**
     * Query base compartida por preview/export (JOIN tareas + devoluciones)
     * Trae TODAS (finalizadas y no), ordenadas por fecha asc + id asc.
     */
    private function baseExportQuery(string $desde, string $hasta, string $equipo)
    {
        $q = DevolucionTarea::query()
            ->from('devoluciontareas as d')
            ->join('tareas as t', 'd.id_tarea', '=', 't.id_tarea')
            ->whereBetween('d.fecha', [$desde, $hasta]) // inclusivo en DATE
            ->select([
                'd.id',
                'd.id_tarea',
                't.tarea as nombre_tarea',
                'd.fecha',
                'd.equipo',
                'd.finalizada',
                'd.devolucion',
                'd.imagen1', 'd.imagen2', 'd.imagen3', 'd.imagen4', 'd.imagen5',
            ])
            ->orderBy('d.fecha', 'asc')
            ->orderBy('d.id', 'asc');

        if ($equipo !== 'todos') {
            $q->where('d.equipo', $equipo);
        }

        return $q;
    }

    public function createExtra($id_tarea)
    {
        $tarea = Tarea::findOrFail($id_tarea);
        return view('tareas.extras.create', compact('tarea'));
    }

    public function storeExtra(Request $request, $id_tarea)
    {
        $request->validate(['devolucion' => 'required|string']);

        DB::table('devoluciontareas')->insert([
            'id_tarea'   => $id_tarea,
            'equipo'     => auth()->user()->name,
            'devolucion' => $request->devolucion,
            'finalizada' => 'si',
            'fecha'      => now()->toDateString(),
        ]);

        return redirect()->route('tareas.index')->with('success', 'Extra tarea registrada correctamente');
    }

    public function devoluciones($id_tarea)
    {
        $tarea = Tarea::findOrFail($id_tarea);
        $devoluciones = DB::table('devoluciontareas')
            ->where('id_tarea', $id_tarea)
            ->orderByDesc('fecha')
            ->get();

        return view('tareas.devoluciones', compact('tarea', 'devoluciones'));
    }

    public function destroyDevolucion(Tarea $tarea, DevolucionTarea $devolucion)
    {
        $user = auth()->user();

        abort_unless(
            $user->hasRole('colaborador') &&
            $devolucion->equipo === $user->name &&
            Carbon::parse($devolucion->fecha)->isToday(),
            403
        );

        $devolucion->delete();
        return back()->with('success', 'Devolución eliminada.');
    }

    public function guardarDevolucion(Request $request, int $id_tarea)
    {
        $tarea = Tarea::findOrFail($id_tarea);

        $data = $request->validate([
            'finalizada' => 'required|in:si,no',
            'devolucion' => 'nullable|string|max:1000',
            'equipo'     => 'required|string|max:255',
        ]);

        $tarea->devoluciones()->create([
            'equipo'     => $data['equipo'],
            'finalizada' => $data['finalizada'],
            'devolucion' => $data['devolucion'],
            'fecha'      => now()->toDateString(),
        ]);

        return back()->with('success', 'Devolución registrada correctamente.');
    }

    public function create()
    {
        $responsables = $this->equiposActivos();

        return view('tareas.create', compact('responsables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarea'        => 'required|string|max:255',
            'detalle'      => 'required|string',
            'equipo'       => ['required', 'string', Rule::in($this->equiposActivos()->all())],
            'prioridad'    => 'required|in:Alta,Media,Baja',
            'estado'       => 'required|in:1,2,3',
            'lunes'        => 'nullable|in:si',
            'martes'       => 'nullable|in:si',
            'miercoles'    => 'nullable|in:si',
            'jueves'       => 'nullable|in:si',
            'viernes'      => 'nullable|in:si',
            'sabado'       => 'nullable|in:si',
            'domingo'      => 'nullable|in:si',
            'norepite'     => 'nullable|in:si',
            'horacomienzo' => 'nullable|integer|min:0|max:23',
            'horafinal'    => 'nullable|integer|min:0|max:23|gte:horacomienzo',
            'link1'        => 'nullable|url',
            'link2'        => 'nullable|url',
            'link3'        => 'nullable|url',
        ]);

        $validated['creador']     = auth()->user()->name;
        $validated['fecha_carga'] = now()->toDateString();

        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo', 'norepite'] as $d) {
            $validated[$d] = $validated[$d] ?? 'no';
        }

        Tarea::create($validated);

        return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente');
    }

    public function pausadas(Request $request)
    {
        $buscar    = $request->input('buscar');
        $diaFiltro = $request->input('seleccionardia');

        $query = Tarea::where('estado', 2);

        if ($diaFiltro) {
            $query->where($diaFiltro, 'si');
            $mensajeday = "Mostrando tareas pausadas de: " . ucfirst($diaFiltro);
        } else {
            $mensajeday = "Mostrando todas las tareas pausadas";
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('tarea', 'like', "%{$buscar}%")
                  ->orWhere('equipo', 'like', "%{$buscar}%");
            });
        }

        $tareas = $query->orderByDesc('fecha_carga')->paginate(15);

        return view('tareas.pausadas', compact('tareas', 'diaFiltro', 'mensajeday'));
    }

    public function archivadas(Request $request)
    {
        $buscar    = $request->input('buscar');
        $diaFiltro = $request->input('seleccionardia');

        $query = Tarea::where('estado', 3);

        if ($diaFiltro) {
            $query->where($diaFiltro, 'si');
            $mensajeday = "Mostrando tareas archivadas de: " . ucfirst($diaFiltro);
        } else {
            $mensajeday = "Mostrando todas las tareas archivadas";
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('tarea', 'like', "%{$buscar}%")
                  ->orWhere('equipo', 'like', "%{$buscar}%");
            });
        }

        $tareas = $query->orderByDesc('fecha_carga')->paginate(15);

        return view('tareas.archivadas', compact('tareas', 'diaFiltro', 'mensajeday'));
    }

    public function show(int $id_tarea)
    {
        $tarea = Tarea::with('devoluciones')->findOrFail($id_tarea);
        $user  = auth()->user();
        $hoy   = Carbon::today()->toDateString();

        $devoluciones = $user->hasRole('colaborador')
            ? $tarea->devoluciones()->whereDate('fecha', $hoy)->where('equipo', $user->name)->get()
            : $tarea->devoluciones()->orderByDesc('fecha')->paginate(10);

        return view('tareas.show', compact('tarea', 'devoluciones'));
    }

    public function edit($id_tarea)
    {
        $tarea        = Tarea::findOrFail($id_tarea);
        $responsables = $this->equiposActivos();

        return view('tareas.edit', compact('tarea', 'responsables'));
    }

    public function update(Request $request, $id_tarea)
    {
        $tarea = Tarea::findOrFail($id_tarea);

        $validated = $request->validate([
            'tarea'        => 'required|string|max:255',
            'detalle'      => 'nullable|string',
            'equipo'       => ['required', 'string', 'max:255', Rule::in($this->equiposActivos()->all())],
            'prioridad'    => 'required|in:Alta,Media,Baja',
            'estado'       => 'required|integer|in:1,2,3',
            'lunes'        => 'nullable|in:si',
            'martes'       => 'nullable|in:si',
            'miercoles'    => 'nullable|in:si',
            'jueves'       => 'nullable|in:si',
            'viernes'      => 'nullable|in:si',
            'sabado'       => 'nullable|in:si',
            'domingo'      => 'nullable|in:si',
            'norepite'     => 'nullable|in:si',
            'horacomienzo' => 'nullable|integer|min:0|max:23',
            'horafinal'    => 'nullable|integer|min:0|max:23|gte:horacomienzo',
            'link1'        => 'nullable|url',
            'link2'        => 'nullable|url',
            'link3'        => 'nullable|url',
        ]);

        // Normaliza los días desmarcados a "no"
        foreach (['lunes','martes','miercoles','jueves','viernes','sabado','domingo','norepite'] as $d) {
            $validated[$d] = $validated[$d] ?? 'no';
        }

        $tarea->update($validated);

        return redirect()->route('tareas.index')->with('success', 'Tarea actualizada correctamente');
    }

    public function destroy($id_tarea)
    {
        $tarea = Tarea::findOrFail($id_tarea);
        $tarea->delete();
        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente');
    }

    /**
     * Usuarios activos que pueden ser responsables de una tarea (desde la BD).
     */
    private function equiposActivos()
    {
        return User::where('activo', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'editor', 'colaborador']))
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'superadmin'))
            ->where('name', '!=', 'Superadmin')
            ->orderBy('name')
            ->pluck('name');
    }
}
