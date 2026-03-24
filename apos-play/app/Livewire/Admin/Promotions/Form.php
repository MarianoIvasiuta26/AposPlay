<?php

namespace App\Livewire\Admin\Promotions;

use App\Models\Promotion;
use App\Services\PromotionService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public ?Promotion $promotion = null;

    public string $name = '';
    public string $type = 'combo';
    public string $discount_value = '0';
    public ?string $points_bonus = null;
    public string $starts_at = '';
    public string $ends_at = '';
    public string $conditions = '';

    public ?string $conflictMessage = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'type' => 'required|in:combo,coupon,extra_points',
            'discount_value' => 'required|numeric|min:0',
            'points_bonus' => 'nullable|integer|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'conditions' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'type.required' => 'Seleccioná un tipo de promoción.',
            'discount_value.required' => 'Ingresá el valor del descuento.',
            'discount_value.numeric' => 'El valor debe ser numérico.',
            'starts_at.required' => 'Seleccioná una fecha de inicio.',
            'ends_at.required' => 'Seleccioná una fecha de fin.',
            'ends_at.after' => 'La fecha de fin debe ser posterior a la de inicio.',
        ];
    }

    public function mount($promotion = null)
    {
        if ($promotion) {
            $promotion = Promotion::findOrFail($promotion);
            $this->promotion = $promotion;
            $this->name = $promotion->name;
            $this->type = $promotion->type->value;
            $this->discount_value = (string) $promotion->discount_value;
            $this->points_bonus = $promotion->points_bonus ? (string) $promotion->points_bonus : null;
            $this->starts_at = $promotion->starts_at->format('Y-m-d\TH:i');
            $this->ends_at = $promotion->ends_at->format('Y-m-d\TH:i');
            $this->conditions = $promotion->conditions ? json_encode($promotion->conditions) : '';
        } else {
            $this->starts_at = now()->format('Y-m-d\TH:i');
            $this->ends_at = now()->addMonth()->format('Y-m-d\TH:i');
        }
    }

    public function save()
    {
        $this->validate();

        $this->conflictMessage = null;

        $promotionService = app(PromotionService::class);
        $data = [
            'type' => $this->type,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ];

        $conflict = $promotionService->validatePromotion($data, $this->promotion?->id);

        if ($conflict) {
            $this->conflictMessage = "Existe una promoción activa del mismo tipo que se superpone: \"{$conflict->name}\" ({$conflict->starts_at->format('d/m/Y')} - {$conflict->ends_at->format('d/m/Y')}).";
            return;
        }

        $attributes = [
            'name' => $this->name,
            'type' => $this->type,
            'discount_value' => $this->discount_value,
            'points_bonus' => $this->points_bonus ?: null,
            'conditions' => $this->conditions ? json_decode($this->conditions, true) : null,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'is_active' => true,
        ];

        if ($this->promotion) {
            $this->promotion->update($attributes);
            session()->flash('success', 'Promoción actualizada exitosamente.');
        } else {
            $attributes['created_by'] = auth()->id();
            Promotion::create($attributes);
            session()->flash('success', 'Promoción creada exitosamente.');
        }

        return redirect()->route('admin.promotions');
    }

    public function render()
    {
        return view('livewire.admin.promotions.form');
    }
}
