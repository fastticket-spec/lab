<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class AdminLogsController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('AdminLogs/Index', [
            'logs' => Activity::latest()->paginate(50)
                ->withQueryString()
                ->through(fn ($log) => [
                    'id' => $log->id,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'action_date' => $log->created_at->format('d-M-Y H:i:s')
                ])
        ]);
    }
}
