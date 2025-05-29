<div class="space-y-6">
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Create New Scholarship Batch</h1>
        <p class="text-sm text-gray-500">Fill in the batch details and define criteria for assessment.</p>
    </div>

    <form wire:submit.prevent="save" class="bg-white p-8 rounded-xl shadow space-y-8">

        
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Batch Details</h2>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Batch Name</label>
                <input type="text" wire:model.defer="name" id="name" placeholder="e.g., 2025 Spring Batch"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model.defer="description" id="description" rows="3"
                    placeholder="Brief description of the batch..."
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" wire:model.defer="start_date" id="start_date"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" wire:model.defer="end_date" id="end_date"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>

        
        <div class="space-y-4">
            <div class="border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800">Scholarship Criteria</h2>
                <p class="text-sm text-gray-500">Specify each criterion with its weight and type. The total weight of all criteria must be 1.0.</p>
            </div>

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria_total_weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-red-600 text-sm"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-red-600 text-sm"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->


            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $criterion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div wire:key="<?php echo e($criterion['component_id']); ?>" class="p-6 border rounded-lg bg-gray-50 space-y-5">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-medium text-gray-700"><?php echo e($criterion['display_name']); ?> (Criterion <?php echo e($index + 1); ?>)</h3>
                        <!--[if BLOCK]><![endif]--><?php if(count($criteria) > 1): ?>
                            <button type="button" wire:click="removeCriterion(<?php echo e($index); ?>)"
                                    class="text-red-500 hover:text-red-700 text-sm font-semibold">
                                Remove
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="criteria_<?php echo e($index); ?>_name_key" class="block text-sm font-medium text-gray-700">Predefined Name (Optional)</label>
                            <select wire:model.live="criteria.<?php echo e($index); ?>.name_key" id="criteria_<?php echo e($index); ?>_name_key"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.name_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Select or type custom --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableCriteriaNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.name_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="criteria_<?php echo e($index); ?>_custom_name_input" class="block text-sm font-medium text-gray-700">Or Custom Name</label>
                            <input type="text" wire:model.live="criteria.<?php echo e($index); ?>.custom_name_input" id="criteria_<?php echo e($index); ?>_custom_name_input"
                                   placeholder="e.g., Leadership Skills"
                                   class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.custom_name_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   <?php echo e(!empty($criteria[$index]['name_key']) ? 'disabled' : ''); ?>>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.custom_name_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.name_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.custom_name_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->


                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="criteria_<?php echo e($index); ?>_weight" class="block text-sm font-medium text-gray-700">Weight (0.0 to 1.0)</label>
                            <input type="number" step="0.01" wire:model.defer="criteria.<?php echo e($index); ?>.weight" id="criteria_<?php echo e($index); ?>_weight"
                                   placeholder="e.g., 0.3"
                                   class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label for="criteria_<?php echo e($index); ?>_type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select wire:model.defer="criteria.<?php echo e($index); ?>.type" id="criteria_<?php echo e($index); ?>_type"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="benefit">Benefit (Higher is better)</option>
                                <option value="cost">Cost (Lower is better)</option>
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    
                    <div>
                        <label for="criteria_<?php echo e($index); ?>_data_type" class="block text-sm font-medium text-gray-700">Data Type</label>
                        <select wire:model.live="criteria.<?php echo e($index); ?>.data_type" id="criteria_<?php echo e($index); ?>_data_type"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.data_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="numeric">Numeric</option>
                            <option value="qualitative_option">Qualitative (Predefined Options)</option>
                            <option value="qualitative_text">Qualitative (Text to Value Map)</option>
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.data_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <!--[if BLOCK]><![endif]--><?php if($criterion['options_config_type'] === 'options'): ?>
                        <div class="mt-4 p-4 border rounded-md bg-gray-100 space-y-3">
                            <h4 class="text-md font-medium text-gray-700">Define Options</h4>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.options'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $criterion['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div wire:key="criterion-<?php echo e($index); ?>-option-<?php echo e($optIndex); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                    <div class="md:col-span-1">
                                        <label for="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_label" class="block text-xs font-medium text-gray-600">Label</label>
                                        <input type="text" wire:model.defer="criteria.<?php echo e($index); ?>.options.<?php echo e($optIndex); ?>.label" id="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_label"
                                               placeholder="e.g., Excellent"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_value" class="block text-xs font-medium text-gray-600">Stored Value</label>
                                        <input type="text" wire:model.defer="criteria.<?php echo e($index); ?>.options.<?php echo e($optIndex); ?>.value" id="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_value"
                                               placeholder="e.g., excellent"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_numeric_value" class="block text-xs font-medium text-gray-600">Numeric Score</label>
                                        <input type="number" step="any" wire:model.defer="criteria.<?php echo e($index); ?>.options.<?php echo e($optIndex); ?>.numeric_value" id="criteria_<?php echo e($index); ?>_options_<?php echo e($optIndex); ?>_numeric_value"
                                               placeholder="e.g., 5"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.numeric_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.options.'.$optIndex.'.numeric_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="md:col-span-1">
                                        <button type="button" wire:click="removeOption(<?php echo e($index); ?>, <?php echo e($optIndex); ?>)"
                                                class="px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 text-xs w-full">
                                            Remove Option
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            <button type="button" wire:click="addOption(<?php echo e($index); ?>)"
                                    class="mt-2 px-3 py-1.5 bg-sky-500 text-white rounded hover:bg-sky-600 text-xs">
                                + Add Option
                            </button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <!--[if BLOCK]><![endif]--><?php if($criterion['options_config_type'] === 'value_map'): ?>
                        <div class="mt-4 p-4 border rounded-md bg-gray-100 space-y-3">
                            <h4 class="text-md font-medium text-gray-700">Define Text to Value Mappings</h4>
                             <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.value_map'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $criterion['value_map']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mapIndex => $mapEntry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div wire:key="criterion-<?php echo e($index); ?>-valuemap-<?php echo e($mapIndex); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                                    <div class="md:col-span-1">
                                        <label for="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_key" class="block text-xs font-medium text-gray-600">Text Input</label>
                                        <input type="text" wire:model.defer="criteria.<?php echo e($index); ?>.value_map.<?php echo e($mapIndex); ?>.key_input" id="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_key"
                                               placeholder="e.g., High"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.value_map.'.$mapIndex.'.key_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.value_map.'.$mapIndex.'.key_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_value" class="block text-xs font-medium text-gray-600">Numeric Score</label>
                                        <input type="number" step="any" wire:model.defer="criteria.<?php echo e($index); ?>.value_map.<?php echo e($mapIndex); ?>.value_input" id="criteria_<?php echo e($index); ?>_value_map_<?php echo e($mapIndex); ?>_value"
                                               placeholder="e.g., 3"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm <?php $__errorArgs = ['criteria.'.$index.'.value_map.'.$mapIndex.'.value_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['criteria.'.$index.'.value_map.'.$mapIndex.'.value_input'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="md:col-span-1">
                                        <button type="button" wire:click="removeValueMapEntry(<?php echo e($index); ?>, <?php echo e($mapIndex); ?>)"
                                                class="px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 text-xs w-full">
                                            Remove Entry
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            <button type="button" wire:click="addValueMapEntry(<?php echo e($index); ?>)"
                                    class="mt-2 px-3 py-1.5 bg-sky-500 text-white rounded hover:bg-sky-600 text-xs">
                                + Add Value Map Entry
                            </button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

            <button type="button" wire:click="addCriterion"
                    wire:loading.attr="disabled" wire:target="addCriterion"
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <svg wire:loading wire:target="addCriterion" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading wire:target="addCriterion">Adding...</span>
                <span wire:loading.remove wire:target="addCriterion">+ Add Another Criterion</span>
            </button>
        </div>

        
        <div class="flex justify-end gap-3 border-t pt-6">
            <a href="<?php echo e(route('admin.scholarship-batches.index')); ?>" wire:navigate
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm disabled:opacity-75 disabled:cursor-wait">
                <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading wire:target="save">Saving Batch...</span>
                <span wire:loading.remove wire:target="save">Save Batch</span>
            </button>
        </div>
    </form>
</div>
<?php /**PATH D:\Data kuiah\SMT 8\SPK\TUBES\prototype-4\resources\views/livewire/admin/scholarship-batches/create-scholarship-batch.blade.php ENDPATH**/ ?>