# Panduan Arsitektur & Teknologi (Sistem Sidang KP)

Dokumen ini membedah keputusan *Tech Stack* yang dipilih secara spesifik untuk membangun **Sistem Informasi Sidang KP**, beserta *reasoning* dan *use case* implementasinya dalam konteks *source code* proyek ini.

---

## 1. Backend & Framework Inti

### Laravel 12 (PHP 8.2+)
; Ini dibutuhkan karena proyek ini memiliki alur logika akademik yang rumit dan tumpang tindih (Pendaftaran KP -> Validasi Koordinator -> Proses Bimbingan -> Penjadwalan -> Eksekusi Sidang -> Penilaian Multi-aktor). Ekosistem MVC (Model-View-Controller) dan ORM (Eloquent) sangat matang untuk menangani relasi database kompleks tanpa memicu *spaghetti code*.
; Implementasi pada sistem: Bertindak sebagai *core engine* yang mengatur *routing*, memproses logika kalkulasi *business rules* (seperti penghitungan persentase Nilai Akhir), mengorkestrasi interaksi database, dan me-*render* komponen Blade.

### Spatie Permission
; Ini dibutuhkan karena sistem ini diakses oleh 3 aktor dengan *privilege* dan batasan otoritas yang saling bertolak belakang: Koordinator KP, Dosen (sebagai pembimbing/penguji), dan Mahasiswa. Melakukan *hardcode* logika *Role-Based Access Control* (RBAC) secara manual sangat berisiko memunculkan celah keamanan (*Broken Access Control*).
; Implementasi pada sistem: Digunakan sebagai *middleware* untuk memproteksi *route* dan *endpoints* API. Memastikan secara sistem bahwa mahasiswa tidak bisa menembus halaman penjadwalan koordinator, dan *query scope* memastikan dosen hanya bisa memasukkan nilai untuk mahasiswa yang memiliki relasi ID sah dengannya.

### Spatie Activitylog
; Ini dibutuhkan karena dalam sistem akademik (yang memanipulasi nilai krusial mahasiswa), transparansi *audit trail* (*forensic log*) adalah hal wajib untuk akuntabilitas integritas data. Jika ada perubahan status kelulusan atau revisi jadwal secara misterius, sistem harus memiliki *state history*.
; Implementasi pada sistem: Diinjeksi ke dalam model Eloquent. Berjalan secara *asynchronous* di latar belakang untuk mencatat setiap eksekusi *Update/Delete* pada *table* krusial (seperti tabel nilai atau pendaftaran sidang), lengkap dengan menangkap data *old_values* dan *new_values*.

---

## 2. Frontend & Antarmuka (TALL Stack)

### Tailwind CSS (v3.4)
; Ini dibutuhkan karena proyek ini memerlukan *dashboard* dengan UI/UX modern yang harus **100% responsif di *mobile device*** (mengingat kebiasaan *user* dosen/mahasiswa yang sering memantau status via iPhone/Android). Pendekatan *Utility-first* mempercepat iterasi UI tanpa risiko CSS *specificity clashes* (konflik desain).
; Implementasi pada sistem: Mengatur arsitektur *layout*, *grid/flexbox*, dan *media queries* secara langsung (*inline*) di dalam komponen `.blade.php`. Sangat terasa efeknya ketika mengubah tata letak form nilai yang otomatis menjadi vertikal jika ukuran layar (*viewport*) mengecil.

### Alpine.js (v3.4)
; Ini dibutuhkan karena aplikasi ini dirender secara *server-side* (*monolith* klasik), bukan *Single Page Application* seperti React/Vue. Namun, *modern web app* tetap membutuhkan reaktivitas DOM murni (seperti *dropdown*, *modal popup* peringatan, atau transisi *tab*) tanpa perlu me-*load library* lawas yang berat seperti jQuery.
; Implementasi pada sistem: Ditulis via direktif `x-data` dan `x-on` langsung di struktur HTML untuk memanipulasi *DOM state client-side* secara deklaratif pada elemen interaktif.

