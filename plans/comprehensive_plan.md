# Dokumen Perencanaan Sistem Informasi Koperasi

## 1. Product Requirements Document (PRD)

### Tujuan Sistem
Mendigitalisasi pencatatan operasional koperasi (anggota, simpanan, pinjaman, angsuran) untuk meningkatkan akurasi, efisiensi waktu, dan kemudahan akses data.

### Scope Sistem
Sistem berbasis web untuk manajemen internal koperasi (Admin & Petugas).

### User Roles
1. **Admin:** Akses penuh (CRUD seluruh data, manajemen user, laporan).
2. **Petugas:** Akses terbatas (input transaksi simpanan/pinjaman/angsuran, melihat laporan).

### User Stories
- Sebagai Admin, saya ingin mengelola data anggota agar data terpusat.
- Sebagai Petugas, saya ingin menginput transaksi simpanan agar saldo anggota terupdate otomatis.
- Sebagai Admin, saya ingin mencetak laporan bulanan agar dapat dilaporkan ke pengurus.

---

## 2. Software Requirements Specification (SRS)
*(Mendukung fungsionalitas yang didefinisikan dalam PRD dengan fokus pada keandalan sistem CRUD dan pelaporan PDF).*

---

## 3. Use Case & Activity Diagram

| Aktor | Use Case |
| :--- | :--- |
| Admin | Kelola User, Kelola Anggota, Kelola Simpanan, Kelola Pinjaman, Kelola Angsuran, Lihat Laporan |
| Petugas | Kelola Simpanan, Kelola Pinjaman, Kelola Angsuran, Lihat Laporan |

*(Activity Diagram: Alur login -> Dashboard -> Modul terkait -> Transaksi/CRUD -> Selesai)*

---

## 4. Desain Database (ERD & Spesifikasi)

### ERD Ringkas
`Anggota` 1 --- N `Simpanan`
`Anggota` 1 --- N `Pinjaman`
`Pinjaman` 1 --- N `Angsuran`
`User` (RBAC via Spatie)

### Daftar Tabel Utama
- `users` (id, name, email, password)
- `members` (id, nomor_anggota, nik, nama, alamat, telepon, status)
- `savings` (id, member_id, type_id, tanggal, nominal)
- `loans` (id, member_id, tanggal, nominal, bunga, tenor, status)
- `installments` (id, loan_id, tanggal, nominal, denda)

---

## 5. Arsitektur Laravel
- **Pattern:** MVC + Service Layer + Repository Pattern
- **Folder Structure:** (Sesuai yang didefinisikan sebelumnya)

---

## 6. Desain UI/UX & Design System
- **Warna:** `#0d6efd` (Biru Utama), `#ffffff` (Putih), `#f8f9fa` (Abu-abu Muda).
- **Komponen:** Bootstrap 5 (Cards, Tables, Modals, Badges).
- **Layout:** Sidebar (Navigasi), Topbar (Profil), Main Content Area.

---

## 7. Implementasi Hotwire Turbo
- **Turbo Drive:** Navigasi antar halaman (login, dashboard, menu utama).
- **Turbo Frame:** CRUD anggota (tabel, modal tambah/edit).
- **Turbo Stream:** Update status pinjaman/saldo secara real-time setelah transaksi.

---

## 8. Roadmap Pengembangan (3 Minggu)

- **Minggu 1:** Setup Laravel, Auth (Breeze), Role (Spatie), Desain Database, Layout Utama.
- **Minggu 2:** Modul Anggota, Modul Simpanan, Modul Pinjaman.
- **Minggu 3:** Modul Angsuran, Modul Laporan (DomPDF), Testing, Deployment.

---

## 9. Deployment Plan
1. **Persiapan:** `php artisan config:cache`, `php artisan route:cache`, optimasi composer `composer install --optimize-autoloader --no-dev`.
2. **Server:** Konfigurasi VHost Apache/Nginx mengarah ke folder `public`.
3. **Database:** Import SQL ke server MySQL.
4. **Keamanan:** Aktifkan HTTPS (SSL), set `.env` (APP_ENV=production, DEBUG=false).
