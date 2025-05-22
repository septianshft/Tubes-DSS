<div class="space-y-6">
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Create New Scholarship Batch</h1>
        <p class="text-sm text-gray-500">Fill in the batch details and define criteria for assessment.</p>
    </div>

    <form wire:submit.prevent="save" class="bg-white p-8 rounded-xl shadow space-y-8">

        {{-- Batch Details --}}
        <div class="space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Batch Details</h2>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Batch Name</label>
                <input type="text" wire:model.defer="name" id="name" placeholder="e.g., 2025 Spring Batch"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model.defer="description" id="description" rows="3"
                    placeholder="Brief description of the batch..."
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror"></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" wire:model.defer="start_date" id="start_date"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('start_date') border-red-500 @enderror">
                    @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" wire:model.defer="end_date" id="end_date"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('end_date') border-red-500 @enderror">
                    @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Criteria Section --}}
        <div class="space-y-4">
            <div class="border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800">Scholarship Criteria</h2>
                <p class="text-sm text-gray-500">Specify each criterion with its weight and type. The total weight of all criteria must be 1.0.</p>
            </div>

            @error('criteria_total_weight') <div class="mt-2 text-red-600 text-sm">{{ $message }}</div> @enderror
            @error('criteria') <div class="mt-2 text-red-600 text-sm">{{ $message }}</div> @enderror


            @foreach ($criteria as $index => $criterion)
                <div wire:key="{{ $criterion['component_id'] }}" class="p-6 border rounded-lg bg-gray-50 space-y-5">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-medium text-gray-700">{{ $criterion['display_name'] }} (Criterion {{ $index + 1 }})</h3>
                        @if (count($criteria) > 1)
                            <button type="button" wire:click="removeCriterion({{ $index }})"
                                    class="text-red-500 hover:text-red-700 text-sm font-semibold">
                                Remove
                            </button>
                        @endif
                    </div>

                    {{-- Criterion Name Input: Predefined or Custom --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="criteria_{{ $index }}_name_key" class="block text-sm font-medium text-gray-700">Predefined Name (Optional)</label>
                            <select wire:model.live="criteria.{{ $index }}.name_key" id="criteria_{{ $index }}_name_key"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('criteria.'.$index.'.name_key') border-red-500 @enderror">
                                <option value="">-- Select or type custom --</option>
                                @foreach ($availableCriteriaNames as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('criteria.'.$index.'.name_key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="criteria_{{ $index }}_custom_name_input" class="block text-sm font-medium text-gray-700">Or Custom Name</label>
                            <input type="text" wire:model.live="criteria.{{ $index }}.custom_name_input" id="criteria_{{ $index }}_custom_name_input"
                                   placeholder="e.g., Leadership Skills"
                                   class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('criteria.'.$index.'.custom_name_input') border-red-500 @enderror"
                                   {{ !empty($criteria[$index]['name_key']) ? 'disabled' : '' }}>
                            @error('criteria.'.$index.'.custom_name_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                     @error('criteria.'.$index.'.name_key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                     @error('criteria.'.$index.'.custom_name_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror


                    {{-- Weight and Type --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="criteria_{{ $index }}_weight" class="block text-sm font-medium text-gray-700">Weight (0.0 to 1.0)</label>
                            <input type="number" step="0.01" wire:model.defer="criteria.{{ $index }}.weight" id="criteria_{{ $index }}_weight"
                                   placeholder="e.g., 0.3"
                                   class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('criteria.'.$index.'.weight') border-red-500 @enderror">
                            @error('criteria.'.$index.'.weight') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="criteria_{{ $index }}_type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select wire:model.defer="criteria.{{ $index }}.type" id="criteria_{{ $index }}_type"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('criteria.'.$index.'.type') border-red-500 @enderror">
                                <option value="benefit">Benefit (Higher is better)</option>
                                <option value="cost">Cost (Lower is better)</option>
                            </select>
                            @error('criteria.'.$index.'.type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Data Type --}}
                    <div>
                        <label for="criteria_{{ $index }}_data_type" class="block text-sm font-medium text-gray-700">Data Type</label>
                        <select wire:model.live="criteria.{{ $index }}.data_type" id="criteria_{{ $index }}_data_type"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('criteria.'.$index.'.data_type') border-red-500 @enderror">
                            <option value="numeric">Numeric</option>
                            <option value="qualitative_option">Qualitative (Predefined Options)</option>
                            <option value="qualitative_text">Qualitative (Text to Value Map)</option>
                        </select>
                        @error('criteria.'.$index.'.data_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Conditional: Qualitative Options --}}
                    @if ($criterion['options_config_type'] === 'options')
                        <div class="mt-4 p-4 border rounded-md bg-gray-100 space-y-3">
                            <h4 class="text-md font-medium text-gray-700">Define Options</h4>
                            @error('criteria.'.$index.'.options') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @foreach ($criterion['options'] as $optIndex => $option)
                                <div wire:key="criterion-{{ $index }}-option-{{ $optIndex }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                    <div class="md:col-span-1">
                                        <label for="criteria_{{ $index }}_options_{{ $optIndex }}_label" class="block text-xs font-medium text-gray-600">Label</label>
                                        <input type="text" wire:model.defer="criteria.{{ $index }}.options.{{ $optIndex }}.label" id="criteria_{{ $index }}_options_{{ $optIndex }}_label"
                                               placeholder="e.g., Excellent"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm @error('criteria.'.$index.'.options.'.$optIndex.'.label') border-red-500 @enderror">
                                        @error('criteria.'.$index.'.options.'.$optIndex.'.label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="criteria_{{ $index }}_options_{{ $optIndex }}_value" class="block text-xs font-medium text-gray-600">Stored Value</label>
                                        <input type="text" wire:model.defer="criteria.{{ $index }}.options.{{ $optIndex }}.value" id="criteria_{{ $index }}_options_{{ $optIndex }}_value"
                                               placeholder="e.g., excellent"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm @error('criteria.'.$index.'.options.'.$optIndex.'.value') border-red-500 @enderror">
                                        @error('criteria.'.$index.'.options.'.$optIndex.'.value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="criteria_{{ $index }}_options_{{ $optIndex }}_numeric_value" class="block text-xs font-medium text-gray-600">Numeric Score</label>
                                        <input type="number" step="any" wire:model.defer="criteria.{{ $index }}.options.{{ $optIndex }}.numeric_value" id="criteria_{{ $index }}_options_{{ $optIndex }}_numeric_value"
                                               placeholder="e.g., 5"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm @error('criteria.'.$index.'.options.'.$optIndex.'.numeric_value') border-red-500 @enderror">
                                        @error('criteria.'.$index.'.options.'.$optIndex.'.numeric_value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-1">
                                        <button type="button" wire:click="removeOption({{ $index }}, {{ $optIndex }})"
                                                class="px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 text-xs w-full">
                                            Remove Option
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addOption({{ $index }})"
                                    class="mt-2 px-3 py-1.5 bg-sky-500 text-white rounded hover:bg-sky-600 text-xs">
                                + Add Option
                            </button>
                        </div>
                    @endif

                    {{-- Conditional: Qualitative Text (Value Map) --}}
                    @if ($criterion['options_config_type'] === 'value_map')
                        <div class="mt-4 p-4 border rounded-md bg-gray-100 space-y-3">
                            <h4 class="text-md font-medium text-gray-700">Define Text to Value Mappings</h4>
                             @error('criteria.'.$index.'.value_map') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @foreach ($criterion['value_map'] as $mapIndex => $mapEntry)
                                <div wire:key="criterion-{{ $index }}-valuemap-{{ $mapIndex }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                                    <div class="md:col-span-1">
                                        <label for="criteria_{{ $index }}_value_map_{{ $mapIndex }}_key" class="block text-xs font-medium text-gray-600">Text Input</label>
                                        <input type="text" wire:model.defer="criteria.{{ $index }}.value_map.{{ $mapIndex }}.key_input" id="criteria_{{ $index }}_value_map_{{ $mapIndex }}_key"
                                               placeholder="e.g., High"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm @error('criteria.'.$index.'.value_map.'.$mapIndex.'.key_input') border-red-500 @enderror">
                                        @error('criteria.'.$index.'.value_map.'.$mapIndex.'.key_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-1">
                                        <label for="criteria_{{ $index }}_value_map_{{ $mapIndex }}_value" class="block text-xs font-medium text-gray-600">Numeric Score</label>
                                        <input type="number" step="any" wire:model.defer="criteria.{{ $index }}.value_map.{{ $mapIndex }}.value_input" id="criteria_{{ $index }}_value_map_{{ $mapIndex }}_value"
                                               placeholder="e.g., 3"
                                               class="mt-1 block w-full py-1 px-2 border border-gray-300 rounded-md shadow-sm sm:text-sm @error('criteria.'.$index.'.value_map.'.$mapIndex.'.value_input') border-red-500 @enderror">
                                        @error('criteria.'.$index.'.value_map.'.$mapIndex.'.value_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-1">
                                        <button type="button" wire:click="removeValueMapEntry({{ $index }}, {{ $mapIndex }})"
                                                class="px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 text-xs w-full">
                                            Remove Entry
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addValueMapEntry({{ $index }})"
                                    class="mt-2 px-3 py-1.5 bg-sky-500 text-white rounded hover:bg-sky-600 text-xs">
                                + Add Value Map Entry
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach

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

        {{-- Actions --}}
        <div class="flex justify-end gap-3 border-t pt-6">
            <a href="{{ route('admin.scholarship-batches.index') }}" wire:navigate
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
