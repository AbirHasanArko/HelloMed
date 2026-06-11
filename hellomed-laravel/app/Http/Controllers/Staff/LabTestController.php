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
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        
        $patientName = $request->query('patient_name');
        $doctorName = $request->query('doctor_name');
        $testName = $request->query('test_name');
        $date = $request->query('date');
        
        $labTests = LabTestRequest::query()
            ->with(['appointment.user.patientProfile', 'appointment.doctor.user', 'appointment.doctor.department'])
            ->when($status === 'pending', fn ($query) => $query->where('status', 'pending'))
            ->when($status === 'completed', fn ($query) => $query->where('status', 'completed'))
            ->when($testName, fn ($query) => $query->where('test_name', 'like', '%' . $testName . '%'))
            ->when($date, fn ($query) => $query->whereDate('created_at', $date))
            ->when($patientName, function ($query) use ($patientName) {
                $query->whereHas('appointment', function ($q) use ($patientName) {
                    $q->where('patient_name', 'like', '%' . $patientName . '%');
                });
            })
            ->when($doctorName, function ($query) use ($doctorName) {
                $query->whereHas('appointment.doctor.user', function ($q) use ($doctorName) {
                    $q->where('name', 'like', '%' . $doctorName . '%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $patientNames = \App\Models\Appointment::select('patient_name')->distinct()->pluck('patient_name');
        $doctorNames = \App\Models\User::whereHas('doctorProfile')->pluck('name');
        $availableTests = \App\Models\AvailableTest::select('name')->distinct()->pluck('name');

        return view('staff.diagnostic_services.index', [
            'labTests' => $labTests,
            'currentStatus' => $status,
            'patientNames' => $patientNames,
            'doctorNames' => $doctorNames,
            'availableTests' => $availableTests,
            'filters' => [
                'patient_name' => $patientName,
                'doctor_name' => $doctorName,
                'test_name' => $testName,
                'date' => $date,
            ],
        ]);
    }

    public function markAsPaid(LabTestRequest $labTest): RedirectResponse
    {
        abort_unless($labTest->status === 'pending', 400, 'Cannot update payment status of completed diagnostic services.');

        $labTest->update([
            'payment_status' => 'paid',
        ]);

        return back()->with('status', 'Diagnostic service marked as paid successfully.');
    }

    public function upload(Request $request, LabTestRequest $labTest): RedirectResponse
    {
        abort_unless($labTest->status === 'pending', 400, 'Only pending diagnostic services can be uploaded.');
        abort_unless($labTest->payment_status === 'paid', 400, 'Cannot upload results for unpaid diagnostic services.');

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

        return back()->with('status', 'Diagnostic service result uploaded successfully.');
    }

    public function removeResult(LabTestRequest $labTest): RedirectResponse
    {
        abort_unless($labTest->status === 'completed', 400, 'Only completed diagnostic services have results to remove.');

        if ($labTest->result_file_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($labTest->result_file_path);
        }

        $labTest->update([
            'status' => 'pending',
            'result_file_path' => null,
            'uploaded_by' => null,
        ]);

        return back()->with('status', 'Diagnostic service result removed successfully. Test is marked as pending again.');
    }
}
