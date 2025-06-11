<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Admin Dashboard</h1>
        <a href="{{ route('admin.scholarship-batches.create') }}"
           class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            Create New Batch
        </a>
    </div>

    <p class="mb-6 text-gray-600">Welcome, Admin! Here's an overview of the scholarship batches.</p>

    <div class="grid gap-6 mb-8 md:grid-cols-2">
        <!-- Active Batches Card -->
        <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-200">
                Active Scholarship Batches ({{ $totalActiveBatches }})
            </h3>
            @if($activeBatches->count() > 0)
                <ul class="space-y-2">
                    @foreach($activeBatches as $batch)
                        <li class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $batch->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $batch->start_date->format('d M Y') }} - {{ $batch->end_date->format('d M Y') }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                {{ $batch->submissions_count }} Applicants
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 dark:text-gray-400">No active scholarship batches at the moment.</p>
            @endif
        </div>

        <!-- Closed Batches Card -->
        <div class="p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-200">
                Closed Scholarship Batches ({{ $totalClosedBatches }})
            </h3>
            @if($closedBatches->count() > 0)
                <ul class="space-y-2">
                    @foreach($closedBatches as $batch)
                        <li class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $batch->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Closed on: {{ $batch->end_date->format('d M Y') }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">
                                {{ $batch->submissions_count }} Applicants
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 dark:text-gray-400">No closed scholarship batches found.</p>
            @endif
        </div>
    </div>

    {{-- Further content and quick actions can be added below --}}
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Quick Management Links</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <a href="{{ route('admin.scholarship-batches.index') }}" class="block p-4 bg-white rounded-lg shadow hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                <h4 class="font-semibold text-purple-600 dark:text-purple-400">Manage All Batches</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">View, edit, and manage all scholarship batches.</p>
            </a>
            {{-- Add more links as needed, e.g., User Management, View All Submissions --}}
            <a href="#" {{-- Replace # with {{ route('admin.users.index') }} when the route is defined --}}
                class="block p-4 bg-white rounded-lg shadow hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 opacity-50 cursor-not-allowed" {{-- Added opacity and cursor-not-allowed for visual indication --}}
                title="User management route not yet defined (admin.users.index)"> {{-- Added a title for hover information --}}
                <h4 class="font-semibold text-purple-600 dark:text-purple-400">Manage Users</h4>
                <p class="text-sm text-gray-600 dark:text-gray-300">View and manage user accounts.</p>
            </a>
        </div>
    </div>
</div>
