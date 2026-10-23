<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');

        $query = Gallery::with('images')->latest();
        if (!empty($category) && in_array($category, ['makkah', 'madina', 'other'])) {
            $query->where('category', $category);
        }

        $galleries = $query->paginate(15);

        $counts = [
            'all'    => Gallery::count(),
            'makkah' => Gallery::where('category', 'makkah')->count(),
            'madina' => Gallery::where('category', 'madina')->count(),
            'other'  => Gallery::where('category', 'other')->count(),
        ];

        return view('mobile-ops.gallery.index', compact('galleries', 'category', 'counts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'    => 'required|in:makkah,madina,other',
            'title'       => 'required|string|max:200',
            'images'      => 'required|array|min:1|max:10',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'description' => 'nullable|string|max:1000',
        ], [
            'images.required' => 'Please select at least 1 photo for this Ziarat.',
            'images.max'      => 'You cannot upload more than 10 images per Ziarat.',
        ]);

        DB::beginTransaction();
        try {
            $files = $request->file('images');
            $coverPath = $files[0]->store('gallery', 'public');

            $gallery = Gallery::create([
                'category'    => $request->category,
                'title'       => $request->title,
                'image'       => $coverPath,
                'description' => $request->description,
            ]);

            foreach ($files as $index => $file) {
                $path = ($index === 0) ? $coverPath : $file->store('gallery', 'public');
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path,
                    'is_primary' => ($index === 0),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Ziarat created successfully with ' . count($files) . ' image(s).');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save Ziarat: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $gallery = Gallery::with('images')->findOrFail($id);

        $request->validate([
            'category'    => 'required|in:makkah,madina,other',
            'title'       => 'required|string|max:200',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'description' => 'nullable|string|max:1000',
        ]);

        $existingCount = $gallery->images()->count();
        $newFiles      = $request->file('images') ?? [];
        $newCount      = count($newFiles);

        if (($existingCount + $newCount) > 10) {
            $remaining = max(0, 10 - $existingCount);
            return back()->withInput()->with('error', "Maximum limit is 10 images. This Ziarat already has {$existingCount} images. You can only attach {$remaining} more.");
        }

        DB::beginTransaction();
        try {
            $gallery->update([
                'category'    => $request->category,
                'title'       => $request->title,
                'description' => $request->description,
            ]);

            foreach ($newFiles as $file) {
                $path = $file->store('gallery', 'public');
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path,
                    'is_primary' => false,
                ]);
            }

            // Sync legacy cover column if it became null
            if (empty($gallery->image)) {
                $firstImage = $gallery->images()->first();
                if ($firstImage) {
                    $gallery->update(['image' => $firstImage->image_path]);
                }
            }

            DB::commit();
            return back()->with('success', 'Ziarat details and photos updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function deleteImage($id)
    {
        $image       = GalleryImage::findOrFail($id);
        $galleryId   = $image->gallery_id;
        $totalImages = GalleryImage::where('gallery_id', $galleryId)->count();

        if ($totalImages <= 1) {
            return back()->with('error', 'Cannot delete photo: A Ziarat must contain at least one image.');
        }

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $next = GalleryImage::where('gallery_id', $galleryId)->first();
            if ($next) {
                $next->update(['is_primary' => true]);
                Gallery::where('id', $galleryId)->update(['image' => $next->image_path]);
            }
        }

        return back()->with('success', 'Photo removed successfully.');
    }

    public function setPrimaryImage($id)
    {
        $image = GalleryImage::findOrFail($id);

        GalleryImage::where('gallery_id', $image->gallery_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        Gallery::where('id', $image->gallery_id)->update(['image' => $image->image_path]);

        return back()->with('success', 'Cover photo updated successfully.');
    }

    public function destroy($id)
    {
        $gallery = Gallery::with('images')->findOrFail($id);

        foreach ($gallery->images as $img) {
            if (Storage::disk('public')->exists($img->image_path)) {
                Storage::disk('public')->delete($img->image_path);
            }
        }

        if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();

        return back()->with('success', 'Ziarat item and all associated photos deleted successfully.');
    }
}
