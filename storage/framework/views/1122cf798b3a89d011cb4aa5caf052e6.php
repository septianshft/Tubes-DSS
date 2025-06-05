<div>
    
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
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

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Batches</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($statistics['total_batches'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Batches</p>
                        <p class="text-2xl font-semibold text-green-600"><?php echo e($statistics['active_batches'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Upcoming</p>
                        <p class="text-2xl font-semibold text-yellow-600"><?php echo e($statistics['upcoming_batches'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Closed</p>
                        <p class="text-2xl font-semibold text-red-600"><?php echo e($statistics['closed_batches'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Submissions</p>
                        <p class="text-2xl font-semibold text-purple-600"><?php echo e($statistics['total_submissions'] ?? 0); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Batches</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" id="search"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Search by name or description...">
                </div>
            </div>

            
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="closed">Closed</option>
                    <option value="computed_active">Currently Active (by dates)</option>
                    <option value="computed_upcoming">Currently Upcoming (by dates)</option>
                    <option value="computed_expired">Currently Expired (by dates)</option>
                </select>
            </div>

            
            <div>
                <label for="perPage" class="block text-sm font-medium text-gray-700 mb-2">Items per page</label>
                <select wire:model.live="perPage" id="perPage"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if(count($selectedBatches) > 0): ?>
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center mb-2 sm:mb-0">
                        <span class="text-sm text-gray-700 font-medium"><?php echo e(count($selectedBatches)); ?> batch(es) selected</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="bulkActivate"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Activate
                        </button>
                        <button wire:click="bulkClose"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Close
                        </button>
                        <button wire:click="confirmBulkDelete"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                        <button wire:click="clearSelection"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Selection
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
                        
                        <th scope="col" class="px-6 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </th>

                        
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-200"
                            wire:click="sortBy('name')">
                            <div class="flex items-center">
                                Name
                                <!--[if BLOCK]><![endif]--><?php if($sortBy === 'name'): ?>
                                    <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>

                        
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-200"
                            wire:click="sortBy('start_date')">
                            <div class="flex items-center">
                                Duration
                                <!--[if BLOCK]><![endif]--><?php if($sortBy === 'start_date'): ?>
                                    <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </th>

                        
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-200"
                            wire:click="sortBy('submissions_count')">
                            <div class="flex items-center justify-center">
                                Submissions
                                <!--[if BLOCK]><![endif]--><?php if($sortBy === 'submissions_count'): ?>
                                    <!--[if BLOCK]><![endif]--><?php if($sortDirection === 'asc'): ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </th>

                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $computedStatus = $batch->computed_status;
                            $isActive = $computedStatus === 'Active';
                            $isUpcoming = $computedStatus === 'Upcoming';
                            $isClosed = $computedStatus === 'Closed' || $batch->status === 'closed';
                        ?>
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleBatchSelection(<?php echo e($batch->id); ?>)"
                                       <?php if(in_array($batch->id, $selectedBatches)): ?> checked <?php endif; ?>
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <a href="<?php echo e(route('admin.scholarship-batches.submissions', $batch)); ?>" wire:navigate
                                       class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                        <?php echo e($batch->name); ?>

                                    </a>
                                    <!--[if BLOCK]><![endif]--><?php if($batch->description): ?>
                                        <span class="text-xs text-gray-500 mt-1"><?php echo e(Str::limit($batch->description, 50)); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    
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

                                    
                                    <span class="text-xs text-gray-500">
                                        (<?php echo e($computedStatus); ?> by dates)
                                    </span>
                                </div>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex flex-col">
                                    <span><?php echo e($batch->start_date->format('d M Y')); ?> - <?php echo e($batch->end_date->format('d M Y')); ?></span>
                                    <span class="text-xs text-gray-500">
                                        <!--[if BLOCK]><![endif]--><?php if($isActive): ?>
                                            <span class="text-green-600">● Active now</span>
                                        <?php elseif($isUpcoming): ?>
                                            <span class="text-yellow-600">● Starts in <?php echo e($batch->start_date->diffForHumans()); ?></span>
                                        <?php else: ?>
                                            <span class="text-red-600">● Ended <?php echo e($batch->end_date->diffForHumans()); ?></span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </span>
                                </div>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-lg font-semibold text-gray-900"><?php echo e($batch->submissions_count); ?></span>
                                    <div class="flex space-x-2 text-xs">
                                        <!--[if BLOCK]><![endif]--><?php if($batch->pending_submissions_count > 0): ?>
                                            <span class="text-yellow-600"><?php echo e($batch->pending_submissions_count); ?> pending</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <!--[if BLOCK]><![endif]--><?php if($batch->approved_submissions_count > 0): ?>
                                            <span class="text-green-600"><?php echo e($batch->approved_submissions_count); ?> approved</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </td>

                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    
                                    <a href="<?php echo e(route('admin.scholarship-batches.submissions', $batch)); ?>" wire:navigate
                                       class="text-blue-600 hover:text-blue-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                       title="View Submissions">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>

                                    
                                    <a href="<?php echo e(route('admin.scholarship-batches.results', $batch)); ?>" wire:navigate
                                       class="text-purple-600 hover:text-purple-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                       title="View Results & Rankings">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Results
                                    </a>

                                    
                                    <a href="<?php echo e(route('admin.scholarship-batches.edit', $batch)); ?>" wire:navigate
                                       class="text-indigo-600 hover:text-indigo-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                       title="Edit Batch">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>

                                    
                                    <!--[if BLOCK]><![endif]--><?php if($batch->status !== 'active' && !$isClosed): ?>
                                        <button wire:click="confirmActivateBatch(<?php echo e($batch->id); ?>)"
                                                class="text-green-600 hover:text-green-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                title="Activate Batch">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Activate
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($batch->status !== 'closed'): ?>
                                        <button wire:click="confirmCloseBatch(<?php echo e($batch->id); ?>)"
                                                class="text-orange-600 hover:text-orange-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-orange-50 hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                                title="Close Batch">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Close
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic text-sm px-3 py-1">Closed</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    
                                    <button wire:click="deleteBatch(<?php echo e($batch->id); ?>)"
                                            wire:confirm="Are you sure you want to delete '<?php echo e($batch->name); ?>'? All related submissions will also be deleted."
                                            class="text-red-600 hover:text-red-800 inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-xs bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            title="Delete Batch">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
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
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Close Scholarship Batch</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to close batch
                    <strong><?php echo e($batches->find($batchIdToClose)?->name); ?></strong>?
                    <br><br>
                    <span class="text-orange-600 font-medium">Warning:</span> All pending submissions will be automatically rejected. This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="resetCloseConfirmation" type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button wire:click="closeBatch" type="button"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Close Batch
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($confirmingActivateBatch): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm mx-auto">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Activate Scholarship Batch</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to activate batch
                    <strong><?php echo e($batches->find($batchIdToActivate)?->name); ?></strong>?
                    <br><br>
                    <span class="text-blue-600 font-medium">Note:</span> If the start date is in the future, it will be adjusted to today.
                </p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="resetActivateConfirmation" type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button wire:click="activateBatch" type="button"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Activate Batch
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($confirmingBulkDelete): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md mx-auto">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Delete Multiple Batches</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Are you sure you want to delete <strong><?php echo e(count($batchesToDelete)); ?></strong> selected scholarship batches?
                    <br><br>
                    <span class="text-red-600 font-medium">Warning:</span> All related submissions will also be permanently deleted. This action cannot be undone.
                </p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="resetBulkDeleteConfirmation" type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button wire:click="bulkDelete" type="button"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition-colors duration-150 ease-in-out">
                        Delete <?php echo e(count($batchesToDelete)); ?> Batches
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div>
<?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views/livewire/admin/scholarship-batches/list-scholarship-batches.blade.php ENDPATH**/ ?>