<div>
    <div class="mb-6 border-b pb-4 border-gray-200 dark:border-gray-700">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Submit Students for: <?php echo e($batch->name); ?></h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select one or more students and provide the common required information for this scholarship batch.</p>
    </div>

    <?php if(session()->has('message')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?>
    <?php if(session()->has('error')): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <form wire:submit.prevent="saveSubmission" class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 space-y-6">
            
            <div x-data="{
                open: false,
                searchInput: '',
                init() {
                    this.$watch('open', value => {
                        if (value) {
                            this.$nextTick(() => {
                                this.$refs.searchInput?.focus();
                            });
                        }
                    });
                }
            }" class="relative">
                <label for="student_search_button" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Students
                </label>

                
                <div class="mb-3 min-h-[52px] p-3 border rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 flex flex-wrap gap-2 items-start transition-colors duration-200">
                    <?php
                        // Ensure $selectedStudentIds is an array before iterating
                        $studentIdsToDisplay = is_array($selectedStudentIds) ? $selectedStudentIds : [];
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $this->allStudents->whereIn('id', $studentIdsToDisplay); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-700 animate-fade-in">
                            <svg class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="max-w-[120px] truncate"><?php echo e($student->name); ?></span>
                            <button type="button"
                                    wire:click="deselectStudent(<?php echo e($student->id); ?>)"
                                    title="Remove <?php echo e($student->name); ?>"
                                    class="ml-2 flex-shrink-0 text-indigo-500 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full p-0.5 transition-colors duration-150">
                                <svg class="h-3.5 w-3.5" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                </svg>
                            </button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm py-1">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 6.292 4 4 0 010-6.292zM15 21H3v-1a6 6 0 0112 0v1z"></path>
                            </svg>
                            No students selected
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Custom Select Button -->
                <button @click="open = !open"
                        type="button"
                        id="student_search_button"
                        class="relative w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm pl-4 pr-10 py-3 text-left cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 sm:text-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="block truncate text-gray-700 dark:text-gray-300">
                            <?php
                                // Ensure $selectedStudentIds is an array for counting
                                $countSelected = is_array($selectedStudentIds) ? count($selectedStudentIds) : 0;
                            ?>
                            <?php if($countSelected > 0): ?>
                                <?php echo e($countSelected); ?> student<?php echo e($countSelected > 1 ? 's' : ''); ?> selected
                            <?php else: ?>
                                Search and select students...
                            <?php endif; ?>
                        </span>
                    </div>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 transform transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </span>
                </button>

                <!-- Dropdown Panel -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-800 shadow-xl max-h-80 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">

                    <!-- Search Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="search"
                                   x-ref="searchInput"
                                   wire:model.live.debounce.300ms="studentSearch"
                                   placeholder="Search by name or NISN..."
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors duration-200">
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="max-h-64 overflow-y-auto">
                        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <label for="student-<?php echo e($student->id); ?>"
                                   class="flex items-center px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors duration-150 group">
                                <div class="flex items-center flex-1">
                                    <input type="checkbox"
                                           id="student-<?php echo e($student->id); ?>"
                                           wire:model.live="selectedStudentIds"
                                           value="<?php echo e($student->id); ?>"
                                           
                                           <?php if(is_array($selectedStudentIds) && in_array($student->id, $selectedStudentIds)): ?> checked <?php endif; ?>
                                           class="h-4 w-4 text-indigo-600 rounded border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:ring-2 transition-colors duration-150">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-900 dark:group-hover:text-indigo-100 transition-colors duration-150">
                                                <?php echo e($student->name); ?>

                                            </span>
                                            
                                            <?php if(is_array($selectedStudentIds) && in_array($student->id, $selectedStudentIds)): ?>
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            NISN: <?php echo e($student->nisn); ?>

                                        </span>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 6.292 4 4 0 010-6.292zM15 21H3v-1a6 6 0 0112 0v1z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <?php if(!empty($studentSearch)): ?>
                                        No students found matching "<span class="font-medium"><?php echo e($studentSearch); ?></span>".
                                    <?php else: ?>
                                        No students available to select.
                                    <?php endif; ?>
                                </p>
                                <?php if(!empty($studentSearch)): ?>
                                    <button type="button"
                                            wire:click="$set('studentSearch', '')"
                                            class="mt-2 text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 font-medium transition-colors duration-150">
                                        Clear search
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer with selection count -->
                    <?php if(count($students) > 0): ?>
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span><?php echo e(count($students)); ?> student<?php echo e(count($students) > 1 ? 's' : ''); ?> available</span>
                                
                                <span><?php echo e((is_array($selectedStudentIds) ? count($selectedStudentIds) : 0)); ?> selected</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Error Messages -->
                <?php $__errorArgs = ['selectedStudentIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 dark:text-red-400 text-xs mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['selectedStudentIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 dark:text-red-400 text-xs mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <?php echo e($message); ?>

                    </p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            

            
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <?php if(empty($criteriaConfig)): ?>
                    <div class="flex items-center p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-slate-800 dark:text-blue-300" role="alert">
                        <svg class="flex-shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">No specific criteria defined for this scholarship batch.</span> Please ensure all student data is up-to-date or configure criteria in batch settings.
                        </div>
                    </div>
                <?php elseif(empty($selectedStudentIds)): ?>
                    <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-slate-800 dark:text-yellow-300" role="alert">
                        <svg class="flex-shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">Please select one or more students</span> to enter their scholarship criteria information.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="space-y-10"> 
                        <?php $__currentLoopData = $this->allStudents->whereIn('id', $selectedStudentIds)->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg bg-gray-50 dark:bg-slate-800 mb-8 transition-all hover:shadow-xl">
                                <h3 class="flex items-center text-xl font-bold text-gray-900 dark:text-gray-50 mb-6 pb-3 border-b border-gray-300 dark:border-gray-600">
                                    <svg class="w-6 h-6 mr-3 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <?php echo e($student->name); ?>

                                    <span class="ml-3 text-sm font-medium text-gray-500 dark:text-gray-400">(NISN: <?php echo e($student->nisn); ?>)</span>
                                </h3>
                                <div class="space-y-6">
                                    <?php
                                        // Ensure $studentCriteriaValues[$student->id] is initialized if not set
                                        if (!isset($studentCriteriaValues[$student->id])) {
                                            $studentCriteriaValues[$student->id] = [];
                                        }
                                    ?>
                                    <?php $__empty_1 = true; $__currentLoopData = $criteriaConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $criterionId = $criterion['id'] ?? null;
                                            $studentId = $student->id;
                                            $wireModelPath = $criterionId ? "studentCriteriaValues.{$studentId}.{$criterionId}" : null;
                                            $inputFieldId = $criterionId ? "criteriaValues_{$studentId}_{$criterionId}" : "criteriaValues_{$studentId}_" . str()->random(4);

                                            // Ensure $studentCriteriaValues[$studentId][$criterionId] is initialized for file previews
                                            $existingFileValue = null;
                                            if(isset($studentCriteriaValues[$studentId]) && isset($studentCriteriaValues[$studentId][$criterionId])) {
                                                $existingFileValue = $studentCriteriaValues[$studentId][$criterionId];
                                            }

                                            $baseInputClasses = 'mt-1 block w-full rounded-md shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-opacity-50';
                                            $errorInputClasses = 'border-red-500 dark:border-red-400 focus:border-red-500 focus:ring-red-500';
                                            $normalFocusInputClasses = 'focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400';

                                            $baseFileInputClasses = 'mt-1 block w-full text-sm text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-opacity-50';
                                            // Error and Normal Focus for file input can reuse $errorInputClasses and $normalFocusInputClasses or be specific if needed

                                            $baseCheckboxClasses = 'h-4 w-4 text-indigo-600 rounded border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 focus:ring-opacity-50 dark:focus:ring-offset-slate-800';
                                            $errorCheckboxClasses = '!border-red-500 focus:!ring-red-500';
                                            $normalFocusCheckboxClasses = 'focus:ring-indigo-500';
                                        ?>

                                        <?php if($criterionId && $wireModelPath): ?>
                                            <div class="grid grid-cols-1 gap-y-2">
                                                <label for="<?php echo e($inputFieldId); ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    <?php echo e($criterion['name'] ?? 'Unnamed Criterion'); ?>

                                                    <?php if($criterion['is_required'] ?? false): ?> <span class="text-red-500 dark:text-red-400 font-semibold">*</span> <?php endif; ?>
                                                </label>

                                                <?php if(isset($criterion['description'])): ?>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 mb-1"><?php echo e($criterion['description']); ?></p>
                                                <?php endif; ?>

                                                <?php switch($criterion['data_type'] ?? 'text'):
                                                    case ('numeric'): ?>
                                                        <input type="number" step="any" wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>"
                                                               class="<?php echo e($baseInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                        <?php break; ?>

                                                    <?php case ('qualitative_option'): ?>
                                                        <?php if(!empty($criterion['options']) && is_array($criterion['options'])): ?>
                                                            <select wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>"
                                                                    class="<?php echo e($baseInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                                <option value="">-- Select <?php echo e($criterion['name'] ?? ''); ?> --</option>
                                                                <?php $__currentLoopData = $criterion['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($option['value']); ?>"><?php echo e($option['label']); ?></option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                        <?php else: ?>
                                                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">Error: Options not available for this select field (<?php echo e($criterion['name'] ?? 'Unnamed'); ?>).</p>
                                                            <input type="text" wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>" placeholder="Options missing, enter value manually"
                                                                   class="<?php echo e($baseInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                        <?php endif; ?>
                                                        <?php break; ?>

                                                    <?php case ('file'): ?>
                                                        <input type="file" wire:model.defer="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>"
                                                               class="<?php echo e($baseFileInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                        <div wire:loading wire:target="<?php echo e($wireModelPath); ?>" class="text-xs text-indigo-600 dark:text-indigo-400 mt-1.5 flex items-center">
                                                            <svg class="animate-spin h-4 w-4 text-indigo-500 dark:text-indigo-400 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                            Uploading...
                                                        </div>
                                                        <?php
                                                            $existingFileValue = null;
                                                            if(isset($studentCriteriaValues[$studentId]) && isset($studentCriteriaValues[$studentId][$criterionId])) {
                                                                $existingFileValue = $studentCriteriaValues[$studentId][$criterionId];
                                                            }
                                                        ?>
                                                        <?php if(!is_null($existingFileValue) && ($existingFileValue instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile || (is_string($existingFileValue) && !empty($existingFileValue)))): ?>
                                                            <div class="mt-1.5 text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                                                <svg class="w-4 h-4 inline mr-1.5 text-gray-500 dark:text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                                                <?php if($existingFileValue instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && method_exists($existingFileValue, 'getClientOriginalName')): ?>
                                                                    <span>Selected: <?php echo e($existingFileValue->getClientOriginalName()); ?></span>
                                                                    <?php if(method_exists($existingFileValue, 'getSize')): ?>
                                                                        <span class="ml-2 text-gray-500 dark:text-gray-500">(<?php echo e(round($existingFileValue->getSize() / 1024, 1)); ?> KB)</span>
                                                                    <?php endif; ?>
                                                                <?php elseif(is_string($existingFileValue)): ?>
                                                                    <span>Current file: <?php echo e(basename($existingFileValue)); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php break; ?>

                                                    <?php case ('boolean'): ?>
                                                        <div class="mt-2 relative flex items-start">
                                                            <div class="flex items-center h-5">
                                                                <input type="checkbox" wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>"
                                                                    class="<?php echo e($baseCheckboxClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorCheckboxClasses); ?> <?php else: ?> <?php echo e($normalFocusCheckboxClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                            </div>
                                                            <div class="ml-3 text-sm">
                                                                <label for="<?php echo e($inputFieldId); ?>" class="font-medium text-gray-700 dark:text-gray-300">Yes / Confirm</label>
                                                            </div>
                                                        </div>
                                                        <?php break; ?>

                                                    <?php case ('date'): ?>
                                                        <input type="date" wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>"
                                                               class="<?php echo e($baseInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                        <?php break; ?>

                                                    <?php case ('qualitative_text'): ?>
                                                        <input type="text" wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>"
                                                               list="<?php echo e($inputFieldId); ?>_datalist"
                                                               class="<?php echo e($baseInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                        <?php if(!empty($criterion['options']) && is_array($criterion['options'])): ?>
                                                            <datalist id="<?php echo e($inputFieldId); ?>_datalist">
                                                                <?php $__currentLoopData = $criterion['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($option['value']); ?>">
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </datalist>
                                                        <?php endif; ?>
                                                        <?php break; ?>

                                                    <?php default: ?> 
                                                        <?php if(isset($criterion['multiline']) && $criterion['multiline']): ?>
                                                            <textarea wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>" rows="3"
                                                                      class="<?php echo e($baseInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                                                        <?php else: ?>
                                                            <input type="text" wire:model.blur="<?php echo e($wireModelPath); ?>" id="<?php echo e($inputFieldId); ?>"
                                                                   class="<?php echo e($baseInputClasses); ?> <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($errorInputClasses); ?> <?php else: ?> <?php echo e($normalFocusInputClasses); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                        <?php endif; ?>
                                                <?php endswitch; ?>

                                                <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 dark:text-red-400 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        <?php else: ?>
                                            <?php if(!$criterionId): ?>
                                                <div class="my-2 p-3 text-xs text-red-700 bg-red-100 rounded-md dark:bg-red-900/50 dark:text-red-300 flex items-start" role="alert">
                                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                                    <div>
                                                        <strong class="font-semibold">Configuration Error:</strong> Criterion ID is missing for '<?php echo e($criterion['name'] ?? 'Unnamed Criterion'); ?>'. This input cannot be rendered.
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <p class="text-gray-500 dark:text-gray-400 px-1 italic">No criteria configured for this batch, or the configuration is invalid.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        

        
        <?php if($errors->any()): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                <strong>Validation Errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 sticky bottom-0 z-10 border-t border-gray-200 bg-opacity-95 backdrop-blur-sm">
            
            <?php if(app()->environment('local')): ?>
                <div class="flex items-center text-xs text-gray-500 mr-4">
                    Form Ready: <?php echo e($this->checkFormReady() ? 'Yes' : 'No'); ?>

                </div>
            <?php endif; ?>

            <a href="<?php echo e(route('teacher.scholarship-batches.open')); ?>" wire:navigate
                class="inline-flex items-center justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-100">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-100">
                <svg class="h-5 w-5 mr-2 -ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                Submit Applications
            </button>
        </div>
    </form>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div><?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views\livewire\teacher\submissions\create-student-submission-for-batch.blade.php ENDPATH**/ ?>