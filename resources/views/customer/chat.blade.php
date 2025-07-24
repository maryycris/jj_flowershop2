@extends('layouts.customer_app')

@section('content')
<style>
    .chat-popup {
        position: fixed;
        bottom: 32px;
        right: 32px;
        width: 340px;
        max-width: 95vw;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.10);
        z-index: 1055;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .chat-header {
        background: #f8faf8;
        padding: 14px 18px 10px 18px;
        border-bottom: 1px solid #e0e0e0;
        font-weight: 600;
        font-size: 1.08rem;
        color: #444;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .chat-header .close-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #888;
        cursor: pointer;
    }
    .chat-body {
        background: #f8faf8;
        padding: 18px 14px 12px 14px;
        min-height: 180px;
        max-height: 260px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .chat-message {
        max-width: 80%;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 1rem;
        color: #222;
        background: #cbe7cb;
        align-self: flex-end;
        margin-bottom: 2px;
    }
    .chat-message.support {
        background: #e0e0e0;
        color: #333;
        align-self: flex-start;
    }
    .chat-input-area {
        background: #f8faf8;
        padding: 10px 14px;
        border-top: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chat-input {
        flex: 1;
        border: 1px solid #cbe7cb;
        border-radius: 6px;
        padding: 7px 12px;
        font-size: 1rem;
        background: #fff;
    }
    .chat-send-btn {
        background: #7bb47b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 7px 18px;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.2s;
    }
    .chat-send-btn:hover {
        background: #5a9c5a;
    }
</style>
<div class="chat-popup">
    <div class="chat-header">
        Chat Support
        <button class="close-btn" onclick="window.location.href='{{ url()->previous() }}'">&times;</button>
    </div>
    <div class="chat-body" id="chatBody">
        <div class="chat-message support">Chat your concern...</div>
        <div class="chat-message">Hi</div>
        <div class="chat-message support">Hello! How can we help you?</div>
    </div>
    <form class="chat-input-area" onsubmit="event.preventDefault(); sendMessage();">
        <input type="text" class="chat-input" id="chatInput" placeholder="Chat here" autocomplete="off">
        <button type="submit" class="chat-send-btn">Send</button>
    </form>
</div>
<script>
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const chatBody = document.getElementById('chatBody');
        const msg = input.value.trim();
        if(msg) {
            // Show message immediately
            const div = document.createElement('div');
            div.className = 'chat-message';
            div.textContent = msg;
            chatBody.appendChild(div);
            input.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            // Send to backend
            fetch("{{ route('customer.chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: msg })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Failed to send message.');
                }
            })
            .catch(() => {
                alert('Failed to send message.');
            });
        }
    }
</script>
@endsection 