@extends('layouts.app')

@section('title', 'Notifikasi - IDN Menulis')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
        <p class="text-gray-600 mt-1">Semua notifikasi Anda</p>
    </div>

    <div class="bg-white rounded-lg shadow">
        @forelse($notifications as $notification)
            <div class="flex items-start gap-4 px-6 py-4 border-b last:border-b-0 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $notification->title }}</p>
                    <p class="text-gray-600 text-sm">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="text-blue-600 hover:underline text-sm">Lihat</a>
                    @endif
                    @if(!$notification->is_read)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-600 text-sm">Tandai dibaca</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-gray-500">
                <p>Tidak ada notifikasi</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
