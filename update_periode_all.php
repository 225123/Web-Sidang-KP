<?php

$configs = [
    [
        'dir' => 'resources/views/mahasiswa/',
        'focus_color' => 'focus:border-[#F48200] focus:ring-[#F48200]',
        'exclude' => ['berita-acara-pdf-template.blade.php', 'lembar-pengesahan-pdf.blade.php']
    ],
    [
        'dir' => 'resources/views/dosen/',
        'focus_color' => 'focus:border-[#CDA057] focus:ring-[#CDA057]',
        'exclude' => ['berita-acara-pdf-template.blade.php']
    ]
];

foreach ($configs as $config) {
    $dir = $config['dir'];
    $focus = $config['focus_color'];
    $files = scandir($dir);
    
    $snippet = <<<EOD
    <x-slot:headerActions>
        <div x-data="{ open: false, selected: 'Genap 2025/2026' }" class="relative w-[212px] mt-2 md:mt-0">
            <button @click="open = !open" @click.outside="open = false" type="button"
                class="w-full flex items-center justify-between border border-[#CAC0C0] bg-[#FBFBFB] rounded-[5px] shadow-sm text-[13px] font-medium py-1.5 px-3 focus:outline-none {$focus} focus:ring-1 cursor-pointer text-black h-[32px]">

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
        if (in_array($file, $config['exclude'])) continue;
        if (strpos($file, '-detail') !== false || strpos($file, 'Detail') !== false) continue;
        
        $path = $dir . $file;
        $content = file_get_contents($path);
        
        if (strpos($content, '<x-slot:headerActions>') !== false) {
            $content = preg_replace('/<x-slot:headerActions>.*?<\/x-slot:headerActions>/s', $snippet, $content);
            file_put_contents($path, $content);
            echo "Updated $file in $dir\n";
        } else {
            if (preg_match('/<\/x-slot(:sidebar)?>/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $tag = $matches[0][0];
                $pos = $matches[0][1] + strlen($tag);
                $content = substr_replace($content, "\n\n" . $snippet, $pos, 0);
                file_put_contents($path, $content);
                echo "Inserted $file in $dir\n";
            } else {
                echo "Failed to find slot in $file in $dir\n";
            }
        }
    }
}
echo "Done.\n";
