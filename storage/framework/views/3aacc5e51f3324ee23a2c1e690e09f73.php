<div class="space-y-8">
    <div class="mb-8 border-b border-gray-200 pb-5">
        <h1 class="text-3xl font-bold leading-tight text-gray-900">Edit Scholarship Batch</h1>
        <p class="mt-1 text-sm text-gray-600">Update the batch details and criteria for assessment. Ensure all information is accurate.</p>
    </div>

    <form wire:submit.prevent="update" class="space-y-10">

        
        <section class="bg-white p-6 sm:p-8 rounded-xl shadow-lg space-y-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-3">Batch Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Batch Name</label>
                    <input type="text" wire:model.lazy="name" id="name" placeholder="e.g., 2025 Spring Scholarship Program"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-2 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div></div>

                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model.lazy="description" id="description" rows="4"
                        placeholder="Provide a brief overview of this scholarship batch, its goals, and target applicants."
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-2 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Application Start Date</label>
                    <input type="date" wire:model.lazy="start_date" id="start_date"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-2 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Application End Date</label>
                    <input type="date" wire:model.lazy="end_date" id="end_date"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-2 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </section>

        
        <section class="bg-white p-6 sm:p-8 rounded-xl shadow-lg space-y-6 border border-gray-200">
            <div class="border-b pb-3">
                <h2 class="text-xl font-semibold text-gray-800">Scholarship Criteria Configuration</h2>
                <p class="text-sm text-gray-600 mt-1">Define the criteria for evaluating submissions. The total weight of all criteria must sum to 1.0.</p>
            </div>

            <?php if($errors->has('criteria_total_weight') || $errors->has('criteria')): ?>
                <div class="rounded-md bg-red-50 p-4 my-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm-1 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul role="list" class="list-disc pl-5 space-y-1">
                                    <?php $__errorArgs = ['criteria_total_weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <li><?php echo e($message); ?></li> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <?php $__errorArgs = ['criteria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <li><?php echo e($message); ?></li> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="space-y-6">
                <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div wire:key="<?php echo e($criterion['component_id']); ?>" class="p-6 border border-gray-200 rounded-lg bg-gray-50/50 space-y-5 shadow-sm hover:shadow-md transition-shadow duration-150">
                        <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
                            <h3 class="text-lg font-semibold text-indigo-700"><?php echo e($criterion['display_name']); ?> (Criterion #<?php echo e($index + 1); ?>)</h3>
                            <?php if(count($criteria) > 1): ?>
                                <button type="button" wire:click="removeCriterion(<?php echo e($index); ?>)"
                                        class="text-sm font-medium text-red-600 hover:text-red-800 transition-colors duration-150 ease-in-out inline-flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remove
                                </button>
                            <?php endif; ?>
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="criteria_<?php echo e($index); ?>_name_key" class="block text-sm font-medium text-gray-700 mb-1">Predefined Name <span class="text-gray-400">(Optional)</span></label>
                                <select wire:model.live="criteria.<?php echo e($index); ?>.name_key" id="criteria_<?php echo e($index); ?>_name_key"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.name_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Select or type custom below --</option>
                                    <?php $__currentLoopData = $availableCriteriaNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['criteria.'.$index.'.name_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="criteria_<?php echo e($index); ?>_custom_name_input" class="block text-sm font-medium text-gray-700 mb-1">Or Custom Name <span class="text-red-500 text-xs">*</span></label>
                                <input type="text" wire:model.live.debounce.300ms="criteria.<?php echo e($index); ?>.custom_name_input" id="criteria_<?php echo e($index); ?>_custom_name_input"
                                       placeholder="e.g., Leadership Experience"
                                       class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.custom_name_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       <?php echo e(!empty($criteria[$index]['name_key']) ? 'disabled' : ''); ?>>
                                <?php $__errorArgs = ['criteria.'.$index.'.custom_name_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <?php if($errors->has("criteria.{$index}.name_key") && $errors->has("criteria.{$index}.custom_name_input")): ?>
                            <p class="text-red-600 text-xs">Please select a predefined name or enter a custom name for the criterion.</p>
                        <?php endif; ?>

                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 pt-3">
                            <div>
                                <label for="criteria_<?php echo e($index); ?>_weight" class="block text-sm font-medium text-gray-700 mb-1">Weight <span class="text-gray-400">(0.0-1.0)</span> <span class="text-red-500 text-xs">*</span></label>
                                <input type="number" step="0.01" wire:model.lazy="criteria.<?php echo e($index); ?>.weight" id="criteria_<?php echo e($index); ?>_weight"
                                       placeholder="e.g., 0.30"
                                       class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php $__errorArgs = ['criteria.'.$index.'.weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="criteria_<?php echo e($index); ?>_type" class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500 text-xs">*</span></label>
                                <select wire:model.lazy="criteria.<?php echo e($index); ?>.type" id="criteria_<?php echo e($index); ?>_type"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="benefit">Benefit (Higher is better)</option>
                                    <option value="cost">Cost (Lower is better)</option>
                                </select>
                                <?php $__errorArgs = ['criteria.'.$index.'.type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="criteria_<?php echo e($index); ?>_data_type" class="block text-sm font-medium text-gray-700 mb-1">Data Type <span class="text-red-500 text-xs">*</span></label>
                                <select wire:model.live="criteria.<?php echo e($index); ?>.data_type" id="criteria_<?php echo e($index); ?>_data_type"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.data_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="numeric">Numeric (e.g., GPA, Score)</option>
                                    <option value="qualitative_option">Qualitative - Options (e.g., Good, Fair, Poor)</option>
                                    <option value="qualitative_text">Qualitative - Text Map (e.g., Yes=1, No=0)</option>
                                </select>
                                <?php $__errorArgs = ['criteria.'.$index.'.data_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-red-600 text-xs"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        
                        <?php if($criterion['options_config_type'] === 'options'): ?>
                            <div class="mt-5 p-5 border border-indigo-200 rounded-md bg-indigo-50/30 space-y-4 shadow-inner">
                                <h4 class="text-md font-semibold text-indigo-800">Define Options for '<?php echo e($criterion['display_name']); ?>'</h4>
                                <p class="text-xs text-gray-600">Define the selectable options and their corresponding numeric values for assessment.</p>
                                <?php $__errorArgs = ["criteria.{$index}.options"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="my-2 text-red-600 text-xs bg-red-100 p-2 rounded-md"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php $__currentLoopData = $criterion['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div wire:key="criterion-<?php echo e($index); ?>-option-<?php echo e($optIndex); ?>" class="grid grid-cols-1 md:grid-cols-11 gap-x-4 gap-y-3 items-end p-3 bg-white rounded-md border border-gray-200">
                                        <div class="md:col-span-5">
                                            <label for="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_label" class="block text-xs font-medium text-gray-600 mb-0.5">Option Label <span class="text-red-500 text-xs">*</span></label>
                                            <input type="text" wire:model.lazy="criteria.<?php echo e($index); ?>.options.<?php echo e($optIndex); ?>.label" id="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_label"
                                                   placeholder="e.g., Excellent"
                                                   class="mt-1 block w-full py-1.5 px-2.5 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <?php $__errorArgs = ["criteria.{$index}.options.{$optIndex}.label"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_value" class="block text-xs font-medium text-gray-600 mb-0.5">Stored Value <span class="text-red-500 text-xs">*</span></label>
                                            <input type="text" wire:model.lazy="criteria.<?php echo e($index); ?>.options.<?php echo e($optIndex); ?>.value" id="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_value"
                                                   placeholder="e.g., excellent" title="This value is stored in the database. Usually a slug or short code."
                                                   class="mt-1 block w-full py-1.5 px-2.5 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <?php $__errorArgs = ["criteria.{$index}.options.{$optIndex}.value"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_numeric_value" class="block text-xs font-medium text-gray-600 mb-0.5">Numeric Value <span class="text-red-500 text-xs">*</span></label>
                                            <input type="number" step="any" wire:model.lazy="criteria.<?php echo e($index); ?>.options.<?php echo e($optIndex); ?>.numeric_value" id="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_numeric_value"
                                                   placeholder="e.g., 5"
                                                   class="mt-1 block w-full py-1.5 px-2.5 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.numeric_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <?php $__errorArgs = ["criteria.{$index}.options.{$optIndex}.numeric_value"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="md:col-span-2 flex items-end justify-end">
                                            <?php if(count($criterion['options']) > 1): ?>
                                                <button type="button" wire:click="removeOption(<?php echo e($index); ?>, <?php echo e($optIndex); ?>)"
                                                        class="text-red-500 hover:text-red-700 text-xs font-medium p-1.5 rounded-md hover:bg-red-50 transition-colors duration-150 ease-in-out">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" wire:click="addOption(<?php echo e($index); ?>)"
                                        class="mt-3 px-3 py-1.5 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 text-xs font-medium inline-flex items-center transition-colors duration-150 ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    Add Option
                                </button>
                            </div>
                        <?php endif; ?>

                        
                        <?php if($criterion['options_config_type'] === 'value_map'): ?>
                            <div class="mt-5 p-5 border border-teal-200 rounded-md bg-teal-50/30 space-y-4 shadow-inner">
                                <h4 class="text-md font-semibold text-teal-800">Define Text to Value Mappings for '<?php echo e($criterion['display_name']); ?>'</h4>
                                <p class="text-xs text-gray-600">Map specific text inputs (case-sensitive) to numeric values for assessment.</p>
                                <?php $__errorArgs = ["criteria.{$index}.value_map"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="my-2 text-red-600 text-xs bg-red-100 p-2 rounded-md"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php $__currentLoopData = $criterion['value_map']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mapIndex => $mapEntry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div wire:key="criterion-<?php echo e($index); ?>-valuemap-<?php echo e($mapIndex); ?>" class="grid grid-cols-1 md:grid-cols-11 gap-x-4 gap-y-3 items-end p-3 bg-white rounded-md border border-gray-200">
                                        <div class="md:col-span-5">
                                            <label for="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_key" class="block text-xs font-medium text-gray-600 mb-0.5">Text Input (Exact Match) <span class="text-red-500 text-xs">*</span></label>
                                            <input type="text" wire:model.lazy="criteria.<?php echo e($index); ?>.value_map.<?php echo e($mapIndex); ?>.key_input" id="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_key"
                                                   placeholder="e.g., 'Yes' or 'Active Member'"
                                                   class="mt-1 block w-full py-1.5 px-2.5 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-teal-500 focus:border-teal-500 <?php $__errorArgs = ['criteria.'.$index.'.value_map.'.$mapIndex.'.key_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <?php $__errorArgs = ["criteria.{$index}.value_map.{$mapIndex}.key_input"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="md:col-span-4">
                                            <label for="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_value" class="block text-xs font-medium text-gray-600 mb-0.5">Corresponding Numeric Value <span class="text-red-500 text-xs">*</span></label>
                                            <input type="number" step="any" wire:model.lazy="criteria.<?php echo e($index); ?>.value_map.<?php echo e($mapIndex); ?>.value_input" id="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_value"
                                                   placeholder="e.g., 1 or 0.75"
                                                   class="mt-1 block w-full py-1.5 px-2.5 border border-gray-300 rounded-md shadow-sm sm:text-sm focus:ring-teal-500 focus:border-teal-500 <?php $__errorArgs = ['criteria.'.$index.'.value_map.'.$mapIndex.'.value_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <?php $__errorArgs = ["criteria.{$index}.value_map.{$mapIndex}.value_input"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="md:col-span-2 flex items-end justify-end">
                                            <?php if(count($criterion['value_map']) > 1): ?>
                                                <button type="button" wire:click="removeValueMapEntry(<?php echo e($index); ?>, <?php echo e($mapIndex); ?>)"
                                                        class="text-red-500 hover:text-red-700 text-xs font-medium p-1.5 rounded-md hover:bg-red-50 transition-colors duration-150 ease-in-out">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" wire:click="addValueMapEntry(<?php echo e($index); ?>)"
                                        class="mt-3 px-3 py-1.5 bg-teal-500 text-white rounded-md hover:bg-teal-600 text-xs font-medium inline-flex items-center transition-colors duration-150 ease-in-out">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    Add Text-Value Map
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="pt-4">
                <button type="button" wire:click="addCriterion" wire:loading.attr="disabled" wire:target="addCriterion"
                        class="w-full sm:w-auto px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 text-sm font-medium disabled:opacity-60 disabled:cursor-not-allowed transition-colors duration-150 ease-in-out inline-flex items-center justify-center">
                    <svg wire:loading wire:target="addCriterion" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="addCriterion" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span wire:loading wire:target="addCriterion">Adding Criterion...</span>
                    <span wire:loading.remove wire:target="addCriterion">Add Another Criterion</span>
                </button>
            </div>
        </section>

        
        <section class="flex flex-col sm:flex-row justify-end items-center gap-3 border-t border-gray-200 pt-6 mt-8">
            <a href="<?php echo e(route('admin.scholarship-batches.index')); ?>" wire:navigate
                class="w-full sm:w-auto px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm font-medium transition-colors duration-150 ease-in-out text-center">
                Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" wire:target="update"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 text-sm font-medium disabled:opacity-60 disabled:cursor-wait transition-colors duration-150 ease-in-out">
                <svg wire:loading wire:target="update" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg wire:loading.remove wire:target="update" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                </svg>
                <span wire:loading wire:target="update">Updating Batch...</span>
                <span wire:loading.remove wire:target="update">Save Changes</span>
            </button>
        </section>
    </form>
</div><?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views\livewire\admin\scholarship-batches\edit-scholarship-batch.blade.php ENDPATH**/ ?>