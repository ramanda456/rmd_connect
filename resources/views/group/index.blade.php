@extends('layouts.app')

@section('content')
<div class="container mt-4" style="max-width: 700px;">

    {{-- Form buat group baru --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Buat Group Baru</div>
        <div class="card-body">
            <form method="POST" action="{{ route('group.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Group</label>
                    <input type="text" name="name" class="form-control"
                           placeholder="Contoh: Tim Backend" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tambah Member</label>
                    @foreach($users as $user)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="member_ids[]" value="{{ $user->id }}"
                                   id="user-{{ $user->id }}">
                            <label class="form-check-label" for="user-{{ $user->id }}">
                                {{ $user->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">Buat Group</button>
            </form>
        </div>
    </div>

    {{-- Daftar group --}}
    <h5>Group Kamu</h5>
    @if($groups->isEmpty())
        <p class="text-muted">Belum ada group. Buat group baru di atas!</p>
    @else
        <div class="list-group">
            @foreach($groups as $group)
                <a href="{{ route('group.chat', $group->id) }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between">
                    <span> {{ $group->name }}</span>
                    <small class="text-muted">{{ $group->members->count() }} member</small>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-3">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">← Dashboard</a>
    </div>
</div>
@endsection