@php
    $status = $status ?? null;
    $map = [
        'active'      => ['label' => 'Aktif',         'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
        'inactive'    => ['label' => 'Tidak Aktif',   'bg' => 'bg-slate-100',   'text' => 'text-slate-700'],
        'paid'        => ['label' => 'Lunas',         'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
        'pending'     => ['label' => 'Tertunda',      'bg' => 'bg-amber-100',   'text' => 'text-amber-800'],
        'partial'     => ['label' => 'Sebagian',      'bg' => 'bg-amber-100',   'text' => 'text-amber-800'],
        'late'        => ['label' => 'Telat',         'bg' => 'bg-red-100',     'text' => 'text-red-800'],
        'completed'   => ['label' => 'Selesai',       'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
        'deposit'     => ['label' => 'Setoran',       'bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
        'withdrawal'  => ['label' => 'Penarikan',     'bg' => 'bg-red-100',     'text' => 'text-red-800'],
    ];
    $info = $map[$status] ?? ['label' => ucfirst((string) $status), 'bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $info['bg'] }} {{ $info['text'] }}">
    {{ $info['label'] }}
</span>