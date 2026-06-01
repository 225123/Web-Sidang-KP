<?php

$file = __DIR__ . '/resources/views/koordinator/dashboard.blade.php';
$content = file_get_contents($file);

// 1. Rename "Progress Bimbingan" to "Progress Bimbingan Saya"
$content = str_replace(' Progress Bimbingan</h3>', ' Progress Bimbingan Saya</h3>', $content);

// 2. We need to replace the grid rows starting from Row 3.
$marker = '<!-- NEW ROW 3: Progress Bimbingan (col-1) & Persetujuan Menunggu (col-2) -->';
$pos = strpos($content, $marker);
if ($pos === false) {
    echo "Marker not found!";
    exit;
}

$partStart = substr($content, 0, $pos);
$partEnd = substr($content, strpos($content, '<script>'));

// Extract Progress Bimbingan Saya
$progBimbStart = '<!-- Progress Bimbingan -->';
$progBimbEndMarker = '<!-- Persetujuan Menunggu -->';
$progBimbPosStart = strpos($content, $progBimbStart);
$progBimbPosEnd = strpos($content, $progBimbEndMarker);
$progBimbStr = substr($content, $progBimbPosStart, $progBimbPosEnd - $progBimbPosStart);
$progBimbStr = trim($progBimbStr);

// Extract Persetujuan Menunggu
$persetujuanStart = '<!-- Persetujuan Menunggu -->';
$persetujuanEndMarker = '<!-- NEW ROW 4: Notifikasi (paling bawah kiri) -->';
$persetujuanPosStart = strpos($content, $persetujuanStart);
$persetujuanPosEnd = strpos($content, $persetujuanEndMarker);
$persetujuanStr = substr($content, $persetujuanPosStart, $persetujuanPosEnd - $persetujuanPosStart);
$persetujuanStr = trim($persetujuanStr);
// Strip outer div closures safely
$persetujuanStr = preg_replace('/<\/div>\s*<\/div>\s*$/', '', $persetujuanStr); 
$persetujuanStr .= "\n            </div>";

// Extract Notifikasi
$notifStart = '<!-- Notifications (Dynamic) -->';
$notifPosStart = strpos($content, $notifStart);
$notifPosEnd = strpos($content, '<script>');
$notifStr = substr($content, $notifPosStart, $notifPosEnd - $notifPosStart);
// Strip the ending divs
$notifStr = preg_replace('/<\/div>\s*<\/div>\s*<\/div>\s*$/s', '</div>', $notifStr);
$notifStr = trim($notifStr);
$notifStr = str_replace('lg:col-span-3 w-full', 'lg:col-span-1 w-full', $notifStr);

// Progress Bimbingan Umum Card
$progUmumStr = <<<HTML
            <!-- Progress Bimbingan Umum -->
            <div class="lg:col-span-2 bg-[#FEFEFF] rounded-[10px] border border-[#D9D9D9] p-6 shadow-sm overflow-hidden flex flex-col min-h-[302px]" id="analyticsSection">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="font-semibold text-[#1A1A1A] text-[18px] font-inter tracking-tight"> Progress Bimbingan Umum</h3>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-12 flex-1 w-full max-w-2xl mx-auto">
                    <!-- Left: Status Stats -->
                    <div class="flex-1 w-full space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[8px] border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                <span class="text-[13px] font-medium text-gray-600">Belum Mulai</span>
                            </div>
                            <span class="text-[16px] font-black text-gray-700">{{ \$countBelumUmum }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-[8px] border border-blue-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                <span class="text-[13px] font-medium text-blue-700">Dalam Proses (1-11)</span>
                            </div>
                            <span class="text-[16px] font-black text-blue-800">{{ \$countDimulaiUmum }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-[8px] border border-green-100 shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-[13px] font-medium text-green-700">Memenuhi (12)</span>
                            </div>
                            <span class="text-[16px] font-black text-green-800">{{ \$countMemenuhiUmum }}</span>
                        </div>
                    </div>

                    <!-- Right: Circular Progress -->
                    <div class="shrink-0 flex flex-col items-center">
                        <div class="relative w-36 h-36">
                            <canvas id="overallProgressChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-[28px] font-black text-black leading-none">{{ \$displayPercentUmum }}%</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase mt-1">Total Progres</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
HTML;

$chartJsScript = <<<HTML
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initChartUmum() {
            const chartCanvas = document.getElementById('overallProgressChart');
            if (!chartCanvas) return;

            const ctx = chartCanvas.getContext('2d');
            const overallPercent = {{ \$overallPercentUmum }};

            const config = {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [overallPercent, 100 - overallPercent],
                        backgroundColor: ['#2563eb', '#f1f5f9'],
                        borderWidth: 0,
                        hoverOffset: 0,
                        cutout: '85%',
                        borderRadius: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    },
                    animation: {
                        duration: 2500,
                        easing: 'easeOutQuart'
                    }
                }
            };

            if (window.umumChartInstance) {
                window.umumChartInstance.destroy();
            }
            window.umumChartInstance = new Chart(ctx, config);
        }

        document.addEventListener('DOMContentLoaded', initChartUmum);
        document.addEventListener('turbo:load', initChartUmum);
    </script>
HTML;

$newLayout = <<<HTML
        <!-- NEW ROW 3: Progress Bimbingan Saya (col-1) & Progress Bimbingan Umum (col-2) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
$progBimbStr
$progUmumStr
        </div>

        <!-- NEW ROW 4: Persetujuan Menunggu (col-2) & Notifikasi (col-1) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
$persetujuanStr
$notifStr
        </div>
    </div>

$chartJsScript

HTML;

$finalContent = $partStart . ltrim($newLayout) . "    " . $partEnd;
file_put_contents($file, $finalContent);

echo "Dashboard rewritten to include Progress Bimbingan Umum successfully!";
