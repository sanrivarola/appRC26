<?php

namespace App\Exports;

use App\Models\Horario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class HorariosExport implements FromCollection, WithHeadings, WithEvents
{
    protected $equipo, $fecha1, $fecha2;

    public function __construct($equipo, $fecha1, $fecha2)
    {
        $this->equipo = $equipo;
        $this->fecha1 = $fecha1;
        $this->fecha2 = $fecha2;
    }

    public function collection()
    {
        $query = Horario::query();

        if ($this->fecha1 && $this->fecha2) {
            $query->whereBetween('dia', [$this->fecha1, $this->fecha2]);
        }

        if ($this->equipo && $this->equipo !== 'todos') {
            $query->where('equipo', $this->equipo);
        }

        $horarios = $query->orderBy('dia')->get();

        $data = [];

        foreach ($horarios as $h) {
            $entrada = $h->horarioentrada ? Carbon::parse($h->horarioentrada) : null;
            $salida  = $h->horariosalida ? Carbon::parse($h->horariosalida) : null;

            $diffSeconds = null;

            if ($entrada && $salida) {
                $diffSeconds = $entrada->diffInSeconds($salida);
            }

            $data[] = [
                $h->equipo,
                $h->dia,
                $entrada ? $entrada->format('d-m-Y h:i a') : '',
                $salida  ? $salida->format('d-m-Y h:i a') : '',
                $diffSeconds !== null ? $diffSeconds / 3600 / 24 : null, // Excel time
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['Equipo', 'Fecha', 'Entrada', 'Salida', 'Horas trabajadas'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Encabezados en negrita
                $event->sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => ['bold' => true]
                ]);

                // Formato hh:mm para la columna de horas
                $highestRow = $event->sheet->getHighestRow();
                $event->sheet->getDelegate()->getStyle("E2:E{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('[h]:mm');
            }
        ];
    }
}
