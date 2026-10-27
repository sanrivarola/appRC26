<?php

namespace App\Http\Controllers;

use App\Models\Relevamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RelevamientoController extends Controller
{
    /*──────────────────────────────  LISTADO  ──────────────────────────────*/
    public function index(Request $request)
    {
        $user = Auth::user();

        /* cantidad por página */
        $perPage = in_array($request->input('perPage'), [25, 50, 100])
            ? (int) $request->input('perPage')
            : 25;

        /* rango opcional */
        $from = $request->filled('desde')
            ? Carbon::parse($request->input('desde'))->startOfDay()
            : Carbon::minValue();

        $to = $request->filled('hasta')
            ? Carbon::parse($request->input('hasta'))->endOfDay()
            : Carbon::maxValue();

        $query = Relevamiento::whereBetween('fechacarga', [$from, $to])

            /* archivados / activos */
            ->when(
                $request->boolean('archivados'),

                /* mostrar SOLO archivados */
                fn($q) => $q->where('archivar', 'si'),

                /* mostrar SOLO activos */
                fn($q) => $q->where('archivar', 'no')
            )

            ->orderByDesc('fechacarga');

        /* usuarios normales sólo ven los suyos */
        if (!$user->hasRole('admin')) {
            $query->where('equipo', $user->name);
        }

        $relevamientos = $query
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view(
            'relevamientos.index',
            compact('relevamientos', 'from', 'to', 'perPage')
        );
    }

    /*──────────────────────────────  NUEVO  ───────────────────────────────*/
    public function create()
    {
        return view('relevamientos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulorelevamiento'  => 'required|string|max:255',
            'detallerelevamiento' => 'required|string',
            'foto1'               => 'nullable|image|max:10240',
        ]);

        $data['equipo']     = Auth::user()->name;
        $data['archivar']   = 'no';
        $data['fechacarga'] = now()->toDateString();
        $data['foto1']      = $this->handleImage($request);

        Relevamiento::create($data);

        return redirect()
            ->route('relevamientos.index')
            ->with('success', 'Relevamiento cargado correctamente');
    }

    /*──────────────────────────────  MOSTRAR  ─────────────────────────────*/
    public function show($id)
    {
        $relevamiento = Relevamiento::findOrFail($id);

        if (
            !Auth::user()->hasRole('admin') &&
            $relevamiento->equipo !== Auth::user()->name
        ) {
            abort(403);
        }

        return view('relevamientos.show', compact('relevamiento'));
    }

    /*──────────────────────────────  EDITAR  ──────────────────────────────*/
    public function edit($id)
    {
        $relevamiento = Relevamiento::findOrFail($id);

        if (
            !Auth::user()->hasRole('admin') &&
            $relevamiento->equipo !== Auth::user()->name
        ) {
            abort(403);
        }

        return view('relevamientos.edit', compact('relevamiento'));
    }

    public function update(Request $request, $id)
    {
        $relevamiento = Relevamiento::findOrFail($id);

        if (
            !Auth::user()->hasRole('admin') &&
            $relevamiento->equipo !== Auth::user()->name
        ) {
            abort(403);
        }

        $data = $request->validate([
            'titulorelevamiento'  => 'required|string|max:255',
            'detallerelevamiento' => 'required|string',
            'foto1'               => 'nullable|image|max:10240',
        ]);

        /* reemplazar imagen */
        if ($request->hasFile('foto1')) {

            if ($relevamiento->foto1) {
                Storage::disk('public')
                    ->delete('relevamientos/' . $relevamiento->foto1);
            }

            $data['foto1'] = $this->handleImage($request);
        }

        $relevamiento->update($data);

        return redirect()
            ->route('relevamientos.index')
            ->with('success', 'Relevamiento actualizado');
    }

    /*──────────────────────────────  ARCHIVAR  ───────────────────────────*/
    public function toggleArchive($id)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $relevamiento = Relevamiento::findOrFail($id);

        $nuevoEstado = $relevamiento->archivar === 'si'
            ? 'no'
            : 'si';

        $relevamiento->update([
            'archivar' => $nuevoEstado,
        ]);

        return back()->with(
            'success',
            $nuevoEstado === 'si'
                ? 'Relevamiento archivado'
                : 'Relevamiento restaurado'
        );
    }

    /*──────────────────────────────  ELIMINAR  ───────────────────────────*/
    public function destroy($id)
    {
        $relevamiento = Relevamiento::findOrFail($id);

        if (
            !Auth::user()->hasRole('admin') &&
            $relevamiento->equipo !== Auth::user()->name
        ) {
            abort(403);
        }

        if ($relevamiento->foto1) {
            Storage::disk('public')
                ->delete('relevamientos/' . $relevamiento->foto1);
        }

        $relevamiento->delete();

        return back()->with('success', 'Relevamiento eliminado');
    }

    /*──────────────────────────────  EXPORT PDF  ─────────────────────────*/
    public function export(int $id)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $relevamiento = Relevamiento::findOrFail($id);

        $pdf = Pdf::loadView(
            'relevamientos.pdf',
            compact('relevamiento')
        );

        return $pdf->download(
            'relevamiento-' .
            $relevamiento->equipo . '-' .
            $relevamiento->fechacarga .
            '.pdf'
        );
    }

    /*──────────────────────────────  HELPER IMG  ─────────────────────────*/
    private function handleImage(Request $request): ?string
    {
        if (!$request->hasFile('foto1')) {
            return null;
        }

        $file = $request->file('foto1');

        $filename = Str::uuid() . '.jpg';

        $mime = $file->getMimeType();

        $image = match ($mime) {
            'image/png'  => imagecreatefrompng($file->getPathname()),
            'image/webp' => imagecreatefromwebp($file->getPathname()),
            default      => imagecreatefromjpeg($file->getPathname()),
        };

        if (!$image) {
            return null;
        }

        /* resize */
        $w = imagesx($image);
        $h = imagesy($image);

        if ($w > 1280) {

            $nw = 1280;
            $nh = intval($h * (1280 / $w));

            $tmp = imagecreatetruecolor($nw, $nh);

            imagecopyresampled(
                $tmp,
                $image,
                0,
                0,
                0,
                0,
                $nw,
                $nh,
                $w,
                $h
            );

            imagedestroy($image);

            $image = $tmp;
        }

        /* guardar jpeg */
        ob_start();

        imagejpeg($image, null, 75);

        $jpegData = ob_get_clean();

        imagedestroy($image);

        Storage::disk('public')
            ->put('relevamientos/' . $filename, $jpegData);

        return $filename;
    }
}