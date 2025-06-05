<div>
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">My Submissions</h1>
        <p class="text-sm text-gray-500">Track the status and details of your scholarship applications.</p>
    </div>

    
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <?php
                    $tabs = [
                        ['value' => '', 'label' => 'All Statuses'],
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'under_review', 'label' => 'Under Review'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                    ];
                ?>

                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        wire:click="$set('statusFilter', '<?php echo e($tab['value']); ?>')"
                        type="button"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                            <?php if($statusFilter == $tab['value']): ?>
                                border-indigo-500 text-indigo-600
                            <?php else: ?>
                                border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300
                            <?php endif; ?>
                        "
                    >
                        <?php echo e($tab['label']); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </nav>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Student Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Scholarship Batch
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Submitted At
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Score
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="submission-<?php echo e($submission->id); ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo e($submission->student->name ?? 'N/A'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo e($submission->scholarshipBatch->name ?? 'N/A'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?php echo e($submission->submission_date ? $submission->submission_date->format('d M Y, H:i') : 'N/A'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php switch($submission->status):
                                    case ('pending'): ?> bg-yellow-100 text-yellow-800 <?php break; ?>
                                    <?php case ('under_review'): ?> bg-blue-100 text-blue-800 <?php break; ?>
                                    <?php case ('approved'): ?> bg-green-100 text-green-800 <?php break; ?>
                                    <?php case ('rejected'): ?> bg-red-100 text-red-800 <?php break; ?>
                                    <?php default: ?> bg-gray-100 text-gray-800
                                <?php endswitch; ?>
                            ">
                                <?php echo e(ucwords(str_replace('_', ' ', $submission->status))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($submission->final_saw_score ?? 'Not Scored'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            
                            
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <!--[if BLOCK]><![endif]--><?php if($statusFilter): ?>
                                No submissions found with status "<?php echo e(ucwords(str_replace('_', ' ', $statusFilter))); ?>".
                            <?php else: ?>
                                You have not made any submissions yet.
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($submissions->links()); ?>

    </div>
</div>
<?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views/livewire/teacher/submissions/list-submissions.blade.php ENDPATH**/ ?>