@php
    // Helper to get qualitative label if available
    $getQualitativeLabel = function ($criterionId, $studentSubmittedValue, $allBatchCriteriaConfigs) {
        $criterionConfig = null;
        if (is_array($allBatchCriteriaConfigs)) {
            foreach ($allBatchCriteriaConfigs as $cfg) {
                if (isset($cfg['id']) && $cfg['id'] === $criterionId) {
                    $criterionConfig = $cfg;
                    break;
                }
            }
        }

        if ($criterionConfig && isset($criterionConfig['data_type'])) {
            $dataType = $criterionConfig['data_type'];

            if ($dataType === 'qualitative_option' && isset($criterionConfig['options']) && is_array($criterionConfig['options'])) {
                foreach ($criterionConfig['options'] as $option) {
                    if (isset($option['value']) && $option['value'] == $studentSubmittedValue && isset($option['label'])) {
                        return $option['label'];
                    }
                }
            } elseif ($dataType === 'qualitative_text' && isset($criterionConfig['value_map']) && is_array($criterionConfig['value_map'])) {
                foreach ($criterionConfig['value_map'] as $mapItem) {
                    // For qualitative_text with value_map like {key_input, value_input},
                    // key_input is matched against student's submitted value, and key_input is returned as label.
                    if (isset($mapItem['key_input']) && $mapItem['key_input'] == $studentSubmittedValue) {
                        return $mapItem['key_input'];
                    }
                }
            }
        }
        return $studentSubmittedValue; // Fallback to the raw submitted value
    };
