{{-- Blade Template: @extends = pakai layout utama --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>👋 Halo, {{ Auth::user()->name }}!</h4>
    <hr>
    <h5>Pengguna Online</h5>
    <div class="list-group">
        @foreach($users as $user)
            <a href="{{ route('chat.private', $user->id) }}"
               class="list-group-item list-group-item-action d-flex justify-content-between">
                <span>{{ $user->name }}</span>
                {{-- Tampilkan badge online/offline --}}
                @if($user->is_online)
                    <span class="badge bg-success">Online</span>
                @else
                    <span class="badge bg-secondary">Offline</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
@endsection