<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index()
    {
        $activities = ActivityLog::with(['user', 'shipment'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('activity-log.index', compact('activities'));
    }
}
