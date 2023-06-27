<?php

namespace App\Http\Controllers;

use App\Http\Requests\Events\EventEditRequest;
use App\Http\Requests\Events\EventRequest;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class EventController extends Controller
{
    public function __construct(private EventService $eventService)
    {
    }

    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Events/Index', [
            'events' => $this->eventService->fetchEvents($request)
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Events/Create', [
            'editMode' => false
        ]);
    }

    public function store(EventRequest $request)
    {
        $eventImage = $this->eventService->getFile($request, 'event_image_upload');
        $eventBannerImage = $this->eventService->getFile($request, 'event_banner_upload');

        $event = $this->eventService->createEvent(
            $request->except([
                'event_image',
                'event_banner_upload'
            ]),
            $eventImage,
            $eventBannerImage
        );

        if (!$event) {
            return $this->view(
                data: [
                    'message' => 'Could not create event'
                ], statusCode: Response::HTTP_BAD_REQUEST, flashMessage: 'Event could not be created', messageType: 'danger', component: '/events/create', returnType: 'redirect');
        }

        $message = 'Event created successfully';

        return $this->view(
            data: [
                'message' => $message,
                'data' => $event
            ], flashMessage: $message, component: '/events', returnType: 'redirect'
        );
    }

    public function edit(string $eventId): \Inertia\Response
    {
        $event = $this->eventService->find($eventId);

        return Inertia::render('Events/Create', [
            'editMode' => true,
            'event' => $event
        ]);
    }

    public function duplicateEvent(string $eventId)
    {
        $this->eventService->processDuplicateEvent($eventId);
        return redirect('/events');
    }

    public function update(EventEditRequest $request, string $eventId)
    {
        $event = $this->eventService->findOneOrFail($eventId);
        $eventImage = $this->eventService->getFile($request, 'event_image_upload');
        $eventBanner = $this->eventService->getFile($request, 'event_banner_upload');

        $updatedEvent = $this->eventService->editEvent(
            $event,
            $request->except([
                'event_image',
                'event_banner',
                'event_attachment'
            ]),
            $eventImage,
            $eventBanner,
        );

        if (!$updatedEvent) {
            return $this->view(
                data: [
                    'message' => 'Could not update event'
                ], statusCode: Response::HTTP_BAD_REQUEST, component: 'Events\Create');
        }

        $updatedEvent = $this->eventService->find($eventId);

        $message = 'Event updated successfully';

        return $this->view(
            data: [
                'message' => $message,
                'data' => $updatedEvent,
            ], flashMessage: $message, component: $request->return_url ? $request->return_url : '/events', returnType: 'redirect');
    }
//    public function fetchOrganiserEvents()
//    {
//        return $this->eventService->fetchAllOrganiserEvents();
//    }
}
