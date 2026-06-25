<x-app-layout>
    <x-slot name="header">
        <h2>Detail Anggota</h2>
    </x-slot>

    <section class="w-full">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Anggota</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">{{ $member->name }}</h1>
                <p class="mt-1 text-sm text-outline">{{ $member->member_number }} | {{ $member->nik }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:opacity-90">
                    <span class="material-symbols-outlined icon-fill text-[20px]">edit</span>
                    Edit
                </a>
                <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant bg-surface-container-lowest px-4 py-2 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low">
                    Kembali
                </a>
            </div>
        </div>

        <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Nomor Anggota</p>
                    <p class="mt-1 text-sm font-semibold text-on-surface">{{ $member->member_number }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Status</p>
                    <p class="mt-1 text-sm font-semibold text-on-surface">{{ $member->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Jenis Kelamin</p>
                    <p class="mt-1 text-sm font-semibold text-on-surface">{{ $member->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tanggal Bergabung</p>
                    <p class="mt-1 text-sm font-semibold text-on-surface">{{ optional($member->joined_at)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Tempat, Tanggal Lahir</p>
                    <p class="mt-1 text-sm font-semibold text-on-surface">
                        {{ $member->birth_place ?: '-' }}{{ $member->birth_date ? ', '. $member->birth_date->format('d M Y') : '' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Kontak</p>
                    <p class="mt-1 text-sm font-semibold text-on-surface">{{ $member->phone ?: '-' }}</p>
                    <p class="text-sm text-outline">{{ $member->email ?: '-' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Alamat</p>
                    <p class="mt-1 text-sm font-semibold leading-6 text-on-surface">{{ $member->address ?: '-' }}</p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-between rounded-2xl bg-surface-container-low px-4 py-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-outline">Aksi Cepat</p>
                    <p class="text-sm font-semibold text-on-surface">Edit atau hapus data anggota ini.</p>
                </div>
                <form
                    action="{{ route('members.destroy', $member) }}"
                    method="POST"
                    data-confirm="Anda akan menghapus data anggota ini."
                    data-confirm-title="Hapus anggota"
                    data-confirm-message="Tindakan ini tidak dapat dibatalkan."
                    data-confirm-button="Ya, hapus"
                    data-confirm-tone="danger"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-error-container px-4 py-2 text-sm font-bold text-on-error-container transition hover:opacity-90">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
