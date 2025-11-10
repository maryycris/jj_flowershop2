@php($active = $active ?? '')
<style>
/* Alternative navbar that visually matches the cart navbar (mobile only) */
@media (max-width: 650px) {
    .alt-topbar { position: fixed !important; top: 0; left: 0; right: 0; width: 100vw; height: 56px; background: #8ACB88; z-index: 4000; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); pointer-events: auto; }
    .alt-topbar .brand { display: flex; align-items: center; gap: 8px; flex-shrink: 0; margin-left: 27px; }
    .alt-topbar .brand img { height: 43px; border-radius: 50%; background: transparent; }
    .alt-topbar .brand .text { line-height: 1.05; color: #fff; font-weight: 300; font-size: 1.2rem; letter-spacing: .2px; }
    .alt-topbar .brand .text small { display: block; font-weight: 500; font-size: .75rem; letter-spacing: .2px; }
    .alt-topbar .icons { display: flex; align-items: center; gap: 20px; flex-shrink: 0;  margin-right: 67px; }
    .alt-topbar .icons a { color: #fff; font-size: 1.3rem; display: inline-flex; align-items: center; }
    .alt-topbar .icons a.active { color: #fff; opacity: 1; }

    .alt-bottom-nav { position: fixed !important; bottom: 0; left: 0; right: 0; width: 100vw; height: 56px; background: #8ACB88; z-index: 4000; display: flex; align-items: stretch; justify-content: space-around; box-shadow: 0 -2px 8px rgba(0,0,0,0.08); pointer-events: auto; }
    .alt-bottom-nav a { flex: 1 1 0; color: #fff; text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 11px; }
    .alt-bottom-nav a i { font-size: 20px; margin-bottom: 2px; }
    .alt-bottom-nav a.active { background: rgba(255,255,255,0.18); }
    .alt-bottom-nav .profile-dropdown-wrapper.active { background: rgba(255,255,255,0.18); }
    
    /* Profile Dropdown Menu Styles for Alt Navbar */
    .alt-bottom-nav .profile-dropdown-wrapper {
        flex: 1 1 0;
        position: relative;
        display: flex;
        align-items: stretch;
        justify-content: center;
    }
    .alt-bottom-nav .profile-trigger {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        color: #fff !important;
        text-decoration: none !important;
        cursor: pointer;
        width: 100%;
        gap: 2px;
    }
    .alt-bottom-nav .profile-trigger i {
        font-size: 20px;
        margin-bottom: 2px;
        display: block;
    }
    .alt-bottom-nav .profile-trigger span {
        font-size: 11px;
        display: block;
        text-align: center;
    }
    .alt-bottom-nav .profile-dropdown-menu {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-bottom: 10px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 180px;
        z-index: 1200;
        display: none;
        padding: 8px 0;
        overflow: hidden;
    }
    .alt-bottom-nav .profile-dropdown-menu.show {
        display: block !important;
    }
    .alt-bottom-nav .profile-dropdown-menu .dropdown-item {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: flex-start;
        color: #333;
        text-decoration: none;
        font-size: 0.9rem;
        border-bottom: 1px solid #eee;
        transition: background 0.2s;
    }
    .alt-bottom-nav .profile-dropdown-menu .dropdown-item i { color: #6c757d; }
    .alt-bottom-nav .profile-dropdown-menu .dropdown-item:hover {
        background: #f8f9fa;
    }
    .alt-bottom-nav .profile-dropdown-menu .dropdown-item:last-child {
        border-bottom: none;
    }
    .alt-bottom-nav .profile-dropdown-menu button.dropdown-item {
        width: 100%;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        color: #dc3545;
    }
    .alt-bottom-nav .profile-dropdown-menu button.dropdown-item i { color: #dc3545; }
    .alt-bottom-nav .profile-dropdown-menu button.dropdown-item:hover {
        background: #f8f9fa;
    }

    /* Hide global fixed navbars on these pages to avoid double headers/footers */
    .customer-top-navbar, .mobile-bottom-nav { display: none !important; }

    /* Give page content breathing room between fixed alt bars */
    body { padding-top: 64px !important; padding-bottom: 70px !important; overflow-x: hidden; }
}
</style>

<script>
// Toggle Profile Dropdown Menu for Alt Navbar
function toggleProfileMenu(event) {
    event.stopPropagation();
    const trigger = event.currentTarget;
    const wrapper = trigger.closest('.profile-dropdown-wrapper');
    if (!wrapper) return;
    
    const menu = wrapper.querySelector('.profile-dropdown-menu');
    if (!menu) return;
    
    const isVisible = menu.classList.contains('show');
    
    // Close all other dropdowns
    document.querySelectorAll('.profile-dropdown-menu').forEach(m => {
        m.classList.remove('show');
    });
    
    // Toggle this menu
    if (!isVisible) {
        menu.classList.add('show');
    }
}

// Close dropdown when clicking outside (only if not already attached by main layout)
if (typeof window.profileMenuListenerAttached === 'undefined') {
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.profile-dropdown-wrapper')) {
            document.querySelectorAll('.profile-dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
    window.profileMenuListenerAttached = true;
}
</script>

<div class="alt-topbar d-lg-none">
    <a href="{{ route('customer.dashboard') }}" class="brand" style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:8px;">
        <img src="/images/logo.png" alt="JJ Flower Shop" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
        <div class="text">J ' J FLOWER<small>SHOP Est. 2023</small></div>
    </a>
    <div class="icons">
        <a href="{{ route('customer.favorites') }}" title="Favorites"><i class="bi bi-heart"></i></a>
        <a href="{{ route('customer.cart.index') }}" title="Cart" class="{{ request()->routeIs('customer.cart.index') ? 'active' : '' }}"><i class="bi bi-cart{{ request()->routeIs('customer.cart.index') ? '-fill' : '' }}"></i></a>
    </div>
</div>

<nav class="alt-bottom-nav d-lg-none">
    <a href="{{ route('customer.dashboard') }}"><i class="bi bi-house-door"></i><span>Home</span></a>
    <a href="{{ route('customer.products.bouquet-customize') }}" class="{{ $active==='customize' ? 'active' : '' }}"><i class="bi bi-brush"></i><span>Customize</span></a>
    <a href="{{ route('customer.notifications.index') }}" class="{{ $active==='notifications' ? 'active' : '' }}"><i class="bi bi-bell"></i><span>Notifications</span></a>
    <div class="profile-dropdown-wrapper {{ request()->routeIs('customer.account.*') || request()->routeIs('customer.address_book.*') || request()->routeIs('customer.orders.*') || request()->routeIs('customer.trackOrders.*') ? 'active' : '' }}" style="position: relative;">
        <a href="#" class="profile-trigger" onclick="event.preventDefault(); toggleProfileMenu(event);" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: inherit;">
            <i class="bi bi-person{{ request()->routeIs('customer.account.*') || request()->routeIs('customer.address_book.*') || request()->routeIs('customer.orders.*') || request()->routeIs('customer.trackOrders.*') ? '-fill' : '' }}"></i>
            <span>My Profile</span>
        </a>
        <div class="profile-dropdown-menu" id="profileDropdownAlt" style="position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 10px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 180px; z-index: 1200; display: none; padding: 8px 0;">
            <a href="{{ route('customer.account.index') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                <i class="bi bi-person me-2"></i>Profile
            </a>
            <a href="{{ route('customer.address_book.index') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                <i class="bi bi-book me-2"></i>Address Book
            </a>
            <a href="{{ route('customer.orders.index') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                <i class="bi bi-bag me-2"></i>My Purchase
            </a>
            <a href="{{ route('customer.trackOrders.page') }}" class="dropdown-item" style="padding: 10px 16px; display: block; color: #333; text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid #eee;">
                <i class="bi bi-truck me-2"></i>Track Order
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="dropdown-item" style="width: 100%; padding: 10px 16px; display: block; color: #dc3545; text-decoration: none; font-size: 0.9rem; border: none; background: none; text-align: left; cursor: pointer;">
                    <i class="bi bi-box-arrow-right me-2"></i>Log Out
                </button>
            </form>
        </div>
    </div>
</nav>


