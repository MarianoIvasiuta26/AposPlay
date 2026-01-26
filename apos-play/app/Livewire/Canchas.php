<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cancha;

class Canchas extends Component
{
    // Form
    public $nombre;
    public $direccion;
    public $precio;
    public $tipo;
    public $cantidad_jugadores;

    // UI
    public $showForm = false;
    public $showConfirmModal = false;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'precio' => 'required|numeric|min:0',
        'tipo' => 'required|in:futbol,padel',
        'cantidad_jugadores' => 'required|integer|min:1',
    ];

    public function render()
    {
        return view('livewire.canchas', [
            'canchas' => Cancha::where('user_id', auth()->id())->get()
        ]);
    }

    // Paso 3 – Crear cancha
    public function crearCancha()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    // Paso 5 → 6
    public function confirmarGuardado()
    {
        $this->validate();
        $this->showConfirmModal = true;
    }

    // Paso 8 → 9
    public function guardarCancha()
    {
        Cancha::create([
            'user_id' => auth()->id(),
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'precio' => $this->precio,
            'tipo' => $this->tipo,
            'cantidad_jugadores' => $this->cantidad_jugadores,
        ]);

        session()->flash('success', 'Cancha creada correctamente.');

        $this->resetForm();
        $this->showForm = false;
        $this->showConfirmModal = false;
    }

    private function resetForm()
    {
        $this->reset([
            'nombre',
            'direccion',
            'precio',
            'tipo',
            'cantidad_jugadores',
        ]);
    }
}
