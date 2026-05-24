# Panduan Penggunaan Halaman Mahasiswa (Ekstra Detail)

Dokumen ini berisi penjelasan sangat rinci mengenai setiap klik, *pop-up*, dan antarmuka *input* yang ada di dalam portal Mahasiswa, sesuai dengan fitur dan file *blade* yang ada di sistem.

---

## 1. Dashboard (`dashboard.blade.php`)
Halaman ini adalah pusat informasi utama saat mahasiswa pertama kali *login*. Terdapat kotak pemilih **Periode Kerja Praktik** di bagian atas (berupa kotak abu-abu dengan panah *dropdown*). Di bawahnya terdapat panel "Status Kerja Praktik" yang memvisualisasikan *progress* bar dengan indikator persentase bulat yang terisi otomatis seiring perjalanan KP. Di sebelahnya ada panel "Sidang Kerja Praktik" yang akan kosong jika mahasiswa belum dijadwalkan, atau berisi detail jika sudah. Di sisi kanan layar, terdapat riwayat *timeline* notifikasi.
**Aksi dan Letak Tombol:**
- **Tombol Navigasi Tengah:** Terdapat tombol raksasa bertuliskan **"Mendaftar Projek"** atau **"Mendaftar Sidang"**. Tombol ini akan otomatis mengunci diri dan berubah menjadi teks abu-abu **"Sudah Mendaftar"** setelah mahasiswa menyelesaikannya.
- **Mode Pelihat:** Di sudut kanan atas, terdapat tombol **"Mode Pelihat"** (atau ikon panah). Mengkliknya akan menyembunyikan *sidebar* menu di sebelah kiri untuk melebarkan layar.
- **Membuka Pesan:** Teks judul notifikasi di sebelah kanan dapat diklik. Saat diklik, layar akan meredup dan memunculkan *pop-up modal* melayang di tengah layar yang memuat pesan utuhnya.

---

## 2. Pendaftaran KP (`Pendaftaran-KP.blade.php`)
Halaman ini adalah formulir yang sangat dinamis. Di baris pertama, mahasiswa dihadapkan dengan isian judul KP. Selanjutnya ada kotak **Jenis KP** ("Internal" / "External") dan kotak **Pengerjaan KP** ("Individu" / "Kelompok"). Jika diklik, kotak ini bukan *dropdown* HTML biasa, melainkan *pop-up* kecil yang bila opsi dipilih akan tersorot warna kuning. Jika memilih "Kelompok", akan muncul kotak *input* baru bernama **"Anggota Kelompok"** yang dapat diketik NIM/Namanya. Saat anggota dipilih dari daftar yang muncul, nama mereka berubah menjadi *tag* biru. Jika teman tersebut sudah mendaftar KP, namanya di-abu-abukan dan dicap label merah **"Sedang KP"**. Di bagian bawah terdapat kolom **"Supervisior"** yang bisa diketik. Jika yang diketik adalah dosen internal, opsi dosen akan muncul di *dropdown*. Namun jika mahasiswa mengetik nama orang luar (External) yang tidak ada di daftar, *form* secara otomatis akan melahirkan kotak *input* baru yang meminta **"Email Supervisior Eksternal"** untuk diisi.
**Aksi dan Letak Tombol:**
- **Menghapus Anggota:** Pada *tag* biru anggota kelompok yang sudah dipilih, terdapat tombol kecil ikon **"✕"** untuk menghapusnya dari tim.
- **Menyorot Dropdown:** Mengklik panah bawah pada kotak "Jenis KP" atau "Supervisior" akan menggelar menu yang bisa di- *scroll*.
- **Submit:** Di ujung bawah *form*, terdapat tombol hijau bertuliskan **"SUBMIT"** (ikon roket menyamping). Saat diklik, sistem akan memvalidasi *form*. Jika berhasil, layar formulir akan hilang digantikan dengan halaman konfirmasi berlogo centang hijau besar di tengah layar.

---

