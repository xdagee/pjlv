@extends('layouts.footer')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Notifications</h4>
                            <p class="card-category">All your notifications</p>
                        </div>
                        <div class="card-body">
                            @if($notifications->count() > 0)
                                <div class="d-flex justify-content-end mb-3">
                                    <form action="{{ route('notifications.readAll') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info">
                                            <i class="material-icons">done_all</i> Mark All as Read
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="text-primary">
                                        <tr>
                                            <th>Status</th>
                                            <th>Title</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($notifications as $notification)
                                            <tr class="{{ $notification->read_at ? '' : 'table-info' }}">
                                                <td>
                                                    @if($notification->read_at)
                                                        <span class="badge badge-secondary">Read</span>
                                                    @else
                                                        <span class="badge badge-primary">New</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $notification->title }}</strong>
                                                    @if($notification->type === 'success')
                                                        <i class="material-icons text-success"
                                                            style="font-size: 14px;">check_circle</i>
                                                    @elseif($notification->type === 'warning')
                                                        <i class="material-icons text-warning" style="font-size: 14px;">warning</i>
                                                    @elseif($notification->type === 'danger')
                                                        <i class="material-icons text-danger" style="font-size: 14px;">error</i>
                                                    @else
                                                        <i class="material-icons text-info" style="font-size: 14px;">info</i>
                                                    @endif
                                                </td>
                                                <td>{{ $notification->message }}</td>
                                                <td>{{ $notification->created_at->diffForHumans() }}</td>
                                                <td class="td-actions text-right">
                                                    @if($notification->link)
                                                        <form action="{{ route('notifications.read', $notification->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-info btn-sm"
                                                                title="View & Mark as Read">
                                                                <i class="material-icons">visibility</i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if(!$notification->read_at)
                                                        <form action="{{ route('notifications.read', $notification->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                title="Mark as Read">
                                                                <i class="material-icons">done</i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('notifications.destroy', $notification->id) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                            onclick="return confirm('Delete this notification?')">
                                                            <i class="material-icons">delete</i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <i class="material-icons" style="font-size: 48px;">notifications_off</i>
                                                    <p>No notifications yet</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($notifications->hasPages())
                                <div class="d-flex justify-content-center">
                                    {{ $notifications->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection