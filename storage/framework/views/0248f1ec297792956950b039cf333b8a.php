<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Results & Rankings</h1>
            <p class="mt-1 text-sm text-gray-600"><?php echo e($batch->name); ?> - Final Results and Analytics</p>
            <p class="mt-1 text-xs text-blue-600"><?php echo e($this->getRankingModeDescription()); ?></p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Ranking Mode:</label>
                <button wire:click="toggleRankingMode"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 <?php echo e($rankingMode === 'academic' ? 'bg-indigo-600' : 'bg-gray-200'); ?>">
                    <span class="sr-only">Toggle ranking mode</span>
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out <?php echo e($rankingMode === 'academic' ? 'translate-x-5' : 'translate-x-0'); ?>"></span>
                </button>
                <span class="text-sm text-gray-600"><?php echo e($rankingMode === 'academic' ? 'Academic' : 'Administrative'); ?></span>
            </div>

            <button wire:click="refreshScores"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh Scores
            </button>
            <button wire:click="exportResults"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Results
            </button>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo e(session('message')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($showStatistics): ?>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Submissions</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo e($totalSubmissions); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                                <dd class="text-lg font-medium text-green-600"><?php echo e($approvedCount); ?> / <?php echo e($quota); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                <dd class="text-lg font-medium text-yellow-600"><?php echo e($pendingCount); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                <dd class="text-lg font-medium text-blue-600"><?php echo e(number_format($averageScore, 4)); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Auto-approve Top Candidates</h4>
                <p class="text-sm text-gray-600 mb-3">Automatically approve the highest-scoring candidates based on available slots.</p>
                <div class="flex space-x-2">
                    <input type="number" wire:model="autoSelectCount"
                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Number to select" min="1" max="<?php echo e($remainingSlots); ?>">
                    <button wire:click="autoSelectTopCandidates"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Select
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1"><?php echo e($remainingSlots); ?> slots remaining</p>
            </div>

            
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Bulk Actions</h4>
                <p class="text-sm text-gray-600 mb-3">Apply actions to selected submissions.</p>
                <div class="space-y-2">
                    <button wire:click="selectAll"
                        class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Select All (<?php echo e($submissions->count()); ?>)
                    </button>
                    <button wire:click="clearSelection"
                        class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Clear Selection
                    </button>
                </div>
                <!--[if BLOCK]><![endif]--><?php if(!empty($selectedStudentIds)): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e(count($selectedStudentIds)); ?> selected</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Score Statistics</h4>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Highest:</span>
                        <span class="font-medium text-green-600"><?php echo e(number_format($highestScore, 4)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Average:</span>
                        <span class="font-medium text-blue-600"><?php echo e(number_format($averageScore, 4)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Lowest:</span>
                        <span class="font-medium text-red-600"><?php echo e(number_format($lowestScore, 4)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Current Mode: <?php echo e($rankingMode === 'academic' ? 'Academic View' : 'Administrative View'); ?>

                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <!--[if BLOCK]><![endif]--><?php if($rankingMode === 'academic'): ?>
                        <p><strong>Academic View:</strong> Multiple students can share the same rank if they have identical SAW scores. This is commonly used in academic settings where tied scores receive equal recognition.</p>
                        <p class="mt-1"><em>Example: If 3 students have the same top score, they all get rank #1, and the next student gets rank #4.</em></p>
                    <?php else: ?>
                        <p><strong>Administrative View:</strong> Each student receives a unique sequential rank using tie-breaking mechanisms. This provides a clear order for administrative decisions.</p>
                        <p class="mt-1"><em>Example: Students are ranked 1, 2, 3, 4... with ties broken by submission order (earlier submissions win).</em></p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="flex space-x-4">
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
                    <select wire:model.live="statusFilter" id="status-filter"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="need_revision">Need Revision</option>
                    </select>
                </div>
                <div>
                    <label for="results-per-page" class="block text-sm font-medium text-gray-700">Results per page</label>
                    <select wire:model.live="resultsPerPage" id="results-per-page"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" wire:click="selectAll" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DSS Score</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr wire:key="submission-<?php echo e($submission->id); ?>" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <input type="checkbox" wire:click="toggleSelection(<?php echo e($submission->id); ?>)"
                                    <?php if(in_array($submission->id, $selectedStudentIds)): ?> checked <?php endif; ?>
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <!--[if BLOCK]><![endif]--><?php if($submission->rank <= 3): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php if($submission->rank == 1): ?> bg-yellow-100 text-yellow-800
                                        <?php elseif($submission->rank == 2): ?> bg-gray-100 text-gray-800
                                        <?php else: ?> bg-orange-100 text-orange-800 <?php endif; ?>">
                                        #<?php echo e($submission->rank); ?>

                                        <!--[if BLOCK]><![endif]--><?php if($rankingMode === 'academic' && $this->getTiedSubmissionsAtRank($submission->rank) > 1): ?>
                                            <span class="ml-1 text-xs">(<?php echo e($this->getTiedSubmissionsAtRank($submission->rank)); ?> tied)</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-900">
                                        #<?php echo e($submission->rank ?? 'N/A'); ?>

                                        <!--[if BLOCK]><![endif]--><?php if($rankingMode === 'academic' && $submission->rank && $this->getTiedSubmissionsAtRank($submission->rank) > 1): ?>
                                            <span class="text-xs text-gray-500 ml-1">(<?php echo e($this->getTiedSubmissionsAtRank($submission->rank)); ?> tied)</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo e(optional($submission->student)->name ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e(optional($submission->student)->email ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e(optional($submission->student)->nisn ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-mono"><?php echo e(isset($submission->final_saw_score) ? number_format((float)$submission->final_saw_score, 4) : 'N/A'); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full
                                    <?php switch($submission->status):
                                        case ('pending'): ?> bg-yellow-100 text-yellow-800 <?php break; ?>
                                        <?php case ('approved'): ?> bg-green-100 text-green-800 <?php break; ?>
                                        <?php case ('rejected'): ?> bg-red-100 text-red-800 <?php break; ?>
                                        <?php case ('need_revision'): ?> bg-blue-100 text-blue-800 <?php break; ?>
                                        <?php default: ?> bg-gray-100 text-gray-800
                                    <?php endswitch; ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $submission->status))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($submission->created_at->format('d M Y, H:i')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo e(route('admin.scholarship-batches.submissions.show', ['batch' => $batch->id, 'submission' => $submission->id])); ?>"
                                    wire:navigate class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>

                                <!--[if BLOCK]><![endif]--><?php if($submission->status !== 'approved'): ?>
                                    <button wire:click="bulkUpdateStatus('approved', [<?php echo e($submission->id); ?>])"
                                        class="text-green-600 hover:text-green-900 mr-2">Approve</button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if($submission->status !== 'rejected'): ?>
                                    <button wire:click="bulkUpdateStatus('rejected', [<?php echo e($submission->id); ?>])"
                                        class="text-red-600 hover:text-red-900">Reject</button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a4 4 0 01-4-4V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-semibold text-lg">No submissions found.</p>
                                    <p class="text-sm">No submissions match the current filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($submissions->hasPages()): ?>
            <div class="px-6 py-3 border-t border-gray-200">
                <?php echo e($submissions->links()); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if(!empty($selectedStudentIds)): ?>
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-6 py-4 shadow-lg">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700"><?php echo e(count($selectedStudentIds)); ?> submission(s) selected</span>
                <div class="flex space-x-3">
                    <button wire:click="bulkUpdateStatus('approved', $selectedStudentIds)"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        Approve Selected
                    </button>
                    <button wire:click="bulkUpdateStatus('rejected', $selectedStudentIds)"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        Reject Selected
                    </button>
                    <button wire:click="clearSelection"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Clear
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views/livewire/admin/results/scholarship-results.blade.php ENDPATH**/ ?>