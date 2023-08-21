<?php

namespace App\Http\Controllers;

use App\Services\PreferenceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PreferencesController extends Controller
{
    private array $pageLists = [
        ['id' => 'email', 'name' => 'Email Settings', 'iI8' => 'preferences.email'],
    ];

    public function __construct(private PreferenceService $preferenceService)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        $page = $request->page ?? 'email';

        return Inertia::render('Preferences/Index', [
            'pageLists' => $this->pageLists,
            'currentPage' => $page,
            'data' => $this->preferenceService->fetchPreference()
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|mimes:jpeg,jpg,png|max:4000',
        ]);

        return $this->preferenceService->uploadLogo($request);
    }

    public function store(Request $request)
    {
        return $this->preferenceService->updatePreference($request);
    }
}
