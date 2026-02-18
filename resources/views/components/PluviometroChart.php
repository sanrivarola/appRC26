<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PluviometroChart extends Component
{
    public $data;
    public function __construct($data){ $this->data=$data; }

    public function render(){ return view('components.pluviometro-chart'); }
}