@endphp

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submission Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

                    @if (session()->has('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-medium text-gray-900 dark:text-white">
                            {{ $batch->name }} - {{ $batch->year }}
                        </h1>
                        <a href="{{ route('admin.scholarship-batches.submissions', $batch->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                            {{ __('Back to Submissions') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Student Information</h3>
                            <p><strong class="dark:text-gray-400">Name:</strong> {{ $submission->student->name ?? 'N/A' }}</p>
                            <p><strong class="dark:text-gray-400">NISN:</strong> {{ $submission->student->nisn ?? 'N/A' }}</p>
                            <p><strong class="dark:text-gray-400">Email:</strong> {{ $submission->student->user->email ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Submission Status</h3>
                            <p><strong class="dark:text-gray-400">Submitted At:</strong> {{ $submission->created_at->format('d M Y, H:i') }}</p>
                            <p><strong class="dark:text-gray-400">Status:</strong> <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @switch($submission->status)
                                    @case('submitted') bg-blue-200 text-blue-800 @break
                                    @case('under_review') bg-yellow-200 text-yellow-800 @break
                                    @case('approved') bg-green-200 text-green-800 @break
                                    @case('rejected') bg-red-200 text-red-800 @break
                                    @case('revision_requested') bg-orange-200 text-orange-800 @break
                                    @default bg-gray-200 text-gray-800 @break
                                @endswitch
                            ">{{ ucwords(str_replace('_', ' ', $submission->status)) }}</span></p>
                            @if($submission->revision_notes)
                                <p><strong class="dark:text-gray-400">Revision Notes:</strong> {{ $submission->revision_notes }}</p>
                            @endif
                            @if($submission->status_updated_by)
                                <p><strong class="dark:text-gray-400">Last Updated By:</strong> {{ $submission->statusUpdatedBy->name ?? 'Unknown' }}
                                @if($submission->status_updated_at)
                                    on {{ $submission->status_updated_at->format('d M Y, H:i') }}
                                @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-3">Criteria Details & Scores</h3>
                        @if (!empty($criteriaDetails))
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Criterion</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted Value</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Normalized Value</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Weight</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($criteriaDetails as $criterionId => $details)
                                            @php
                                                // Log::debug("[BLADE] Iterating for criterionId: " . ($details['id'] ?? 'UNKNOWN_ID') . " in ShowSubmission Blade", [
                                                //     'details_array' => $details,
                                                //     'raw_value_from_details' => $details['rawValue'] ?? 'NOT_SET_IN_BLADE_RAW',
                                                //     'display_value_from_details' => $details['displayValue'] ?? 'NOT_SET_IN_BLADE_DISPLAY',
                                                //     'normalized_score_from_details' => $details['normalizedScore'] ?? 'NOT_SET_IN_BLADE_NORM',
                                                //     'is_normalized_score_numeric' => is_numeric($details['normalizedScore'] ?? null)
                                                // ]);
                                            @endphp
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $details['name'] ?? 'Unnamed Criterion' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{-- Directly use displayValue prepared by the backend, or fallback --}}
                                                    {{ $details['displayValue'] ?? ($details['rawValue'] ?? 'Val N/A') }}
                                                    @if (isset($details['file_path']) && $details['file_path'])
                                                        <a href="{{ url('storage/' . $details['file_path']) }}" target="_blank" class="ml-2 text-blue-500 hover:text-blue-700">(View File)</a>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{-- Let's be explicit with the check for normalizedScore --}}
                                                    @if (isset($details['normalizedScore']) && is_numeric($details['normalizedScore']))
                                                        {{ number_format((float)$details['normalizedScore'], 4) }}
                                                    @else
                                                        Fallback! NormScore: [{{ $details['normalizedScore'] ?? 'NOT SET' }}] IsNumeric: [{{ is_numeric($details['normalizedScore'] ?? null) ? 'Yes' : 'No' }}]
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ number_format($details['weight'] ?? 0, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ ucfirst($details['type'] ?? 'N/A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg shadow text-center">
                                <h4 class="text-lg font-semibold text-blue-700 dark:text-blue-300">Final DSS Score (SAW)</h4>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($sawScore ?? 0, 4) }}</p>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No criteria details available for this submission or batch configuration is missing.</p>
                        @endif
                    </div>

                    {{-- Section for Detailed SAW Calculation Steps --}}
                    @if (!empty($sawCalculationSteps) && isset($sawCalculationSteps['steps']))
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Detailed Criterion Calculation Steps</h3>
                            <div class="space-y-6">
                                @foreach ($sawCalculationSteps['steps'] as $index => $step)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
                                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Criterion #{{ $index + 1 }}: {{ $step['criterion_name'] ?? ($step['criterion_id'] ?? 'N/A') }}
                                        </h4>
                                        @if (isset($step['error']))
                                            <p class="text-red-500 dark:text-red-400"><strong>Error:</strong> {{ $step['error'] }}</p>
                                        @else
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                                <p><strong class="dark:text-gray-400">Submitted Value:</strong> {{ $step['raw_value_submitted'] ?? 'N/A' }}</p>
                                                <p><strong class="dark:text-gray-400">Numeric Value:</strong> {{ $step['numeric_value_for_calc'] ?? $step['numeric_value'] ?? 'N/A' }}</p>
                                                <p><strong class="dark:text-gray-400">Min Value (Batch):</strong> {{ $step['min_value_for_criterion'] ?? 'N/A' }}</p>
                                                <p><strong class="dark:text-gray-400">Max Value (Batch):</strong> {{ $step['max_value_for_criterion'] ?? 'N/A' }}</p>
                                                <p><strong class="dark:text-gray-400">Criterion Type:</strong> {{ ucfirst($step['criterion_type'] ?? 'N/A') }}</p>
                                                <p><strong class="dark:text-gray-400">Criterion Weight:</strong> {{ number_format($step['criterion_weight'] ?? 0, 2) }}</p>
                                                <p class="md:col-span-2"><strong class="dark:text-gray-400">Normalization Formula:</strong> <code class="text-xs bg-gray-200 dark:bg-gray-600 p-1 rounded">{{ $step['normalization_formula_string'] ?? 'N/A' }}</code></p>
                                                <p><strong class="dark:text-gray-400">Normalized Value:</strong> {{ number_format($step['normalized_value_after_clamping'] ?? 0, 6) }}</p>
                                                <p><strong class="dark:text-gray-400">Stored Normalized Value:</strong> {{ number_format($step['normalized_value_stored'] ?? 0, 4) }}</p>
                                                <p><strong class="dark:text-gray-400">Weighted Score Contribution:</strong> {{ number_format($step['weighted_score_contribution'] ?? 0, 4) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                             <p class="text-gray-500 dark:text-gray-400">No detailed calculation steps available.</p>
                        </div>
                    @endif

                    {{-- Section for Calculation Summary --}}
                    @if (!empty($sawCalculationSteps) && isset($sawCalculationSteps['summary']))
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Calculation Summary</h3>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow text-sm space-y-2">
                                <p><strong class="dark:text-gray-400">Total Weighted Score (Sum of Contributions):</strong> {{ number_format($sawCalculationSteps['summary']['total_weighted_score_from_steps'] ?? 0, 6) }}</p>
                                @if(isset($sawCalculationSteps['summary']['tie_breaking_factor_added']))
                                    <p><strong class="dark:text-gray-400">Tie-Breaking Factor (based on Submission ID {{ $sawCalculationSteps['summary']['submission_id_for_tie_break'] ?? 'N/A' }}):</strong> +{{ number_format($sawCalculationSteps['summary']['tie_breaking_factor_added'] ?? 0, 8) }}</p>
                                    <p><strong class="dark:text-gray-400">Score Before Final Rounding (with Tie-Breaker):</strong> {{ number_format($sawCalculationSteps['summary']['score_before_final_rounding'] ?? 0, 8) }}</p>
                                @endif
                                <p><strong class="dark:text-gray-400">Final Calculated Score (Stored):</strong> {{ number_format($sawCalculationSteps['summary']['final_score_stored'] ?? 0, 4) }}</p>
                                <p><strong class="dark:text-gray-400">Final Rank (Stored):</strong> {{ $sawCalculationSteps['summary']['final_rank_stored'] ?? 'N/A' }}</p>
                                @if(isset($sawCalculationSteps['summary']['notes']))
                                    <p class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600"><strong class="dark:text-gray-400">Notes:</strong> {{ $sawCalculationSteps['summary']['notes'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($submission->status !== 'approved' && $submission->status !== 'rejected')
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-3">Actions</h3>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <button wire:click="approveSubmission"
                                    wire:confirm="Are you sure you want to approve this submission?"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-600 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Approve
                            </button>
                            <button wire:click="rejectSubmission"
                                    wire:confirm="Are you sure you want to reject this submission?"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Reject
                            </button>
                            <button wire:click="requestRevision"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 focus:outline-none focus:border-yellow-600 focus:ring focus:ring-yellow-100 active:bg-yellow-500 disabled:opacity-25 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                Request Revision
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Revision Notes Modal --}}
    @if ($showRevisionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:click="closeRevisionModal">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-lg w-full mx-4" wire:click.stop>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Request Revision</h3>
                    <p class="text-sm text-gray-600">Provide specific feedback for the teacher about what needs to be revised.</p>
                </div>

                <form wire:submit.prevent="requestRevisionWithNotes">
                    <div class="mb-4">
                        <label for="revisionNotes" class="block text-sm font-medium text-gray-700 mb-2">
                            Revision Notes <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="revisionNotes"
                            id="revisionNotes"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('revisionNotes') border-red-500 @enderror"
                            placeholder="Please explain what specific information or documentation is needed..."
                            maxlength="1000">
                        </textarea>
                        @error('revisionNotes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ strlen($revisionNotes) }}/1000 characters</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            wire:click="closeRevisionModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-150 ease-in-out">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition duration-150 ease-in-out"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50">
                            <span wire:loading.remove wire:target="requestRevisionWithNotes">Send Revision Request</span>
                            <span wire:loading wire:target="requestRevisionWithNotes">Sending...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
