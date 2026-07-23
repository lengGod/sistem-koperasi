<x-app-layout>
    <x-slot name="header">
        <h2>{{ isset($vehicleType) ? 'Edit' : 'Tambah' }} Jenis Kendaraan</h2>
    </x-slot>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-on-surface">{{ isset($vehicleType) ? 'Edit' : 'Tambah' }} Jenis Kendaraan</h1>
    </div>

    <form method="POST" action="{{ isset($vehicleType) ? route('parking-types.update', $vehicleType) : route('parking-types.store') }}"
        class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
        @csrf
        @if (isset($vehicleType)) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-on-surface">Nama Kendaraan</label>
                <input id="name" name="name" type="text" value="{{ old('name', $vehicleType->name ?? '') }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            </div>
            <div>
                <label for="hourly_rate" class="mb-2 block text-sm font-bold text-on-surface">Tarif per Jam</label>
                <input id="hourly_rate" name="hourly_rate" type="number" step="0.01" value="{{ old('hourly_rate', $vehicleType->hourly_rate ?? '') }}"
                    class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary" required>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('parking-types.index') }}"
                class="rounded-xl border border-outline-variant px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                Batal
            </a>
            <button type="submit"
                class="rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary transition hover:opacity-90">
                Simpan
            </button>
        </div>
    </form>
</x-app-layout>
