<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Events/Event/Dashboard', []);
    }
}
