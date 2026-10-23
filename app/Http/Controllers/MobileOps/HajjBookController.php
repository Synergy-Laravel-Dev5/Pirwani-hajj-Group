<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\HajjBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HajjBookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = HajjBook::latest();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $books = $query->paginate(15);
        $totalCount = HajjBook::count();

        return view('mobile-ops.books.index', compact('books', 'search', 'totalCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'file'      => 'required|file|max:51200', // max 50MB, allows any file type
        ]);

        $thumbnailPath = $request->file('thumbnail')->store('books/thumbnails', 'public');

        $uploadedFile = $request->file('file');
        $filePath     = $uploadedFile->store('books/files', 'public');
        $fileType     = strtoupper($uploadedFile->getClientOriginalExtension());
        $fileSize     = $this->formatFileSize($uploadedFile->getSize());

        HajjBook::create([
            'title'      => $request->title,
            'thumbnail'  => $thumbnailPath,
            'file_path'  => $filePath,
            'file_type'  => $fileType,
            'file_size'  => $fileSize,
        ]);

        return back()->with('success', 'Book added successfully.');
    }

    public function update(Request $request, $id)
    {
        $book = HajjBook::findOrFail($id);

        $request->validate([
            'title'     => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'file'      => 'nullable|file|max:51200',
        ]);

        $data = ['title' => $request->title];

        if ($request->hasFile('thumbnail')) {
            if ($book->thumbnail && Storage::disk('public')->exists($book->thumbnail)) {
                Storage::disk('public')->delete($book->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('books/thumbnails', 'public');
        }

        if ($request->hasFile('file')) {
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }
            $uploadedFile      = $request->file('file');
            $data['file_path'] = $uploadedFile->store('books/files', 'public');
            $data['file_type'] = strtoupper($uploadedFile->getClientOriginalExtension());
            $data['file_size'] = $this->formatFileSize($uploadedFile->getSize());
        }

        $book->update($data);

        return back()->with('success', 'Book updated successfully.');
    }

    public function destroy($id)
    {
        $book = HajjBook::findOrFail($id);

        if ($book->thumbnail && Storage::disk('public')->exists($book->thumbnail)) {
            Storage::disk('public')->delete($book->thumbnail);
        }

        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }

        $book->delete();

        return back()->with('success', 'Book deleted successfully.');
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
