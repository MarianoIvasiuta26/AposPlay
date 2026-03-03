<?php

namespace App\Livewire\Admin;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\User;
use App\Notifications\CouponAssigned;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Coupons extends Component
{
    // View state
    public bool $showCreateForm = false;
    public bool $showConfirmModal = false;

    // Form fields
    public string $type = 'percentage';
    public string $value = '';
    public string $description = '';
    public array $selectedUsers = [];
    public string $validFrom = '';
    public ?string $validUntil = null;
    public ?int $maxUses = null;

    // Search
    public string $search = '';

    protected function rules(): array
    {
        return [
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0.01' . ($this->type === 'percentage' ? '|max:100' : ''),
            'description' => 'required|string|min:3|max:500',
            'selectedUsers' => 'required|array|min:1',
            'selectedUsers.*' => 'exists:users,id',
            'validFrom' => 'required|date|after_or_equal:today',
            'validUntil' => 'nullable|date|after:validFrom',
            'maxUses' => 'nullable|integer|min:1',
        ];
    }

    protected function messages(): array
    {
        return [
            'type.required' => 'Seleccioná el tipo de descuento.',
            'value.required' => 'Ingresá el valor del descuento.',
            'value.numeric' => 'El valor debe ser numérico.',
            'value.min' => 'El valor debe ser mayor a 0.',
            'value.max' => 'El porcentaje no puede ser mayor a 100.',
            'description.required' => 'Ingresá una descripción para el cupón.',
            'description.min' => 'La descripción debe tener al menos 3 caracteres.',
            'selectedUsers.required' => 'Seleccioná al menos un cliente.',
            'selectedUsers.min' => 'Seleccioná al menos un cliente.',
            'validFrom.required' => 'Seleccioná una fecha de inicio.',
            'validFrom.after_or_equal' => 'La fecha de inicio debe ser hoy o posterior.',
            'validUntil.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'maxUses.min' => 'El máximo de usos debe ser al menos 1.',
        ];
    }

    public function mount()
    {
        $this->validFrom = now()->format('Y-m-d');
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->showCreateForm = true;
    }

    public function cancelCreate()
    {
        $this->resetForm();
        $this->showCreateForm = false;
    }

    public function resetForm()
    {
        $this->reset(['type', 'value', 'description', 'selectedUsers', 'validUntil', 'maxUses']);
        $this->type = 'percentage';
        $this->validFrom = now()->format('Y-m-d');
        $this->resetValidation();
    }

    /**
     * Step 1: validate and show confirmation modal
     */
    public function saveCoupon()
    {
        $this->validate();
        $this->dispatch('open-modal', name: 'confirm-coupon-modal');
    }

    /**
     * Step 2: confirmed — create and assign
     */
    public function confirmSave()
    {
        $this->validate();

        $coupon = Coupon::create([
            'code' => Coupon::generateCode(),
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'max_uses' => $this->maxUses,
            'valid_from' => $this->validFrom,
            'valid_until' => $this->validUntil ?: null,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        // Attach selected users
        $coupon->users()->attach($this->selectedUsers);

        // Notify each selected user
        $users = User::whereIn('id', $this->selectedUsers)->get();
        foreach ($users as $user) {
            $user->notify(new CouponAssigned($coupon));
        }

        $this->dispatch('close-modal', name: 'confirm-coupon-modal');
        $this->resetForm();
        $this->showCreateForm = false;

        session()->flash('success', '¡Cupón creado exitosamente! Se notificó a los clientes seleccionados.');
    }

    public function toggleStatus(int $couponId)
    {
        $coupon = Coupon::where('created_by', auth()->id())->findOrFail($couponId);
        $coupon->update(['is_active' => !$coupon->is_active]);
    }

    public function deleteCoupon(int $couponId)
    {
        $coupon = Coupon::where('created_by', auth()->id())->findOrFail($couponId);
        $coupon->delete();

        session()->flash('success', 'Cupón eliminado exitosamente.');
    }

    public function render()
    {
        $coupons = Coupon::where('created_by', auth()->id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('users')
            ->latest()
            ->get();

        $availableUsers = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('livewire.admin.coupons', [
            'coupons' => $coupons,
            'availableUsers' => $availableUsers,
        ]);
    }
}
