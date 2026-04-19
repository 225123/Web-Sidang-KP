import sys

with open(r'f:\SEMESTER 6\KP\KP-Web_Sidang_KP\resources\views\koordinator\components\kp-table.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
skip = False
for i, line in enumerate(lines):
    # This is line 63 which is </td>
    if line.strip() == '</td>' and 'isSelectionMode' in lines[i-7]:
        new_lines.append(line)
        new_lines.append('                @endif\n')
        continue
        
    if 'div class="font-bold text-[13px] text-gray-800 leading-snug"' in line:
        # Start skipping the duplicated block
        skip = True
        continue
    
    if skip and '</td>' in line:
        skip = False
        continue
        
    if not skip:
        new_lines.append(line)

with open(r'f:\SEMESTER 6\KP\KP-Web_Sidang_KP\resources\views\koordinator\components\kp-table.blade.php', 'w', encoding='utf-8') as f:
    f.writelines(new_lines)