## 3. Status Pendaftaran (`Status-Pendaftaran.blade.php`)
Halaman ini adalah tabel riwayat pengajuan. Tabel ini menampilkan kolom `No`, `Proyek & Instansi`, `Jenis KP`, dan `Status Approval` (ditandai dengan warna-warni *badge*, seperti hijau untuk "Disetujui" dan kuning berkedip untuk "Menunggu"). Jika ada catatan penolakan dari Koordinator, alasan penolakannya akan muncul di bawah judul proyek mahasiswa.
**Aksi dan Letak Tombol:**
- **Filter Status:** Terdapat deretan tombol *tab* di atas tabel bertuliskan **"Semua Status"**, **"Menunggu"**, **"Disetujui"**, dan **"Ditolak"**. Mengklik salah satunya akan menyembunyikan data tabel yang tidak sesuai dengan kategori tersebut.
- **Aksi Lanjutan:** Pada baris yang ditolak, di ujung kanan tabel akan muncul tombol interaktif. Jika berkasnya hanya salah unggah, tombol berbunyi **"Lengkapi Sekarang"**. Namun jika proyeknya ditolak total oleh Koordinator, tombol yang muncul berbunyi **"Mendaftar KP Baru"** untuk mereset pendaftaran mahasiswa.

---

## 4. Bimbingan Dosen (Logbook) (`bimbingan-dosen.blade.php`)
Halaman ini menyimpan seluruh riwayat *logbook*. Di dalamnya terdapat tabel dengan kolom `Tanggal`, `Waktu & Tempat` (dibagi baris jam dan nama tempat), `Detail Pembahasan` (dibagi baris topik kapital dan deskripsi panjang), `Bukti` (berisi pratinjau gambar/thumbnail), dan `Status`. Kolom gambar bukti dirancang interaktif. Jika tidak ada gambar, ia menampilkan kotak "*No Img*".
**Aksi dan Letak Tombol:**
- **Membesarkan Gambar (Zoom):** Pada kolom `Bukti`, gambar *thumbnail* kecil akan menyala (*hover*) jika kursor diarahkan. Apabila **diklik**, gambar tersebut akan mengambang menutupi seluruh layar (*Zoom/Pop-up Image Viewer*). Klik ikon **"✕"** di sudut kanan atas atau klik area luar gambar untuk menutupnya.
- **Form Tambah (Modal):** Klik tombol kuning **"Tambah Bimbingan"** di atas tabel. Sebuah *modal pop-up* berlatar putih akan turun dari atas layar. Di sini mahasiswa wajib mengisi *form* kalender (`Tanggal`), *input* angka (`Waktu Mulai - Selesai`), dan kotak teks (`Tempat`, `Topik`, dan `Detail Isi` maksimal 500 kata).
- **Interaksi Upload File:** Di dalam modal yang sama, klik tombol putih **"Pilih Bukti"**. *Jendela folder* komputer akan terbuka. Setelah gambar JPG/PNG dipilih, gambarnya akan muncul menggantikan area abu-abu garis putus-putus. Jika kursor digeser ke atas gambar tersebut, sebuah ikon **"✕" (Silang Merah)** muncul melayang untuk menghapus gambar tersebut tanpa harus mengulang form. Terakhir, klik **"Submit Logbook"**.
- **Menghapus Baris Tabel:** Baris riwayat bimbingan yang bersatus kuning ("Menunggu") dapat dihapus secara manual dengan mengklik teks ikon silang/hapus di area kanan baris tersebut.
- **Cetak Laporan:** Tombol merah **"Export PDF"** di kiri atas untuk melahirkan dokumen unduhan secara otomatis.

---

## 5. Persetujuan Sidang KP (`persetujuan-sidang-kp.blade.php`)
Halaman ini adalah tahapan untuk meminta *Acc* Dosen Pembimbing sebelum bisa mendaftar sidang. Terdapat sistem formulir *upload* dokumen Laporan KP (PDF) dengan opsi alternatif. Mahasiswa dapat memilih untuk menekan tombol **"Pilih file (Max 5MB)"** untuk mengunggah PDF, atau mengetik URL di kotak bertuliskan **"Link GDrive jika > 5MB"**. Keduanya saling bertautan (*dynamic forms*); jika kotak URL mulai diketik, tombol *upload* file otomatis tersembunyi, begitupun sebaliknya. 
**Aksi dan Letak Tombol:**
- **Mekanisme Upload & Hapus:** Klik tombol abu-abu **"Pilih file"**. Jika berhasil, nama *file* akan muncul dan di sebelahnya terdapat ikon **"✕"** abu-abu-merah. Mengkliknya akan mereset form.
- **Mengajukan Berkas:** Setelah dilampirkan, tekan tombol besar **"AJUKAN"**.
- **Status Transisi & Batalkan:** Seketika tombol akan berubah menjadi *badge* abu-abu status **"Telah Mengajukan"**. Jika mahasiswa merasa salah unggah sebelum dikoreksi dosen, terdapat tombol kecil ikon silang merah (**"Batalkan Pengajuan"**) di samping tautan *file*. Mengkliknya akan memunculkan *alert* konfirmasi untuk menghapus data.
- **Sertifikat Lulus:** Jika disetujui, layar berubah sepenuhnya. Formulir unggahan hilang dan digantikan oleh *Preview* dokumen (ditampilkan dalam kotak *Iframe Viewer* interaktif). Di bawahnya terdapat tombol abu-abu tebal **"Unduh File"** untuk mengambil Surat Persetujuan Sidang versi PDF berstempel/TTD.

