<div>
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Submit Students for: <?php echo e($batch->name); ?></h1>
        <p class="text-sm text-gray-500 mt-1">Select one or more students and provide the common required information for this scholarship batch.</p>
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
            
            <div x-data="{ open: false }" class="relative">
                <label for="student_search_button" class="block text-sm font-medium text-gray-700">Select Students *</label>

                
                <div class="mt-1 mb-2 min-h-[44px] p-2 border rounded-md border-gray-300 flex flex-wrap gap-2">
                    <?php $__empty_1 = true; $__currentLoopData = $this->allStudents->whereIn('id', $selectedStudentIds); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                            <?php echo e($student->name); ?>

                            <button type="button" wire:click="deselectStudent(<?php echo e($student->id); ?>)" title="Remove <?php echo e($student->name); ?>"
                                    class="ml-1.5 flex-shrink-0 text-indigo-500 hover:text-indigo-700 focus:outline-none">
                                <svg class="h-3 w-3" stroke="currentColor" fill="none" viewBox="0 0 8 8"><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" /></svg>
                            </button>
                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="text-gray-500 text-sm p-1">No students selected</span>
                    <?php endif; ?>
                </div>

                <!-- Custom Select Button -->
                <button @click="open = !open" type="button" id="student_search_button"
                        class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <span class="block truncate">
                        Click to select or search students...
                    </span>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <!-- Dropdown Panel -->
                <div x-show="open" @click.away="open = false" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-72 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                    <div class="p-2">
                        <input type="search" wire:model.live.debounce.300ms="studentSearch" placeholder="Search by name or NISN..."
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-10 px-3">
                    </div>
                    <ul class="max-h-56 overflow-y-auto">
                        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li>
                                <label for="student-<?php echo e($student->id); ?>"
                                       class="flex items-center px-3 py-2 hover:bg-indigo-50 cursor-pointer text-gray-900">
                                    <input type="checkbox"
                                           id="student-<?php echo e($student->id); ?>"
                                           wire:model.live="selectedStudentIds"
                                           value="<?php echo e($student->id); ?>"
                                           class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded border-gray-300 focus:ring-indigo-500">
                                    <span class="ml-3 block text-sm font-normal">
                                        <?php echo e($student->name); ?> <span class="text-gray-500"> (NISN: <?php echo e($student->nisn); ?>)</span>
                                    </span>
                                </label>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="px-3 py-2 text-center text-gray-500 text-sm">
                                <?php if(!empty($studentSearch)): ?>
                                    No students found matching "<?php echo e($studentSearch); ?>".
                                <?php else: ?>
                                    No students available to select.
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php $__errorArgs = ['selectedStudentIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['selectedStudentIds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            

            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <?php if(empty($criteriaConfig)): ?>
                    <p class="text-gray-600 px-1">No specific criteria defined for this scholarship batch. Please ensure all student data is up-to-date.</p>
                <?php elseif(empty($selectedStudentIds)): ?>
                    <p class="text-gray-600 px-1">Please select one or more students to enter their scholarship criteria information.</p>
                <?php else: ?>
                    <div class="space-y-8"> 
                        <?php $__currentLoopData = $this->allStudents->whereIn('id', $selectedStudentIds)->sortBy('name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div wire:key="student-criteria-block-<?php echo e($student->id); ?>" class="p-4 border border-gray-300 rounded-lg shadow-sm bg-white">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-3">
                                    Criteria for: <span class="font-bold text-indigo-600"><?php echo e($student->name); ?></span>
                                    <span class="text-sm text-gray-500 ml-2">(NISN: <?php echo e($student->nisn); ?>)</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                    <?php $__currentLoopData = $criteriaConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(isset($criterion['id']) && isset($criterion['name'])): ?>
                                            <?php
                                                $studentId = $student->id;
                                                $criterionId = $criterion['id'];
                                                $wireModelPath = "studentCriteriaValues.{$studentId}.{$criterionId}";
                                                $elementId = "student_{$studentId}_criterion_{$criterionId}";
                                            ?>
                                            <div wire:key="student-<?php echo e($studentId); ?>-criterion-<?php echo e($criterionId); ?>">
                                                <label for="<?php echo e($elementId); ?>" class="block text-sm font-medium text-gray-700">
                                                    <?php echo e($criterion['name']); ?>

                                                    <?php if(strpos($criterion['rules'] ?? '', 'required') !== false): ?>
                                                        <span class="text-red-500 font-semibold">*</span>
                                                    <?php endif; ?>
                                                </label>
                                                <?php $inputType = $criterion['type'] ?? 'text'; ?>

                                                <?php if($inputType === 'select' && isset($criterion['options'])): ?>
                                                    <select wire:model.lazy="<?php echo e($wireModelPath); ?>" id="<?php echo e($elementId); ?>"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                        <option value="">-- Select <?php echo e($criterion['name']); ?> --</option>
                                                        <?php $__currentLoopData = $criterion['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($optionValue); ?>"><?php echo e($optionLabel); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                <?php elseif($inputType === 'textarea'): ?>
                                                    <textarea wire:model.lazy="<?php echo e($wireModelPath); ?>" id="<?php echo e($elementId); ?>" rows="3"
                                                              placeholder="Enter <?php echo e(strtolower($criterion['name'])); ?>"
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"></textarea>
                                                <?php else: ?> 
                                                    <input type="<?php echo e($inputType); ?>" wire:model.lazy="<?php echo e($wireModelPath); ?>" id="<?php echo e($elementId); ?>"
                                                           placeholder="Enter <?php echo e(strtolower($criterion['name'])); ?>"
                                                           <?php if(isset($criterion['min'])): ?> min="<?php echo e($criterion['min']); ?>" <?php endif; ?>
                                                           <?php if(isset($criterion['max'])): ?> max="<?php echo e($criterion['max']); ?>" <?php endif; ?>
                                                           <?php if(isset($criterion['step'])): ?> step="<?php echo e($criterion['step']); ?>" <?php endif; ?>
                                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                <?php endif; ?>

                                                <?php if(isset($criterion['description'])): ?>
                                                    <p class="mt-1 text-xs text-gray-500"><?php echo e($criterion['description']); ?></p>
                                                <?php endif; ?>
                                                <?php $__errorArgs = [$wireModelPath];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-xs text-red-600"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <a href="<?php echo e(route('teacher.scholarship-batches.open')); ?>" wire:navigate
                class="inline-flex items-center justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Submit Applications
            </button>
        </div>
    </form>
</div><?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views\livewire\teacher\submissions\create-student-submission-for-batch.blade.php ENDPATH**/ ?>