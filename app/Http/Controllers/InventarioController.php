<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\AreaInventario;
use App\Models\SubareaInventario;
use App\Models\HistorialInventario;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InventarioController extends Controller
{
    /* INDEX (todos los roles) ---------------------------------------- */
    public function index(Request $request)
    {
        $query = Inventario::with(['area','subarea'])
            ->when($request->search, fn($q, $s) =>
                $q->where('nombreproducto', 'like', "%{$s}%")
            )
            // Filtrar bajo stock si se pide
            ->when($request->boolean('low_stock'), fn($q) =>
                $q->whereColumn('cantidad', '<', 'stockminimo')
            );

        // Filtro por área
        $areaId = $request->input('area_id');
        if ($areaId) {
            $query->where('area_id', $areaId);
        }

        // Filtro por subárea (tu columna se llama subareainventario)
        $subareaId = $request->input('subareainventario');
        if ($subareaId) {
            $query->where('subareainventario', $subareaId);
        }

        // Paginación
        $perPage     = (int) $request->input('per_page', 25);
        $inventarios = $query
            ->orderBy('nombreproducto')
            ->paginate($perPage)
            ->appends($request->query());

        // Items bajo stock mínimo (para alerta)
        $lowStock = $inventarios->filter(fn($item) => $item->cantidad < $item->stockminimo);

        // Listado de áreas
        $areas = AreaInventario::orderBy('nombre')->get();

        // Subáreas: si hay área seleccionada, solo las de esa área
        $subareas = SubareaInventario::when($areaId, fn($q) => $q->where('areapadre', $areaId))
                                     ->orderBy('nombre')
                                     ->get();

        return view('inventario.index', compact(
            'inventarios',
            'lowStock',
            'areas',
            'subareas',
            'perPage',
            'areaId',
            'subareaId'
        ));
    }

    /* CREATE / STORE (admin) ----------------------------------------- */
    public function create()
    {
        $areas    = AreaInventario::orderBy('nombre')->get();
        $subareas = SubareaInventario::orderBy('nombre')->get();
        return view('inventario.create', compact('areas','subareas'));
    }

    public function store(Request $request)
    {
        // OJO: ajusta 'exists:' a los nombres REALES de tus tablas/PKs
        $data = $request->validate([
            'area_id'            => 'required|exists:area_inventarios,id_areainventario',
            'subareainventario'  => 'nullable|exists:inventario_subareas,id_subareasinventario',
            'nombreproducto'     => 'required|string|max:255',
            'detalle'            => 'nullable|string',
            'vencimiento'        => 'nullable|date',
            'cantidad'           => 'required|integer|min:0',
            'stockminimo'        => 'required|integer|min:0',
        ]);

        // Tu columna es NOT NULL
        $data['fechacarga'] = now()->toDateString();

        Inventario::create($data);

        return redirect()->route('inventario.index')
                         ->with('success','Producto añadido.');
    }

    /* SHOW (todos) ---------------------------------------------------- */
    public function show(Inventario $inventario)
    {
        return view('inventario.show', compact('inventario'));
    }

    /* EDIT / UPDATE (admin) ------------------------------------------ */
    public function edit(Inventario $inventario)
    {
        $areas    = AreaInventario::orderBy('nombre')->get();
        $subareas = SubareaInventario::orderBy('nombre')->get();
        return view('inventario.edit', compact('inventario','areas','subareas'));
    }

    public function update(Request $request, Inventario $inventario)
    {
        $data = $request->validate([
            'area_id'            => 'required|exists:area_inventarios,id_areainventario',
            'subareainventario'  => 'nullable|exists:inventario_subareas,id_subareasinventario',
            'nombreproducto'     => 'required|string|max:255',
            'detalle'            => 'nullable|string',
            'vencimiento'        => 'nullable|date',
            'cantidad'           => 'required|integer|min:0',
            'stockminimo'        => 'required|integer|min:0',
        ]);

        $inventario->update($data);

        return back()->with('success','Producto actualizado.');
    }

    /* MOVIMIENTOS DE STOCK ------------------------------------------- */
    public function updateStock(Request $request, Inventario $inventario)
    {
        $data = $request->validate([
            'cantidad_operacion' => 'required|integer|min:0',
            'tipo_operacion'     => 'required|in:entrada,salida',
        ]);

        // Ajustar stock
        $delta = $data['tipo_operacion'] === 'entrada'
            ? $data['cantidad_operacion']
            : -$data['cantidad_operacion'];

        $inventario->increment('cantidad', $delta);

        // Registrar historial (evitamos ordenar por 'id' que no existe)
        HistorialInventario::create([
            'producto_id'        => $inventario->id_inventario,
            'tipo_operacion'     => $data['tipo_operacion'],
            'cantidad_operacion' => $data['cantidad_operacion'],
            'equipo'             => auth()->user()->name,
            'fecha'              => now()->toDateString(),
        ]);

        return back()->with('success','Stock actualizado y registrado en historial.');
    }

    public function adjust(Request $request, Inventario $inventario)
    {
        $data = $request->validate([
            'delta' => 'required|integer',
        ]);

        $delta = $data['delta'];
        $inventario->increment('cantidad', $delta);

        HistorialInventario::create([
            'producto_id'        => $inventario->id_inventario,
            'tipo_operacion'     => $delta > 0 ? 'entrada' : 'salida',
            'cantidad_operacion' => abs($delta),
            'equipo'             => auth()->user()->name,
            'fecha'              => now()->toDateString(),
        ]);

        return back()->with('success','Stock y historial actualizados.');
    }

    public function history(Request $request, Inventario $inventario)
    {
        $historial = $inventario->historial()
            ->paginate(20)
            ->appends($request->query());

        return view('inventario.history', compact('inventario','historial'));
    }
    
    public function historial()
    {
        return $this->hasMany(HistorialInventario::class, 'producto_id', 'id_inventario')
            ->orderBy('fecha', 'desc')
            ->orderBy('id_historialinventario', 'desc');
    }

    /* DESTROY (admin) ----------------------------------------------- */
    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return back()->with('success','Producto eliminado.');
    }
    
    
        /* Exportar (admin) ----------------------------------------------- */
 public function exportXlsx(Request $request)
{
    // Base query (misma lógica que index)
    $query = Inventario::with(['area', 'subarea'])
        ->when($request->search, fn($q, $s) =>
            $q->where('nombreproducto', 'like', "%{$s}%")
        )
        ->when($request->boolean('low_stock'), fn($q) =>
            $q->whereColumn('cantidad', '<', 'stockminimo')
        );

    if ($request->area_id) {
        $query->where('area_id', $request->area_id);
    }

    if ($request->subarea_id) {
        $query->where('subareainventario', $request->subarea_id);
    }

    $inventarios = $query
        ->orderBy('area_id')
        ->orderBy('nombreproducto')
        ->get()
        ->groupBy(fn($item) => $item->area->nombre ?? 'Sin área');

    $spreadsheet = new Spreadsheet();

    $sheetIndex = 0;

    foreach ($inventarios as $areaNombre => $items) {
        $sheet = $sheetIndex === 0
            ? $spreadsheet->getActiveSheet()
            : $spreadsheet->createSheet();

        $sheet->setTitle(substr($areaNombre, 0, 31)); // límite Excel

        // Encabezados
        $sheet->fromArray([
            ['Producto', 'Subárea', 'Cantidad', 'Stock mínimo']
        ], null, 'A1');

        $row = 2;
        foreach ($items as $item) {
            $sheet->fromArray([
                $item->nombreproducto,
                $item->subarea->nombre ?? '',
                $item->cantidad,
                $item->stockminimo,
            ], null, "A{$row}");
            $row++;
        }

        $sheetIndex++;
    }

    $filename = 'inventario_' . now()->format('Ymd_His') . '.xlsx';

    return response()->streamDownload(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

}
