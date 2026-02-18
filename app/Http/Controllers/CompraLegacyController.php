<?php

namespace App\Http\Controllers;

use App\Models\CompraLegacy;
use Illuminate\Http\Request;

class CompraLegacyController extends Controller
{
    public function index(Request $request)
    {
        $query = CompraLegacy::with(['area','proveedor'])
            ->when($request->search, fn($q,$s) => $q->where('producto','like',"%{$s}%"))
            ->when($request->prioridad, fn($q,$p) => $q->where('prioridad',$p))
            ->orderByDesc('fechacarga');

        $compras = $query->paginate(20)->appends($request->query());

        return view('compras_legacy.index', [
            'compras'     => $compras,
            'prioridades' => ['Alta','Media','Baja'],
        ]);
    }

    public function show($id)
    {
        $compra = CompraLegacy::with(['area','proveedor'])->findOrFail($id);
        return view('compras_legacy.show', compact('compra'));
    }
}
