<div class="space-y-6">
    <div class="mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">View Submissions for {{ $batch->name }}</h1>
        <p class="text-sm text-gray-500">Review and manage student submissions for this scholarship batch.</p>
    </div>

    <div class="bg-white p-8 rounded-xl shadow space-y-6">
        {{-- Flash Message for Errors --}}
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Filters and Search --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="searchTerm" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" wire:model.live.debounce.300ms="searchTerm" id="searchTerm"
                       placeholder="Search by name, NIM..."
                       class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="need_revision">Need Revision</option>
                </select>
            </div>
        </div>

        {{-- Submissions Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('student.nim')">
                            NIM
                            @if($sortBy === 'student.nim')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('student.name')">
                            Student Name
                             @if($sortBy === 'student.name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                            Submission Date
                             @if($sortBy === 'created_at')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                            Status
                            @if($sortBy === 'status')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('final_saw_score')">
                            DSS Score
                            @if($sortBy === 'final_saw_score')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rank
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($submissions as $submission)
                        <tr wire:key="submission-{{ $submission->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ optional($submission->student)->nisn ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ optional($submission->student)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($submission->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('approved') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                        @case('need_revision') bg-blue-100 text-blue-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{-- Display SAW Score, format to a few decimal places if it's a float --}}
                                {{ isset($submission->final_saw_score) ? number_format((float)$submission->final_saw_score, 4) : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{-- Display Rank --}}
                                {{ $submission->rank ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.scholarship-batches.submissions.show', ['batch' => $batch->id, 'submission' => $submission->id]) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                {{-- Add more actions like approve, reject, etc. --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No submissions found for this batch matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $submissions->links() }}
        </div>

         <div class="flex justify-start gap-3 border-t pt-6">
            <a href="{{ route('admin.scholarship-batches.index') }}" wire:navigate
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                Back to Batches
            </a>
        </div>
    </div>
</div>