---

## 6. Pendaftaran Sidang (`pendaftaran-sidang.blade.php`)
Halaman ini adalah form unggahan berlapis. Mahasiswa akan menjumpai beberapa kotak *upload* yang terpisah untuk **"Laporan KP"**, **"Laporan Bimbingan KP"**, dan **"Berkas Lainnya"**. Jika *file* terlalu besar, mahasiswa dapat memasukkannya di kotak teks panjang berlabel **"Link Google Drive"**. Di bawahnya terdapat dua *form* kotak teks wajib (*required*) untuk mengisi tautan portofolio, yaitu **"Link Project (Github)"** dan **"Link Deploy / Publish Project"**.
**Aksi dan Letak Tombol:**
- **Mekanisme Upload & Hapus:** Klik tombol biru bertuliskan **"Pilih File"** (*Choose File*) di masing-masing kotak "Laporan KP" dkk. Jika file yang dipilih lebih besar dari 5MB, sebuah peringatan (*alert*) merah akan meletup. Jika berhasil diunggah, nama berkas akan menggantikan teks biru tadi, lalu di sebelahnya seketika muncul tombol kecil ikon **"✕"** abu-abu-merah. Mengkliknya akan membuang berkas tersebut dari antrean *upload*.
- **Submit Berkas:** Tekan tombol hijau **"SUBMIT BERKAS"** di bawah.
- **Tampilan Validasi Koordinator:** Jika *form* mahasiswa ditolak oleh pihak kampus, akan muncul kotak merah muda besar di bagian atas bertuliskan teguran, contohnya: *"Alasan Penolakan: Laporan kurang bab 5"*. Jika disetujui penuh, form unggahan akan hilang dan digantikan oleh tombol jalan pintas berwarna kuning cerah bertuliskan **"Menuju Jadwal Sidang"**.

---

## 7. Jadwal Sidang (`jadwal-sidang.blade.php`)
Halaman ini adalah panel pajangan yang memuat detail waktu ujian. Di bagian atas, terdapat desain kalender ikonik melingkar (*Date Circle*) berwarna kuning tebal yang menampilkan hari dan tanggal. Di bawahnya, sebuah tabel informasi sederhana memuat rincian `Tanggal Sidang`, `Ruangan` (bisa berupa teks kelas fisik atau tautan *Zoom/Meet*), `Waktu Sidang`, `Dosen Penguji 1 & 2`, serta `Status`.
**Aksi dan Letak Tombol:**
- **Tombol Sinkronisasi Kalender:** Di sudut kanan bawah kotak jadwal, terdapat sebuah tombol abu-abu berikon amplop surat bertuliskan **"Kirim Kalender via Email"**.
- **Animasi Proses:** Saat diklik, tombol tersebut akan bereaksi dengan memutar ikon *loading spinner* ("Mengirim...") saat sistem sedang menghubungi *server*. Jika sukses terkirim ke email, tombol tersebut seketika berubah warna menjadi hijau terang berikon centang dengan teks **"Email Terkirim"**. Sistem otomatis menyimpan rekam jejak ini, sehingga tombol tersebut akan terkunci (*disabled*) apabila dikunjungi kembali, mencegah mahasiswa melakukan *spam* pengiriman email.

---

