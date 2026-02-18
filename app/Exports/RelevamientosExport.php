<?php

namespace App\Exports;

use App\Models\Relevamiento;
use Maatwebsite\Excel\Concerns\FromCollection;

class RelevamientosExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Relevamiento::all();
    }
}
