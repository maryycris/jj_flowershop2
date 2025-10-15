

<?php $__env->startSection('admin_content'); ?>
<div class="container-fluid pt-4">
    <h1 class="h3 mb-4 text-gray-800">Notifications</h1>

    <!-- Simple Search Bar (button inside the input, no surrounding box) -->
    <form method="GET" action="<?php echo e(route('admin.notifications.index')); ?>" class="mb-3">
        <div class="position-relative">
            <input type="text" class="form-control search-input-with-button" id="search" name="search"
                   value="<?php echo e(request('search')); ?>"
                   placeholder="Search by date, user name, or notification type (e.g., Product_added)...">
            <button type="submit" class="btn btn-primary position-absolute top-0 end-0 h-100 px-4 rounded-start-0">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="notifications-container">
                <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $data = $notification->data;
                    $isClickable = !empty($data['action_url']);
                    $icon = $data['icon'] ?? 'fas fa-bell';
                    $color = $data['color'] ?? 'primary';
                    $title = $data['title'] ?? ucfirst($data['type'] ?? 'Notification');
                    $message = $data['message'] ?? 'No message';
                    $targetUrl = $data['action_url'] ?? 'javascript:void(0)';
                ?>
                <a href="<?php echo e($targetUrl); ?>" class="list-group-item d-flex justify-content-between align-items-start text-decoration-none <?php echo e($notification->read() ? 'bg-light text-muted' : ''); ?>" 
                   <?php if($isClickable): ?> onclick="handleAdminNotificationClick(<?php echo e($notification->id); ?>)" <?php endif; ?>>
                    <div class="ms-2 me-auto">
                        <div class="d-flex align-items-center mb-1">
                            <i class="<?php echo e($icon); ?> text-<?php echo e($color); ?> me-2"></i>
                            <div class="fw-bold"><?php echo e($title); ?></div>
                        </div>
                        <span class="text-reset"><?php echo e($message); ?></span>
                        <div class="text-muted small">Date: <?php echo e($notification->created_at->format('Y-m-d H:i')); ?></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <?php if(!$notification->read()): ?>
                            <div class="badge bg-<?php echo e($color); ?> rounded-pill me-2">New</div>
                        <?php endif; ?>
                        <?php if($isClickable): ?>
                            <i class="fas fa-external-link-alt text-muted me-2"></i>
                        <?php endif; ?>
                        <div class="form-check">
                            <input class="form-check-input mark-as-read-checkbox" type="checkbox" data-notification-id="<?php echo e($notification->id); ?>" <?php echo e($notification->read() ? 'checked disabled' : ''); ?>>
                            <label class="form-check-label" for="notificationCheck<?php echo e($notification->id); ?>"></label>
                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item">
                    <p class="text-center mb-0">No new notifications.</p>
                </div>
                <?php endif; ?>
                </div>
            </div>
            
            
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.mark-as-read-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const notificationId = this.dataset.notificationId;
                const isChecked = this.checked;

                if (isChecked) {
                    fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Optionally, visually update the notification item
                            this.closest('.list-group-item').classList.add('bg-light', 'text-muted');
                            this.disabled = true; // Disable the checkbox after marking as read
                            // You might want to remove the checkbox or change its appearance further
                        } else {
                            // Handle error
                            alert('Failed to mark notification as read.');
                            this.checked = false; // Revert checkbox state
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while marking notification as read.');
                        this.checked = false; // Revert checkbox state
                    });
                }
            });
        });
    });

    // Handle clickable notification clicks for admin
    function handleAdminNotificationClick(notificationId) {
        // Mark notification as read
        fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the notification UI
                const notificationItem = document.querySelector(`[onclick*="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.add('bg-light', 'text-muted');
                    const checkbox = notificationItem.querySelector('.mark-as-read-checkbox');
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.disabled = true;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .notifications-container {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        background: #fff;
    }

    /* Search input with button inside */
    .search-input-with-button {
        padding-right: 3.25rem; /* space for the button */
        box-shadow: none;
    }
    
    /* Custom scrollbar styling */
    .notifications-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .notifications-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .notifications-container::-webkit-scrollbar-thumb {
        background: #5E8458;
        border-radius: 4px;
    }
    
    .notifications-container::-webkit-scrollbar-thumb:hover {
        background: #4a6b45;
    }
    
    .list-group-item {
        border-color: #e3e6f0;
        padding: 1rem 1.25rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s ease;
        border-left: none;
        border-right: none;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fc;
    }
    
    .list-group-item .fw-bold {
        color: #385E42; /* Dark green for headings */
    }
    
    .search-form .form-label {
        font-weight: 600;
        color: #385E42;
    }
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>