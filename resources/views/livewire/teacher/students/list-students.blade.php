<div> {{-- 👈 Single root wrapper for the entire component --}}

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b pb-4 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Students</h1>
            <p class="text-sm text-gray-500">View and manage student information.</p>
        </div>
        <a href="{{ route('teacher.students.create') }}" wire:navigate
            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Student
        </a>
    </div>

    {{-- Search --}}
    <div class="mt-6">
        <label for="search" class="sr-only">Search Students</label>
        <div class="relative">
            <input
                type="text"
                id="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by name or NIS..."
                class="block w-full pl-4 pr-10 py-3 text-sm border border-gray-300 rounded-md placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="mt-6 overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['NIS', 'Name', 'Class', 'Date of Birth', 'Address'] as $header)
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($students as $student)
                    <tr wire:key="student-{{ $student->id }}" class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $student->nis }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $student->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $student->class ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ optional($student->date_of_birth)->format('d M Y') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $student->address ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pt-4">
        {{ $students->links() }}
    </div>

</div> {{-- 👈 Closing root wrapper --}}