### Vite
; Ini dibutuhkan karena standar *bundling* industri modern telah bergeser dari Webpack (Laravel Mix) ke Vite yang memanfaatkan ES Modules bawaan *browser*. Kecepatan kompilasinya (HMR - *Hot Module Replacement*) secara instan sangat krusial saat masa *development*.
; Implementasi pada sistem: Dikonfigurasi untuk melakukan *bundling* (*tree-shaking*) dan *minification* aset Tailwind CSS dan JavaScript. Menghasilkan *build* statis berukuran minimal yang disajikan saat aplikasi mode *production*.

---

## 3. Ekstensi Fungsional Spesifik

### barryvdh/laravel-dompdf
; Ini dibutuhkan karena *output* final dari administrasi akademik kampus mewajibkan ketersediaan *hardcopy* dokumen cetak (PDF) yang legal, rapi, dan sesuai dengan tata letak (*layout*) birokrasi, seperti dokumen Berita Acara Sidang.
; Implementasi pada sistem: Secara dinamis meng-*compile* sintaks HTML/CSS (yang telah di-inject dengan data nilai mahasiswa dan *Base64 image* tanda tangan digital) lalu me-*render*-nya *on-the-fly* menjadi *stream output file* berformat `.pdf` untuk diunduh *user*.

### maatwebsite/excel (Laravel Excel)
; Ini dibutuhkan karena *data entry* manual untuk ratusan entitas (pembuatan akun mahasiswa baru atau dosen) setiap pergantian semester sangat rentan terhadap *human error* dan tidak *scalable*.
; Implementasi pada sistem: Mem-*parsing file stream* berformat `.xlsx`/`.csv` yang diunggah koordinator menjadi struktur *array/collection* untuk kemudian di-*batch insert* ke tabel database (fitur Import Users). Juga digunakan untuk *compile query* database menjadi laporan *spreadsheet* bagi koordinator.

### Flysystem (AWS S3 & Google Drive Ext)
; Ini dibutuhkan karena beban *storage* dari *file upload* (Draft Laporan KP mahasiswa yang berukuran puluhan MB, bukti revisi, dsb) akan mematikan server utama jika dipaksakan ditampung di *local disk*, terutama di infrastruktur *serverless* yang *ephemeral* (tidak permanen).
; Implementasi pada sistem: Di-*set* pada `config/filesystems.php`. Semua *stream file* yang di-submit dari form *upload* otomatis di-*pipe* (dilempar) melalui API ke layanan *Cloud Storage* eksternal, sehingga server aplikasi tetap sangat ringan dan hanya menyimpan URL Path-nya saja di database.

---

## 4. Arsitektur Infrastruktur & Deployment

Aplikasi ini didesain secara *environment-agnostic* (tidak terikat pada infrastruktur fisik tertentu), dengan dukungan dua *deployment strategies*:

### Serverless Runtime (Vercel)
; Ini dibutuhkan karena Vercel menyediakan infrastruktur *edge network* yang *auto-scaling*, yang sangat ideal untuk proyek kampus dengan tingkat *traffic* yang fluktuatif (hanya ramai/sibuk saat minggu pendaftaran sidang).
; Implementasi pada sistem: Memanfaatkan manifest `vercel.json` untuk memaksa infrastruktur Vercel menggunakan *custom buildpack* PHP (`vercel-php@0.9.0`). Menerapkan *routing override* agar Vercel me-*serve asset* statis secara independen, lalu mem-*proxy* semua *request dynamic* Laravel ke *entrypoint* `api/index.php`.

### Containerization (Docker)
; Ini dibutuhkan karena *environment consistency*. Jika pihak kampus suatu saat memutuskan untuk melakukan *host* sistem ini secara tertutup di mesin VPS/On-Premise milik kampus, Docker mengeliminasi masalah "*it works on my machine*" dengan membungkus aplikasi dan *environment*-nya sendiri.
; Implementasi pada sistem: Menggunakan `Dockerfile` berbasis Alpine Linux untuk mem-*build image* berukuran sangat efisien. *Image* ini sudah merakit Nginx (sebagai *Web Server/Reverse Proxy*), PHP 8.4-FPM (sebagai *application processor*), beserta semua *extension binary* wajib (`gd`, `pdo`), memastikan aplikasi langsung *up-and-running* di OS server mana pun.
