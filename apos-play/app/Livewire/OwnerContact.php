<?php

namespace App\Livewire;

use App\Mail\OwnerRequestMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Rule;
use Livewire\Component;

class OwnerContact extends Component
{
    #[Rule('required|string|max:100')]
    public string $complexName = '';

    #[Rule('required|string|max:100')]
    public string $complexCity = '';

    #[Rule('required|string|max:150')]
    public string $complexAddress = '';

    #[Rule('required|string|max:100')]
    public string $complexCourts = '';

    #[Rule('required|string|max:100')]
    public string $contactName = '';

    #[Rule('required|email|max:150')]
    public string $contactEmail = '';

    #[Rule('required|string|max:30')]
    public string $contactPhone = '';

    public bool $sent = false;
    public string $error = '';

    public function submit(): void
    {
        $this->validate();

        $destination = config('mail.contact_email', config('mail.from.address'));

        try {
            Mail::to($destination)->send(new OwnerRequestMail(
                complexName: $this->complexName,
                complexCity: $this->complexCity,
                complexAddress: $this->complexAddress,
                complexCourts: $this->complexCourts,
                contactName: $this->contactName,
                contactEmail: $this->contactEmail,
                contactPhone: $this->contactPhone,
            ));

            $this->sent = true;
        } catch (\Exception $e) {
            $this->error = 'Hubo un error al enviar la solicitud. Por favor intentá de nuevo más tarde.';
        }
    }

    public function render()
    {
        return view('livewire.owner-contact');
    }
}
