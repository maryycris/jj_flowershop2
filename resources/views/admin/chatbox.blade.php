@php $hideSidebar = true; @endphp
@extends('layouts.admin_app')

@section('admin_content')
<div class="container-fluid">
    <div class="row" style="height: 70vh;">
        <!-- User List -->
        <div class="col-md-3 border-end" style="overflow-y: auto;">
            <h5 class="mt-3">Users</h5>
            <ul class="list-group">
                @foreach($users as $user)
                <a href="{{ route('admin.chatbox', ['user_id' => $user->id]) }}" class="list-group-item list-group-item-action @if($selectedUserId == $user->id) active @endif">
                    {{ $user->name }} <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                </a>
                @endforeach
            </ul>
        </div>
        <!-- Chat Area -->
        <div class="col-md-9 d-flex flex-column" style="height: 100%;">
            <div class="flex-grow-1 overflow-auto p-3" style="background: #f8f9fa;">
                @if($messages && count($messages))
                    @foreach($messages as $msg)
                        <div class="mb-2 d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="p-2 rounded {{ $msg->sender_id == auth()->id() ? 'bg-success text-white' : 'bg-light border' }}" style="max-width: 60%;">
                                <div class="small fw-bold">{{ $msg->sender_id == auth()->id() ? 'You' : $users->where('id', $msg->sender_id)->first()->name }}</div>
                                <div>{{ $msg->message }}</div>
                                <div class="text-end small text-muted">{{ $msg->created_at->format('M d, H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted mt-5">No messages yet. Start the conversation!</div>
                @endif
            </div>
            @if($selectedUserId)
            <form action="{{ route('admin.chatbox.send') }}" method="POST" class="d-flex p-3 border-top bg-white">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $selectedUserId }}">
                <input type="text" name="message" class="form-control me-2" placeholder="Type your message..." required autocomplete="off">
                <button type="submit" class="btn btn-success">Send</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection 