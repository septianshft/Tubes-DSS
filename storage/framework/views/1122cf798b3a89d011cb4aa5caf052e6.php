<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Scholarship Batches</h1>
            <p class="text-sm text-gray-500">Manage and oversee all scholarship programs.</p>
        </div>
        <a href="<?php echo e(route('admin.scholarship-batches.create')); ?>" wire:navigate class="mt-4 sm:mt-0 px-6 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition duration-150 ease-in-out flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Create New Batch
        </a>
    </div>

    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="mb-4 px-4 py-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md shadow-sm" role="alert">
            <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                <div>
                    <p class="font-bold">Success</p>
                    <p class="text-sm"><?php echo e(session('message')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php if(session()->has('error')): ?>
        <div class="mb-4 px-4 py-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-md shadow-sm" role="alert">
             <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                <div>
                    <p class="font-bold">Error</p>
                    <p class="text-sm"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Submissions</th> 
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="<?php echo e(route('admin.scholarship-batches.submissions', $batch)); ?>" wire:navigate class="hover:text-indigo-600">
                                    <?php echo e($batch->name); ?>

                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php switch($batch->status):
                                    case ('active'): ?> bg-green-100 text-green-800 <?php break; ?>
                                    <?php case ('upcoming'): ?> bg-yellow-100 text-yellow-800 <?php break; ?>
                                    <?php case ('closed'): ?> bg-red-100 text-red-800 <?php break; ?>
                                    <?php default: ?> bg-gray-100 text-gray-800
                                <?php endswitch; ?>
                                ">
                                    <?php echo e(ucfirst($batch->status)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo e($batch->start_date->format('d M Y')); ?> - <?php echo e($batch->end_date->format('d M Y')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center"><?php echo e($batch->submissions_count); ?></td> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?php echo e(route('admin.scholarship-batches.submissions', $batch)); ?>" wire:navigate class="text-blue-600 hover:text-blue-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        View
                                    </a>
                                    <a href="<?php echo e(route('admin.scholarship-batches.edit', $batch)); ?>" wire:navigate class="text-indigo-600 hover:text-indigo-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        Edit
                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($batch->status !== 'closed'): ?>
                                        <button wire:click="confirmCloseBatch(<?php echo e($batch->id); ?>)"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-150 ease-in-out"
                                            title="Close Batch">
                                            <i class="fas fa-lock"></i> Close
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic text-sm">Closed</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <button wire:click="deleteBatch(<?php echo e($batch->id); ?>)" wire:confirm="Are you sure you want to delete '<?php echo e($batch->name); ?>'? All related submissions will also be deleted." class="text-red-600 hover:text-red-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    <p class="font-semibold text-lg">No scholarship batches found.</p>
                                    <p class="text-gray-400">Why not create one now?</p>
                                    <a href="<?php echo e(route('admin.scholarship-batches.create')); ?>" wire:navigate class="mt-4 px-5 py-2.5 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 transition duration-150 ease-in-out text-sm font-medium">
                                        Create First Batch
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($batches->hasPages()): ?>
    <div class="mt-6">
        <?php echo e($batches->links()); ?>

    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($confirmingCloseBatch): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm mx-auto">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirm Close Batch</h3>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to close batch
                    <strong><?php echo e($batches->find($batchIdToClose)?->name); ?></strong>?
                    This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="resetCloseConfirmation" type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button wire:click="closeBatch" type="button"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Confirm Close
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>
<?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views/livewire/admin/scholarship-batches/list-scholarship-batches.blade.php ENDPATH**/ ?>