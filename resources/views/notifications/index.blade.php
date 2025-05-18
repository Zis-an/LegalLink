@extends('adminlte::page') {{-- or your layout --}}

@section('title', 'Notification History')

@section('content')
    <h3>Notification History</h3>

    <ul class="list-group">
        @forelse ($notifications as $notification)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $notification->data['author'] ?? 'System' }}</strong> -
                    {{ $notification->data['message'] ?? 'No message' }}
                    <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                </div>

                {{-- Mark as read button (only if unread) --}}
                @if (is_null($notification->read_at))
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button class="btn btn-sm btn-success">Mark as read</button>
                    </form>
                @else
                    <span class="badge bg-secondary">Read</span>
                @endif
            </li>
        @empty
            <li class="list-group-item">No notifications found.</li>
        @endforelse
    </ul>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
@endsection
