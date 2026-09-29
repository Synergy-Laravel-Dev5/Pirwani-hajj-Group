<?php

namespace App\Http\Controllers;

use App\Models\Train;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TrainController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('train_view');

        $trains = Train::latest()->get();
        $trashCount = Train::onlyTrashed()->count();

        return view('train.index', compact('trains', 'trashCount'));
    }

    public function create()
    {
        return redirect()->route('train.index');
    }

    public function store(Request $request)
    {
        $this->authorize('train_create');

        $request->validate([
            'train_name' => 'required|string|max:255',
            'train_code' => 'nullable|string|max:100',
            'status'     => 'required|in:active,inactive',
        ]);

        try {
            $train = Train::create($request->only([
                'train_name',
                'train_code',
                'status',
            ]));

            logUserActivity('Train Created', 'Name: ' . $train->train_name . ' | Code: ' . $train->train_code, $train->id, 'Train');

            return redirect()->route('train.index')->with('success', 'Train created successfully.');
        } catch (Exception $e) {
            Log::error('Train creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating train: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('train_edit');

        $train = Train::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($train);
        }

        return redirect()->route('train.index');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('train_edit');

        $train = Train::findOrFail($id);

        $request->validate([
            'train_name' => 'required|string|max:255',
            'train_code' => 'nullable|string|max:100',
            'status'     => 'required|in:active,inactive',
        ]);

        try {
            $train->update($request->only([
                'train_name',
                'train_code',
                'status',
            ]));

            logUserActivity('Train Updated', 'Name: ' . $train->train_name . ' | Code: ' . $train->train_code, $train->id, 'Train');

            return redirect()->route('train.index')->with('success', 'Train updated successfully.');
        } catch (Exception $e) {
            Log::error('Train update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating train: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('train_trash');

        try {
            $train = Train::findOrFail($id);
            $train->delete();

            logUserActivity('Train Deleted', 'Name: ' . $train->train_name . ' | Code: ' . $train->train_code, $train->id, 'Train');

            return redirect()->route('train.index')->with('success', 'Train moved to trash successfully.');
        } catch (Exception $e) {
            Log::error('Train deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting train.');
        }
    }

    public function trash()
    {
        $this->authorize('train_trash_view');

        $trains = Train::onlyTrashed()->latest()->get();

        return view('train.trash', compact('trains'));
    }

    public function restore($id)
    {
        $this->authorize('train_restore');

        try {
            $train = Train::onlyTrashed()->findOrFail($id);
            $train->restore();

            logUserActivity('Train Restored', 'Name: ' . $train->train_name . ' | Code: ' . $train->train_code, $train->id, 'Train');

            return redirect()->route('train.trash')->with('success', 'Train restored successfully.');
        } catch (Exception $e) {
            Log::error('Train restore failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error restoring train.');
        }
    }
}
