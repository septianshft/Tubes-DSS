<div>
    <div class="mb-6 border-b pb-4 border-gray-200 dark:border-gray-700">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Submit Students for: {{ $batch->name }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select one or more students and provide the common required information for this scholarship batch.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="saveSubmission" class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 space-y-6">
            {{-- Custom Multi-select for Students --}}
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

                {{-- Display selected students as pills --}}
                <div class="mb-3 min-h-[52px] p-3 border rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 flex flex-wrap gap-2 items-start transition-colors duration-200">
                    @php
                        // Ensure $selectedStudentIds is an array before iterating
                        $studentIdsToDisplay = is_array($selectedStudentIds) ? $selectedStudentIds : [];
                    @endphp
                    @forelse ($this->allStudents->whereIn('id', $studentIdsToDisplay) as $student)
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 border border-indigo-200 dark:border-indigo-700 animate-fade-in">
                            <svg class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="max-w-[120px] truncate">{{ $student->name }}</span>
                            <button type="button"
                                    wire:click="deselectStudent({{ $student->id }})"
                                    title="Remove {{ $student->name }}"
                                    class="ml-2 flex-shrink-0 text-indigo-500 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full p-0.5 transition-colors duration-150">
                                <svg class="h-3.5 w-3.5" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                                    <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm py-1">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 6.292 4 4 0 010-6.292zM15 21H3v-1a6 6 0 0112 0v1z"></path>
                            </svg>
                            No students selected
                        </div>
                    @endforelse
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
                            @php
                                // Ensure $selectedStudentIds is an array for counting
                                $countSelected = is_array($selectedStudentIds) ? count($selectedStudentIds) : 0;
                            @endphp
                            @if($countSelected > 0)
                                {{ $countSelected }} student{{ $countSelected > 1 ? 's' : '' }} selected
                            @else
                                Search and select students...
                            @endif
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
                        @forelse ($students as $student)
                            <label for="student-{{ $student->id }}"
                                   class="flex items-center px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors duration-150 group">
                                <div class="flex items-center flex-1">
                                    <input type="checkbox"
                                           id="student-{{ $student->id }}"
                                           wire:model.live="selectedStudentIds"
                                           value="{{ $student->id }}"
                                           {{-- Ensure $selectedStudentIds is an array for in_array check --}}
                                           @if(is_array($selectedStudentIds) && in_array($student->id, $selectedStudentIds)) checked @endif
                                           class="h-4 w-4 text-indigo-600 rounded border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:ring-2 transition-colors duration-150">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-900 dark:group-hover:text-indigo-100 transition-colors duration-150">
                                                {{ $student->name }}
                                            </span>
                                            {{-- Ensure $selectedStudentIds is an array for in_array check --}}
                                            @if(is_array($selectedStudentIds) && in_array($student->id, $selectedStudentIds))
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            NISN: {{ $student->nisn }}
                                        </span>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 6.292 4 4 0 010-6.292zM15 21H3v-1a6 6 0 0112 0v1z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    @if (!empty($studentSearch))
                                        No students found matching "<span class="font-medium">{{ $studentSearch }}</span>".
                                    @else
                                        No students available to select.
                                    @endif
                                </p>
                                @if (!empty($studentSearch))
                                    <button type="button"
                                            wire:click="$set('studentSearch', '')"
                                            class="mt-2 text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 font-medium transition-colors duration-150">
                                        Clear search
                                    </button>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    <!-- Footer with selection count -->
                    @if(count($students) > 0)
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ count($students) }} student{{ count($students) > 1 ? 's' : '' }} available</span>
                                {{-- Ensure $selectedStudentIds is an array for counting --}}
                                <span>{{ (is_array($selectedStudentIds) ? count($selectedStudentIds) : 0) }} selected</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Error Messages -->
                @error('selectedStudentIds')
                    <p class="text-red-600 dark:text-red-400 text-xs mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
                @error('selectedStudentIds.*')
                    <p class="text-red-600 dark:text-red-400 text-xs mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            {{-- End Custom Multi-select --}}

            {{-- Criteria Section --}}
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                @if(empty($criteriaConfig))
                    <div class="flex items-center p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-slate-800 dark:text-blue-300" role="alert">
                        <svg class="flex-shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">No specific criteria defined for this scholarship batch.</span> Please ensure all student data is up-to-date or configure criteria in batch settings.
                        </div>
                    </div>
                @elseif(empty($selectedStudentIds))
                    <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-slate-800 dark:text-yellow-300" role="alert">
                        <svg class="flex-shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">Please select one or more students</span> to enter their scholarship criteria information.
                        </div>
                    </div>
                @else
                    <div class="space-y-10"> {{-- Container for all student criteria blocks, increased spacing --}}
                        @foreach ($this->allStudents->whereIn('id', $selectedStudentIds)->sortBy('name') as $student)
                            <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg bg-gray-50 dark:bg-slate-800 mb-8 transition-all hover:shadow-xl">
                                <h3 class="flex items-center text-xl font-bold text-gray-900 dark:text-gray-50 mb-6 pb-3 border-b border-gray-300 dark:border-gray-600">
                                    <svg class="w-6 h-6 mr-3 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $student->name }}
                                    <span class="ml-3 text-sm font-medium text-gray-500 dark:text-gray-400">(NISN: {{ $student->nisn }})</span>
                                </h3>
                                <div class="space-y-6">
                                    @php
                                        // Ensure $studentCriteriaValues[$student->id] is initialized if not set
                                        if (!isset($studentCriteriaValues[$student->id])) {
                                            $studentCriteriaValues[$student->id] = [];
                                        }
                                    @endphp
                                    @forelse ($criteriaConfig as $criterion)
                                        @php
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
                                        @endphp

                                        @if($criterionId && $wireModelPath)
                                            <div class="grid grid-cols-1 gap-y-2">
                                                <label for="{{ $inputFieldId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $criterion['name'] ?? 'Unnamed Criterion' }}
                                                    @if($criterion['is_required'] ?? false) <span class="text-red-500 dark:text-red-400 font-semibold">*</span> @endif
                                                </label>

                                                @if(isset($criterion['description']))
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 mb-1">{{ $criterion['description'] }}</p>
                                                @endif

                                                @switch($criterion['data_type'] ?? 'text')
                                                    @case('numeric')
                                                        <input type="number" step="any" wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}"
                                                               class="{{ $baseInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror">
                                                        @break

                                                    @case('qualitative_option')
                                                        @if (!empty($criterion['options']) && is_array($criterion['options']))
                                                            <select wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}"
                                                                    class="{{ $baseInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror">
                                                                <option value="">-- Select {{ $criterion['name'] ?? '' }} --</option>
                                                                @foreach ($criterion['options'] as $option)
                                                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">Error: Options not available for this select field ({{ $criterion['name'] ?? 'Unnamed' }}).</p>
                                                            <input type="text" wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}" placeholder="Options missing, enter value manually"
                                                                   class="{{ $baseInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror">
                                                        @endif
                                                        @break

                                                    @case('file')
                                                        <input type="file" wire:model.defer="{{ $wireModelPath }}" id="{{ $inputFieldId }}"
                                                               class="{{ $baseFileInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror">
                                                        <div wire:loading wire:target="{{ $wireModelPath }}" class="text-xs text-indigo-600 dark:text-indigo-400 mt-1.5 flex items-center">
                                                            <svg class="animate-spin h-4 w-4 text-indigo-500 dark:text-indigo-400 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                            Uploading...
                                                        </div>
                                                        @php
                                                            $existingFileValue = null;
                                                            if(isset($studentCriteriaValues[$studentId]) && isset($studentCriteriaValues[$studentId][$criterionId])) {
                                                                $existingFileValue = $studentCriteriaValues[$studentId][$criterionId];
                                                            }
                                                        @endphp
                                                        @if (!is_null($existingFileValue) && ($existingFileValue instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile || (is_string($existingFileValue) && !empty($existingFileValue))))
                                                            <div class="mt-1.5 text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                                                <svg class="w-4 h-4 inline mr-1.5 text-gray-500 dark:text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                                                @if ($existingFileValue instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && method_exists($existingFileValue, 'getClientOriginalName'))
                                                                    <span>Selected: {{ $existingFileValue->getClientOriginalName() }}</span>
                                                                    @if(method_exists($existingFileValue, 'getSize'))
                                                                        <span class="ml-2 text-gray-500 dark:text-gray-500">({{ round($existingFileValue->getSize() / 1024, 1) }} KB)</span>
                                                                    @endif
                                                                @elseif (is_string($existingFileValue))
                                                                    <span>Current file: {{ basename($existingFileValue) }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @break

                                                    @case('boolean')
                                                        <div class="mt-2 relative flex items-start">
                                                            <div class="flex items-center h-5">
                                                                <input type="checkbox" wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}"
                                                                    class="{{ $baseCheckboxClasses }} @error($wireModelPath) {{ $errorCheckboxClasses }} @else {{ $normalFocusCheckboxClasses }} @enderror">
                                                            </div>
                                                            <div class="ml-3 text-sm">
                                                                <label for="{{ $inputFieldId }}" class="font-medium text-gray-700 dark:text-gray-300">Yes / Confirm</label>
                                                            </div>
                                                        </div>
                                                        @break

                                                    @case('date')
                                                        <input type="date" wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}"
                                                               class="{{ $baseInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror">
                                                        @break

                                                    @case('qualitative_text')
                                                        <input type="text" wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}"
                                                               list="{{ $inputFieldId }}_datalist"
                                                               class="{{ $baseInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror">
                                                        @if (!empty($criterion['options']) && is_array($criterion['options']))
                                                            <datalist id="{{ $inputFieldId }}_datalist">
                                                                @foreach ($criterion['options'] as $option)
                                                                    <option value="{{ $option['value'] }}">
                                                                @endforeach
                                                            </datalist>
                                                        @endif
                                                        @break

                                                    @default {{-- text, textarea, or any other --}}
                                                        @if(isset($criterion['multiline']) && $criterion['multiline'])
                                                            <textarea wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}" rows="3"
                                                                      class="{{ $baseInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror"></textarea>
                                                        @else
                                                            <input type="text" wire:model.blur="{{ $wireModelPath }}" id="{{ $inputFieldId }}"
                                                                   class="{{ $baseInputClasses }} @error($wireModelPath) {{ $errorInputClasses }} @else {{ $normalFocusInputClasses }} @enderror">
                                                        @endif
                                                @endswitch

                                                @error($wireModelPath) <span class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @else
                                            @if(!$criterionId)
                                                <div class="my-2 p-3 text-xs text-red-700 bg-red-100 rounded-md dark:bg-red-900/50 dark:text-red-300 flex items-start" role="alert">
                                                    <svg class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                                    <div>
                                                        <strong class="font-semibold">Configuration Error:</strong> Criterion ID is missing for '{{ $criterion['name'] ?? 'Unnamed Criterion' }}'. This input cannot be rendered.
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @empty
                                        <p class="text-gray-500 dark:text-gray-400 px-1 italic">No criteria configured for this batch, or the configuration is invalid.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Debug information
        @if (app()->environment('local'))
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-md text-xs">
                <strong>Debug Info:</strong><br>
                Selected Students: {{ count($selectedStudentIds) }}<br>
                Criteria Config: {{ count($criteriaConfig) }}<br>
                Student Criteria Values: {{ json_encode($studentCriteriaValues) }}
            </div>
        @endif --}}

        {{-- Validation errors display --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                <strong>Validation Errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 sticky bottom-0 z-10 border-t border-gray-200 bg-opacity-95 backdrop-blur-sm">
            {{-- Form readiness status (debug) --}}
            @if (app()->environment('local'))
                <div class="flex items-center text-xs text-gray-500 mr-4">
                    Form Ready: {{ $this->checkFormReady() ? 'Yes' : 'No' }}
                </div>
            @endif

            <a href="{{ route('teacher.scholarship-batches.open') }}" wire:navigate
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
</div>
