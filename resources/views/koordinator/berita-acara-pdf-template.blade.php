<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Sidang Kerja Praktek</title>
    <style>
        @page {
            margin: 40px 40px 60px 40px; /* top right bottom left */
        }
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 13px; 
            line-height: 1.5; 
            /* Remove body padding since @page margin handles it */
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .underline { text-decoration: underline; }
        .w-full { width: 100%; }
        .text-justify { text-align: justify; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px; vertical-align: top; }
        .w-40 { width: 180px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-4 { margin-top: 16px; }
        .mt-8 { margin-top: 32px; }
        .signature-table { width: 100%; margin-top: 50px; text-align: left; }
        .signature-table td { width: 50%; padding-bottom: 60px; }
        .signature-line { border-bottom: 1px solid black; width: 80%; margin-top: 70px; margin-bottom: 5px; }
        .header-table { width: 100%; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table td { padding: 0; }
        .logo { width: 90px; height: 90px; }
        .header-text h1 { font-size: 17px; margin: 0 0 5px 0; letter-spacing: 1px; }
        .header-text h2 { font-size: 15px; margin: 0 0 5px 0; }
        
        .content-table { margin-left: 20px; width: 90%; }

        /* Footer Section */
        .footer {
            position: fixed;
            bottom: -30px; /* Places it inside the 60px bottom margin */
            left: 0;
            right: 0;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 9pt;
            color: #888;
        }

        .page-number:after {
            content: "Halaman " counter(page) " dari " counter(pages);
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 120px; text-align: center;">
                <img src="{{ public_path('images/logo.png') }}" class="logo">
            </td>
            <td class="text-center header-text" style="padding-right: 80px;">
                <h1 class="uppercase">Universitas Kristen Krida Wacana</h1>
                <h2 class="uppercase">Fakultas Teknologi Cerdas</h2>
                <h2 class="uppercase">Program Studi Informatika</h2>
            </td>
        </tr>
    </table>

    <div class="text-center font-bold underline mb-6" style="font-size: 15px; margin-top: 30px;">BERITA ACARA SIDANG KERJA PRAKTEK</div>

    <div style="padding: 0 10px;">
        <p class="text-justify" style="line-height: 1.8;">
            Pada hari ini _____________, tanggal _____________, pukul _________ WIB, bertempat di ruangan _____________, telah dilaksanakan kegiatan <strong>Sidang Kerja Praktek</strong> Program Studi Informatika, Fakultas Teknologi Cerdas, Universitas Kristen Krida Wacana.
        </p>

        <p style="margin-top: 20px;">Sidang tersebut dilaksanakan terhadap mahasiswa dengan data sebagai berikut:</p>

        <table class="content-table">
            <tr><td class="w-40">Nama</td><td>: ________________________________</td></tr>
            <tr><td>NIM</td><td>: ________________________________</td></tr>
            <tr><td>Judul Kerja Praktek</td><td>: ________________________________</td></tr>
        </table>

        <p style="margin-top: 25px;">Sidang ini dihadiri dan dinilai oleh tim penguji sebagai berikut:</p>

        <table class="content-table">
            <tr><td class="w-40">Dosen Penguji 1</td><td>: ________________________________</td></tr>
            <tr><td>Dosen Penguji 2</td><td>: ________________________________</td></tr>
        </table>

        <p class="text-justify" style="margin-top: 30px;">
            Demikian berita acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <div class="mt-8">
            <p>Mengetahui,</p>
            <table class="signature-table">
                <tr>
                    <td>
                        <p class="font-bold">Dosen Penguji 1</p>
                        <div class="signature-line"></div>
                        <p>NIDK/NIDN : ______________</p>
                    </td>
                    <td>
                        <p class="font-bold">Dosen Penguji 2</p>
                        <div class="signature-line"></div>
                        <p>NIDK/NIDN : ______________</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="font-bold">Koordinator Kerja Praktek</p>
                        <div class="signature-line"></div>
                        <p>NIDK/NIDN : ______________</p>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <table width="100%">
            <tr>
                <td style="text-align: left;">Dokumen ini diterbitkan pada : {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} | Berita Acara Sidang KP</td>
                <td style="text-align: right;"><span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
