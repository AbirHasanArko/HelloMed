@extends('layouts.app')

@section('title', 'Lab Tests')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Lab Test Requests</h1>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-md bg-green-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <a href="{{ route('staff.lab-tests.index', ['status' => 'pending']) }}" 
                   class="{{ $currentStatus === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm">
                    Pending Requests
                </a>
                <a href="{{ route('staff.lab-tests.index', ['status' => 'completed']) }}" 
                   class="{{ $currentStatus === 'completed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm">
                    Completed Requests
                </a>
            </nav>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor & Dept</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($labTests as $test)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $test->appointment->patient_name }}</div>
                                <div class="text-sm text-gray-500">{{ $test->appointment->patient_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Dr. {{ $test->appointment->doctor->user->name ?? 'Unknown' }}</div>
                                <div class="text-sm text-gray-500">{{ $test->appointment->doctor->department->name ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $test->test_name }}</div>
                                <div class="text-sm text-gray-500">{{ $test->created_at->format('M d, Y h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 line-clamp-2" title="{{ $test->notes }}">{{ $test->notes ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($test->status === 'pending')
                                    <button type="button" 
                                            onclick="openUploadModal({{ $test->id }}, '{{ addslashes($test->test_name) }}', '{{ addslashes($test->appointment->patient_name) }}')"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Upload Result
                                    </button>
                                @else
                                    <a href="{{ route('lab-tests.download', $test) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No {{ $currentStatus }} lab test requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($labTests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $labTests->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeUploadModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Upload Lab Result
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Patient: <span id="modalPatientName" class="font-medium text-gray-900"></span><br>
                                    Test: <span id="modalTestName" class="font-medium text-gray-900"></span>
                                </p>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Result File (PDF, JPG, PNG)</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Upload a file</span>
                                                    <input id="file-upload" name="result_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500" id="file-name-display">
                                                up to 5MB
                                            </p>
                                        </div>
                                    </div>
                                    @error('result_file')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Upload & Complete
                    </button>
                    <button type="button" onclick="closeUploadModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openUploadModal(testId, testName, patientName) {
        document.getElementById('modalTestName').innerText = testName;
        document.getElementById('modalPatientName').innerText = patientName;
        document.getElementById('uploadForm').action = '/staff/lab-tests/' + testId + '/upload';
        document.getElementById('uploadModal').classList.remove('hidden');
        document.getElementById('file-upload').value = '';
        document.getElementById('file-name-display').innerText = 'up to 5MB';
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
    }

    document.getElementById('file-upload').addEventListener('change', function(e) {
        if(e.target.files.length > 0) {
            document.getElementById('file-name-display').innerText = e.target.files[0].name;
        }
    });

    @if($errors->has('result_file'))
        // Keep modal open if there was a validation error
        // Note: this requires knowing which test failed, ideally from flash data.
        // For simplicity, we just show the error message.
    @endif
</script>
@endsection
