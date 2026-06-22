# Panduan Arsitektur & Teknologi (Sistem Sidang KP)

Dokumen ini membedah keputusan teknologi (Tech Stack) yang dipilih secara khusus untuk membangun **Sistem Informasi Sidang KP**, beserta alasan spesifik mengapa teknologi tersebut digunakan dan bagaimana penerapannya dalam konteks proyek ini.

---

## 1. Backend & Framework Inti

### Laravel 12 (PHP 8.2+)
- **Kenapa digunakan?** Proyek ini memiliki alur logika akademik yang rumit dan tumpang tindih (Pendaftaran KP -> Validasi Koordinator -> Proses Bimbingan -> Penjadwalan -> Eksekusi Sidang -> Penilaian Multi-aktor). Laravel dipilih karena memiliki ekosistem MVC (Model-View-Controller) dan ORM (Eloquent) yang sangat matang untuk menangani relasi database kompleks ini tanpa membuat kode menjadi berantakan (*spaghetti code*).
- **Fungsi spesifik dalam web ini:** Menjadi "otak" utama sistem yang mengatur *routing* (navigasi halaman), memproses logika formulir (seperti kalkulasi Nilai Akhir dari persentase berbagai penguji), mengirimkan notifikasi/email otomatis, dan memastikan integritas data akademik di database.

### Spatie Permission
- **Kenapa digunakan?** Sistem ini diakses oleh 3 aktor dengan hak dan batasan privasi yang sangat berbeda: Koordinator KP, Dosen (sebagai pembimbing/penguji), dan Mahasiswa. Mengkoding logika *Role-Based Access Control* (RBAC) secara manual sangat berisiko fatal jika terjadi celah keamanan.
- **Fungsi spesifik dalam web ini:** Melindungi *route* dan tombol aksi. Memastikan secara mutlak bahwa mahasiswa tidak bisa menembus halaman penjadwalan koordinator, dan seorang dosen hanya bisa memasukkan nilai untuk mahasiswa yang secara sah dibimbing/diujinya.

### Spatie Activitylog
- **Kenapa digunakan?** Dalam sistem akademik, transparansi rekam jejak (*audit trail*) sangat krusial untuk akuntabilitas. Jika ada perubahan status kelulusan atau revisi jadwal secara tiba-tiba, harus ada bukti forensik sistem.
- **Fungsi spesifik dalam web ini:** Berjalan secara *silent* di latar belakang untuk mencatat setiap kali tabel penting (seperti nilai atau jadwal sidang) di-update, lengkap dengan riwayat "siapa yang mengubah", "jam berapa", dan "apa nilai sebelumnya".

---

## 2. Frontend & Antarmuka (TALL Stack)

### Tailwind CSS (v3.4)
- **Kenapa digunakan?** Proyek ini memerlukan *dashboard* yang terlihat profesional, modern, dan harus **100% responsif di perangkat mobile** (karena dosen/mahasiswa sering memantau status atau membalas notifikasi via *smartphone*, seperti iPhone). Tailwind memungkinkan pembuatan desain unik ini dengan cepat tanpa konflik CSS.
- **Fungsi spesifik dalam web ini:** Mengatur *layout*, *grid*, warna, *typography*, dan tampilan responsif (seperti mengubah tata letak form nilai menjadi vertikal jika dibuka di HP) langsung melalui penulisan *class* di dalam file `.blade.php`.

### Alpine.js (v3.4)
- **Kenapa digunakan?** Aplikasi ini adalah aplikasi *server-rendered* klasik, bukan *Single Page Application* seperti React/Vue. Namun, UI modern butuh sedikit reaktivitas (seperti *dropdown*, *modal popup* konfirmasi hapus jadwal, peringatan, atau transisi *tab*). Alpine.js memberikan reaktivitas tersebut dengan ukuran file yang sangat kecil.
- **Fungsi spesifik dalam web ini:** Menangani interaktivitas UI murni di sisi *client-side* tanpa perlu jQuery.

