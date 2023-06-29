<?php

namespace App\Services;

use App\Models\AccessLevel;
use App\Models\EventSurvey;
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;

class AccessLevelsService extends BaseRepository
{
    public function __construct(AccessLevel $model)
    {
        parent::__construct($model);
    }

    public function fetchAccessLevels(Request $request, string $eventId)
    {
        return $this->model->query()
            ->with(['event', 'surveyAccessLevels.surveys'])
            ->whereEventId($eventId)
            ->latest()
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(function ($accessLevel) {
                $quantity = $accessLevel->quantity_available;

                return [
                    'id' => $accessLevel->id,
                    'title' => $accessLevel->title,
                    'title_arabic' => $accessLevel->title_arabic,
                    'quantity_available' => $quantity,
                    'quantity_filled' => $accessLevel->quantity_filled,
                    'event' => $accessLevel->event,
                    'status' => $accessLevel->status,
                    'has_surveys' => !!optional($accessLevel->surveyAccessLevels)->surveys
                ];
            });
    }

    public function createAccessLevel(array $data, string $eventId)
    {
        try {
            $accessLevel = $this->create($data + ['event_id' => $eventId]);

            $message = 'Access level created';
            return $this->view(data: ['access_level' => $accessLevel, 'message' => $message], flashMessage: $message, component: "/event/$eventId/access-levels", returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);
            $message = 'Could not create access level';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/access-levels/create", returnType: 'redirect');
        }
    }

    public function updateAccessLevel(Request $request, string $eventId, string $accessLevelId)
    {
        try {
            $this->find($accessLevelId)->update($request->all());
            $message = 'Access level updated successfully.';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/access-levels", returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);
            $message = 'Could not update access level';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/access-levels/$accessLevelId/edit", returnType: 'redirect');
        }
    }

    public function updateAccessLevelStatus(Request $request, string $eventId, string $accessLevelId)
    {
        try {
            $accessLevel = $this->find($accessLevelId);
            $accessLevel->update(['status' => $accessLevel->status ? 0 : 1]);

            $message = 'Access level status updated successfully.';

            return $this->view(data: ['message' => $message], flashMessage: $message, component: "/event/$eventId/access-levels", returnType: 'redirect');
        } catch (\Throwable $th) {
            \Log::error($th);
            $message = 'Could not update access level status';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: "/event/$eventId/access-levels/$accessLevelId/edit", returnType: 'redirect');
        }
    }
}
