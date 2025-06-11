<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Admin Dashboard</h1>
        <a href="<?php echo e(route('admin.scholarship-batches.create')); ?>"
           class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            Create New Batch
        </a>
    </div>

    <p class="mb-6 text-gray-600">Welcome, Admin! Here's an overview of the scholarship batches.</p>

    <div class="grid gap-6 mb-8 md:grid-cols-2">
        <!-- Active Batches Card -->
        <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-200">
                Active Scholarship Batches (<?php echo e($totalActiveBatches); ?>)
            </h3>
            <!--[if BLOCK]><![endif]--><?php if($activeBatches->count() > 0): ?>
                <ul class="space-y-2">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $activeBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-100"><?php echo e($batch->name); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <?php echo e($batch->start_date->format('d M Y')); ?> - <?php echo e($batch->end_date->format('d M Y')); ?>

                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                <?php echo e($batch->submissions_count); ?> Applicants
                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </ul>
            <?php else: ?>
                <p class="text-gray-500 dark:text-gray-400">No active scholarship batches at the moment.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!-- Closed Batches Card -->
        <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-200">
                Closed Scholarship Batches (<?php echo e($totalClosedBatches); ?>)
            </h3>
            <!--[if BLOCK]><![endif]--><?php if($closedBatches->count() > 0): ?>
                <ul class="space-y-2">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $closedBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-100"><?php echo e($batch->name); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Closed on: <?php echo e($batch->end_date->format('d M Y')); ?>

                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">
                                <?php echo e($batch->submissions_count); ?> Applicants
                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </ul>
            <?php else: ?>
                <p class="text-gray-500 dark:text-gray-400">No closed scholarship batches found.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Quick Management Links</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <a href="<?php echo e(route('admin.scholarship-batches.index')); ?>" class="block p-4 bg-white rounded-lg shadow hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                <h4 class="font-semibold text-purple-600 dark:text-purple-400">Manage All Batches</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">View, edit, and manage all scholarship batches.</p>
            </a>
            
            <a href="#" 
                class="block p-4 bg-white rounded-lg shadow hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 opacity-50 cursor-not-allowed" 
                title="User management route not yet defined (admin.users.index)"> 
                <h4 class="font-semibold text-purple-600 dark:text-purple-400">Manage Users</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">View and manage user accounts.</p>
            </a>
        </div>
    </div>
</div>
<?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views/livewire/admin/dashboard.blade.php ENDPATH**/ ?>