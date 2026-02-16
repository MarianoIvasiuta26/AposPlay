<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Court;
use App\Models\CourtAddress;
use App\Models\CourtsXAdmin;
use Illuminate\Support\Facades\DB;

class Canchas extends Component
{
    // Formulario Cancha
    public $nombre;
    public $precio;
    public $tipo;
    public $cantidad_jugadores;

    // Formulario Dirección
    public $street;
    public $number;
    public $city;
    public $province;
    public $zip_code;
    public $country;

    // Estado
    public $courtId = null;
    public $isEditing = false;

    // UI
    public $showForm = false;
    public $showConfirmModal = false;

    protected $rules = [
        // Validaciones Cancha
        'nombre' => 'required|string|max:255',
        'precio' => 'required|numeric|min:0',
        'tipo' => 'required|in:futbol,padel',
        'cantidad_jugadores' => 'required|integer|min:1',

        // Validaciones Dirección
        'street' => 'required|string|max:255',
        'number' => 'required|string|max:50',
        'city' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'zip_code' => 'required|string|max:20',
        'country' => 'required|string|max:255',
    ];

    public function render()
    {
        // Obtener las canchas asociadas al administrador actual
        $canchas = Court::whereHas('courtsXAdmin', function ($query) {
            $query->where('user_id', auth()->id());
        })->with('address')->get();

        return view('livewire.canchas', [
            'canchas' => $canchas
        ]);
    }

    public function crearCancha()
    {
        $this->resetForm();
        $this->courtId = null;
        $this->isEditing = false;
        $this->showForm = true;
    }

    public function editarCancha($id)
    {
        $cancha = Court::findOrFail($id);
        
        // Verificar propiedad
        $esPropia = $cancha->courtsXAdmin()->where('user_id', auth()->id())->exists();
        if (!$esPropia) {
            abort(403);
        }

        $this->courtId = $cancha->id;
        
        // Datos Cancha
        $this->nombre = $cancha->name;
        $this->precio = $cancha->price;
        $this->tipo = $cancha->type;
        $this->cantidad_jugadores = $cancha->number_players;

        // Datos Dirección
        if ($cancha->address) {
            $this->street = $cancha->address->street;
            $this->number = $cancha->address->number;
            $this->city = $cancha->address->city;
            $this->province = $cancha->address->province;
            $this->zip_code = $cancha->address->zip_code;
            $this->country = $cancha->address->country;
        }

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
        $this->validate();

        DB::transaction(function () {
            if ($this->isEditing && $this->courtId) {
                $cancha = Court::findOrFail($this->courtId);
                
                // Verificar propiedad nuevamente por seguridad
                $esPropia = $cancha->courtsXAdmin()->where('user_id', auth()->id())->exists();
                if (!$esPropia) {
                    abort(403);
                }

                // Actualizar dirección
                $cancha->address->update([
                    'street' => $this->street,
                    'number' => $this->number,
                    'city' => $this->city,
                    'province' => $this->province,
                    'zip_code' => $this->zip_code,
                    'country' => $this->country,
                ]);

                // Actualizar cancha
                $cancha->update([
                    'name' => $this->nombre,
                    'price' => $this->precio,
                    'type' => $this->tipo,
                    'number_players' => $this->cantidad_jugadores,
                ]);

                session()->flash('success', 'Cancha actualizada correctamente.');
            } else {
                // Crear dirección
                $address = CourtAddress::create([
                    'street' => $this->street,
                    'number' => $this->number,
                    'city' => $this->city,
                    'province' => $this->province,
                    'zip_code' => $this->zip_code,
                    'country' => $this->country,
                ]);

                // Crear cancha
                $cancha = Court::create([
                    'name' => $this->nombre,
                    'price' => $this->precio,
                    'type' => $this->tipo,
                    'number_players' => $this->cantidad_jugadores,
                    'court_address_id' => $address->id,
                ]);

                // Asignar al admin
                CourtsXAdmin::create([
                    'court_id' => $cancha->id,
                    'user_id' => auth()->id(),
                ]);

                session()->flash('success', 'Cancha creada correctamente.');
            }
        });

        $this->resetForm();
        $this->courtId = null;
        $this->isEditing = false;
        $this->showForm = false;
        $this->showConfirmModal = false;
    }

    private function resetForm()
    {
        $this->reset([
            'nombre',
            'precio',
            'tipo',
            'cantidad_jugadores',
            'street',
            'number',
            'city',
            'province',
            'zip_code',
            'country',
        ]);
    }
}
