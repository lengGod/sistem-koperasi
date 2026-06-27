@php
    $status = $status ?? null;
    $map = [
        'active'      => ['label' => 'Aktif',         'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'icon' => 'check_circle'],
        'inactive'    => ['label' => 'Pasif',         'bg' => 'bg-slate-200',   'text' => 'text-slate-700',   'icon' => 'pause_circle'],
        'paid'        => ['label' => 'Lunas',         'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'icon' => null],
        'pending'     => ['label' => 'Tertunda',      'bg' => 'bg-amber-100',   'text' => 'text-amber-800',   'icon' => null],
        'partial'     => ['label' => 'Sebagian',      'bg' => 'bg-amber-100',   'text' => 'text-amber-800',   'icon' => null],
        'late'        => ['label' => 'Telat',         'bg' => 'bg-red-100',     'text' => 'text-red-800',     'icon' => null],
        'completed'   => ['label' => 'Selesai',       'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'icon' => null],
        'deposit'     => ['label' => 'Setoran',       'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'icon' => null],
        'withdrawal'  => ['label' => 'Penarikan',     'bg' => 'bg-red-100',     'text' => 'text-red-800',     'icon' => null],
    ];
    $info = $map[$status] ?? ['label' => ucfirst((string) $status), 'bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
@endphp
<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $info['bg'] }} {{ $info['text'] }}">
    @if (! empty($info['icon']))
        <span class="material-symbols-outlined text-[14px]">{{ $info['icon'] }}</span>
    @endif
    {{ $info['label'] }}
</span>