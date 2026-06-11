<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LabTestRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class LabTestController extends Controller
{
    public function index(): View
    {
        $status = request('status', 'pending');
        
        $labTests = LabTestRequest::query()
            ->with(['appointment.user.patientProfile', 'appointment.doctor.department'])
            ->when($status === 'pending', fn ($query) => $query->where('status', 'pending'))
            ->when($status === 'completed', fn ($query) => $query->where('status', 'completed'))
            ->latest()
            ->paginate(15);

        return view('staff.lab_tests.index', [
            'labTests' => $labTests,
            'currentStatus' => $status,
        ]);
    }

    public function upload(Request $request, LabTestRequest $labTest): RedirectResponse
    {
        abort_unless($labTest->status === 'pending', 400, 'Only pending lab tests can be uploaded.');

        $validated = $request->validate([
            'result_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
        ]);

        $file = $request->file('result_file');
        if (!$file) {
            return back()->withErrors(['result_file' => 'File is required']);
        }

        $path = $file->store('lab_results', 'local');

        $labTest->update([
            'status' => 'completed',
            'result_file_path' => $path,
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Lab test result uploaded successfully.');
    }
}
