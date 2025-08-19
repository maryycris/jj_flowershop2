<?php $__env->startSection('content'); ?>
<div class="container-fluid" style="background: #f4faf4; min-height: 100vh;">
    <div class="row" style="min-height: 100vh;">
        <!-- Sidebar -->
        <div class="col-md-3 px-0">
            <?php echo $__env->make('customer.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
        <!-- Main Content -->
        <div class="col-md-9 d-flex flex-column align-items-start justify-content-start py-5">
            <h4 class="mb-4" style="font-weight: 500; color: #222;">My Notifications</h4>
            <?php if($notifications->count()): ?>
            <div class="mb-3 d-flex gap-2">
                <!-- Mark All as Read -->
                <form method="POST" action="<?php echo e(route('customer.notifications.markAllAsRead')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary btn-sm">Mark All as Read</button>
                </form>
                <!-- Delete All -->
                <form method="POST" action="<?php echo e(route('customer.notifications.destroyAll')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm">Delete All</button>
                </form>
            </div>
            <?php endif; ?>
            <div class="w-100" style="max-width: 520px;">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                    $type = $data['type'] ?? ($notification->type ?? 'Notification');
                    $message = $data['message'] ?? (is_string($data) ? $data : 'No details available.');
                    $date = $notification->created_at->format('M d, Y');
                    $isRead = $notification->read();
                    $bgColor = $isRead ? '#fff' : '#e3f0ff'; // blue for unread, white for read
                ?>
                    <div class="d-flex align-items-center mb-3 notification-card-custom notification-item"
                        style="border-radius: 8px; background: <?php echo e($bgColor); ?>; padding: 18px 24px; min-height: 80px; box-shadow: none; border: 1px solid #e0e0e0; cursor:pointer; position:relative;"
                        data-id="<?php echo e($notification->id); ?>"
                        data-type="<?php echo e($type); ?>"
                        data-message="<?php echo e($message); ?>"
                        data-date="<?php echo e($date); ?>"
                        data-is-read="<?php echo e($isRead ? '1' : '0'); ?>">
                        <div class="flex-grow-1">
                            <div style="font-size: 0.95rem; color: #888; margin-bottom: 4px;"><?php echo e($date); ?></div>
                            <div style="font-size: 1.08rem; color: #333;"><?php echo e(\Illuminate\Support\Str::limit($message, 80)); ?></div>
                        </div>
                        <div class="dropdown" style="position:absolute; top:10px; right:10px; z-index:2;">
                            <button class="btn btn-sm btn-light p-1 px-2 border-0" type="button" id="dropdownMenuButton<?php echo e($notification->id); ?>" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius:50%;">
                                <span style="font-size:1.3rem; line-height:1;">&#8942;</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton<?php echo e($notification->id); ?>">
                                <?php if(!$isRead): ?>
                                <li><a class="dropdown-item mark-read-action" href="#" data-id="<?php echo e($notification->id); ?>">Mark as Read</a></li>
                                <?php else: ?>
                                <li><a class="dropdown-item mark-unread-action" href="#" data-id="<?php echo e($notification->id); ?>">Mark as Unread</a></li>
                                <?php endif; ?>
                                <li>
                                    <form action="<?php echo e(route('customer.notifications.delete', $notification->id)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="d-flex align-items-center mb-3 notification-card-custom" style="border-radius: 8px; background: #fff; padding: 18px 24px; min-height: 80px; box-shadow: none; border: 1px solid #e0e0e0;">
                        <div class="flex-grow-1">
                            <div style="font-size: 0.95rem; color: #888; margin-bottom: 4px;">No notifications</div>
                            <div style="font-size: 1.08rem; color: #333;">You have no notifications at this time.</div>
                        </div>
                        <div class="ms-3" style="width: 48px; height: 48px; background: #f4f4f4; border-radius: 6px; border: 1px solid #d2d2d2; display: flex; align-items: center; justify-content: center;"></div>
                </div>
                <?php endif; ?>
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($notifications->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Details Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notificationModalLabel">Notification Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 id="modalType"></h6>
        <p id="modalMessage"></p>
        <small class="text-muted" id="modalDate"></small>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Prevent dropdown click from triggering modal
        document.querySelectorAll('.dropdown-toggle, .dropdown-menu').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
        document.querySelectorAll('.notification-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                // Prevent modal if delete button is clicked
                if (e.target.classList.contains('delete-notification-action')) return;
                document.getElementById('modalType').textContent = this.getAttribute('data-type');
                document.getElementById('modalMessage').textContent = this.getAttribute('data-message');
                document.getElementById('modalDate').textContent = this.getAttribute('data-date');
                var modal = new bootstrap.Modal(document.getElementById('notificationModal'));
                modal.show();
                if (this.getAttribute('data-is-read') === '0') {
                    var notifId = this.getAttribute('data-id');
                    fetch('/customer/notifications/' + notifId + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.style.background = '#fff';
                            this.setAttribute('data-is-read', '1');
                        }
                    });
                }
            });
        });
        // Mark as Read
        document.querySelectorAll('.mark-read-action').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var notifId = this.getAttribute('data-id');
                fetch('/customer/notifications/' + notifId + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var card = this.closest('.notification-item');
                        card.style.background = '#fff';
                        card.setAttribute('data-is-read', '1');
                        // Change dropdown to show 'Mark as Unread'
                        this.parentElement.innerHTML = '<a class="dropdown-item mark-unread-action" href="#" data-id="' + notifId + '">Mark as Unread</a>';
                    }
                }.bind(this));
            });
        });
        // Mark as Unread
        document.querySelectorAll('.mark-unread-action').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var notifId = this.getAttribute('data-id');
                fetch('/customer/notifications/' + notifId + '/unread', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var card = this.closest('.notification-item');
                        card.style.background = '#e3f0ff';
                        card.setAttribute('data-is-read', '0');
                        // Change dropdown to show 'Mark as Read'
                        this.parentElement.innerHTML = '<a class="dropdown-item mark-read-action" href="#" data-id="' + notifId + '">Mark as Read</a>';
                    }
                }.bind(this));
            });
        });
        // Delete
        document.querySelectorAll('.delete-notification-action').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var notifId = this.getAttribute('data-id');
                if (confirm('Delete this notification?')) {
                    fetch('/customer/notifications/' + notifId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.notification-item').remove();
                        }
                    }.bind(this));
                }
            });
        });
        // Mark All as Read
        var markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function() {
                fetch('/customer/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item').forEach(function(item) {
                            item.style.background = '#fff';
                            item.setAttribute('data-is-read', '1');
                        });
                    }
                });
            });
        }
        // Delete All
        var deleteAllBtn = document.getElementById('deleteAllBtn');
        if (deleteAllBtn) {
            deleteAllBtn.addEventListener('click', function() {
                if (confirm('Delete all notifications?')) {
                    fetch('/customer/notifications/destroy-all', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('.notification-item').forEach(function(item) {
                                item.remove();
                            });
                        }
                    });
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body {
        background: #f4faf4 !important;
    }
    .sidebar-links .sidebar-link {
        display: block;
        padding: 6px 18px;
        border-radius: 4px;
        color: #222;
        font-weight: 400;
        text-decoration: none;
        margin-bottom: 2px;
        background: transparent;
        transition: background 0.2s, color 0.2s;
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
    .notification-card-custom {
        transition: background 0.2s;
    }
.notification-card-custom[data-is-read="0"] {
    background: #e3f0ff !important;
}
.notification-card-custom[data-is-read="1"] {
    background: #fff !important;
}
.delete-notification-btn {
    font-size: 1.2rem;
    line-height: 1;
    padding: 0 8px;
    border-radius: 50%;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/notifications/index.blade.php ENDPATH**/ ?>