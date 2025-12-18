@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Notifikasi</h2>
            <p class="text-gray-600 mt-1">{{ $unreadCount }} notifikasi belum dibaca</p>
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium">
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-sm border divide-y">
        @forelse($notifications as $notification)
        <div class="p-6 hover:bg-gray-50 transition {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        @if(!$notification->read_at)
                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                        @endif
                        <h4 class="font-semibold text-gray-800">
                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                        </h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">
                        {{ $notification->data['message'] ?? 'Anda memiliki notifikasi baru' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                
                @if(!$notification->read_at)
                <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                        Tandai Dibaca
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-500">Tidak ada notifikasi</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="flex justify-center">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection