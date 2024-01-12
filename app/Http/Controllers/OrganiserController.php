<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organiser\EditOrganiserLogoRequest;
use App\Http\Requests\Organiser\OrganiserRequest;
use App\Services\OrganiserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrganiserController extends Controller
{
    public function __construct(private OrganiserService $organiserService)
    {

    }

    public function index(Request $request)
    {
        $organisers = $this->organiserService->fetchOrganisers($request);

        if (request()->expectsJson()) {
            return $this->view(['message' => 'Organisers Fetched', 'data' => $organisers], 200);
        }

        return Inertia::render('Organisers/Index', [
            'organisers' => $organisers
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Organisers/Create', [
            'editMode' => false
        ]);
    }

    public function store(OrganiserRequest $request)
    {
        try {
            $organiserLogo = $this->organiserService->getFile($request, 'organiser_logo');
            $organiserLogoArabic = $this->organiserService->getFile($request, 'organiser_logo_arabic');
            $organiserBanner = $this->organiserService->getFile($request, 'organiser_banner');
            $organiserBannerArabic = $this->organiserService->getFile($request, 'organiser_banner_arabic');

            $organiser = $this->organiserService->createOrganiser(
                $request->except(['organiser_logo', 'organiser_logo_arabic', 'organiser_banner', 'organiser_banner_arabic']),
                $organiserLogo, $organiserLogoArabic, $organiserBanner, $organiserBannerArabic);

            if (!$organiser) {
                return $this->view(
                    data: [
                        'message' => 'Could not create organiser'
                    ], statusCode: Response::HTTP_BAD_REQUEST, component: 'Organisers\Create');
            }

            return $this->view(
                data: [
                    'message' => 'Organiser created successfully',
                    'data' => $organiser
                ], flashMessage: 'Organiser created successfully', component: '/organisers', returnType: 'redirect'
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            throw $th;
        }

    }

    public function edit(string $id): \Inertia\Response
    {
        return Inertia::render('Organisers/Create', [
            'editMode' => true,
            'organiser' => $this->organiserService->find($id)
        ]);
    }

    public function update(OrganiserRequest $request, string $organiserId)
    {
        $organiser = $this->organiserService->find($organiserId);

        $updateOrganiser = $this->organiserService->update($request->except(['organiser_logo', 'organiser_logo_arabic']), $organiserId);

        if (!$updateOrganiser) {
            return $this->view(
                data: [
                    'message' => 'Could not update organiser'
                ], statusCode: Response::HTTP_BAD_REQUEST, component: 'Organisers\Edit');
        }

        $this->logActivity("updated organiser details for $organiser->name", $organiser);

        $message = 'Organiser updated successfully';

        return $this->view(
            data: [
                'message' => $message,
                'data' => $organiser->refresh()
            ], flashMessage: $message, component: '/organisers', returnType: 'redirect'
        );
    }

    public function updateLogos(EditOrganiserLogoRequest $request, string $organiserId)
    {
        $organiser = $this->organiserService->findOneOrFail($organiserId);

        $updateOrganiser = $this->organiserService->editOrganiserLogos($request->all(), $organiser);

        if (!$updateOrganiser) {
            return $this->view(
                data: [
                    'message' => 'Could not update organiser'
                ], statusCode: Response::HTTP_BAD_REQUEST, component: 'Organisers\Edit');
        }
        $message = 'Organiser updated successfully';

        $this->logActivity("updated organiser logo for $organiser->name", $organiser);

        return $this->view(
            data: [
                'message' => $message,
                'data' => $organiser
            ], flashMessage: $message, component: '/organisers', returnType: 'redirect'
        );
    }

    public function loginOrganiser(string $organiserId)
    {
        $organiser = $this->organiserService->findOneOrFail($organiserId);

        $setActiveOrganiser = auth()->user()->account()->update(['active_organiser' => $organiser->id]);

//        $setActiveOrganiser = $organiser->account()->update(['active_organiser' => $organiser->id]);


        if (!$setActiveOrganiser) {
            return $this->view(
                data: [
                    'message' => 'Could not log in organiser'
                ], statusCode: Response::HTTP_BAD_REQUEST, component: 'Organisers\Edit' //change component
            );
        }

        $this->logActivity("logged in to $organiser->name organiser", $organiser);

        return $this->view(
            data: [
                'message' => 'Organiser logged in',
                'data' => $organiser
            ], component: '/organisers', returnType: 'redirect'
        );
    }

    public function logoutOrganiser(string $organiserId)
    {
        $organiser = $this->organiserService->findOneOrFail($organiserId);

        $setActiveOrganiser = auth()->user()->account()->update(['active_organiser' => null]);

        if (!$setActiveOrganiser) {
            return $this->view(
                data: [
                    'message' => 'Could not log out organiser'
                ], statusCode: Response::HTTP_BAD_REQUEST, component: 'Organisers\Edit' //change component
            );
        }

        $this->logActivity("logged out of $organiser->name organiser", $organiser);

        return $this->view(
            data: [
                'message' => 'Organiser logged out',
                'data' => $organiser
            ], component: '/organisers', returnType: 'redirect'
        );
    }
}
