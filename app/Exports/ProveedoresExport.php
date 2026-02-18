<?php

namespace App\Exports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProveedoresExport implements FromCollection, WithHeadings, WithMapping
{
    /**  Datos que se exportan */
    public function collection()
    {
        return Proveedor::orderBy('fechacarga')
            ->select('area', 'nombre', 'datos', 'producto',
                     'responsable', 'detalle', 'creador', 'fechacarga')
            ->get();
    }

    /**  Cabeceras de la hoja */
    public function headings(): array
    {
        return [
            'Área',
            'Nombre',
            'Datos de contacto',
            'Producto / Servicio',
            'Responsable',
            'Detalle',
            'Creado por',
            'Fecha de carga',
        ];
    }

    /**  Formateo columna a columna (opcional) */
    public function map($row): array
    {
        return [
            $row->area,
            $row->nombre,
            $row->datos,
            $row->producto,
            $row->responsable,
            $row->detalle,
            $row->creador,
            // Excel entiende yyyy-mm-dd
            \Carbon\Carbon::parse($row->fechacarga)
                ->format('Y-m-d'),
        ];
    }
}
