<div>
    <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Open Scholarship Batches</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and manage currently active scholarship batches available for student applications.</p>
    </div>

    {{-- Add a section for filters or search if needed in the future --}}
    {{-- <div class="mb-6 p-4 bg-gray-50 dark:bg-slate-800 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">Filters</h3>
        </div> --}}

    <div class="grid gap-6">
        @forelse ($batches as $batch)
            @php
                $computedStatus = $batch->computed_status;
                $isEffectivelyOpen = $computedStatus === 'Active';

                $displayStatusText = $computedStatus;
                $statusClasses = 'bg-gray-100 text-gray-800';

                if ($computedStatus === 'Active') {
                    $statusClasses = 'bg-green-100 text-green-800';
                } elseif ($computedStatus === 'Closed') {
                    $statusClasses = 'bg-red-100 text-red-800';
                } elseif ($computedStatus === 'Upcoming') {
                    $statusClasses = 'bg-blue-100 text-blue-800';
                }
            @endphp

            <div wire:key="batch-{{ $batch->id }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $batch->name }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                {{ $batch->description }}
                            </p>
                        </div>
                        <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses }}">
                            {{ $displayStatusText }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>
                                {{ \Illuminate\Support\Carbon::parse($batch->start_date)->format('M d, Y') }} -
                                {{ \Illuminate\Support\Carbon::parse($batch->end_date)->format('M d, Y') }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-3">
                            @if ($isEffectivelyOpen)
                                <a href="{{ route('teacher.submissions.create-for-batch', ['batch' => $batch->id]) }}"
                                   wire:navigate
                                   class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Submit Student
                                </a>
                            @else
                                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0h-2m8-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2z"/>
                                    </svg>
                                    Submissions Closed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">No Open Scholarship Batches</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    There are currently no active scholarship batches available. Check back later or contact an administrator if you believe this is an error.
                </p>
            </div>
        @endforelse
    </div>

    @if ($batches->hasPages())
        <div class="mt-6 px-2">
            {{ $batches->links() }}
        </div>
    @endif
</div>
