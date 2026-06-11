<?php

namespace App\Http\Controllers;

use App\Models\LabTestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LabTestDownloadController extends Controller
{
    public function __invoke(Request $request, LabTestRequest $labTest): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user, 403, 'Unauthorized access to lab test result.');

        $isPatient = $user->role === 'patient' && $labTest->appointment->user_id === $user->id;
        $isDoctor = $user->role === 'doctor' && $labTest->appointment->doctor_id === $user->doctorProfile?->id;
        $isStaffOrAdmin = in_array($user->role, ['staff', 'admin'], true);

        abort_unless($isPatient || $isDoctor || $isStaffOrAdmin, 403, 'Unauthorized access to lab test result.');
        abort_unless($labTest->status === 'completed' && $labTest->result_file_path, 404, 'Lab test result not found.');

        $filePath = $labTest->result_file_path;
        abort_unless(Storage::disk('local')->exists($filePath), 404, 'File not found on server.');

        return Storage::disk('local')->download($filePath, "Lab_Result_{$labTest->test_name}.pdf");
    }
}
