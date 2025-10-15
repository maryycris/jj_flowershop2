<?php $hideSidebar = true; ?>


<?php $__env->startSection('admin_content'); ?>
<div class="container-fluid">
    <div class="row" style="height: 70vh;">
        <!-- User List -->
        <div class="col-md-3 border-end" style="overflow-y: auto;">
            <h5 class="mt-3">Users</h5>
            <ul class="list-group">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.chatbox', ['user_id' => $user->id])); ?>" class="list-group-item list-group-item-action <?php if($selectedUserId == $user->id): ?> active <?php endif; ?>">
                    <?php echo e($user->name); ?> <span class="badge bg-secondary"><?php echo e(ucfirst($user->role)); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <!-- Chat Area -->
        <div class="col-md-9 d-flex flex-column" style="height: 100%;">
            <div class="flex-grow-1 overflow-auto p-3" style="background: #f8f9fa;">
                <?php if($messages && count($messages)): ?>
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-2 d-flex <?php echo e($msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start'); ?>">
                            <div class="p-2 rounded <?php echo e($msg->sender_id == auth()->id() ? 'bg-success text-white' : 'bg-light border'); ?>" style="max-width: 60%;">
                                <div class="small fw-bold"><?php echo e($msg->sender_id == auth()->id() ? 'You' : $users->where('id', $msg->sender_id)->first()->name); ?></div>
                                <div><?php echo e($msg->message); ?></div>
                                <div class="text-end small text-muted"><?php echo e($msg->created_at->format('M d, H:i')); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center text-muted mt-5">No messages yet. Start the conversation!</div>
                <?php endif; ?>
            </div>
            <?php if($selectedUserId): ?>
            <form action="<?php echo e(route('admin.chatbox.send')); ?>" method="POST" class="d-flex p-3 border-top bg-white">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="receiver_id" value="<?php echo e($selectedUserId); ?>">
                <input type="text" name="message" class="form-control me-2" placeholder="Type your message..." required autocomplete="off">
                <button type="submit" class="btn btn-success">Send</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/chatbox.blade.php ENDPATH**/ ?>