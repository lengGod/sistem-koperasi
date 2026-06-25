<x-app-layout>
    <x-slot name="header">
        <h2>Anggota</h2>
    </x-slot>

    <section class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Anggota</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Daftar Anggota</h1>
            <p class="mt-1 text-sm text-outline">Cari, filter, dan kelola data anggota koperasi.</p>
        </div>

        <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
            <span class="material-symbols-outlined icon-fill text-[20px]">person_add</span>
            Tambah Anggota
        </a>
    </section>

    <form method="GET" action="{{ route('members.index') }}" class="dashboard-card mb-6 rounded-3xl bg-surface-container-lowest p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="md:col-span-2">
                <label for="search" class="mb-2 block text-sm font-bold text-on-surface">Cari</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Nomor anggota, NIK, nama, atau telepon" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-bold text-on-surface">Status</label>
                <select id="status" name="status" class="w-full rounded-xl border-outline-variant bg-surface-container-lowest text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap justify-end gap-2">
            <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                Reset
            </a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                <span class="material-symbols-outlined icon-fill text-[20px]">filter_alt</span>
                Terapkan
            </button>
        </div>
    </form>

    @php
        $memberIds = $members->pluck('id')->all();
    @endphp

    <form
        x-data="{ selected: [], memberIds: @js($memberIds) }"
        method="POST"
        action="{{ route('members.bulk-destroy') }}"
        data-confirm="Anda akan menghapus anggota yang dipilih."
        data-confirm-title="Hapus anggota terpilih"
        data-confirm-message="Tindakan ini akan menghapus semua anggota yang dicentang dan tidak dapat dibatalkan."
        data-confirm-button="Ya, hapus"
        data-confirm-tone="danger"
        class="dashboard-card overflow-hidden rounded-3xl bg-surface-container-lowest"
    >
        @csrf

        <div class="flex flex-col gap-3 border-b border-outline-variant px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-on-surface">Data Anggota</h3>
                <p class="text-sm text-outline">Pilih satu atau lebih anggota untuk dihapus bersamaan.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low" @click="selected = selected.length === memberIds.length ? [] : memberIds.slice()">
                    Pilih Semua
                </button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-error-container px-4 py-2 text-sm font-bold text-on-error-container transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50" :disabled="selected.length === 0">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1160px] text-left text-sm">
                <thead class="bg-surface-container-low text-xs font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                    <tr>
                        <th class="w-14 px-6 py-4">
                            <input type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary" @change="selected = $event.target.checked ? memberIds.slice() : []" :checked="selected.length === memberIds.length && memberIds.length > 0">
                        </th>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nomor Anggota</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">NIK</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($members as $member)
                        <tr class="transition hover:bg-surface-container">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="member_ids[]" value="{{ $member->id }}" class="rounded border-outline-variant text-primary focus:ring-primary" x-model="selected">
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $loop->iteration + ($members->currentPage() - 1) * $members->perPage() }}</td>
                            <td class="px-6 py-4 font-bold text-on-surface">{{ $member->member_number }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-on-surface">{{ $member->name }}</div>
                                <div class="text-xs text-outline">{{ $member->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $member->nik }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">
                                <div>{{ $member->phone ?: '-' }}</div>
                                <div class="text-xs text-outline">{{ $member->email ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $member->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-error-container text-on-error-container' }} rounded-full px-2.5 py-1 text-xs font-extrabold">
                                    {{ $member->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('members.show', $member) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                                        Detail
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="rounded-xl border border-outline-variant px-3 py-2 text-sm font-bold text-primary transition hover:bg-primary-fixed">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-outline">
                                Belum ada data anggota.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-outline-variant px-6 py-4">
            {{ $members->links() }}
        </div>
    </form>
</x-app-layout>
