<div>
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Open Scholarship Batches</h1>
        <p class="text-sm text-gray-500">View currently active scholarship batches available for student applications.</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Batch Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Application Period
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($batches as $batch)
                    @php
                        $isEffectivelyOpen = ($batch->status === 'open') ||
                                             ($batch->status === 'upcoming' &&
                                              now()->between($batch->start_date, $batch->end_date));

                        $displayStatusText = Str::title(str_replace('_', ' ', $batch->status));
                        $displayStatusClass = 'bg-gray-100 text-gray-700'; // Default/fallback

                        if ($isEffectivelyOpen) {
                            $displayStatusText = 'Open';
                            $displayStatusClass = 'bg-green-100 text-green-700';
                        } elseif ($batch->status === 'closed') {
                            $displayStatusText = 'Closed';
                            $displayStatusClass = 'bg-red-100 text-red-700';
                        } elseif ($batch->status === 'upcoming') { // and not yet effectively open
                            $displayStatusText = 'Upcoming';
                            $displayStatusClass = 'bg-blue-100 text-blue-700';
                        }
                        // Add other specific statuses if needed, e.g., evaluating, draft

                    @endphp
                    <tr wire:key="batch-{{ $batch->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $batch->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-700">
                            {{ Str::limit($batch->description, 70) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Illuminate\Support\Carbon::parse($batch->start_date)->format('d M Y') }} - {{ \Illuminate\Support\Carbon::parse($batch->end_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $displayStatusClass }}">
                                {{ $displayStatusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if ($isEffectivelyOpen)
                                <a href="{{ route('teacher.submissions.create-for-batch', ['batch' => $batch->id]) }}"
                                   wire:navigate
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Submit Student
                                </a>
                            @else
                                <span class="text-gray-400 italic text-xs">Submissions closed</span>
                            @endif
                            {{-- <a href="#" class="text-indigo-600 hover:text-indigo-900">View Details</a> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No open scholarship batches found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $batches->links() }}
    </div>
</div>
