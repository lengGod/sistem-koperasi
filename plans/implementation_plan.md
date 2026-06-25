# Rencana Implementasi - Sistem Informasi Koperasi

Dokumen ini menguraikan peta jalan (roadmap) dan keputusan arsitektur untuk Sistem Informasi Koperasi.

## Struktur Proyek (Clean Code)
Struktur ini mengikuti prinsip modularitas dan pemisahan tanggung jawab (separation of concerns):
```text
app/
├── Http/
│   ├── Controllers/          # Controller (Handle request)
│   ├── Requests/             # Form Request (Validation)
│   └── Middleware/           # Middleware (Auth & Role)
├── Models/                   # Eloquent Models
├── Services/                 # Business Logic Layer
├── Repositories/             # Data Access Layer (Interface & Implementation)
├── Providers/                # Service Provider
└── Policies/                 # Authorization Policies

resources/
├── views/
│   ├── layouts/              # Blade Layouts (Master, Auth)
│   ├── components/           # Blade Components (Reusable UI)
│   └── dashboard/            # Modul views
└── js/
    └── turbo/                # Konfigurasi Hotwire Turbo
```

## Tahapan Implementasi (Roadmap)

### Tahap 1: Fondasi & Pengaturan Dasar
- [x] Inisialisasi proyek & konfigurasi lingkungan.
- [x] Instalasi dan konfigurasi Laravel Breeze (Autentikasi).
- [x] Instalasi dan konfigurasi Spatie Permission.
- [x] Instalasi dan konfigurasi Bootstrap 5 & Hotwire Turbo.
- [x] Pembuatan Layout Utama (Sidebar, Topbar).
- [x] Pengaturan Migrasi Database (Role, Permission, User).

### Tahap 2: Manajemen Anggota
- [ ] Migrasi & Factory untuk `Members`.
- [ ] Pembuatan CRUD Controller, Form Request, Service, dan Repository untuk `Members`.
- [ ] Tampilan Blade untuk daftar anggota, buat, edit, hapus (dengan Turbo).

### Tahap 3: Inti Finansial (Simpanan & Pinjaman)
- [ ] Skema Database: `SavingsTypes`, `Savings`, `Loans`, `Installments`.
- [ ] Logika Service/Repository untuk mengelola Simpanan dan Pinjaman.

### Tahap 4: Transaksi & Pelaporan
- [ ] Implementasi logika `Installment` (Angsuran).
- [ ] Pembuatan Dashboard Pelaporan (Statistik, Grafik).
- [ ] Implementasi Ekspor PDF (contoh: `dompdf` atau `snappy`).

### Tahap 5: Penyempurnaan & Pengujian
- [ ] Pengujian fitur (Feature testing) untuk modul inti.
- [ ] Penyempurnaan UI (Statistik dashboard, tabel, modal).
- [ ] Konfigurasi siap produksi.

---
*Catatan: Rencana ini dapat diubah. Silakan perbarui jika diperlukan.*
