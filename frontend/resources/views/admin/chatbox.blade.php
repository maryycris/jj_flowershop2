@php $hideSidebar = true; @endphp
@extends('layouts.admin_app')

@push('styles')
<style>
/* Clean and Simple Chat UI */
.chat-container {
    height: calc(100vh - 150px);
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: row;
}

.users-sidebar {
    background: #f8f9fa;
    border-right: 1px solid #e0e0e0;
    width: 35%;
    min-width: 250px;
    display: flex;
    flex-direction: column;
}

.users-header {
    background: #7bb47b;
    color: white;
    padding: 12px 15px;
    margin: 0;
    font-weight: 600;
    font-size: 1rem;
    border-bottom: 1px solid #6aa06a;
}

.user-item {
    transition: all 0.2s ease;
    border: none;
    border-radius: 0;
    margin: 0;
    padding: 10px 15px;
    background: transparent;
    border-bottom: 1px solid #f0f0f0;
    position: relative;
    color: #000000;
}

.user-item:hover {
    background: #e8f5e8;
    color: #000000;
}

.user-item.active {
    background: rgba(123, 180, 123, 0.3);
    color: #000000;
    border-left: 4px solid #5a8a5a;
}

.user-name {
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 2px;
}

.user-role {
    font-size: 0.65rem;
    padding: 2px 5px;
    border-radius: 3px;
    font-weight: 500;
}

.user-item:not(.active) .user-role {
    background: #e3f2fd;
    color: #1976d2;
}

.user-item.active .user-role {
    background: rgba(123, 180, 123, 0.5);
    color: #000000;
}

.chat-area {
    background: #ffffff;
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 65%;
    flex: 1;
}

.chat-area form {
    width: 100%;
}

.chat-header {
    background: #7bb47b;
    color: white;
    padding: 12px 15px;
    border-bottom: 1px solid #6aa06a;
    display: flex;
    align-items: center;
}

.chat-messages {
    background: #ffffff;
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    min-height: 300px;
}

.message-bubble {
    max-width: 70%;
    margin-bottom: 10px;
}

.message-sent {
    background: #7bb47b;
    color: white;
    border-radius: 15px 15px 3px 15px;
    padding: 8px 12px;
    margin-left: auto;
}

.message-received {
    background: #f1f1f1;
    color: #333;
    border-radius: 15px 15px 15px 3px;
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
}

.message-sender {
    font-weight: 600;
    font-size: 0.75rem;
    margin-bottom: 2px;
    opacity: 0.9;
}

.message-text {
    font-size: 0.85rem;
    line-height: 1.3;
    word-wrap: break-word;
}

.message-time {
    font-size: 0.65rem;
    opacity: 0.7;
    margin-top: 2px;
}

.chat-input-area {
    background: #ffffff;
    padding: 15px 20px;
    border-top: 2px solid #7bb47b;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    min-height: 60px;
}

.message-input {
    border: 2px solid #a8d5a8;
    border-radius: 25px;
    padding: 12px 18px;
    font-size: 0.9rem;
    flex: 1;
    background: white;
    outline: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    color: #000000;
}

.message-input::placeholder {
    color: #666666;
}

.message-input:focus {
    border-color: #9bc99b;
    box-shadow: 0 0 0 3px rgba(168, 213, 168, 0.3);
}

.send-button {
    background: #7bb47b;
    border: none;
    border-radius: 25px;
    padding: 12px 20px;
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 80px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.send-button:hover {
    background: #6aa06a;
    transform: translateY(-1px);
}

.send-button:active {
    transform: translateY(0);
}

.empty-state {
    text-align: center;
    color: #6c757d;
    font-size: 0.9rem;
    margin-top: 30px;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

/* Scrollbar Styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .chat-container {
        height: calc(100vh - 150px);
    }
    
    .message-bubble {
        max-width: 85%;
    }
    
    .chat-input-area {
        padding: 8px 12px;
    }
    
    .message-input {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}
</style>
@endpush

@section('admin_content')
<div class="container-fluid p-4 d-flex justify-content-center">
    <div class="chat-container" style="width: 100%; max-width: 1000px; max-height: 67vh;">
        <!-- Users Sidebar -->
        <div class="users-sidebar">
            <div class="users-header">
                <i class="bi bi-people-fill me-2"></i>
                Active Users
            </div>
            <div class="p-3" style="height: calc(100% - 60px); overflow-y: auto;">
                @foreach($users as $user)
                <a href="{{ route('admin.chatbox', ['user_id' => $user->id]) }}" 
                   class="user-item d-block text-decoration-none {{ $selectedUserId == $user->id ? 'active' : '' }}">
                    <div class="user-name">{{ $user->name }}</div>
                    <span class="user-role">{{ ucfirst($user->role) }}</span>
                </a>
                @endforeach
            </div>
        </div>
        
        <!-- Chat Area -->
        <div class="chat-area">
            @if($selectedUserId)
                @php $selectedUser = $users->where('id', $selectedUserId)->first(); @endphp
                <div class="chat-header">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 45px; height: 45px; color: #4CAF50;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $selectedUser->name }}</h6>
                            <small class="opacity-75">{{ ucfirst($selectedUser->role) }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="chat-messages flex-grow-1">
                    @if($messages && count($messages))
                        @foreach($messages as $msg)
                            <div class="d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="message-bubble {{ $msg->sender_id == auth()->id() ? 'message-sent' : 'message-received' }}">
                                    <div class="message-sender">
                                        {{ $msg->sender_id == auth()->id() ? 'You' : $users->where('id', $msg->sender_id)->first()->name }}
                                    </div>
                                    <div class="message-text">{{ $msg->message }}</div>
                                    <div class="message-time text-end">{{ $msg->created_at->format('M d, H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="bi bi-chat-dots"></i>
                            <div>No messages yet. Start the conversation!</div>
                        </div>
                    @endif
                </div>
                
                <div class="chat-input-area">
                    <form action="{{ route('admin.chatbox.send') }}" method="POST" class="d-flex w-100" style="gap: 10px;">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $selectedUserId }}">
                        <input type="text" name="message" class="message-input" 
                               placeholder="Type your message here..." required autocomplete="off">
                        <button type="submit" class="send-button">
                            Send
                        </button>
                    </form>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center text-muted">
                        <i class="bi bi-chat-dots" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="mt-3">Select a user to start chatting</h5>
                        <p>Choose someone from the user list to begin your conversation</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 