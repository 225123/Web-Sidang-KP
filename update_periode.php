<?php

$dir = 'resources/views/koordinator/';
$files = scandir($dir);

$snippet = <<<'EOD'
    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px]">
            <button @click="open = !open" @click.outside="open = false" type="button"
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none focus:border-[#4CC098] focus:ring-1 focus:ring-[#4CC098] cursor-pointer text-black h-[32px]">

                <span x-text="selected"></span>

                <svg :class="open ? 'rotate-0' : 'rotate-90'"
                    class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition style="display: none;"
                class="absolute z-50 w-full mt-1 bg-[#FBFBFB] border border-[#CAC0C0] rounded-[5px] shadow-lg overflow-hidden">
                <ul class="py-1 text-[13px] font-medium text-black">
                    <li>
                        <button @click="selected = 'Genap 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Genap 2025/2026
                        </button>
                    </li>
                    <li>
                        <button @click="selected = 'Ganjil 2025/2026'; open = false" type="button"
                            class="block w-full text-left px-3 py-2 hover:bg-[#E8E5E5] transition-colors cursor-pointer">
                            Ganjil 2025/2026
                        </button>
                    </li>
                </ul>
            </div>
            <input type="hidden" name="periode" :value="selected">
        </div>
    </x-slot:headerActions>
EOD;

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
    
    // Skip excluded pages
    if (in_array($file, ['periode-kp.blade.php', 'backup.blade.php', 'dashboard.blade.php', 'berita-acara-pdf-template.blade.php'])) continue;
    
    // Skip detail pages
    if (strpos($file, '-detail') !== false || strpos($file, 'Detail') !== false) continue;
    
    $path = $dir . $file;
    $content = file_get_contents($path);
    
    // Check if it already has headerActions
    if (strpos($content, '<x-slot:headerActions>') !== false) {
        // Replace existing headerActions
        $content = preg_replace('/<x-slot:headerActions>.*?<\/x-slot:headerActions>/s', $snippet, $content);
        file_put_contents($path, $content);
        echo "Updated $file\n";
    } else {
        // Find </x-slot:sidebar> or </x-slot>
        if (preg_match('/<\/x-slot(:sidebar)?>/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $tag = $matches[0][0];
            $pos = $matches[0][1] + strlen($tag);
            $content = substr_replace($content, "\n\n" . $snippet, $pos, 0);
            file_put_contents($path, $content);
            echo "Inserted $file\n";
        } else {
            echo "Failed to find slot in $file\n";
        }
    }
}
echo "Done.\n";