## 8. Hasil Sidang (`hasil-sidang.blade.php`)
Halaman ini menampilkan rekapitulasi performa sidang mahasiswa, dan memiliki dua kondisi utama:
1. **Kondisi Kosong (Belum Dinilai):** Jika penguji belum memasukkan nilai sama sekali, halaman hanya menampilkan kotak putih besar dengan ikon lingkaran kuning bertuliskan *"Hasil Sidang Belum Tersedia"*.
2. **Kondisi Terisi (Sudah Dinilai):** Halaman akan membelah diri menjadi beberapa bagian:
   - **Tabel Nilai:** Layar terbagi menjadi dua kolom ("Dosen Penguji 1" dan "Dosen Penguji 2"), di mana masing-masing merincikan skor `Laporan`, `Produk`, dan `Presentasi`.
   - **Catatan Sidang:** Terdapat kotak khusus yang memuat teks masukan atau kritik dari penguji.
   - **Hasil Akhir:** Tertera Nilai Akhir angka/huruf dan Status Lulus (misal: "Lanjut" atau "Lulus dengan Revisi").
**Aksi dan Letak Tombol:**
- **Jalan Pintas Revisi:** Jika mahasiswa mendapat status *"Lulus dengan Revisi"*, tepat di sebelah kanan status kelulusan akan muncul tombol biru tebal berikon panah **"Lanjut ke Halaman Revisi"**. Mengkliknya akan mengantarkan mahasiswa berpindah ke menu Revisi tanpa perlu mengakses *sidebar*.

---

## 9. Revisi (`revisi.blade.php`)
Halaman pengumpulan perbaikan ini bersifat sangat dinamis dan akan kosong (menampilkan teks *"Tidak Ada Revisi Aktif"*) jika mahasiswa tidak bersatus "Lulus Dengan Revisi". Jika wajib revisi, halaman ini memuat ringkasan profil, catatan revisi (sebagai pengingat), dan form unggahan.
**Aksi, Status, dan Letak Tombol:**
- **Mekanisme Upload File & Link:** Tersedia kotak **"Pilih file (Max 5MB)"** untuk mengunggah PDF yang beroperasi saling bertautan (*toggle*) dengan kotak input **"Link GDrive"**. Terdapat pula teks merah peringatan batas akhir revisi (5 hari setelah sidang).
- **Hapus Berkas:** Jika sudah mengunggah, ikon tautan akan muncul. Di sebelahnya terdapat tombol ikon silang **"✕"**. Jika diklik, sistem memicu *alert popup* konfirmasi *"Hapus Berkas"*. Tombol ini akan menghilang jika revisi sudah disahkan.
- **Tombol Submit (Validasi Aktif):** Jika mahasiswa bersatus aktif, tombol hijau **"SUBMIT REVISI"** dapat diklik. Namun jika ia menonaktifkan diri (Mode Pelihat), tombol terganti dengan peringatan merah *"Status Anda Tidak Aktif"*.
- **Kotak Status Pemeriksaan:** Setelah disubmit, tombol submit hilang dan digantikan oleh kotak status raksasa di tengah layar.
  - **Sedang Diperiksa:** Kotak berwarna **Kuning** berikon jam pasir jika status *"Menunggu"*.
  - **Revisi Disahkan:** Kotak berubah **Hijau** berikon centang besar jika status *"Disahkan/Diterima"*.

---

## 10. Nilai Akhir (`nilai-akhir.blade.php`)
Halaman rekapitulasi kelulusan akhir. Halaman ini juga memiliki pengkondisian: jika Koordinator belum memublikasikan nilai, halaman akan terkunci menampilkan ikon kuning *"Nilai Belum Dipublikasi"*.
**Kondisi Terpublikasi:**
- **Rincian Komponen:** Layar memaparkan pembagian porsi secara mendetail. Sebelah Kiri untuk Dosen Pembimbing (40%) & Supervisor Instansi (10%), dan sebelah Kanan untuk Dosen Penguji 1 & 2 (50%).
- **Peringatan Pinalti (Dinamis):** Jika mahasiswa terlambat merevisi dan mendapatkan sanksi penurunan grade, tepat di bawah teks "Nilai Akhir" akan muncul sisipan teks merah miring: *"*** Grade diturunkan karena revisi belum lengkap"*.
**Aksi dan Letak Tombol:**
- **Unduh Transkrip:** Klik tombol merah **"Download Nilai"** di pojok kanan bawah untuk mengunduh rekap skor ke PDF.
- **Unduh Berita Acara (Kondisional):** Terdapat tombol **"Download Berita Acara"**. Tombol ini bersensor cerdas; ia akan berwarna biru dan bisa diklik HANYA JIKA status pelaksanaan sidang *"Selesai"*. Jika belum selesai tuntas, tombol itu berubah menjadi kotak abu-abu kusam, terkunci (*disabled cursor*), dan teksnya berganti menjadi *"Berita Acara Belum Tersedia"*.

