<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TrainingSessionController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('training_session_view');
        $trainingSessions = TrainingSession::latest()->paginate(20);
        return view('training_sessions.index', compact('trainingSessions'));
    }

    public function create()
    {
        $this->authorize('training_session_create');
        $trainingSession = new TrainingSession();
        return view('training_sessions.create', compact('trainingSession'));
    }

    public function store(Request $request)
    {
        $this->authorize('training_session_create');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'session_date' => 'nullable|date',
            'session_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $trainingSession = TrainingSession::create($data);

            DB::commit();

            if (function_exists('logUserActivity')) {
                logUserActivity('Training Session Created', 'Training Session: ' . $trainingSession->name, $trainingSession->id, 'TrainingSession');
            }

            return redirect()->route('training-session.index')->with('success', 'Training Session Created Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('training_session_edit');
        $trainingSession = TrainingSession::findOrFail($id);
        return view('training_sessions.edit', compact('trainingSession'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('training_session_edit');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'session_date' => 'nullable|date',
            'session_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $trainingSession = TrainingSession::findOrFail($id);
            $trainingSession->update($data);

            DB::commit();

            if (function_exists('logUserActivity')) {
                logUserActivity('Training Session Updated', 'Training Session: ' . $trainingSession->name, $trainingSession->id, 'TrainingSession');
            }

            return redirect()->route('training-session.index')->with('success', 'Training Session Updated Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('training_session_trash');
        $trainingSession = TrainingSession::findOrFail($id);

        if (function_exists('logUserActivity')) {
            logUserActivity('Training Session Deleted', 'Training Session: ' . $trainingSession->name, $trainingSession->id, 'TrainingSession');
        }

        $trainingSession->delete();

        return back()->with('success', 'Training Session Deleted');
    }
}