### Vite
- **Kenapa digunakan?** Merupakan standar industri modern yang menggantikan Webpack (Laravel Mix) karena kemampuannya me-*rebuild* aset secara instan.
- **Fungsi spesifik dalam web ini:** Melakukan kompilasi (*bundling*) dan minifikasi file Tailwind CSS dan JavaScript agar ukuran halamannya menjadi sangat kecil dan *website* memuat dengan sangat cepat saat *production*.

---

## 3. Utilitas Fungsional Khusus

### barryvdh/laravel-dompdf
- **Kenapa digunakan?** Hasil akhir dari seluruh proses administrasi KP adalah dokumen cetak yang sah secara hukum/akademik.
- **Fungsi spesifik dalam web ini:** Secara instan mengubah tampilan HTML yang berisi rekapitulasi nilai akhir, tabel *log* bimbingan, dan injeksi *tanda tangan digital* (dari Dosen/Koordinator) menjadi file PDF rapi yang siap diunduh atau dicetak (Berita Acara Sidang, Lembar Persetujuan).

### maatwebsite/excel (Laravel Excel)
- **Kenapa digunakan?** Koordinator KP harus menangani data puluhan/ratusan pengguna baru setiap pergantian tahun ajaran. Menginput data satu per satu dari antarmuka web sangat tidak masuk akal secara efisiensi waktu.
- **Fungsi spesifik dalam web ini:** Menjalankan fitur **Import Data** (membaca file `.xlsx`/`.csv` dari *device* koordinator dan secara otomatis membuatkan akun user) serta fitur **Export Data** (menyimpan rekap data mahasiswa ke bentuk Excel).

### Flysystem (AWS S3 & Google Drive Ext)
- **Kenapa digunakan?** File laporan Kerja Praktek (PDF/Word), bukti revisi, dan berkas persyaratan memiliki ukuran yang cukup besar. Jika disimpan langsung di *local disk* server, kapasitas penyimpanan *server* utama akan cepat penuh.
- **Fungsi spesifik dalam web ini:** Memberikan kemampuan pada sistem untuk secara otomatis "melempar" file yang diunggah mahasiswa ke layanan penyimpanan *Cloud* (seperti Google Drive kampus atau AWS S3 Bucket), menjaga *server* aplikasi tetap ringan.

---

## 4. Arsitektur Infrastruktur & Deployment

Aplikasi ini didesain secara fleksibel (*environment-agnostic*) sehingga bisa di-*deploy* di lingkungan mana pun, didukung oleh dua strategi khusus:

### Deployment Serverless (Vercel)
- **Teknologi: `vercel.json`**
- **Kenapa digunakan?** Vercel aslinya adalah *platform* untuk React/Next.js. Namun, dengan *config* ini, proyek Laravel dapat berjalan di lingkungan *serverless* Vercel (yang sering digunakan mahasiswa untuk *hosting* gratis yang cepat dan aman tanpa perlu memikirkan konfigurasi *server*).
- **Fungsinya:** File konfigurasi ini memaksa *runtime* Vercel untuk membaca PHP (`vercel-php@0.9.0`), menyajikan aset statis secara independen, dan mem-_bypass_ rute API utama ke file `index.php` Laravel.

### Deployment Container (Docker)
- **Teknologi: `Dockerfile` & Nginx**
- **Kenapa digunakan?** Jika pihak kampus memutuskan untuk me-*hosting* sistem ini secara tertutup di *server* lokal (On-Premise) atau VPS berbayar murni, Docker adalah cara teraman untuk memastikan aplikasi berjalan tanpa error versi (*it works on my machine* problem).
- **Fungsinya:** File `Dockerfile` bertindak sebagai resep otomatis untuk membuat "sistem operasi mini" (Container) yang sudah dikonfigurasi sempurna dengan sistem Linux Alpine, Nginx Web Server, PHP 8.4-FPM, dan semua ekstensi yang diperlukan (seperti `zip`, `gd`, `pdo`). Ini menjamin aplikasi langsung hidup dengan satu perintah standar Docker.
