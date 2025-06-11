<div class="space-y-8">
    <div class="mb-8 border-b pb-6 dark:border-gray-700">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-50">Create New Scholarship Batch</h1>
        <p class="mt-2 text-base text-gray-600 dark:text-gray-400">Fill in the batch details and define criteria for assessment. Ensure all information is accurate.</p>
    </div>

    <form wire:submit.prevent="save" class="bg-white dark:bg-gray-800/50 p-8 rounded-xl shadow-xl space-y-10 ring-1 ring-gray-200 dark:ring-gray-700">

        {{-- Batch Details --}}
        <fieldset class="space-y-6 border-b dark:border-gray-700 pb-8">
            <legend class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Batch Information</legend>

            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Batch Name</label>
                    <div class="mt-2">
                        <input type="text" wire:model.defer="name" id="name" placeholder="e.g., 2025 Spring Scholarship"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('name') border-red-500 ring-red-500 @enderror">
                    </div>
                    @error('name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-6">
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Description</label>
                    <div class="mt-2">
                        <textarea wire:model.defer="description" id="description" rows="4"
                            placeholder="Provide a brief overview of this scholarship batch, its purpose, and target applicants."
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('description') border-red-500 ring-red-500 @enderror"></textarea>
                    </div>
                    @error('description') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="start_date" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Start Date</label>
                    <div class="mt-2">
                        <input type="date" wire:model.defer="start_date" id="start_date"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('start_date') border-red-500 ring-red-500 @enderror">
                    </div>
                    @error('start_date') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="end_date" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">End Date</label>
                    <div class="mt-2">
                        <input type="date" wire:model.defer="end_date" id="end_date"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('end_date') border-red-500 ring-red-500 @enderror">
                    </div>
                    @error('end_date') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </fieldset>

        {{-- Criteria Section --}}
        <fieldset class="space-y-6">
            <div class="border-b dark:border-gray-700 pb-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Scholarship Criteria</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Define the criteria for evaluating applicants. The total weight of all criteria must sum to 1.0.</p>
            </div>

            {{-- Global errors for criteria --}}
            @error('criteria_total_weight') <div class="my-3 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-md text-sm text-red-700 dark:text-red-300">{{ $message }}</div> @enderror
            @error('criteria') <div class="my-3 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-md text-sm text-red-700 dark:text-red-300">{{ $message }}</div> @enderror

            <div class="space-y-8">
                @foreach ($criteria as $index => $criterion)
                    <div wire:key="{{ $criterion['component_id'] }}" class="p-6 border rounded-lg bg-gray-50 dark:bg-gray-800/30 dark:border-gray-700 shadow-md space-y-6 relative group">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $criterion['display_name'] ?: 'New Criterion' }} (Criterion {{ $index + 1 }})</h3>
                            @if (count($criteria) > 1)
                                <button type="button" wire:click="removeCriterion({{ $index }})"
                                        title="Remove Criterion"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.58.177-2.34.296a.75.75 0 0 0-.53.904l.091.454A25.06 25.06 0 0 0 3.239 6.7c-.43.246-.817.538-1.158.875a.75.75 0 0 0 .398 1.358c.329-.18.68-.343 1.05-.488l.351.263c.786.59 1.636 1.085 2.537 1.476l.231.1a.75.75 0 0 0 .821-.14L10 7.591l2.438 3.656a.75.75 0 0 0 .821.14l.231-.1c.901-.39 1.751-.885 2.537-1.476l.351-.263c.37-.145.721-.309 1.05-.488a.75.75 0 0 0 .398-1.358c-.34-.337-.728-.63-1.158-.875a25.06 25.06 0 0 0-.013-1.248l.091-.454a.75.75 0 0 0-.53-.904c-.76-.119-1.545-.22-2.34-.296V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM7.5 3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25V4h-5V3.75Z" clip-rule="evenodd" />
                                        <path d="M3.984 8.032a.75.75 0 0 0-.398 1.358c.329.18.68.343 1.05.488l.351.263c.786.59 1.636 1.085 2.537 1.476l.231.1a.75.75 0 0 0 .821-.14L10 7.591l2.438 3.656a.75.75 0 0 0 .821.14l.231-.1c.901-.39 1.751-.885 2.537-1.476l.351-.263c.37-.145.721-.309 1.05-.488a.75.75 0 0 0 .398-1.358c-.34-.337-.728-.63-1.158-.875a25.06 25.06 0 0 0-.013-1.248l.091-.454a.75.75 0 0 0-.53-.904c-.76-.119-1.545-.22-2.34-.296V3.75A2.75 2.75 0 0 0 11.25 1h-2.5A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.58.177-2.34.296a.75.75 0 0 0-.53.904l.091.454c.004.02.009.04.013.06V6.7c-.43.246-.817.538-1.158.875Z" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Criterion Name Input: Predefined or Custom --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="criteria_{{ $index }}_name_key" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Predefined Name <span class="text-xs text-gray-500 dark:text-gray-400">(Optional)</span></label>
                                <select wire:model.live="criteria.{{ $index }}.name_key" id="criteria_{{ $index }}_name_key"
                                        class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('criteria.'.$index.'.name_key') border-red-500 ring-red-500 @enderror">
                                    <option value="">-- Select or type custom name below --</option>
                                    @foreach ($availableCriteriaNames as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('criteria.'.$index.'.name_key') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="criteria_{{ $index }}_custom_name_input" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Or Custom Name</label>
                                <input type="text" wire:model.live.debounce.500ms="criteria.{{ $index }}.custom_name_input" id="criteria_{{ $index }}_custom_name_input"
                                       placeholder="e.g., Academic Excellence"
                                       class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('criteria.'.$index.'.custom_name_input') border-red-500 ring-red-500 @enderror"
                                       {{ !empty($criteria[$index]['name_key']) ? 'disabled bg-gray-100 dark:bg-gray-700/30 cursor-not-allowed' : '' }}>
                                @error('criteria.'.$index.'.custom_name_input') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        {{-- Combined error for name_key or custom_name_input if needed, or rely on individual ones --}}
                        @if ($errors->has('criteria.'.$index.'.name_key') && $errors->has('criteria.'.$index.'.custom_name_input'))
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">Please select a predefined name or enter a custom name.</p>
                        @endif

                        {{-- Weight, Type, and Data Type --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                            <div>
                                <label for="criteria_{{ $index }}_weight" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Weight <span class="text-xs text-gray-500 dark:text-gray-400">(0.0 to 1.0)</span></label>
                                <input type="number" step="0.01" min="0" max="1" wire:model.defer="criteria.{{ $index }}.weight" id="criteria_{{ $index }}_weight"
                                       placeholder="e.g., 0.25"
                                       class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('criteria.'.$index.'.weight') border-red-500 ring-red-500 @enderror">
                                @error('criteria.'.$index.'.weight') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="criteria_{{ $index }}_type" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Type</label>
                                <select wire:model.defer="criteria.{{ $index }}.type" id="criteria_{{ $index }}_type"
                                        class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('criteria.'.$index.'.type') border-red-500 ring-red-500 @enderror">
                                    <option value="benefit">Benefit (Higher is better)</option>
                                    <option value="cost">Cost (Lower is better)</option>
                                </select>
                                @error('criteria.'.$index.'.type') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="criteria_{{ $index }}_data_type" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Data Type</label>
                                <select wire:model.live="criteria.{{ $index }}.data_type" id="criteria_{{ $index }}_data_type"
                                        class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700/50 dark:text-gray-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm @error('criteria.'.$index.'.data_type') border-red-500 ring-red-500 @enderror">
                                    <option value="numeric">Numeric (e.g., GPA, Score)</option>
                                    <option value="qualitative_option">Qualitative - Options (e.g., Yes/No, Rating Scale)</option>
                                    <option value="qualitative_text">Qualitative - Text Map (e.g., Grade A -> 4.0)</option>
                                </select>
                                @error('criteria.'.$index.'.data_type') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Conditional: Qualitative Options --}}
                        @if ($criterion['options_config_type'] === 'options')
                            <div class="mt-6 p-5 border rounded-lg bg-gray-100 dark:bg-gray-700/60 dark:border-gray-600 space-y-4 shadow">
                                <h4 class="text-base font-semibold text-gray-800 dark:text-gray-100">Define Options for "{{ $criterion['display_name'] ?: 'This Criterion' }}"</h4>
                                @error('criteria.'.$index.'.options') <p class="my-2 p-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-md text-xs text-red-700 dark:text-red-300">{{ $message }}</p> @enderror
                                <div class="space-y-3">
                                    @foreach ($criterion['options'] as $optIndex => $option)
                                        <div wire:key="criterion-{{ $index }}-option-{{ $optIndex }}" class="grid grid-cols-1 md:grid-cols-10 gap-x-4 gap-y-2 items-center p-3 rounded-md border dark:border-gray-600/50 bg-white dark:bg-gray-700/30">
                                            <div class="md:col-span-3">
                                                <label for="criteria_{{ $index }}_options_{{ $optIndex }}_label" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Display Label</label>
                                                <input type="text" wire:model.defer="criteria.{{ $index }}.options.{{ $optIndex }}.label" id="criteria_{{ $index }}_options_{{ $optIndex }}_label"
                                                       placeholder="e.g., Excellent"
                                                       class="mt-1 block w-full py-1.5 px-2.5 border-gray-300 dark:border-gray-500 dark:bg-gray-600/50 dark:text-gray-100 rounded-md shadow-sm sm:text-xs focus:ring-indigo-500 focus:border-indigo-500 @error('criteria.'.$index.'.options.'.$optIndex.'.label') border-red-500 ring-red-500 @enderror">
                                                @error('criteria.'.$index.'.options.'.$optIndex.'.label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="md:col-span-3">
                                                <label for="criteria_{{ $index }}_options_{{ $optIndex }}_value" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Stored Value</label>
                                                <input type="text" wire:model.defer="criteria.{{ $index }}.options.{{ $optIndex }}.value" id="criteria_{{ $index }}_options_{{ $optIndex }}_value"
                                                       placeholder="e.g., excellent_rating"
                                                       class="mt-1 block w-full py-1.5 px-2.5 border-gray-300 dark:border-gray-500 dark:bg-gray-600/50 dark:text-gray-100 rounded-md shadow-sm sm:text-xs focus:ring-indigo-500 focus:border-indigo-500 @error('criteria.'.$index.'.options.'.$optIndex.'.value') border-red-500 ring-red-500 @enderror">
                                                @error('criteria.'.$index.'.options.'.$optIndex.'.value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="criteria_{{ $index }}_options_{{ $optIndex }}_numeric_value" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Numeric Score</label>
                                                <input type="number" step="any" wire:model.defer="criteria.{{ $index }}.options.{{ $optIndex }}.numeric_value" id="criteria_{{ $index }}_options_{{ $optIndex }}_numeric_value"
                                                       placeholder="e.g., 5"
                                                       class="mt-1 block w-full py-1.5 px-2.5 border-gray-300 dark:border-gray-500 dark:bg-gray-600/50 dark:text-gray-100 rounded-md shadow-sm sm:text-xs focus:ring-indigo-500 focus:border-indigo-500 @error('criteria.'.$index.'.options.'.$optIndex.'.numeric_value') border-red-500 ring-red-500 @enderror">
                                                @error('criteria.'.$index.'.options.'.$optIndex.'.numeric_value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="md:col-span-2 flex items-end justify-end">
                                                <button type="button" wire:click="removeOption({{ $index }}, {{ $optIndex }})" title="Remove Option"
                                                        class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 dark:bg-red-700/50 dark:text-red-300 dark:hover:bg-red-700/80 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                                        <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addOption({{ $index }})"
                                        class="mt-3 inline-flex items-center gap-x-1.5 rounded-md bg-sky-500 dark:bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-sky-600 dark:hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                    </svg>
                                    Add Option
                                </button>
                            </div>
                        @endif

                        {{-- Conditional: Qualitative Text (Value Map) --}}
                        @if ($criterion['options_config_type'] === 'value_map')
                            <div class="mt-6 p-5 border rounded-lg bg-gray-100 dark:bg-gray-700/60 dark:border-gray-600 space-y-4 shadow">
                                <h4 class="text-base font-semibold text-gray-800 dark:text-gray-100">Define Text to Value Mappings for "{{ $criterion['display_name'] ?: 'This Criterion' }}"</h4>
                                 @error('criteria.'.$index.'.value_map') <p class="my-2 p-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-md text-xs text-red-700 dark:text-red-300">{{ $message }}</p> @enderror
                                <div class="space-y-3">
                                    @foreach ($criterion['value_map'] as $mapIndex => $mapEntry)
                                        <div wire:key="criterion-{{ $index }}-valuemap-{{ $mapIndex }}" class="grid grid-cols-1 md:grid-cols-7 gap-x-4 gap-y-2 items-center p-3 rounded-md border dark:border-gray-600/50 bg-white dark:bg-gray-700/30">
                                            <div class="md:col-span-3">
                                                <label for="criteria_{{ $index }}_value_map_{{ $mapIndex }}_key" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Expected Text Input</label>
                                                <input type="text" wire:model.defer="criteria.{{ $index }}.value_map.{{ $mapIndex }}.key_input" id="criteria_{{ $index }}_value_map_{{ $mapIndex }}_key"
                                                       placeholder="e.g., High Distinction"
                                                       class="mt-1 block w-full py-1.5 px-2.5 border-gray-300 dark:border-gray-500 dark:bg-gray-600/50 dark:text-gray-100 rounded-md shadow-sm sm:text-xs focus:ring-indigo-500 focus:border-indigo-500 @error('criteria.'.$index.'.value_map.'.$mapIndex.'.key_input') border-red-500 ring-red-500 @enderror">
                                                @error('criteria.'.$index.'.value_map.'.$mapIndex.'.key_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="md:col-span-2 text-center self-center pt-4 text-gray-500 dark:text-gray-400">&rarr;</div>
                                            <div class="md:col-span-1">
                                                <label for="criteria_{{ $index }}_value_map_{{ $mapIndex }}_value" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Numeric Score</label>
                                                <input type="number" step="any" wire:model.defer="criteria.{{ $index }}.value_map.{{ $mapIndex }}.value_input" id="criteria_{{ $index }}_value_map_{{ $mapIndex }}_value"
                                                       placeholder="e.g., 4.0"
                                                       class="mt-1 block w-full py-1.5 px-2.5 border-gray-300 dark:border-gray-500 dark:bg-gray-600/50 dark:text-gray-100 rounded-md shadow-sm sm:text-xs focus:ring-indigo-500 focus:border-indigo-500 @error('criteria.'.$index.'.value_map.'.$mapIndex.'.value_input') border-red-500 ring-red-500 @enderror">
                                                @error('criteria.'.$index.'.value_map.'.$mapIndex.'.value_input') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="md:col-span-1 flex items-end justify-end">
                                                <button type="button" wire:click="removeValueMapEntry({{ $index }}, {{ $mapIndex }})" title="Remove Mapping"
                                                        class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 dark:bg-red-700/50 dark:text-red-300 dark:hover:bg-red-700/80 transition-colors duration-150">
                                                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                                        <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addValueMapEntry({{ $index }})"
                                        class="mt-3 inline-flex items-center gap-x-1.5 rounded-md bg-sky-500 dark:bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-sky-600 dark:hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                    </svg>
                                    Add Value Map Entry
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-between">
                <button type="button" wire:click="addCriterion"
                        wire:loading.attr="disabled" wire:target="addCriterion"
                        class="inline-flex items-center gap-x-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150">
                    <svg wire:loading wire:target="addCriterion" class="animate-spin -ml-1 mr-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="addCriterion" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 -ml-0.5">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                    <span wire:loading.remove wire:target="addCriterion">Add Criterion</span>
                    <span wire:loading wire:target="addCriterion">Adding...</span>
                </button>

                {{-- Total Weight Progress --}}
                @php
                    $totalWeight = collect($criteria)->sum(function($criterion) {
                        return is_numeric($criterion['weight']) ? (float)$criterion['weight'] : 0;
                    });
                    $progress = $totalWeight * 100;
                    $progressColor = $totalWeight > 1 ? 'bg-red-500' : ($totalWeight == 1 ? 'bg-green-500' : 'bg-blue-500');
                @endphp
                <div class="w-1/3">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Weight: {{ number_format($totalWeight, 2) }} / 1.00</span>
                        <span class="text-sm font-medium {{ $totalWeight > 1 ? 'text-red-500 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="{{ $progressColor }} h-2.5 rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                    </div>
                     @if ($totalWeight > 1)
                        <p class="text-xs text-red-500 dark:text-red-400 mt-1">Total weight exceeds 1.0. Please adjust.</p>
                    @elseif ($totalWeight < 1 && count($criteria) > 0)
                         <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Total weight is less than 1.0.</p>
                    @endif
                </div>
            </div>
        </fieldset>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-x-4 border-t dark:border-gray-700 pt-8 mt-10">
            <a href="{{ route('admin.scholarship-batches.index') }}" wire:navigate
                class="rounded-md bg-gray-100 dark:bg-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-800 dark:text-gray-200 shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-150">
                Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="inline-flex items-center justify-center gap-x-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-75 disabled:cursor-wait transition-colors duration-150">
                <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg wire:loading.remove wire:target="save" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-5 h-5 -ml-0.5">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"></path>
                </svg>
                <span wire:loading.remove wire:target="save">Save Scholarship Batch</span>
                <span wire:loading wire:target="save">Saving Batch...</span>
            </button>
        </div>
    </form>
</div>
