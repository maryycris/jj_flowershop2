@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots me-2"></i>
                        Chat Support - Main Chat Page
                        <small class="d-block text-success-light">Use this dedicated chat interface for better experience</small>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="chat-container" style="height: 400px; overflow-y: auto; padding: 20px;" id="chatContainer">
                        <div class="text-center text-muted">
                            <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                            <p class="mt-2">Start a conversation with our support team</p>
                        </div>
                    </div>
                    <div class="chat-input-container p-3 border-top">
                        <form id="chatForm" class="d-flex gap-2">
                            <input type="text" class="form-control" id="messageInput" placeholder="Type your message..." autocomplete="off">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-message {
    margin-bottom: 15px;
    padding: 10px 15px;
    border-radius: 15px;
    max-width: 70%;
    word-wrap: break-word;
}

.chat-message.user {
    background-color: #7bb47b;
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 5px;
}

.chat-message.support {
    background-color: #f8f9fa;
    color: #333;
    border: 1px solid #e9ecef;
    border-bottom-left-radius: 5px;
}

.chat-time {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 5px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chatContainer');
    const messageInput = document.getElementById('messageInput');
    const chatForm = document.getElementById('chatForm');

    // Load existing messages
    loadMessages();

    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            // Add user message to chat
            addMessage(message, 'user');
            messageInput.value = '';

            // Send to backend
            fetch("{{ route('customer.chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add support response
                    setTimeout(() => {
                        addMessage('Thank you for your message. Our support team will get back to you soon.', 'support');
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                addMessage('Sorry, there was an error sending your message. Please try again.', 'support');
            });
        }
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        messageDiv.innerHTML = `
            <div>${text}</div>
            <div class="chat-time">${new Date().toLocaleTimeString()}</div>
        `;
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function loadMessages() {
        fetch("{{ route('customer.chat.messages') }}")
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                chatContainer.innerHTML = '';
                data.messages.forEach(msg => {
                    const sender = msg.sender_id == {{ Auth::id() }} ? 'user' : 'support';
                    addMessage(msg.message, sender);
                });
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
    }
});
</script>
@endsection 