<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\TutorialVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TutorialVideosController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = TutorialVideo::latest();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $videos = $query->paginate(15);
        $totalCount = TutorialVideo::count();

        return view('mobile-ops.videos.index', compact('videos', 'search', 'totalCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'source_type' => 'required|in:file,link',
            'video_file'  => 'required_if:source_type,file|nullable|mimetypes:video/mp4,video/quicktime,video/webm|max:102400', // max 100MB
            'video_link'  => 'required_if:source_type,link|nullable|url|max:500',
        ], [
            'video_file.required_if' => 'Please upload a video file when File Upload is selected.',
            'video_link.required_if' => 'Please provide a valid video link when External URL is selected.',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('videos/thumbnails', 'public');
        }

        $videoFilePath = null;
        $videoLink = null;

        if ($request->source_type === 'file') {
            $videoFilePath = $request->file('video_file')->store('videos/files', 'public');
        } else {
            $videoLink = $request->video_link;
        }

        TutorialVideo::create([
            'title'       => $request->title,
            'description' => $request->description,
            'thumbnail'   => $thumbnailPath,
            'video_type'  => $request->source_type,
            'video_file'  => $videoFilePath,
            'video_link'  => $videoLink,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Video tutorial uploaded successfully.');
    }

    public function update(Request $request, $id)
    {
        $video = TutorialVideo::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'source_type' => 'required|in:file,link',
            'video_file'  => 'nullable|mimetypes:video/mp4,video/quicktime,video/webm|max:102400',
            'video_link'  => 'nullable|url|max:500',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'video_type'  => $request->source_type,
            'is_active'   => $request->has('is_active'),
        ];

        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('videos/thumbnails', 'public');
        }

        if ($request->source_type === 'file') {
            if ($request->hasFile('video_file')) {
                if ($video->video_file && Storage::disk('public')->exists($video->video_file)) {
                    Storage::disk('public')->delete($video->video_file);
                }
                $data['video_file'] = $request->file('video_file')->store('videos/files', 'public');
            }
            $data['video_link'] = null;
        } else {
            // Switching to or updating link
            if ($video->video_file && Storage::disk('public')->exists($video->video_file)) {
                Storage::disk('public')->delete($video->video_file);
            }
            $data['video_file'] = null;
            $data['video_link'] = $request->video_link ?? $video->video_link;
        }

        $video->update($data);

        return back()->with('success', 'Video updated successfully.');
    }

    public function destroy($id)
    {
        $video = TutorialVideo::findOrFail($id);

        if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
            Storage::disk('public')->delete($video->thumbnail);
        }

        if ($video->video_file && Storage::disk('public')->exists($video->video_file)) {
            Storage::disk('public')->delete($video->video_file);
        }

        $video->delete();

        return back()->with('success', 'Video deleted successfully.');
    }
}
