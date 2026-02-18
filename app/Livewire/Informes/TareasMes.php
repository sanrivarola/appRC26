<?php

namespace App\Livewire\Informes;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TareasExport;

/**
 *  Componente Livewire  ▸  Informes ▸  Tareas por período
 *
 *  • Selector “7 d / 30 d / 365 d / personalizado”
 *  • Agrupación dinámica:
 *      – 7 d   → día
 *      – 30 d  → semana ISO
 *      – 365 d → mes
 *  • Gráfico consumido vía Alpine/Chart.js (entangle)
 *  • Exportar a Excel (opcional)
 */
class TareasMes extends Component
{
    /* ---------------------------- Estado público ---------------------------- */

    public string  $preset = '30d';   // 7d | 30d | 365d | custom
    public ?string $from   = null;    // YYYY-MM-DD  (custom)
    public ?string $to     = null;

    /**  Payload reactivo para Alpine (labels, values, rango) */
    public array $chartPayload = [
        'labels'     => [],
        'values'     => [],
        'range_from' => null,
        'range_to'   => null,
    ];

    /* ------------------------------ Ciclo de vida --------------------------- */

    /**  Al montar el componente creamos el primer dataset */
    public function mount(): void
    {
        $this->generate();
    }

    /* --------------------------- Listeners Livewire ------------------------- */

    /**  Cambia el preset (selector) */
public function updatedPreset()
{
    dd('preset cambió a → '.$this->preset);
        // Si no es “custom”, limpiamos fechas
        if ($this->preset !== 'custom') {
            $this->from = $this->to = null;
        }
        $this->generate();
    }

    /**  Cambios en fecha “from” dentro de preset custom */
    public function updatedFrom(): void
    {
        if ($this->preset === 'custom') $this->generate();
    }

    /**  Cambios en fecha “to” dentro de preset custom */
    public function updatedTo(): void
    {
        if ($this->preset === 'custom') $this->generate();
    }

    /* ------------------------------ Render ---------------------------------- */

    public function render()
    {
        return view('livewire.informes.tareas-mes');
    }

    /* --------------------------- Lógica interna ----------------------------- */

    private function generate(): void
    {
        /* ---------- Rango de fechas ---------------------------------------- */
        [$from, $to] = match ($this->preset) {
            '7d'   => [now()->copy()->subDays(6)->startOfDay(),  now()->endOfDay()],
            '30d'  => [now()->copy()->subDays(29)->startOfDay(), now()->endOfDay()],
            '365d' => [now()->copy()->subDays(364)->startOfDay(),now()->endOfDay()],
            'custom' => [
                Carbon::parse($this->from)->startOfDay(),
                Carbon::parse($this->to  )->endOfDay(),
            ],
        };

        /* ---------- Agrupación dinámica ------------------------------------ */
        [$groupSql, $orderBy] = match ($this->preset) {
            '7d'   => ['DATE(t.fecha_carga)',            'periodo'],          // día
            '30d'  => ['YEARWEEK(t.fecha_carga, 3)',     'periodo'],          // semana ISO
            default=> ['DATE_FORMAT(t.fecha_carga,"%Y-%m")','periodo'],       // mes
        };

        /* ---------- Consulta ------------------------------------------------ */
        $rows = DB::table('tareas as t')
            ->selectRaw("$groupSql as periodo, COUNT(*) as total")
            ->join('devoluciontareas as d','d.id_tarea','=','t.id_tarea')
            ->where('d.finalizada','si')
            ->whereBetween('t.fecha_carga', [$from->toDateString(), $to->toDateString()])
            ->groupBy('periodo')
            ->orderBy($orderBy)
            ->get();

        /* ---------- Payload para Alpine/Chart.js --------------------------- */
        $this->chartPayload = [
            'labels'     => $rows->pluck('periodo'),
            'values'     => $rows->pluck('total'),
            'range_from' => $from->toDateString(),
            'range_to'   => $to->toDateString(),
        ];
    }

    /* ------------------------------ Export --------------------------------- */

    /**  Exportar a Excel el rango actual */
    public function exportExcel()
    {
        $from = Carbon::parse($this->chartPayload['range_from']);
        $to   = Carbon::parse($this->chartPayload['range_to']);

        return Excel::download(
            new TareasExport($from, $to),
            'tareas-'.$from->format('Ymd').'-'.$to->format('Ymd').'.xlsx'
        );
    }
}
