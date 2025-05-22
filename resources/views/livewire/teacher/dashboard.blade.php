<div class="container mx-auto px-4 py-8">
    <div class="mb-8 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Teacher Dashboard</h1>
        <p class="text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}! Here's an overview of your scholarship activities.</p>
    </div>

    {{-- Summary Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        {{-- Active Submissions --}}
        <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Submissions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $activeSubmissionsCount }}</p>
                </div>
            </div>
        </div>

        {{-- Approved Submissions --}}
        <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 bg-opacity-20 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Approved Submissions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $approvedSubmissionsCount }}</p>
                </div>
            </div>
        </div>

        {{-- Rejected Submissions --}}
        <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-500 bg-opacity-20 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Rejected Submissions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $rejectedSubmissionsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions & Upcoming Batches --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-1 bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('teacher.scholarship-batches.open') }}" wire:navigate
                   class="block w-full text-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition-colors duration-300">
                    Submit New Application
                </a>
                <a href="{{ route('teacher.submissions.index') }}" wire:navigate
                   class="block w-full text-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg shadow-md hover:bg-gray-300 transition-colors duration-300">
                    View All My Submissions
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Upcoming Batch Deadlines</h2>
            @if ($upcomingBatches && $upcomingBatches->count() > 0)
                <ul class="space-y-3">
                    @foreach ($upcomingBatches as $batch)
                        <li class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <a href="{{ route('teacher.submissions.create-for-batch', $batch) }}" wire:navigate class="font-semibold text-indigo-600 hover:text-indigo-800">{{ $batch->name }}</a>
                                    <p class="text-xs text-gray-500">
                                        Status: <span class="font-medium @if($batch->status == 'open') text-green-600 @else text-yellow-600 @endif">{{ Str::title($batch->status) }}</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if ($batch->end_date)
                                        <p class="text-sm text-red-600 font-medium">Closes: {{ Carbon\Carbon::parse($batch->end_date)->format('d M Y') }}</p>
                                    @elseif($batch->start_date && $batch->status == 'upcoming')
                                        <p class="text-sm text-blue-600 font-medium">Opens: {{ Carbon\Carbon::parse($batch->start_date)->format('d M Y') }}</p>
                                    @else
                                        <p class="text-sm text-gray-500">No specific deadline</p>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">No upcoming scholarship batch deadlines.</p>
            @endif
        </div>
    </div>

    {{-- Recent Submissions --}}
    <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h2>
        @if ($recentSubmissions && $recentSubmissions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted On</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($recentSubmissions as $submission)
                            <tr wire:key="recent-submission-{{ $submission->id }}" class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $submission->student->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $submission->scholarshipBatch->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $submission->submission_date ? Carbon\Carbon::parse($submission->submission_date)->format('d M Y, H:i') : 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @switch($submission->status)
                                            @case('pending') bg-yellow-100 text-yellow-800 @break
                                            @case('under_review') bg-blue-100 text-blue-800 @break
                                            @case('approved') bg-green-100 text-green-800 @break
                                            @case('rejected') bg-red-100 text-red-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        {{ Str::title(str_replace('_', ' ', $submission->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600">No recent submissions found.</p>
        @endif
    </div>

    {{-- Placeholder for Notifications/Alerts --}}
    <div class="mt-8 bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition-shadow duration-300 ease-in-out">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Notifications</h2>
        <p class="text-gray-600">No new notifications at the moment.</p>
    </div>
</div>
