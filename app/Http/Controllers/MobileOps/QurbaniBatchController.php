<?php

namespace App\Http\Controllers\MobileOps;

use App\Http\Controllers\Controller;
use App\Models\BookingPerson;
use App\Models\QurbaniBatch;
use App\Models\QurbaniBatchHajji;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QurbaniBatchController extends Controller
{
    public function index(Request $request)
    {
        $batches = QurbaniBatch::withCount([
            'hajjis',
            'hajjis as completed_hajjis_count' => function ($q) {
                $q->where('status', 'completed');
            }
        ])->latest()->get();

        return view('mobile-ops.qurbani.index', compact('batches'));
    }

    public function create()
    {
        return view('mobile-ops.qurbani.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_name'   => 'required|string|max:255',
            'qurbani_date' => 'required|date',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'title'        => 'nullable|string|max:255',
            'message'      => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $batch = QurbaniBatch::create([
            'batch_name'   => $request->batch_name,
            'qurbani_date' => $request->qurbani_date,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'title'        => $request->title ?: "Qurbani Announcement: {$request->batch_name}",
            'message'      => $request->message ?: "Dear Pilgrim, your Qurbani time slot has been scheduled from {$request->start_time} to {$request->end_time} on {$request->qurbani_date}.",
            'status'       => 'active',
            'created_by'   => Auth::id(),
            'notes'        => $request->notes,
        ]);

        return redirect()->route('mobile-ops.qurbani.show', $batch->id)
            ->with('success', 'Qurbani batch created successfully! You can now assign Hajjis.');
    }

    public function show($id)
    {
        $batch = QurbaniBatch::with(['creator', 'hajjis.person', 'hajjis.booking.client'])->findOrFail($id);

        // Fetch already assigned person IDs in THIS batch
        $assignedPersonIds = $batch->hajjis->pluck('booking_person_id')->toArray();

        // Get available booking persons for assignment
        $availablePersons = BookingPerson::with('booking.client')
            ->whereNotIn('id', $assignedPersonIds)
            ->latest('id')
            ->limit(300)
            ->get();

        return view('mobile-ops.qurbani.show', compact('batch', 'availablePersons'));
    }

    public function edit($id)
    {
        $batch = QurbaniBatch::findOrFail($id);
        return view('mobile-ops.qurbani.edit', compact('batch'));
    }

    public function update(Request $request, $id)
    {
        $batch = QurbaniBatch::findOrFail($id);

        $request->validate([
            'batch_name'   => 'required|string|max:255',
            'qurbani_date' => 'required|date',
            'start_time'   => 'required|date_format:H:i',
            'end_time'     => 'required|date_format:H:i|after:start_time',
            'title'        => 'nullable|string|max:255',
            'message'      => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $batch->update([
            'batch_name'   => $request->batch_name,
            'qurbani_date' => $request->qurbani_date,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'title'        => $request->title,
            'message'      => $request->message,
            'notes'        => $request->notes,
        ]);

        return redirect()->route('mobile-ops.qurbani.show', $batch->id)
            ->with('success', 'Qurbani batch updated successfully.');
    }

    public function destroy($id)
    {
        $batch = QurbaniBatch::findOrFail($id);
        $batch->delete();

        return redirect()->route('mobile-ops.qurbani.index')
            ->with('success', 'Qurbani batch deleted successfully.');
    }

    /**
     * Add selected Hajjis to this batch.
     */
    public function assignHajjis(Request $request, $id)
    {
        $batch = QurbaniBatch::findOrFail($id);

        $request->validate([
            'person_ids'   => 'required|array|min:1',
            'person_ids.*' => 'exists:booking_persons,id',
        ]);

        $personIds = $request->person_ids;
        $persons = BookingPerson::whereIn('id', $personIds)->get();

        $addedCount = 0;
        foreach ($persons as $person) {
            $exists = QurbaniBatchHajji::where('qurbani_batch_id', $batch->id)
                ->where('booking_person_id', $person->id)
                ->exists();

            if (!$exists) {
                QurbaniBatchHajji::create([
                    'qurbani_batch_id'  => $batch->id,
                    'booking_id'        => $person->booking_id,
                    'booking_person_id' => $person->id,
                    'status'            => 'pending',
                ]);
                $addedCount++;
            }
        }

        return redirect()->route('mobile-ops.qurbani.show', $batch->id)
            ->with('success', "{$addedCount} Hajji(s) successfully assigned to this batch.");
    }

    /**
     * Remove a Hajji from this batch.
     */
    public function removeHajji($batchId, $hajjiId)
    {
        $assignment = QurbaniBatchHajji::where('qurbani_batch_id', $batchId)
            ->where('id', $hajjiId)
            ->firstOrFail();

        $assignment->delete();

        return redirect()->route('mobile-ops.qurbani.show', $batchId)
            ->with('success', 'Hajji removed from this batch.');
    }
}
