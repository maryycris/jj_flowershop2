<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JJ Flowershop') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-green: #385E42;
            --light-green: #F0F2ED;
            --accent-green: #A0C49D;
            --text-dark: #333;
            --text-light: #666;
            --border-light: #ddd;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: var(--primary-green);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .navbar-scrolled {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            height: 30px;
            margin-right: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.75) !important;
            font-weight: 600;
            margin-right: 15px;
            transition: color 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white !important;
        }

        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .btn-primary:hover {
            background-color: #2a4a34;
            border-color: #2a4a34;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        }

        .card-title {
            color: var(--primary-green);
            font-weight: 600;
        }

        .card-text {
            color: var(--text-light);
        }

        .alert {
            border-radius: 0.5rem;
            margin-top: 1rem;
        }

        /* Footer Styling */
        .footer {
            background-color: var(--primary-green);
            color: white;
            padding: 30px 0;
            margin-top: auto; /* Push footer to the bottom */
        }

        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .social-icons a {
            font-size: 24px;
            margin-right: 15px;
            color: white;
        }

        /* Custom CSS variables for the Admin UI, extendable */
        .primary-bg-dark {
            background-color: var(--primary-green) !important;
        }
        .bg-light-green {
            background-color: var(--light-green) !important;
        }
        
        /* Customer specific styles */
        .bottom-navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 10px 0;
        }
        .bottom-navigation .nav-link {
            color: var(--text-light) !important;
            font-weight: 400;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5px 0;
        }
        .bottom-navigation .nav-link.active {
            color: var(--primary-green) !important;
        }
        .bottom-navigation .nav-link i {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        .smooth-dropdown {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .smooth-dropdown.show {
            opacity: 1;
            transform: translateY(0);
        }
        .smooth-dropdown {
            opacity: 0;
            transform: translateY(10px);
        }
        .customer-top-navbar {
            position: sticky;
            top: 0;
            z-index: 1050;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- New Top Navigation Bar for Customer (standardized for all customer pages) -->
        <nav class="customer-top-navbar" style="background: #8ACB88; color: #fff; padding: 0 6.0vw; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw);">
            <div class="container-fluid px-4 pt-2 pb-1">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-1">
                    <div class="d-flex align-items-center justify-content-center gap-5" style="padding: 0 4.0vw;">
                        <div class="d-flex align-items-center justify-content-center gap-5 pb-1" style="margin-left: 230px;">
                            <a href="{{ route('customer.dashboard') }}" class="nav-link text-white d-flex align-items-center gap-1 @if(request()->routeIs('customer.dashboard')) active @endif"><i class="bi bi-house-door"></i> Home</a>
                            <a href="{{ route('customer.products.customize') }}" class="nav-link text-white d-flex align-items-center gap-1 @if(request()->routeIs('products.customize')) active @endif"><i class="bi bi-brush"></i> Customize</a>
                            <a href="{{ route('customer.notifications.index') }}" class="nav-link text-white d-flex align-items-center gap-1 position-relative @if(request()->routeIs('customer.notifications.index')) active @endif">
                                <i class="bi bi-bell"></i> Notifications
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.8rem;">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown">
                                <button class="nav-link text-white d-flex align-items-center gap-1 btn btn-link p-0" type="button" id="customerUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1rem; background: none; border: none;">
                                    <i class="bi bi-person-circle" style="font-size: 1rem;"></i> {{ Auth::user()->name ?? "customer's name" }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end smooth-dropdown" aria-labelledby="customerUserDropdown">
                                    <li><a class="dropdown-item" href="{{ route('customer.account.index') }}"><i class="bi bi-person"></i> MY ACCOUNT</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> LOGOUT</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <div class="d-flex align-items-center">
                        <img src="/images/logo.png" alt="JJ Flower Shop" style="height: 48px; background: transparent;" class="me-2">
                        <div class="fw-bold" style="font-size: 1.3rem; line-height: 1;">J ' J FLOWER<br><span style="font-size: 0.9rem; font-weight: 400;">SHOP <span class="fs-6">Est. 2023</span></span></div>
                    </div>
                    <form class="flex-grow-1 mx-4" style="max-width: 500px;">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search" aria-label="Search">
                            <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                    <div class="d-flex align-items-center gap-4">
                        <a href="{{ route('customer.cart.index') }}" class="icon-btn text-white position-relative"><i class="bi bi-cart" style="font-size: 1.5rem;"></i></a>
                        <button class="icon-btn text-white position-relative" id="navbarChatBtn" title="Chat Support" style="background: none; border: none; font-size: 1.5rem; padding: 0 0.5rem 0 0;"><i class="bi bi-chat-dots"></i></button>
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

    <!-- Floating Chat Widget -->
    <style>
        .floating-chat-btn {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 2000;
            background: #7bb47b;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
        }
        .chat-popup {
            position: fixed;
            bottom: 100px;
            right: 32px;
            width: 340px;
            max-width: 95vw;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.10);
            z-index: 2050;
            display: none;
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
        /* Ensure chat popup is always on top */
        .chat-popup {
            position: fixed !important;
            z-index: 9999 !important;
        }
        /* Make sure floating button is always visible */
        .floating-chat-btn {
            position: fixed !important;
            z-index: 9998 !important;
        }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .chat-popup {
                width: 90vw !important;
                right: 5vw !important;
                left: 5vw !important;
            }
            .floating-chat-btn {
                bottom: 20px !important;
                right: 20px !important;
                width: 50px !important;
                height: 50px !important;
                font-size: 1.5rem !important;
            }
        }
    </style>
    <button class="floating-chat-btn" id="openChatBtn" title="Chat Support"><i class="bi bi-chat-dots"></i></button>
    <div class="chat-popup" id="chatPopup">
        <div class="chat-header">
            Chat Support
            <button class="close-btn" id="closeChatBtn">&times;</button>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="chat-message support">Chat your concern...</div>
        </div>
        <form class="chat-input-area" onsubmit="event.preventDefault(); sendMessage();">
            <input type="text" class="chat-input" id="chatInput" placeholder="Chat here" autocomplete="off">
            <button type="submit" class="chat-send-btn">Send</button>
        </form>
    </div>
    <script>
        // Chat functionality
        const openChatBtn = document.getElementById('openChatBtn');
        const navbarChatBtn = document.getElementById('navbarChatBtn');
        const chatPopup = document.getElementById('chatPopup');
        const closeChatBtn = document.getElementById('closeChatBtn');
        const chatInput = document.getElementById('chatInput');
        const chatBody = document.getElementById('chatBody');

        // Function to open chat
        function openChat() {
            chatPopup.style.display = 'flex';
            openChatBtn.style.display = 'none';
            chatInput.focus();
            loadMessages();
        }

        // Function to close chat
        function closeChat() {
            chatPopup.style.display = 'none';
            openChatBtn.style.display = 'flex';
        }

        // Open chat from floating button
        if (openChatBtn) {
            openChatBtn.onclick = openChat;
        }

        // Open chat from navbar button
        if (navbarChatBtn) {
            navbarChatBtn.onclick = openChat;
        }

        // Close chat
        if (closeChatBtn) {
            closeChatBtn.onclick = closeChat;
        }

        // Send message function
        function sendMessage() {
            const msg = chatInput.value.trim();
            if (msg) {
                // Show user message immediately
                const userDiv = document.createElement('div');
                userDiv.className = 'chat-message';
                userDiv.textContent = msg;
                chatBody.appendChild(userDiv);
                chatInput.value = '';
                chatBody.scrollTop = chatBody.scrollHeight;

                // Send to backend
                fetch("{{ route('customer.chat.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ message: msg })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show support response
                        setTimeout(() => {
                            const supportDiv = document.createElement('div');
                            supportDiv.className = 'chat-message support';
                            supportDiv.textContent = 'Thank you for your message. Our support team will get back to you soon.';
                            chatBody.appendChild(supportDiv);
                            chatBody.scrollTop = chatBody.scrollHeight;
                        }, 1000);
                    } else {
                        alert('Failed to send message. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to send message. Please try again.');
                });
            }
        }

        // Load messages
        function loadMessages() {
            fetch("{{ route('customer.chat.messages') }}")
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages.length > 0) {
                    chatBody.innerHTML = '';
                    data.messages.forEach(msg => {
                        const div = document.createElement('div');
                        div.className = msg.sender_id == {{ Auth::id() }} ? 'chat-message' : 'chat-message support';
                        div.textContent = msg.message;
                        chatBody.appendChild(div);
                    });
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
            });
        }

        // Handle enter key
        if (chatInput) {
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }

        // Close chat when clicking outside
        document.addEventListener('click', function(e) {
            if (chatPopup && chatPopup.style.display === 'flex') {
                if (!chatPopup.contains(e.target) && !openChatBtn.contains(e.target) && !navbarChatBtn.contains(e.target)) {
                    closeChat();
                }
            }
        });

        // Initialize chat on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure chat elements are properly initialized
            if (openChatBtn && chatPopup && closeChatBtn && chatInput && chatBody) {
                console.log('Chat functionality initialized successfully');
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                    if (alert.classList.contains('show')) {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }
                });
            }, 2000);
        });
    </script>
</body>
</html> 