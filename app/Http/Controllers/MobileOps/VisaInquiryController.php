<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\VisaInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisaInquiryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $query = VisaInquiry::with(['country', 'visaType'])->latest();

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . trim($search) . '%')
                    ->orWhere('phone_number', 'like', '%' . trim($search) . '%')
                    ->orWhere('email', 'like', '%' . trim($search) . '%')
                    ->orWhere('cnic', 'like', '%' . trim($search) . '%');
            });
        }

        $inquiries = $query->paginate(15);
        $counts = [
            'all'         => VisaInquiry::count(),
            'pending'     => VisaInquiry::where('status', 'pending')->count(),
            'contacted'   => VisaInquiry::where('status', 'contacted')->count(),
            'in_progress' => VisaInquiry::where('status', 'in_progress')->count(),
            'completed'   => VisaInquiry::where('status', 'completed')->count(),
        ];

        return view('mobile-ops.visa-inquiries.index', compact('inquiries', 'status', 'search', 'counts'));
    }

    public function show($id)
    {
        $inquiry = VisaInquiry::with(['country', 'visaType'])->findOrFail($id);
        return view('mobile-ops.visa-inquiries.show', compact('inquiry'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,in_progress,completed,cancelled',
        ]);

        $inquiry = VisaInquiry::findOrFail($id);
        $inquiry->update(['status' => $request->status]);

        return back()->with('success', 'Inquiry status updated successfully.');
    }

    public function destroy($id)
    {
        $inquiry = VisaInquiry::findOrFail($id);

        foreach (['passport_document', 'photo_document', 'other_document'] as $fileKey) {
            if ($inquiry->$fileKey && Storage::disk('public')->exists($inquiry->$fileKey)) {
                Storage::disk('public')->delete($inquiry->$fileKey);
            }
        }

        $inquiry->delete();
        return back()->with('success', 'Inquiry deleted successfully.');
    }
}
