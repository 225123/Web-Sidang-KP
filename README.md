# Sistem Informasi Sidang Kerja Praktek (KP)

Sistem Informasi Sidang Kerja Praktek (KP) adalah aplikasi berbasis web yang dirancang khusus untuk memanajemen seluruh siklus hidup kegiatan Kerja Praktek mahasiswa, mulai dari pendaftaran, proses bimbingan, hingga penjadwalan dan penilaian sidang akhir.

Aplikasi ini memfasilitasi tiga peran (role) utama dalam proses akademik:
1. **Koordinator KP**: Mengelola data master (mahasiswa, dosen, periode), memverifikasi pendaftaran, memploting pembimbing dan penguji, menjadwalkan sidang, serta merekap nilai akhir.
2. **Dosen (Pembimbing & Penguji)**: Melakukan persetujuan bimbingan, menyetujui mahasiswa untuk sidang, dan memberikan penilaian (baik sebagai pembimbing maupun penguji).
3. **Mahasiswa**: Mendaftar KP, mengisi log bimbingan, mendaftar sidang, mengunggah revisi, dan melihat hasil akhir kelulusan.

## Fitur Utama

- **Manajemen Pengguna (Role-Based Access Control)**: Memisahkan hak akses dan dashboard sesuai dengan role masing-masing.
- **Siklus Kerja Praktek End-to-End**: Meliputi Pendaftaran KP -> Bimbingan -> Persetujuan Sidang -> Penjadwalan -> Sidang -> Revisi -> Nilai Akhir.
- **Notifikasi Sistem**: Memberikan pemberitahuan secara *real-time* atau *in-app* untuk event-event penting (misal: jadwal sidang rilis, bimbingan ditolak/disetujui).
- **Penilaian Eksternal (Supervisor Instansi)**: Supervisor dari tempat KP dapat memberikan nilai secara langsung melalui tautan unik yang dikirimkan via email tanpa perlu login ke sistem.
- **Export PDF**: Pembuatan laporan secara otomatis (Berita Acara Sidang, Log Bimbingan, Transkrip Nilai) menggunakan PDF generation.

## Panduan Teknis & Arsitektur
Untuk memahami lebih dalam mengenai teknologi yang digunakan, struktur kode, dan cara melakukan *deployment* (Docker/Vercel), silakan membaca [ARCHITECTURE.md](./ARCHITECTURE.md).

## Struktur Database
Dokumentasi lengkap mengenai skema tabel, relasi, dan *Entity Relationship Diagram* (ERD) dapat dilihat di [DATABASE.md](./DATABASE.md).

## Instalasi Lokal (Getting Started)

1. **Kloning Repository**
   ```bash
   git clone https://github.com/225123/Web-Sidang-KP.git
   cd Web-Sidang-KP
   ```

2. **Install Dependensi**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file konfigurasi bawaan dan sesuaikan (terutama untuk koneksi database).
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database**
   ```bash
   php artisan migrate --seed
   ```
   *(Catatan: Gunakan opsi `--seed` jika Anda memiliki data awal/dummy)*

5. **Kompilasi Aset Frontend**
   ```bash
   npm run build
   ```

6. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
