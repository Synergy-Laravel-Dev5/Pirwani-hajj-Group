<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserActivityController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('user_activity_view');
        $userActivities = UserActivity::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.activity_show', compact('userActivities'));
    }
}
