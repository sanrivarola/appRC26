<?php

namespace App\Http\Controllers;

use App\Models\{
    Compra,
    Inventario,
    AreaInventario,
    ProveedorAdmin,
    HistorialInventario,
    CompraMovimiento
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class CompraController extends Controller
{
    /*────────────────────────────────────────────────────
    |  LISTADO - PENDIENTES (a-realizar / en-proceso)
    ────────────────────────────────────────────────────*/
    public function index(Request $request)
    {
        // 1) Leer y sanear orden
        $allowedSorts = ['fechacarga','area_id','producto','prioridad','estado','responsable','id_compras'];
        $sort = $request->get('sort', 'fechacarga');
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'fechacarga';
        }
    
        $direction = strtolower($request->get('direction', 'desc'));
        if (!in_array($direction, ['asc','desc'], true)) {
            $direction = 'desc';
        }
    
        // 2) Query base con filtros + orden
        $base = Compra::with(['area','proveedor'])
            ->when($request->search, fn($q,$s) => $q->where('producto','like',"%{$s}%"))
            ->when($request->prioridad, fn($q,$p) => $q->where('prioridad',$p))
            ->orderBy($sort, $direction);
    
        // 3) Listas
        $aRealizar = (clone $base)
            ->where('estado','Compra a realizar')
            ->paginate(15, ['*'], 'realizar')
            ->appends($request->query());
    
        $enProceso = (clone $base)
            ->where('estado','Compra en proceso')
            ->paginate(15, ['*'], 'proceso')
            ->appends($request->query());
    
        // 4) Render
        return view('compras.index', [
            'aRealizar'   => $aRealizar,
            'enProceso'   => $enProceso,
            'prioridades' => ['Alta','Media','Baja'],
            'modo'        => 'pendientes',
            'sort'        => $sort,
            'direction'   => $direction,
        ]);
    }


    /*────────────────────────────────────────────────────
    |  “REALIZADAS”  (historial)
    ────────────────────────────────────────────────────*/
    public function realizadas(Request $request)
    {
        $query = Compra::with(['area','proveedor'])
            ->where('estado', 'Compra realizada')
            ->when($request->search,    fn($q,$s) => $q->where('producto','like',"%{$s}%"))
            ->when($request->prioridad, fn($q,$p) => $q->where('prioridad',$p));

        $compras = $query->orderByDesc('fechacompra')
                        ->paginate(20)
                        ->appends($request->query());

        return view('compras.index', [
            'compras'     => $compras,
            'prioridades' => ['Alta','Media','Baja'],
            'modo'        => 'realizadas',
        ]);
    }

    /*────────────────────────────────────────────────────
    |  CREATE
    ────────────────────────────────────────────────────*/
    public function create(Request $request)
    {
        return view('compras.create', [
            'areas'           => AreaInventario::orderBy('nombre')->pluck('nombre','id_areainventario'),
            'proveedores'     => ProveedorAdmin::orderBy('nombre')->pluck('nombre','id_proveedores'),
            'prioridades'     => ['Alta','Media','Baja'],
            'estados'         => ['Compra a realizar','Compra en proceso','Compra realizada'],
            'defaultProducto' => $request->input('producto'),
            'defaultAreaId'   => $request->input('area_id'),
        ]);
    }

    /*────────────────────────────────────────────────────
    |  STORE  (redirige a inventario si queda realizada)
    ────────────────────────────────────────────────────*/
    public function store(Request $request)
    {
        $rules = [
            'area_id'     => 'required|exists:area_inventarios,id_areainventario',
            'prioridad'   => 'required|in:Alta,Media,Baja',
            'producto'    => 'required|string', // LONGTEXT en DB
            'cantidad'    => 'required|integer|min:1',
            'responsable' => 'required|string|max:255',
            'estado'      => 'required|in:Compra a realizar,Compra en proceso,Compra realizada',
        ];

        if (auth()->user()->hasAnyRole(['admin','editor'])) {
            $rules['proveedor_id'] = 'required|exists:proveedores_admin,id_proveedores';
        }

        $data = $request->validate($rules);
        $data['proveedor_id'] = $data['proveedor_id'] ?? null;

        $data += [
            'creador'     => auth()->user()->name,
            'fechacarga'  => now()->toDateString(),
            'fechacompra' => ($data['estado'] === 'Compra realizada') ? now()->toDateString() : null,
        ];

        $compra = Compra::create($data);

        // Si se crea ya realizada → unificar y redirigir al inventario
        if ($compra->estado === 'Compra realizada') {
            if (!$compra->archived_at) {
                $compra->archived_at = Carbon::now();
                $compra->save();
            }
            $inv = $this->unificarConInventario($compra);

            if (Route::has('inventarios.show')) {
                return redirect()->route('inventarios.show', $inv->id_inventario)
                    ->with('success','Compra registrada y stock actualizado en el inventario.');
            }
            // Fallback a index de inventarios con búsqueda
            if (Route::has('inventarios.index')) {
                return redirect()->route('inventarios.index', ['search' => $compra->producto])
                    ->with('success','Compra registrada y stock actualizado. Te llevé al inventario.');
            }
        }

        // Caso normal: volver a compras
        return redirect()->route('compras.index')->with('success','Compra registrada');
    }

    /*────────────────────────────────────────────────────
    |  SHOW
    ────────────────────────────────────────────────────*/
    public function show($id_compras)
    {
        $compra = Compra::with(['area','proveedor','movimientos.user'])->findOrFail($id_compras);
        return view('compras.show', compact('compra'));
    }

    /*────────────────────────────────────────────────────
    |  CREAR DESDE INVENTARIO
    ────────────────────────────────────────────────────*/
    public function crearDesdeInventario(Inventario $inventario)
    {
        Compra::create([
            'area_id'     => $inventario->area_id,
            'prioridad'   => 'Media',
            'producto'    => $inventario->nombreproducto,
            'cantidad'    => max(1, ($inventario->stockminimo ?? 1) - ($inventario->cantidad ?? 0)),
            'responsable' => Auth::user()->name,
            'creador'     => Auth::user()->name,
            'estado'      => 'Compra a realizar',
            'fechacarga'  => now()->toDateString(),
        ]);

        return back()->with('success','Compra añadida a la lista');
    }

    /*────────────────────────────────────────────────────
    |  EDIT
    ────────────────────────────────────────────────────*/
    public function edit(Compra $compra)
    {
        return view('compras.edit', [
            'compra'       => $compra,
            'areas'        => AreaInventario::orderBy('nombre')->pluck('nombre','id_areainventario'),
            'proveedores'  => ProveedorAdmin::orderBy('nombre')->pluck('nombre','id_proveedores'),
            'prioridades'  => ['Alta','Media','Baja'],
            'estados'      => ['Compra a realizar','Compra en proceso','Compra realizada'],
        ]);
    }

    /*────────────────────────────────────────────────────
    |  UPDATE  (redirige a inventario si transiciona a realizada)
    ────────────────────────────────────────────────────*/
    public function update(Request $request, Compra $compra)
    {
        $data = $request->validate([
            'area_id'      => ['nullable','exists:area_inventarios,id_areainventario'],
            'proveedor_id' => ['nullable','exists:proveedores_admin,id_proveedores'],
            'prioridad'    => ['required','in:Alta,Media,Baja'],
            'estado'       => ['required','in:Compra a realizar,Compra en proceso,Compra realizada'],
            'producto'     => ['required','string'],
            'cantidad'     => ['required','integer','min:1'],
            'responsable'  => ['required','string'],
            'fechacarga'   => ['sometimes','date'],
            'fechacompra'  => ['nullable','date'],
        ]);

        if (!array_key_exists('fechacarga', $data)) {
            $data['fechacarga'] = $compra->fechacarga;
        }

        $transicionoARealizada = false;
        $inventarioId = null;

        if (($compra->estado ?? null) !== 'Compra realizada' && ($data['estado'] ?? null) === 'Compra realizada') {
            $data['archived_at'] = $compra->archived_at ?: Carbon::now();
            $data['fechacompra'] = $data['fechacompra'] ?? now()->toDateString();
        }

        DB::transaction(function () use ($compra, $data, &$transicionoARealizada, &$inventarioId) {
            $original = $compra->getOriginal();

            $compra->update($data);

            // log de cambios
            $dirty = $compra->getChanges();
            unset($dirty['updated_at']);
            if (!empty($dirty)) {
                $cambios = [];
                foreach ($dirty as $campo => $nuevo) {
                    $antes = $original[$campo] ?? null;
                    if ($antes == $nuevo) continue;
                    $cambios[$campo] = [$antes, $nuevo];
                }
                if ($cambios) {
                    CompraMovimiento::create([
                        'compra_id' => $compra->getKey(),
                        'user_id'   => Auth::id(),
                        'accion'    => 'edit',
                        'cambios'   => json_encode($cambios, JSON_UNESCAPED_UNICODE),
                        'comentario'=> null,
                    ]);
                }
            }

            // Unificar inventario SOLO si transicionó a realizada
            if (($original['estado'] ?? null) !== 'Compra realizada'
                && $compra->estado === 'Compra realizada') {
                $inv = $this->unificarConInventario($compra);
                $inventarioId = $inv->id_inventario;
                $transicionoARealizada = true;
            }
        });

        // Redirigir
        if ($transicionoARealizada && $inventarioId) {
            if (Route::has('inventarios.show')) {
                return redirect()->route('inventarios.show', $inventarioId)
                    ->with('success', 'Compra realizada y stock actualizado en el inventario.');
            }
            if (Route::has('inventarios.index')) {
                return redirect()->route('inventarios.index', ['search' => $compra->producto])
                    ->with('success', 'Compra realizada y stock actualizado. Te llevé al inventario.');
            }
        }

        // Caso normal: volver al listado de compras
        return redirect()->route('compras.index')
            ->with('success', 'Compra actualizada. Cambios registrados en el historial.');
    }

    /*────────────────────────────────────────────────────
    |  DELETE
    ────────────────────────────────────────────────────*/
    public function destroy(Compra $compra)
    {
        $compra->delete();
        return redirect()->route('compras.index')->with('success','Compra eliminada');
    }

    /*────────────────────────────────────────────────────
    |  PASAR AL INVENTARIO  +  HISTORIAL
    ────────────────────────────────────────────────────*/
    protected function unificarConInventario(Compra $compra)
    {
        $inv = Inventario::firstOrNew([
            'area_id'        => $compra->area_id,
            'nombreproducto' => $compra->producto,
        ]);

        $inv->cantidad     = (int) (($inv->cantidad ?? 0) + (int) $compra->cantidad);
        $inv->stockminimo  = $inv->stockminimo ?? 1;
        $inv->fechacarga   = now()->toDateString();
        $inv->save();

        HistorialInventario::create([
            'producto_id'        => $inv->id_inventario,
            'tipo_operacion'     => 'entrada',
            'cantidad_operacion' => $compra->cantidad,
            'equipo'             => $compra->responsable,
            'fecha'              => now()->toDateString(),
        ]);

        return $inv;
    }
}
