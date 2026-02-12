@extends('layouts.app')

@section('title', 'Log Aktivitas - Admin IDN Menulis')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Log Aktivitas</h1>
        <p class="text-gray-600 mt-1">Pantau semua aktivitas di platform</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Waktu</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Pengguna</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aktivitas</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $log->user->full_name ?? 'System' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Belum ada aktivitas tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