---

## 11. Notifikasi (`notifikasi.blade.php` & `notifikasi-detail.blade.php`)
Halaman ini memuat kotak masuk (*inbox*) riwayat pemberitahuan. Di bagian atas terdapat kotak merah bertuliskan **"Belum Dibaca"** berisi angka notifikasi aktif. Di bawahnya terdapat fitur tabel yang dilengkapi sistem navigasi halaman (*Pagination*) interaktif.
**Aksi dan Letak Tombol:**
- **Mencari & Menyaring Pesan:** Tersedia kolom pencarian *"Cari pengirim atau pesan..."* yang jika diketik akan langsung menyaring baris secara *real-time*. Di sebelahnya ada tombol *dropdown* **"Semua Status"** (untuk memfilter Terbaca/Belum Dibaca) dan **"Terbaru/Terlama"** untuk mengurutkan pesan. Di ujung baris ada tombol merah **"Clear Filter"** untuk mereset pencarian.
- **Membaca Pesan:** Saat mengklik salah satu baris pesan, sistem **tidak** memunculkan *pop-up*, melainkan melempar mahasiswa ke halaman baru (`notifikasi-detail.blade.php`) yang dirancang menyerupai antarmuka *email* Gmail.
- **Tampilan Detail Email:** Di halaman detail, tampak informasi avatar inisial pengirim, judul, dan teks panjang pesan. Jika peringatan berasal dari sistem yang memerlukan tindakan, akan muncul tombol biru besar **"Lihat Detail Terkait"** yang otomatis menderek mahasiswa ke halaman yang bermasalah.
- **Lampiran Interaktif (File Attachment):** Jika pesan tersebut melampirkan *file*, akan muncul kartu kotak lampiran ala Gmail di bagian bawah. Jika berupa gambar, kotak memunculkan pratinjau gambarnya. Saat kursor disorot (*hover*) ke kotak lampiran tersebut, layar akan meredup gelap dan memunculkan dua ikon membesar: ikon **Mata** (untuk melihat gambar di tab baru) dan ikon **Unduh** (untuk men-download berkas).

---

## 12. Panduan (`panduan.blade.php`)
Pusat dokumentasi aturan dan panduan. Halaman ini bukan sekadar teks gulir (*scroll*), melainkan dibangun dengan kerangka *Tab Alpine.js* yang membuat transisinya instan tanpa *loading* halaman. Terdapat 3 kategori utama (*Tabs*) di bagian atas:
1. **Tab Fungsi Navigasi & Menu:** Menampilkan deretan kartu kotak-kotak (*grid cards*) yang menjelaskan fungsi setiap menu (Mendaftar KP, Jadwal Sidang, dsb). Di sisi kanannya, terdapat peringatan wajib mengenai keharusan menggambar **Tanda Tangan Digital** di menu Profil.
2. **Tab Peraturan & Pinalti:** Memuat diagram alir (*Timeline*) vertikal 5 fase prosedur KP (dari Pra-Pendaftaran hingga Revisi Sidang). Di bagian bawahnya, terdapat dua kotak merah teguran keras (*Warning Boxes*) mengenai aturan:
   - **Tenggat Waktu Revisi:** Menjelaskan pinalti penurunan 1 tingkat grade jika terlambat (>5 hari).
   - **Minimal Bimbingan:** Menjelaskan sanksi pelarangan mendaftar sidang jika bimbingan kurang dari 12 kali.
3. **Tab Bantuan & FAQ:** Memuat rentetan pertanyaan bertumpuk (*Accordion*). 
   - **Membentangkan Jawaban:** Jika mahasiswa mengklik baris teks pertanyaannya, sistem akan merespons dengan memutar ikon panah ke bawah (rotasi 180 derajat) lalu secara halus menurunkan panel kuning-jingga di bawahnya yang memuat isi jawabannya. Mengklik pertanyaan yang sama akan menggulung panel itu kembali.
