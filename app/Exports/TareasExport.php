<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TareasExport implements FromCollection, WithHeadings
{
    public function __construct(
        private \Carbon\Carbon $from,
        private \Carbon\Carbon $to,
        private string $equipo // ← se agrega aquí
    ) {}

    public function collection(): Collection
    {
        $query = DB::table('tareas as t')
            ->join('devoluciontareas as d', 'd.id_tarea', '=', 't.id_tarea')
            ->select('t.fecha_carga', 't.tarea', 't.equipo', 't.prioridad', 'd.finalizada')
            ->where('d.finalizada', 'si')
            ->whereBetween('t.fecha_carga', [$this->from->toDateString(), $this->to->toDateString()]);

        if ($this->equipo !== 'todos') {
            $query->where('t.equipo', $this->equipo);
        }

        return $query->orderBy('t.fecha_carga')->get();
    }

    public function headings(): array
    {
        return ['Fecha', 'Tarea', 'Responsable', 'Prioridad', 'Finalizada'];
    }
}
