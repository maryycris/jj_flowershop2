

<?php $__env->startSection('content'); ?>
<div class="container py-3">
    <h4 class="mb-3">Loyalty Cards</h4>
    <div class="table-responsive bg-white p-3 rounded">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Stamps</th>
                    <th>Updated</th>
                    <th style="width:260px">Adjust</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center justify-content-between">
                            <span><?php echo e($card->user->first_name ?? $card->user->name); ?> (ID: <?php echo e($card->user_id); ?>)</span>
                            <a href="<?php echo e(route('clerk.loyalty.history', $card)); ?>" class="btn btn-link btn-sm">History</a>
                        </div>
                    </td>
                    <td><?php echo e($card->stamps_count); ?>/5</td>
                    <td><?php echo e($card->updated_at->diffForHumans()); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('clerk.loyalty.adjust', $card)); ?>" class="d-flex gap-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="number" name="delta" class="form-control" value="1" min="-5" max="5" style="width:80px">
                            <input type="text" name="reason" class="form-control" placeholder="Reason (optional)">
                            <button class="btn btn-success">Apply</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-center">No loyalty cards yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/loyalty/index.blade.php ENDPATH**/ ?>