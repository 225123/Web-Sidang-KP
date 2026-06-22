# Panduan Arsitektur & Teknologi

Dokumen ini menjelaskan struktur arsitektur sistem, teknologi yang digunakan, serta fungsinya masing-masing dalam membangun aplikasi **Sistem Informasi Sidang KP**.

## Stack Teknologi (The Tech Stack)

Aplikasi ini dibangun menggunakan arsitektur **TALL Stack** yang dimodifikasi (Tailwind, Alpine, Laravel), namun tidak menggunakan Livewire, melainkan menggunakan Blade biasa dikombinasikan dengan Vanilla JS / AlpineJS untuk reaktivitas ringan.

### 1. Backend & Framework Inti
- **[Laravel 12.0](https://laravel.com/)**: Merupakan kerangka kerja (*framework*) utama di sisi server (PHP 8.2+). Digunakan untuk menangani *routing*, *business logic* (Controllers), ORM database (Eloquent), autentikasi, serta interaksi sistem secara keseluruhan.
- **[Spatie Permission](https://spatie.be/docs/laravel-permission/)**: Library Laravel yang sangat kuat untuk menangani *Role-Based Access Control* (RBAC). Digunakan untuk memisahkan hak akses antara Koordinator, Dosen, dan Mahasiswa.
- **[Spatie Activitylog](https://spatie.be/docs/laravel-activitylog/)**: Digunakan untuk mencatat riwayat aktivitas (*Audit Log*), melacak siapa yang mengubah data apa (contoh: koordinator memvalidasi data).

### 2. Frontend & Styling
- **[Tailwind CSS (v3.4)](https://tailwindcss.com/)**: *Utility-first CSS framework* yang digunakan untuk merancang antarmuka pengguna (UI) secara cepat dan responsif tanpa menulis file CSS eksternal.
- **[Alpine.js (v3.4)](https://alpinejs.dev/)**: Framework JavaScript minimalis. Digunakan untuk membuat elemen UI yang interaktif (seperti *dropdown*, *modal*, *tabs*, peringatan/notifikasi) langsung di dalam file HTML/Blade tanpa perlu jQuery atau framework berat seperti React/Vue.
- **[Vite](https://vitejs.dev/)**: *Build tool* modern yang menggantikan Laravel Mix (Webpack). Digunakan untuk melakukan kompilasi file aset (Tailwind CSS & JavaScript) secara instan saat *development* dan meminifikasinya untuk produksi.
- **Blade Templates**: Mesin *templating* bawaan Laravel yang digunakan untuk merender tampilan (HTML) dari sisi server.

### 3. Utilitas Tambahan
- **[DomPDF (barryvdh/laravel-dompdf)](https://github.com/barryvdh/laravel-dompdf)**: Digunakan untuk men-generate (mencetak) file PDF langsung dari tampilan HTML (Blade). Sangat vital untuk fitur pencetakan **Berita Acara Sidang**, **Lembar Persetujuan**, dan **Daftar Log Bimbingan**.
- **[Laravel Excel (maatwebsite/excel)](https://laravel-excel.com/)**: Digunakan untuk fitur *Import* (unggah data masal, contoh: import daftar mahasiswa baru) dan *Export* laporan ke format Excel/CSV.
- **[Flysystem (AWS S3 & Google Drive Ext)](https://laravel.com/docs/filesystem)**: Digunakan untuk menyimpan file yang diunggah pengguna (seperti file laporan KP, file revisi). Dengan ekstensi ini, aplikasi bisa menyimpan file tidak hanya di lokal, tetapi juga langsung *push* ke Google Drive atau AWS S3 Bucket.

---

## Arsitektur Deployment & Infrastruktur

Aplikasi ini sangat fleksibel dan siap untuk di-*deploy* di dua lingkungan yang sangat berbeda:

### A. Serverless (Vercel)
Aplikasi dikonfigurasi untuk dapat di-*hosting* secara gratis (atau berbayar) di Vercel menggunakan PHP serverless.
- **Fungsi `vercel.json`**: File ini menginstruksikan Vercel untuk menggunakan *runtime* `vercel-php@0.9.0` (kompilator PHP untuk lingkungan *serverless*). Ini juga mengatur *routing* statis seperti `/assets/` agar langsung dilayani Vercel, sedangkan request lainnya diarahkan ke `/api/index.php` (titik masuk Laravel).

### B. Containerization (Docker)
Jika proyek ingin dipasang di VPS biasa (DigitalOcean, AWS EC2, dll), disediakan konfigurasi berbasis Docker.
- **Fungsi `Dockerfile`**: File ini adalah "resep" untuk membuat sistem operasi mini terisolasi yang sudah terpasang Nginx (Web Server) dan PHP-FPM 8.4.
  - Secara otomatis meng-install *dependencies* OS dan ekstensi PHP (`pdo_pgsql`, `pdo_mysql`, `gd`, `zip`).
  - Menjalankan `composer install` dan `npm run build` di dalam kontainer saat *build time*.
  - Mengatur kepemilikan file (permissions) agar aman.
  - Memanggil `supervisord` untuk menjaga agar Nginx dan PHP-FPM terus hidup dan *restart* jika *crash*.

---

## Struktur Direktori Utama

Berikut adalah direktori penting dalam *codebase* ini:

- `app/Http/Controllers/` - Berisi *logic* inti, dipisah berdasarkan role (`Koordinator/`, `Dosen/`, `Mahasiswa/`).
- `app/Models/` - Model Eloquent untuk representasi tabel database.
- `database/migrations/` - *Blueprint* untuk membuat tabel-tabel di database (Lihat `DATABASE.md`).
- `resources/views/` - File antarmuka pengguna (`.blade.php`). Dipisahkan juga per role (`koordinator/`, `dosen/`, `mahasiswa/`) dan tampilan umum seperti email (`emails/`).
- `resources/css/` & `resources/js/` - Aset Tailwind dan AlpineJS yang belum dikompilasi.
- `routes/web.php` - File konfigurasi utama di mana URL (rute) aplikasi didefinisikan dan disambungkan dengan *Controllers*.
- `docker/` - File pendukung untuk *Docker deployment* (konfigurasi Nginx, Supervisor, script startup).
