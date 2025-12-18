@extends('layouts.participant')

@section('title', 'Notifikasi')

@section('content')
<div class="bg-white rounded-lg shadow-sm border divide-y">
    @forelse($notifications as $notification)
    <div class="p-6 hover:bg-gray-50 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h4 class="font-semibold text-gray-800 mb-1">
                    {{ $notification->data['title'] ?? 'Notifikasi' }}
                </h4>
                <p class="text-sm text-gray-600 mb-2">
                    {{ $notification->data['message'] ?? 'Anda memiliki notifikasi baru' }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
    </div>
    @empty
    <div class="p-12 text-center">
        <p class="text-gray-500">Tidak ada notifikasi</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-6 flex justify-center">
    {{ $notifications->links() }}
</div>
@endif
@endsection
