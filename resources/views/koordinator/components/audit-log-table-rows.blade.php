@forelse($logs as $log)
    <tr class="hover:bg-gray-50 transition-colors">
        <td class="py-4 px-6 border-r border-gray-300">
            <div class="font-bold text-black">{{ $log->user->name ?? 'Sistem (Otomatis)' }}</div>
            @if($log->user?->mahasiswa)
                <div class="text-[10px] text-gray-500">{{ $log->user->mahasiswa->nim }}</div>
            @elseif($log->user?->dosen)
                <div class="text-[10px] text-gray-500">{{ $log->user->dosen->nidn }}</div>
            @endif
        </td>
        <td class="py-4 px-6 border-r border-gray-300">{{ $log->role }}</td>
        <td class="py-4 px-6 border-r border-gray-300">{{ $log->module }}</td>
        <td class="py-4 px-6 border-r border-gray-300">
            <span class="px-2 py-1 rounded-[3px] text-[11px] font-bold {{ str_contains($log->action, 'DELETE') ? 'bg-red-100 text-red-700' : (str_contains($log->action, 'UPDATE') ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                {{ $log->action }}
            </span>
        </td>
        <td class="py-4 px-6">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-12 text-gray-400 italic font-medium">Belum ada aktivitas yang tercatat.</td>
    </tr>
@endforelse
