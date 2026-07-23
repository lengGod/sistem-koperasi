<x-app-layout>
    <x-slot name="header">
        <h2>Jenis Kendaraan</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Pengaturan Parkir</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Jenis Kendaraan</h1>
            <p class="mt-1 text-sm text-outline">Kelola daftar jenis kendaraan dan tarif per jam.</p>
        </div>

        <a href="{{ route('parking-types.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">add</span>
            Tambah Jenis
        </a>
    </section>

    <div class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">Nama Kendaraan</th>
                        <th class="px-6 py-4 text-right">Tarif / Jam</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($vehicleTypes as $type)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $type->name }}</td>
                            <td class="px-6 py-4 text-right font-bold text-on-surface">
                                Rp {{ number_format($type->hourly_rate, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('parking-types.edit', $type) }}"
                                        class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">
                                        Edit
                                    </a>
                                    <form action="{{ route('parking-types.destroy', $type) }}" method="POST"
                                        data-confirm="Anda yakin ingin menghapus jenis kendaraan {{ $type->name }}?"
                                        data-confirm-title="Hapus jenis" data-confirm-button="Ya, hapus"
                                        data-confirm-tone="danger">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-error transition hover:bg-error-container">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada data jenis kendaraan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
