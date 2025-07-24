@php use Illuminate\Support\Str; @endphp
<div class="d-flex flex-column align-items-center py-5">
    <img src="{{
        Auth::user()->profile_picture
            ? (Str::startsWith(Auth::user()->profile_picture, 'http')
                ? Auth::user()->profile_picture
                : asset('storage/' . Auth::user()->profile_picture))
            : 'https://via.placeholder.com/80'
    }}" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover; background: #e0e0e0; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#editImageModal" alt="">
    <div class="fw-bold mb-2" style="font-size: 1.1rem; color: #222; text-transform: uppercase;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
    <div class="sidebar-label mb-2" style="color: #888; font-size: 1rem; font-weight: 500;">Manage Account</div>
    <div class="w-100" style="max-width: 260px;">
        <div class="sidebar-links d-flex flex-column gap-1">
            <a href="{{ route('customer.account.index') }}" class="sidebar-link d-flex align-items-center @if(request()->routeIs('customer.account.index')) active-link @endif" style="color: #222; font-weight: @if(request()->routeIs('customer.account.index')) 600 @else 400 @endif;">
                <i class="fas fa-user me-2" style="color: #222;"></i> Profile
            </a>
            <a href="{{ route('customer.address_book.index') }}" class="sidebar-link d-flex align-items-center @if(request()->routeIs('customer.address_book.index')) active-link @endif" style="color: #222; font-weight: @if(request()->routeIs('customer.address_book.index')) 600 @else 400 @endif;">
                <i class="fas fa-map-marker-alt me-2" style="color: #222;"></i> Address Book
            </a>
            <a href="{{ route('customer.account.change_password') }}" class="sidebar-link d-flex align-items-center @if(request()->routeIs('customer.account.change_password')) active-link @endif" style="color: #222; font-weight: @if(request()->routeIs('customer.account.change_password')) 600 @else 400 @endif;">
                <i class="fas fa-key me-2" style="color: #222;"></i> Change Password
            </a>
            <a href="{{ route('customer.orders.index') }}" class="sidebar-link d-flex align-items-center @if(request()->routeIs('customer.orders.index')) active-link @endif" style="color: #222; font-weight: @if(request()->routeIs('customer.orders.index')) 600 @else 400 @endif;">
                <i class="fas fa-shopping-bag me-2" style="color: #222;"></i> My Purchase
            </a>
            <a href="{{ route('customer.trackOrders.page') }}" class="sidebar-link d-flex align-items-center @if(request()->routeIs('customer.trackOrders.page')) active-link @endif" style="color: #222; font-weight: @if(request()->routeIs('customer.trackOrders.page')) 600 @else 400 @endif;">
                <i class="fas fa-map-marked-alt me-2" style="color: #222;"></i> Track Order
            </a>
            <a href="{{ route('customer.chat.index') }}" class="sidebar-link d-flex align-items-center @if(request()->routeIs('customer.chat.index')) active-link @endif" style="color: #222; font-weight: @if(request()->routeIs('customer.chat.index')) 600 @else 400 @endif;">
                <i class="fas fa-comments me-2" style="color: #222;"></i> Chat Support
            </a>
            <a href="{{ route('customer.notifications.index') }}" class="sidebar-link d-flex align-items-center @if(request()->routeIs('customer.notifications.index')) active-link @endif" style="color: #222; font-weight: @if(request()->routeIs('customer.notifications.index')) 600 @else 400 @endif;">
                <i class="fas fa-bell me-2" style="color: #222;"></i> Notifications
            </a>
        </div>
    </div>
</div>
<!-- Edit Image Modal (copied from account page) -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #f8faf8; border-radius: 12px;">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customer.account.update_picture') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                    <input type="file" name="profile_picture" accept="image/*" id="profilePicInputModalSidebar" style="display:none;">
                    <label for="profilePicInputModalSidebar" id="uploadImageLabelSidebar" style="cursor:pointer;">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="background: #eafbe6; border-radius: 8px; padding: 32px 48px; border: 2px dashed #7bb47b;">
                            <span style="font-size: 2rem; color: #7bb47b;">+</span>
                            <span style="font-size: 1.2rem; color: #7bb47b; font-weight: 600;">Upload image</span>
                        </div>
                    </label>
                    <img id="imagePreviewModalSidebar" src="" style="display:none; margin-top: 18px; max-width: 120px; border-radius: 50%;" />
                </div>
                <div class="modal-footer" style="border-top: none; justify-content: center;">
                    <button type="submit" class="btn btn-green" id="savePicBtnModalSidebar" style="width: 120px; display:none;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Modal image preview and save button logic for sidebar
    document.getElementById('profilePicInputModalSidebar')?.addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('imagePreviewModalSidebar');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            document.getElementById('savePicBtnModalSidebar').style.display = 'inline-block';
        }
    });
</script>
<style>
    .sidebar-links .sidebar-link {
        display: block;
        padding: 8px 20px;
        border-radius: 4px;
        color: #222;
        font-weight: 400;
        text-decoration: none;
        margin-bottom: 2px;
        background: transparent;
        transition: background 0.2s, color 0.2s;
        font-size: 1.08rem;
    }
    .sidebar-links .sidebar-link.active-link {
        background: #cbe7cb;
        color: #222;
        font-weight: 600;
    }
    .sidebar-links .sidebar-link:hover {
        background: #e0f2e0;
        color: #222;
    }
    .sidebar-label {
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
</style> 