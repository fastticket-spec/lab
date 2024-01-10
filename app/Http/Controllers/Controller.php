<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Responses\HttpResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, HttpResponse, DispatchesJobs, ActivityLogger;
}
