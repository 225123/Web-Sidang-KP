<?php

$file = __DIR__ . '/resources/views/koordinator/dashboard.blade.php';
$content = file_get_contents($file);

$partStart = substr($content, 0, strpos($content, '<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 min-h-[302px]">'));
$partEnd = substr($content, strpos($content, '<script>'));

function extractDiv($html, $marker, $endMarker) {
    $start = strpos($html, $marker);
    if ($start === false) return "";
    $end = strpos($html, $endMarker, $start);
    return substr($html, $start, $end - $start + strlen($endMarker));
}

// Chart
$chartStart = '<div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 h-[320px] relative shadow-sm flex flex-col">';
$chartStr = substr($content, strpos($content, $chartStart), strpos($content, '<!-- Timeline Card -->') - strpos($content, $chartStart));
$chartStr = trim($chartStr);

// Progress Sidang
$progSidangStart = '<!-- Progress Chart -->';
$progSidangStr = substr($content, strpos($content, $progSidangStart), strpos($content, '<!-- Notifications (Dynamic) -->') - strpos($content, $progSidangStart));
$progSidangStr = trim($progSidangStr);

// Persetujuan Menunggu
$persetujuanStart = '<!-- Persetujuan Menunggu -->';
$persetujuanStr = substr($content, strpos($content, $persetujuanStart), strpos($content, '</div>
        </div>
    </div>') - strpos($content, $persetujuanStart));
$persetujuanStr = trim($persetujuanStr);
$persetujuanStr = preg_replace('/<\/div>\s*<\/div>\s*<\/div>\s*$/s', '</div>', $persetujuanStr); // remove outer grid endings

// Notifikasi
$notifStart = '<!-- Notifications (Dynamic) -->';
$notifStr = substr($content, strpos($content, $notifStart), strpos($content, '</div>
        </div>

        <!-- NEW ROW 3') - strpos($content, $notifStart));
$notifStr = trim($notifStr);
$notifStr = str_replace('h-full max-h-[302px]', 'h-[252px]', $notifStr);

// Progress Bimbingan
$progBimbStart = '<!-- Progress Bimbingan -->';
$progBimbStr = substr($content, strpos($content, $progBimbStart), strpos($content, '<!-- Persetujuan Menunggu -->') - strpos($content, $progBimbStart));
$progBimbStr = trim($progBimbStr);

// Timeline & Jadwal
$timelineJadwal = <<<HTML
            <!-- Timeline Terdekat (New) -->
            <div class="bg-[#ECECEC] rounded-[10px] p-6 shadow-sm border border-[#D9D9D9] h-[132px] flex flex-col justify-center transition-all hover:shadow-md">
                <h3 class="font-bold text-[#1A1A1A] text-[15px] mb-3 tracking-tight">Timeline Terdekat</h3>
                @if(\$timeline)
                    <p class="font-semibold text-[#1A1A1A] text-[14px]">
                        {{\$timeline->nama_kegiatan}} : <span class="font-normal">{{ \Carbon\Carbon::parse(\$timeline->tanggal)->format('d/m/Y') }}</span>
                    </p>
                @else
                    <p class="text-[13px] text-black/60 italic font-medium">Belum ada agenda terdekat...</p>
                @endif
            </div>
HTML;

$jadwalTerdekat = <<<HTML
            <!-- Jadwal Sidang Terdekat -->
            <div class="bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm flex flex-col h-[302px]">
                <h3 class="font-semibold text-[#1A1A1A] text-[18px] mb-4 font-inter tracking-tight">Jadwal Sidang Terdekat</h3>

                <div class="flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-2">
                    @forelse(\$jadwalTerdekat as \$jadwal)
                    <div class="flex flex-col border-l-4 border-[#3B82F6] pl-3 py-1 bg-gray-50/50 rounded-r-md">
                        <span class="text-[11px] font-bold text-gray-500 mb-0.5 font-inter">
                            {{ \Carbon\Carbon::parse(\$jadwal->tanggal_sidang)->format('d M Y') }} • 
                            {{ \Carbon\Carbon::parse(\$jadwal->waktu_mulai_sidang)->format('H:i') }} - {{ \Carbon\Carbon::parse(\$jadwal->waktu_selesai_sidang)->format('H:i') }}
                        </span>
                        <span class="text-[14px] font-bold text-[#1A1A1A] font-inter">{{ \$jadwal->mahasiswa->user->name }}</span>
                        <div class="flex justify-between items-end mt-1 font-inter">
                            <span class="text-[11px] text-[#666666] font-medium">{{ \$jadwal->mahasiswa->nim }}</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-[#E8F5E9] text-[#1B5E20] rounded border border-[#4CAF50]">
                                {{ \$jadwal->ruang_sidang }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 italic text-[13px]">
                        Belum ada jadwal sidang menguji...
                    </div>
                    @endforelse
                </div>
            </div>
HTML;

$newLayout = <<<HTML
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 min-h-[302px]">
$chartStr
$timelineJadwal
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 min-h-[302px]">
$progSidangStr
$jadwalTerdekat
        </div>

        <!-- ROW 3: Progress Bimbingan & Persetujuan Menunggu -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
$progBimbStr
$persetujuanStr
        </div>

        <!-- ROW 4: Notifikasi di paling bawah kiri -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
$notifStr
        </div>
    </div>

HTML;

$finalContent = $partStart . ltrim($newLayout) . "    " . $partEnd;
file_put_contents($file, $finalContent);

echo "Dashboard rewritten to row layout successfully!";
