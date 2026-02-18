<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\MantenimientoDevolucion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MantenimientoController extends Controller
{
    /**
     * Mostrar listado de mantenimientos activos (no archivados).
     * Admin ve todos, colaborador solo los suyos.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Mantenimiento::query()
            ->where('archivado', 0);

        if ($user->hasRole('colaborador')) {
            $query->where('equipo', $user->name);
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('tipo', 'like', "%{$search}%")
                  ->orWhere('responsable', 'like', "%{$search}%")
                  ->orWhere('equipo', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('perPage', 25);
        $mantenciones = $query->orderByDesc('fechacarga')
                              ->paginate($perPage)
                              ->appends($request->query());

        return view('mantenimientos.index', compact('mantenciones', 'perPage'));
    }

    

    public function create()
    {
        $users = User::where('equipoactivo', 1)
                     ->orderBy('name')
                     ->pluck('name');

        return view('mantenimientos.create', compact('users'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'            => 'required|string|max:255',
            'responsable'     => 'nullable|string|max:255',
            'frequency_value' => 'nullable|integer|min:1',
            'frequency_unit'  => 'required|in:días,semanas,meses,años,única vez',
            'descripcion'     => 'nullable|string',
            'equipo'          => 'required|string|max:255',
            'estado'          => 'required|boolean',
        ]);

        // Si no se indica responsable, lo dejamos vacío
        $data['responsable'] = $data['responsable'] ?? '';

        // Fecha de carga
        $data['fechacarga'] = Carbon::today()->toDateString();

        $data['creador'] = auth()->user()->name;

        // 1) Generar campo 'tiempo' combinando valor + unidad
        if ($data['frequency_unit'] === 'única vez') {
            $data['tiempo'] = 'única vez';
        } else {
            // Si viene vacío frequency_value, déjalo como 'única vez' o maneja el error
            $value = $data['frequency_value'] ?? 1;
            $data['tiempo'] = "{$value} {$data['frequency_unit']}";
        }

        // 2) Recalcular next_due_at
        if ($data['frequency_unit'] !== 'única vez' && ! empty($data['frequency_value'])) {
            $fecha = Carbon::parse($data['fechacarga']);
            switch ($data['frequency_unit']) {
                case 'días':    $next = $fecha->addDays($data['frequency_value']);  break;
                case 'semanas': $next = $fecha->addWeeks($data['frequency_value']); break;
                case 'meses':   $next = $fecha->addMonths($data['frequency_value']);break;
                case 'años':    $next = $fecha->addYears($data['frequency_value']); break;
            }
            $data['next_due_at'] = $next->toDateString();
        } else {
            $data['next_due_at'] = null;
        }

        // 3) Al crear, el array $data ya contiene 'tiempo'
        Mantenimiento::create($data);

        return redirect()
            ->route('mantenimientos.index')
            ->with('success','Mantenimiento creado correctamente');
    }


    /**
     * Ver un mantenimiento junto a sus últimas 10 devoluciones.
     */
    public function show(Mantenimiento $mantenimiento)
    {
        $recientes = $mantenimiento->devoluciones()
                                   ->orderByDesc('fecha')
                                   ->limit(10)
                                   ->get();

        return view('mantenimientos.show', compact('mantenimiento', 'recientes'));
    }

    /**
     * Formulario para editar un mantenimiento.
     * Pasa también lista de usuarios activos.
     */
    public function edit($id)
    {
        $m = Mantenimiento::findOrFail($id);

        // Lista de usuarios activos
        $users = User::where('equipoactivo', 1)
                     ->orderBy('name')
                     ->pluck('name');

        // Desglosar 'tiempo' en valor y unidad
        if ($m->tiempo === 'única vez') {
            $frequencyValue = null;
            $frequencyUnit  = 'única vez';
        } elseif (strpos($m->tiempo, ' ') !== false) {
            [$frequencyValue, $frequencyUnit] = explode(' ', $m->tiempo, 2);
        } else {
            $frequencyValue = null;
            $frequencyUnit  = $m->tiempo;
        }

        return view('mantenimientos.edit', compact(
            'm', 'frequencyValue', 'frequencyUnit', 'users'
        ));
    }

    /**
     * Actualiza un mantenimiento existente.
     */
    public function update(Request $request, $id)
    {
        $m = Mantenimiento::findOrFail($id);

        $validated = $request->validate([
            'tipo'            => 'required|string|max:255',
            'responsable'     => 'nullable|string|max:255',
            'frequency_value' => 'nullable|integer|min:1',
            'frequency_unit'  => 'required|in:días,semanas,meses,años,única vez',
            'tiempo'          => 'nullable|string',
            'descripcion'     => 'nullable|string',
            'equipo'          => 'required|string|max:255',
            'estado'          => 'required|boolean',
        ]);

        $validated['responsable'] = $validated['responsable'] ?? $m->responsable;

        // Recalcular next_due_at
        if ($validated['frequency_unit'] !== 'única vez' && ! empty($validated['frequency_value'])) {
            $baseDate = Carbon::parse($m->fechacarga);
            switch ($validated['frequency_unit']) {
                case 'días':    $next = $baseDate->copy()->addDays($validated['frequency_value']);  break;
                case 'semanas': $next = $baseDate->copy()->addWeeks($validated['frequency_value']); break;
                case 'meses':   $next = $baseDate->copy()->addMonths($validated['frequency_value']); break;
                case 'años':    $next = $baseDate->copy()->addYears($validated['frequency_value']);  break;
            }
            $validated['next_due_at'] = $next->toDateString();
        } else {
            $validated['next_due_at'] = null;
        }

        // Mantener consistencia entre estado y archivado
        $validated['archivado'] = $validated['estado'] ? 0 : 1;

        $m->update($validated);

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento actualizado correctamente');
    }

    /**
     * Mostrar formulario para archivar un mantenimiento.
     */
    public function archiveForm($id)
    {
        $m = Mantenimiento::findOrFail($id);
        return view('mantenimientos.archive', compact('m'));
    }


    public function archive(Request $request, $id)
    {
        $data = $request->validate([
            'motivo_archivo' => 'required|string|max:1000',
        ]);

        $m = Mantenimiento::findOrFail($id);
        $m->update([
            'motivo_archivo' => $data['motivo_archivo'],
            'fecha_archivo'  => now()->toDateString(),
            'archivado'      => 1,
            'estado'         => 0,
        ]);

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento archivado correctamente');
    }


    public function destroy($id)
    {
        Mantenimiento::destroy($id);

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento eliminado');
    }

   public function storeDevolucion(Request $request, $id)
    {
        $data = $request->validate([
            'devolucion' => 'required|string',
        ]);

        $fechaActual = now()->toDateString();

        // 1. Guardar devolución
        MantenimientoDevolucion::create([
            'id_mantenimiento' => $id,
            'equipo'           => auth()->user()->name,
            'fecha'            => $fechaActual,
            'devolucion'       => $data['devolucion'],
        ]);

        // 2. Recalcular fecha de próximo mantenimiento
        $mantenimiento = Mantenimiento::findOrFail($id);

        if ($mantenimiento->tiempo !== 'única vez') {
            [$valor, $unidad] = explode(' ', $mantenimiento->tiempo);

            $nextDate = \Carbon\Carbon::parse($fechaActual);
            switch ($unidad) {
                case 'días':    $nextDate->addDays($valor); break;
                case 'semanas': $nextDate->addWeeks($valor); break;
                case 'meses':   $nextDate->addMonths($valor); break;
                case 'años':    $nextDate->addYears($valor); break;
            }

            $mantenimiento->next_due_at = $nextDate->toDateString();
            $mantenimiento->save();
        }

        return back()->with('success', 'Devolución registrada y fecha recalculada');
    }



    public function indexDevoluciones(Request $request, $id)
    {
        $m = Mantenimiento::findOrFail($id);
        $query = $m->devoluciones();

        if ($fecha = $request->input('fecha')) {
            $query->whereDate('fecha', $fecha);
        }

        $devs = $query->orderByDesc('fecha')
                      ->paginate(15)
                      ->appends($request->query());

        return view('mantenimientos.devoluciones.index', compact('m', 'devs', 'fecha'));
    }

    /**
     * Listado de mantenimientos archivados.
     */
    public function archivados(Request $request)
    {
        $query = Mantenimiento::query()
            ->where('archivado', 1);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('tipo','like', "%{$search}%")
                  ->orWhere('responsable','like', "%{$search}%")
                  ->orWhere('equipo','like', "%{$search}%");
            });
        }

        $perPage = $request->input('perPage', 25);
        $mantenciones = $query->orderByDesc('fecha_archivo')
                              ->paginate($perPage)
                              ->appends($request->query());

        return view('mantenimientos.archivados', compact('mantenciones', 'perPage'));
    }
}
