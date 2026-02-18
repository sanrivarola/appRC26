<?php

namespace App\Exports;

use App\Models\DevolucionTarea;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DevolucionesExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected string $desde;
    protected string $hasta;
    protected string $equipo;
    protected bool $soloFinalizadas;

    public function __construct(string $desde, string $hasta, string $equipo = 'todos', bool $soloFinalizadas = false)
    {
        $this->desde           = $desde;
        $this->hasta           = $hasta;
        $this->equipo          = $equipo ?: 'todos';
        $this->soloFinalizadas = $soloFinalizadas;
    }

    public function query()
    {
        // Orden y columnas: Fecha (A), Equipo (B), Tarea (C), Devoluci車n (D), Finalizada (E)
        $q = DevolucionTarea::query()
            ->from('devoluciontareas as d')
            ->join('tareas as t', 'd.id_tarea', '=', 't.id_tarea')
            ->whereBetween('d.fecha', [$this->desde, $this->hasta])
            ->select([
                'd.fecha',                 // A: Fecha
                'd.equipo',                // B: Equipo
                't.tarea as nombre_tarea', // C: Tarea
                'd.devolucion',            // D: Devoluci車n
                'd.finalizada',            // E: Finalizada
                'd.id',                    // para orden estable
            ])
            ->orderBy('d.fecha', 'asc')
            ->orderBy('d.id', 'asc');

        if ($this->equipo !== 'todos') {
            $q->where('d.equipo', $this->equipo);
        }
        if ($this->soloFinalizadas) {
            $q->where('d.finalizada', 'si');
        }

        return $q;
    }

    public function headings(): array
    {
        return ['Fecha', 'Equipo', 'Tarea', 'Devolucion', 'Finalizada'];
    }

    public function map($row): array
    {
        // Fecha como fecha nativa de Excel
        $excelDate = ExcelDate::stringToExcel($row->fecha); // 'YYYY-MM-DD' -> n迆mero Excel

        // Devoluci車n segura
        $dev = $row->devolucion;
        if (is_array($dev) || is_object($dev)) {
            $dev = json_encode($dev, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($dev)) {
            $dev = preg_replace("/\r\n|\r|\n/", " ", $dev);
        }

        return [
            $excelDate,          // A: Fecha
            $row->equipo,        // B: Equipo
            $row->nombre_tarea,  // C: Tarea
            $dev,                // D: Devoluci車n
            $row->finalizada,    // E: Finalizada
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_YYYYMMDD2, // Fecha
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $last  = $sheet->getHighestRow();

                // Encabezado en negrita
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);

                // Fijar fila 1
                $sheet->freezePane('A2');

                // Autofiltro
                $sheet->setAutoFilter("A1:E{$last}");

                // Envolver texto en Devoluci車n (columna D)
                $sheet->getStyle("D2:D{$last}")->getAlignment()->setWrapText(true);

                // Resaltar Finalizada = "no" en rojo suave (columna E)
                if ($last >= 2) {
                    $cond = new Conditional();
                    $cond->setConditionType(Conditional::CONDITION_CELLIS)
                         ->setOperatorType(Conditional::OPERATOR_EQUAL)
                         ->addCondition('"no"');
                    $cond->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
                    $cond->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
                                        ->getStartColor()->setARGB('FFFFE5E5');

                    $rangeStyle = $sheet->getStyle("E2:E{$last}");
                    $existing   = $rangeStyle->getConditionalStyles();
                    $existing[] = $cond;
                    $rangeStyle->setConditionalStyles($existing);
                }
            },
        ];
    }
}
