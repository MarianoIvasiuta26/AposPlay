<div>
    <h2>Reservar: {{ $court->name }}</h2>
    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($date)->translatedFormat('l j F Y') }}</p>
    <p><strong>Horario:</strong> {{ $schedule->start_time }} - {{ $schedule->end_time }} ({{ $schedule->turn }})</p>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="submit">
        <div>
            <label for="start_time">Hora de inicio:</label>
            <input type="time" wire:model="start_time">
            @error('start_time') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="duration_hours">Duración (horas):</label>
            <select wire:model="duration_hours">
                @for ($i = 1; $i <= 4; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
            @error('duration_hours') <span class="error">{{ $message }}</span> @enderror
        </div>

        <button type="submit">Reservar cancha</button>
    </form>
</div>
