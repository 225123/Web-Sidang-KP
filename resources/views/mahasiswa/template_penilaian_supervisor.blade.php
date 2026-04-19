<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Template Surat Penilaian Supervisor</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 11pt; }

        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 30px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .label-col { width: 150px; }
        .sep-col { width: 20px; }

        .intro-text {
            text-align: justify;
            margin-bottom: 20px;
        }

        /* Styling Penilaian */
        .assessment-section {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .assessment-section td {
            padding: 12px 5px;
            border-bottom: 1px dashed #ccc;
        }
        .criteria {
            font-weight: bold;
            width: 40%;
        }
        .score-box {
            font-style: italic;
            color: #333;
        }

        /* Footer / Tanda Tangan */
        .footer-container {
            margin-top: 50px;
            float: right;
            width: 250px;
            text-align: left;
        }
        .signature-space {
            height: 80px;
        }
        .supervisor-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 0;
        }

        /* Clearfix for float */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Lembar Penilaian Instansi</h2>
        <p>Kerja Praktik Mahasiswa Informatika</p>
    </div>

    <div class="title">SURAT PENILAIAN SUPERVISOR</div>

    <table class="info-table">
        <tr>
            <td class="label-col">Nama Mahasiswa</td>
            <td class="sep-col">:</td>
            <td><strong>{{ $data['nama_mahasiswa'] }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">NIM</td>
            <td class="sep-col">:</td>
            <td>{{ $data['nim'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Nama Proyek</td>
            <td class="sep-col">:</td>
            <td>{{ $data['nama_projek'] }}</td>
        </tr>
        <tr>
            <td class="label-col">Instansi/Perusahaan</td>
            <td class="sep-col">:</td>
            <td>{{ $data['nama_instansi'] }}</td>
        </tr>
    </table>

    <div class="intro-text">
        Berdasarkan hasil pengamatan dan evaluasi kinerja mahasiswa selama melaksanakan Kerja Praktik (KP), bersama ini supervisor memberikan penilaian dengan rincian sebagai berikut:
    </div>

    <table class="assessment-section">
        <tr>
            <td class="criteria">Kemampuan dan Motivasi Kerja</td>
            <td class="sep-col">:</td>
            <td class="score-box">.................................................</td>
        </tr>
        <tr>
            <td class="criteria">Kualitas Kerja</td>
            <td class="sep-col">:</td>
            <td class="score-box">.................................................</td>
        </tr>
        <tr>
            <td class="criteria">Inisiatif dan Kreatifitas</td>
            <td class="sep-col">:</td>
            <td class="score-box">.................................................</td>
        </tr>
        <tr>
            <td class="criteria">Sikap dan Kedisiplinan</td>
            <td class="sep-col">:</td>
            <td class="score-box">.................................................</td>
        </tr>
    </table>

    <div class="intro-text">
        Demikian penilaian ini diberikan secara objektif untuk dapat dipergunakan sebagaimana mestinya dalam proses akademik.
    </div>

    <div class="clearfix">
        <div class="footer-container">
            <div>........................, ........................</div>
            <div style="margin-bottom: 5px;">Supervisor,</div>
            
            <div class="signature-space">
                </div>

            <div class="supervisor-name">......................................................</div>
            <div>Jabatan: .....................................................</div>
        </div>
    </div>

</body>
</html>
