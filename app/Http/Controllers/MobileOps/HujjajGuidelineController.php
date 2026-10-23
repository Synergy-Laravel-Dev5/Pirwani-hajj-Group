<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\HujjajGuideline;
use Illuminate\Http\Request;

class HujjajGuidelineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = HujjajGuideline::latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . trim($search) . '%')
                    ->orWhere('sub_heading', 'like', '%' . trim($search) . '%');
            });
        }

        $guidelines = $query->paginate(15);
        $totalCount = HujjajGuideline::count();

        return view('mobile-ops.guidelines.index', compact('guidelines', 'search', 'totalCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'content'     => 'required|string',
        ]);

        HujjajGuideline::create([
            'title'       => $request->title,
            'sub_heading' => $request->sub_heading,
            'content'     => $request->content,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Guideline published successfully.');
    }

    public function update(Request $request, $id)
    {
        $guideline = HujjajGuideline::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'content'     => 'required|string',
        ]);

        $guideline->update([
            'title'       => $request->title,
            'sub_heading' => $request->sub_heading,
            'content'     => $request->content,
            'is_active'   => $request->has('is_active'),
        ]);

        return back()->with('success', 'Guideline updated successfully.');
    }

    public function destroy($id)
    {
        $guideline = HujjajGuideline::findOrFail($id);
        $guideline->delete();

        return back()->with('success', 'Guideline deleted successfully.');
    }
}
