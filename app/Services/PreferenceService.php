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

            $path = $this->uploadFile($img, 'email-', "-$request->image_type-", 1400);

            if (!$path) {
                return false;
            }

            $activeOrganiser = auth()->user()->activeOrganiser();

            if ($request->image_type == 'logo') {
                $this->updateOrCreate(
                    ['organiser_id' => $activeOrganiser],
                    ['email_logo_url' => Storage::disk(config('filesystems.default'))->url($path)]
                );
                $message = 'Logo uploaded successfully';
            } else if ($request->image_type == 'headerImage') {
                $this->updateOrCreate(
                    ['organiser_id' => $activeOrganiser],
                    ['email_header_image_url' => Storage::disk(config('filesystems.default'))->url($path)]
                );
                $message = 'Header image uploaded successfully';
            } else {
                $this->updateOrCreate(
                    ['organiser_id' => $activeOrganiser],
                    ['email_footer_image_url' => Storage::disk(config('filesystems.default'))->url($path)]
                );
                $message = 'Footer image uploaded successfully';
            }

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

    public function deleteLogo(string $type)
    {
        $activeOrganiser = auth()->user()->activeOrganiser();

        if ($type == 'logo') {
            $this->updateOrCreate(
                ['organiser_id' => $activeOrganiser],
                ['email_logo_url' => null]
            );
            $message = 'Logo deleted successfully';
        } else if ($type == 'headerImage') {
            $this->updateOrCreate(
                ['organiser_id' => $activeOrganiser],
                ['email_header_image_url' => null]
            );
            $message = 'Header image deleted successfully';
        } else {
            $this->updateOrCreate(
                ['organiser_id' => $activeOrganiser],
                ['email_footer_image_url' => null]
            );
            $message = 'Footer image deleted successfully';
        }

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
