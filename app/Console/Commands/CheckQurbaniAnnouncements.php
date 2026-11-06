<?php

namespace App\Console\Commands;

use App\Models\QurbaniBatch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckQurbaniAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qurbani:check-announcements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks scheduled Qurbani batches and fires announcements when the start time is reached';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // Find batches scheduled for today whose start_time <= current time and status is 'scheduled'
        // or batches past today that are still marked as 'scheduled'
        $batches = QurbaniBatch::where('status', 'scheduled')
            ->where(function ($query) use ($today, $currentTime) {
                $query->where('qurbani_date', '<', $today)
                    ->orWhere(function ($q) use ($today, $currentTime) {
                        $q->where('qurbani_date', '=', $today)
                          ->where('start_time', '<=', $currentTime);
                    });
            })
            ->with('hajjis')
            ->get();

        if ($batches->isEmpty()) {
            $this->info('No Qurbani announcements due at this time.');
            return 0;
        }

        $firedCount = 0;

        foreach ($batches as $batch) {
            $batch->update([
                'status'   => 'fired',
                'fired_at' => Carbon::now(),
            ]);

            // Update linked hajjis status
            $batch->hajjis()->update([
                'status'      => 'notified',
                'notified_at' => Carbon::now(),
            ]);

            $firedCount++;
            $this->info("Fired Qurbani announcement for batch: [{$batch->id}] {$batch->batch_name} with {$batch->hajjis->count()} hajjis.");
            Log::info("Qurbani Announcement Fired: Batch ID {$batch->id} ({$batch->batch_name}) triggered at " . Carbon::now()->toDateTimeString());
        }

        $this->info("Successfully fired {$firedCount} Qurbani announcement(s).");
        return 0;
    }
}
