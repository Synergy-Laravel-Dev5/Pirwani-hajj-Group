<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\HajjBook;
use App\Models\HujjajGuideline;
use App\Models\PackageInquiry;
use App\Models\TutorialVideo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'inquiries_pending'   => PackageInquiry::where('status', 'pending')->count(),
            'inquiries_contacted' => PackageInquiry::where('status', 'contacted')->count(),
            'inquiries_closed'    => PackageInquiry::where('status', 'closed')->count(),
            'inquiries_total'     => PackageInquiry::count(),
            'ziarat_total'        => Gallery::count(),
            'books_total'         => HajjBook::count(),
            'guidelines_total'    => HujjajGuideline::count(),
            'videos_total'        => TutorialVideo::count(),
        ];

        $recentInquiries = PackageInquiry::with('package')->latest()->take(5)->get();

        return view('mobile-ops.dashboard', compact('stats', 'recentInquiries'));
    }
    public function inquiries(Request $request)
    {
        $status = $request->get('status');

        $query = PackageInquiry::with('package')->latest();

        if (!empty($status) && in_array($status, ['pending', 'contacted', 'closed'])) {
            $query->where('status', $status);
        }

        $inquiries = $query->paginate(15);

        $counts = [
            'all'       => PackageInquiry::count(),
            'pending'   => PackageInquiry::where('status', 'pending')->count(),
            'contacted' => PackageInquiry::where('status', 'contacted')->count(),
            'closed'    => PackageInquiry::where('status', 'closed')->count(),
        ];

        return view('mobile-ops.inquiries.index', compact('inquiries', 'counts', 'status'));
    }

    /**
     * Update inquiry operational status
     */
    public function updateInquiryStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,closed',
        ]);

        $inquiry = PackageInquiry::findOrFail($id);
        $inquiry->status = $request->status;
        $inquiry->save();

        return back()->with('success', 'Inquiry status updated successfully.');
    }
}
