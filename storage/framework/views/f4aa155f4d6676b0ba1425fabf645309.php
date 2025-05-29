<div>    <div class="mb-6 pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Edit Student</h1>
        <p class="text-sm text-gray-500 mt-1">Update the details for <?php echo e($student->name ?: 'Student'); ?>. Required fields are marked with an asterisk (*)</p>
    </div>

    <form wire:submit.prevent="update" class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Personal Information Section -->
        <div class="border-b border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" wire:model="name" id="name" placeholder="Student's full name"
                            class="mt-1 block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label for="nisn" class="block text-sm font-medium text-gray-700">NISN (National Student ID Number) *</label>
                        <input type="text" wire:model="nisn" id="nisn" placeholder="e.g., 1234567890"
                            class="mt-1 block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nisn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                        <input type="date" wire:model="date_of_birth" id="date_of_birth"
                            class="mt-1 block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address *</label>
                        <textarea wire:model="address" id="address" rows="3" placeholder="Student's complete address"
                            class="mt-1 block w-full rounded-md border-gray-300 text-base px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Performance Section -->
        <div class="border-b border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Academic Performance</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="average_score" class="block text-sm font-medium text-gray-700">Average Score</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" step="0.01" wire:model="average_score" id="average_score" placeholder="0-100"
                                class="block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">/100</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Student's average academic score (0-100)</p>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['average_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label for="class_attendance_percentage" class="block text-sm font-medium text-gray-700">Class Attendance (%)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" step="0.01" wire:model="class_attendance_percentage" id="class_attendance_percentage" placeholder="0-100"
                                class="block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Percentage of classes the student has attended</p>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['class_attendance_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label for="tuition_payment_delays" class="block text-sm font-medium text-gray-700">Tuition Payment Delays</label>
                        <input type="number" wire:model="tuition_payment_delays" id="tuition_payment_delays" min="0" placeholder="Number of delays"
                            class="mt-1 block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Number of times tuition payment has been delayed</p>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['tuition_payment_delays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Extracurricular Activities Section -->
        <div>
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Extracurricular Activities</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="extracurricular_position" class="block text-sm font-medium text-gray-700">Position in Activities</label>
                        <input type="text" wire:model="extracurricular_position" id="extracurricular_position" placeholder="e.g., Team Captain, Club President"
                            class="mt-1 block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Student's position or role in extracurricular activities</p>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['extracurricular_position'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label for="extracurricular_activeness" class="block text-sm font-medium text-gray-700">Activeness Level</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" wire:model="extracurricular_activeness" id="extracurricular_activeness"
                                placeholder="0-100" min="0" max="100"
                                class="block w-full rounded-md border-gray-300 text-base h-11 px-4 focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">/100</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Student's participation level in extracurricular activities (0-100)</p>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['extracurricular_activeness'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <a href="<?php echo e(route('teacher.students.index')); ?>" wire:navigate
                class="inline-flex items-center justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Student
            </button>
        </div>
    </form>
</div>
<?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views/livewire/teacher/students/edit-student.blade.php ENDPATH**/ ?>