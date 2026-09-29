<?php

namespace App\Http\Controllers;

use App\Models\HajjApplication;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HajjApplicationController extends Controller
{
    /**
     * Public Open Link Form for Clients.
     */
    public function form($package_id = null)
    {
        $packages = Package::all();
        $selectedPackage = $package_id ? Package::find($package_id) : ($packages->first() ?? null);

        return view('hajj_application.form', compact('packages', 'selectedPackage', 'package_id'));
    }

    /**
     * Public Submit Hajj Application.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'nullable|integer|exists:packages,id',
            'year' => 'nullable|string|max:50',
            'package_name' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'gender' => 'nullable|string|max:20',
            'surname' => 'nullable|string|max:150',
            'given_name' => 'required|string|max:150',
            'cnic_no' => 'required|string|max:50',
            'dob' => 'nullable|date',
            'passport_no' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'father_or_husband_name' => 'nullable|string|max:150',
            'postal_address' => 'nullable|string',
            'tehsil_code' => 'nullable|string|max:50',
            'mobile_no' => 'required|string|max:50',
            'telephone_no' => 'nullable|string|max:50',
            'is_married' => 'nullable|string|max:20',
            'fiqah' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:20',
            'performed_hajj_last_5_years' => 'nullable|string|max:10',
            'hajj_e_badal' => 'nullable|string|max:10',
            'group_worker' => 'nullable|string|max:10',
            'is_mehram_of_lady' => 'nullable|string|max:10',
            'nominee_name' => 'nullable|string|max:150',
            'nominee_contact' => 'nullable|string|max:50',
            'nominee_cnic' => 'nullable|string|max:50',
            'nominee_relation' => 'nullable|string|max:100',
            'mehram_name' => 'nullable|string|max:150',
            'mehram_relation' => 'nullable|string|max:100',
            'signature' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hajj_applications'), $filename);
            $validated['photo'] = 'uploads/hajj_applications/' . $filename;
        }

        if (empty($validated['package_name']) && !empty($validated['package_id'])) {
            $pkg = Package::find($validated['package_id']);
            if ($pkg) {
                $validated['package_name'] = $pkg->package_title ?? ($pkg->name ?? $pkg->code);
            }
        }

        $application = HajjApplication::create($validated);

        return redirect()->route('hajj-application.success', $application->id)
            ->with('success', 'Your Hajj Application has been submitted successfully!');
    }

    /**
     * Public Success / Confirmation Page.
     */
    public function success($id)
    {
        $application = HajjApplication::with('package')->findOrFail($id);

        return view('hajj_application.success', compact('application'));
    }

    /**
     * Admin Index - List all submitted applications.
     */
    public function index(Request $request)
    {
        $query = HajjApplication::with('package')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('given_name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('cnic_no', 'like', "%{$search}%")
                  ->orWhere('passport_no', 'like', "%{$search}%")
                  ->orWhere('mobile_no', 'like', "%{$search}%")
                  ->orWhere('package_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        $applications = $query->paginate(20);
        $packages = Package::all();
        $trashCount = HajjApplication::onlyTrashed()->count();

        return view('hajj_application.index', compact('applications', 'packages', 'trashCount'));
    }

    /**
     * Admin Show - View formatted Contract Form (matching Image 2).
     */
    public function show($id)
    {
        $application = HajjApplication::with('package')->findOrFail($id);

        return view('hajj_application.show', compact('application'));
    }

    /**
     * Admin Update Status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $application = HajjApplication::findOrFail($id);
        $application->update(['status' => $request->status]);

        return back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Admin Delete Application.
     */
    public function destroy($id)
    {
        $application = HajjApplication::findOrFail($id);
        $application->delete();

        return back()->with('success', 'Application moved to trash.');
    }
}
