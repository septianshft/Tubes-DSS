<div>
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Submit Students for: {{ $batch->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Select one or more students and provide the common required information for this scholarship batch.</p>
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
            <div x-data="{ open: false }" class="relative">
                <label for="student_search_button" class="block text-sm font-medium text-gray-700">Select Students *</label>

                {{-- Display selected students as pills --}}
                <div class="mt-1 mb-2 min-h-[44px] p-2 border rounded-md border-gray-300 flex flex-wrap gap-2">
                    @forelse ($this->allStudents->whereIn('id', $selectedStudentIds) as $student)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                            {{ $student->name }}
                            <button type="button" wire:click="deselectStudent({{ $student->id }})" title="Remove {{ $student->name }}"
                                    class="ml-1.5 flex-shrink-0 text-indigo-500 hover:text-indigo-700 focus:outline-none">
                                <svg class="h-3 w-3" stroke="currentColor" fill="none" viewBox="0 0 8 8"><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" /></svg>
                            </button>
                        </span>
                    @empty
                        <span class="text-gray-500 text-sm p-1">No students selected</span>
                    @endforelse
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
                        @forelse ($students as $student)
                            <li>
                                <label for="student-{{ $student->id }}"
                                       class="flex items-center px-3 py-2 hover:bg-indigo-50 cursor-pointer text-gray-900">
                                    <input type="checkbox"
                                           id="student-{{ $student->id }}"
                                           wire:model.live="selectedStudentIds"
                                           value="{{ $student->id }}"
                                           class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out rounded border-gray-300 focus:ring-indigo-500">
                                    <span class="ml-3 block text-sm font-normal">
                                        {{ $student->name }} <span class="text-gray-500"> (NISN: {{ $student->nisn }})</span>
                                    </span>
                                </label>
                            </li>
                        @empty
                            <li class="px-3 py-2 text-center text-gray-500 text-sm">
                                @if (!empty($studentSearch))
                                    No students found matching "{{ $studentSearch }}".
                                @else
                                    No students available to select.
                                @endif
                            </li>
                        @endforelse
                    </ul>
                </div>
                @error('selectedStudentIds') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                @error('selectedStudentIds.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            {{-- End Custom Multi-select --}}

            {{-- Criteria Section --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                @if(empty($criteriaConfig))
                    <p class="text-gray-600 px-1">No specific criteria defined for this scholarship batch. Please ensure all student data is up-to-date.</p>
                @elseif(empty($selectedStudentIds))
                    <p class="text-gray-600 px-1">Please select one or more students to enter their scholarship criteria information.</p>
                @else
                    <div class="space-y-8"> {{-- Container for all student criteria blocks --}}
                        @foreach ($this->allStudents->whereIn('id', $selectedStudentIds)->sortBy('name') as $student)
                            <div wire:key="student-criteria-block-{{ $student->id }}" class="p-4 border border-gray-300 rounded-lg shadow-sm bg-white">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-3">
                                    Criteria for: <span class="font-bold text-indigo-600">{{ $student->name }}</span>
                                    <span class="text-sm text-gray-500 ml-2">(NISN: {{ $student->nisn }})</span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                    @foreach ($criteriaConfig as $criterion)
                                        @if (isset($criterion['id']) && isset($criterion['name']))
                                            @php
                                                $studentId = $student->id;
                                                $criterionId = $criterion['id'];
                                                $wireModelPath = "studentCriteriaValues.{$studentId}.{$criterionId}";
                                                $elementId = "student_{$studentId}_criterion_{$criterionId}";
                                            @endphp
                                            <div wire:key="student-{{ $studentId }}-criterion-{{ $criterionId }}">
                                                <label for="{{ $elementId }}" class="block text-sm font-medium text-gray-700">
                                                    {{ $criterion['name'] }}
                                                    @if(strpos($criterion['rules'] ?? '', 'required') !== false)
                                                        <span class="text-red-500 font-semibold">*</span>
                                                    @endif
                                                </label>
                                                @php $inputType = $criterion['type'] ?? 'text'; @endphp

                                                @if ($inputType === 'select' && isset($criterion['options']))
                                                    <select wire:model.lazy="{{ $wireModelPath }}" id="{{ $elementId }}"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                        <option value="">-- Select {{ $criterion['name'] }} --</option>
                                                        @foreach ($criterion['options'] as $optionValue => $optionLabel)
                                                            <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif ($inputType === 'textarea')
                                                    <textarea wire:model.lazy="{{ $wireModelPath }}" id="{{ $elementId }}" rows="3"
                                                              placeholder="Enter {{ strtolower($criterion['name']) }}"
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"></textarea>
                                                @else {{-- text, number, date, etc. --}}
                                                    <input type="{{ $inputType }}" wire:model.lazy="{{ $wireModelPath }}" id="{{ $elementId }}"
                                                           placeholder="Enter {{ strtolower($criterion['name']) }}"
                                                           @if(isset($criterion['min'])) min="{{ $criterion['min'] }}" @endif
                                                           @if(isset($criterion['max'])) max="{{ $criterion['max'] }}" @endif
                                                           @if(isset($criterion['step'])) step="{{ $criterion['step'] }}" @endif
                                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                @endif

                                                @if(isset($criterion['description']))
                                                    <p class="mt-1 text-xs text-gray-500">{{ $criterion['description'] }}</p>
                                                @endif
                                                @error($wireModelPath) <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <a href="{{ route('teacher.scholarship-batches.open') }}" wire:navigate
                class="inline-flex items-center justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Submit Applications
            </button>
        </div>
    </form>
</div>
