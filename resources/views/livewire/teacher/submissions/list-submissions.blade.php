<div>
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">My Submissions</h1>
        <p class="text-sm text-gray-500">Track the status and details of your scholarship applications.</p>
    </div>

    {{-- Tabbed Navigation for Status Filter --}}
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                @php
                    $tabs = [
                        ['value' => '', 'label' => 'All Statuses'],
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'under_review', 'label' => 'Under Review'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'rejected', 'label' => 'Rejected'],
                    ];
                @endphp

                @foreach ($tabs as $tab)
                    <button
                        wire:click="$set('statusFilter', '{{ $tab['value'] }}')"
                        type="button"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                            @if ($statusFilter == $tab['value'])
                                border-indigo-500 text-indigo-600
                            @else
                                border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300
                            @endif
                        "
                    >
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Student Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Scholarship Batch
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Submitted At
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Score
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($submissions as $submission)
                    <tr wire:key="submission-{{ $submission->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $submission->student->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $submission->scholarshipBatch->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $submission->submission_date ? $submission->submission_date->format('d M Y, H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($submission->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('under_review') bg-blue-100 text-blue-800 @break
                                    @case('approved') bg-green-100 text-green-800 @break
                                    @case('rejected') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                {{ ucwords(str_replace('_', ' ', $submission->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $submission->final_saw_score ?? 'Not Scored' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            {{-- Link to view submission details if applicable --}}
                            {{-- <a href="#" class="text-indigo-600 hover:text-indigo-900">View Details</a> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if($statusFilter)
                                No submissions found with status "{{ ucwords(str_replace('_', ' ', $statusFilter)) }}".
                            @else
                                You have not made any submissions yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
</div>
