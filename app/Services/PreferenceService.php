<?php

namespace App\Services;

use App\Models\Preference;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PreferenceService extends BaseRepository
{
    use HasFile;
    protected string $images_path;

    public function __construct(Preference $model, private FileService $file)
    {
        parent::__construct($model);

        $this->images_path = config('filesystems.directory') . "preferences/";
    }

    public function fetchPreference()
    {
        $activeOrganiser = auth()->user()->activeOrganiser();

        return $this->model->query()
            ->whereOrganiserId($activeOrganiser)
            ->first();
    }

    public function uploadLogo(Request $request)
    {
        $route = "/organiser-preferences";
        try {
            $img = $request->file('logo');

            $path = $this->uploadFile($img, 'email-', '-logo-', 1400);

            if (!$path) {
                return false;
            }

            $activeOrganiser = auth()->user()->activeOrganiser();

            $this->updateOrCreate(['organiser_id' => $activeOrganiser], ['email_logo_url' => Storage::disk(config('filesystems.default'))->url($path)]);
            $message = 'Logo uploaded successfully';

            return $this->view(
                data: [
                    'message' => $message,
                ],
                flashMessage: $message,
                component: $route,
                returnType: 'redirect'
            );
        } catch (\Throwable $th) {
            \Log::error($th);

            $message = 'An error occurred while uploading logo!';

            return $this->view(data: ['message' => $message], flashMessage: $message, messageType: 'danger', component: $route, returnType: 'redirect');
        }
    }

    public function updatePreference(Request $request)
    {
        $activeOrganiser = auth()->user()->activeOrganiser();

        $this->updateOrCreate(['organiser_id' => $activeOrganiser], $request->all());

        $message = 'Updated successfully';
        return $this->view(
            data: [
                'message' => $message,
            ],
            flashMessage: $message,
            component: "/organiser-preferences",
            returnType: 'redirect'
        );
    }
}
