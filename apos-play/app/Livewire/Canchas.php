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

    // Estado
    public $canchaId = null;
    public $isEditing = false;

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

    public function crearCancha()
    {
        $this->resetForm();
        $this->canchaId = null;
        $this->isEditing = false;
        $this->showForm = true;
    }

    public function editarCancha($id)
    {
        $cancha = Cancha::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $this->canchaId = $cancha->id;
        $this->nombre = $cancha->nombre;
        $this->direccion = $cancha->direccion;
        $this->precio = $cancha->precio;
        $this->tipo = $cancha->tipo;
        $this->cantidad_jugadores = $cancha->cantidad_jugadores;

        $this->isEditing = true;
        $this->showForm = true;
    }

    public function confirmarGuardado()
    {
        $this->validate();
        $this->showConfirmModal = true;
    }

    public function guardarCancha()
    {
        if ($this->isEditing && $this->canchaId) {
            $cancha = Cancha::where('id', $this->canchaId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $cancha->update([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
                'precio' => $this->precio,
                'tipo' => $this->tipo,
                'cantidad_jugadores' => $this->cantidad_jugadores,
            ]);

            session()->flash('success', 'Cancha actualizada correctamente.');
        } else {
            Cancha::create([
                'user_id' => auth()->id(),
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
                'precio' => $this->precio,
                'tipo' => $this->tipo,
                'cantidad_jugadores' => $this->cantidad_jugadores,
            ]);

            session()->flash('success', 'Cancha creada correctamente.');
        }

        $this->resetForm();
        $this->canchaId = null;
        $this->isEditing = false;
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
