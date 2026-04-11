Write-Host "Sedang memasang Laravel Breeze..."
composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction

Write-Host "Menyelesaikan pemasangan library Spatie & Export (PDF/Excel)..."
composer require spatie/laravel-permission spatie/laravel-activitylog barryvdh/laravel-dompdf maatwebsite/excel

Write-Host "Mempublikasikan file konfigurasi package utama..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"

Write-Host "Memasang plugin UI (Chart.js & FullCalendar)..."
npm install
npm install chart.js fullcalendar

Write-Host "Kompilasi Frontend Tailwind CSS & Vite..."
npm run build

Write-Host "Instalasi selesai!"
