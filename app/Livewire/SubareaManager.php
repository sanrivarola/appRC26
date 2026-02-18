<?php

namespace App\Livewire; 

use Livewire\Component;
use App\Models\AreaInventario;
use App\Models\SubareaInventario;

class SubareaManager extends Component
{
    public AreaInventario $area;
    public string $nombre = '';
    public ?SubareaInventario $editing = null;

    protected $rules = [
        'nombre' => 'required|string|max:255',
    ];

    public function render()
    {
        $subareas = $this->area->subareas()->latest()->get();
        return view('livewire.subarea-manager', compact('subareas'));
    }

    public function create()
    {
        $this->validate();

        SubareaInventario::create([
            'nombre'    => $this->nombre,
            'areapadre' => $this->area->id_areainventario,
        ]);

        $this->reset('nombre');
        session()->flash('success', 'Subárea creada.');
    }

    public function startEdit(SubareaInventario $subarea)
    {
        $this->editing = $subarea;
        $this->nombre  = $subarea->nombre;
    }

    public function update()
    {
        $this->validate();

        $this->editing->update(['nombre' => $this->nombre]);
        $this->reset('nombre', 'editing');
        session()->flash('success', 'Subárea actualizada.');
    }

    public function delete(SubareaInventario $subarea)
    {
        $subarea->delete();
        session()->flash('success', 'Subárea eliminada.');
    }
}
