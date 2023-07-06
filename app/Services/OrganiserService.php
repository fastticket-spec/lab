<?php

namespace App\Services;

use App\Models\Organiser;
use App\Repositories\BaseRepository;
use App\Services\traits\HasFile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class OrganiserService extends BaseRepository
{
    use HasFile;

    protected $images_path = 'user_content/organiser_images/';

    public function __construct(Organiser $model, private AuthService $authService, private FileService $file)
    {
        parent::__construct($model);
    }

    public function fetchOrganisers(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $accountId = auth()->user()->account->id;

        return $this->model->query()
            ->latest()
            ->where('account_id', $accountId)
            ->when(($searchTerm = $request->q), function ($q) use ($searchTerm) {
                $q->where(function ($sQ) use ($searchTerm) {
                    $sQ->where('name', 'like', "%$searchTerm%")
                        ->orWhere('name_arabic', 'like', "%$searchTerm%")
                        ->orWhere('email', 'like', "%$searchTerm%")
                        ->orWhere('phone', 'like', "%$searchTerm%");
                });
            })
            ->paginate($request->per_page ?: 10)
            ->withQueryString()
            ->through(fn($organiser) => [
                'id' => $organiser->id,
                'name' => $organiser->name,
                'name_arabic' => $organiser->name_arabic,
                'email' => $organiser->email,
                'phone' => $organiser->phone,
                'events_count' => $organiser->events_count,
                'status' => $organiser->status,
                'logo' => $organiser->logo_url,
                'logo_arabic' => $organiser->logo_arabic_url,
                'banner' => $organiser->banner_url,
                'banner_arabic' => $organiser->banner_arabic_url,
            ]);
    }

    public function createOrganiser(array $data, UploadedFile $organiserLogo = null, UploadedFile $organiserLogoArabic = null, UploadedFile $organiserNavBarLogo = null, UploadedFile $organiserBanner = null, UploadedFile $organiserBannerArabic = null)
    {
        $user = $this->authService->find(auth()->user()->id);

        if ($organiserLogo) {
            $organiserLogo = $this->uploadFile($organiserLogo, $data['name'], '-logo-', 250);
        }

        if ($organiserLogoArabic) {
            $organiserLogoArabic = $this->uploadFile($organiserLogoArabic, $data['name'], '-logo-ar-');
        }

        if ($organiserBanner) {
            $organiserBanner = $this->uploadFile($organiserBanner, $data['name'], '-banner-');
        }

        if ($organiserBannerArabic) {
            $organiserBannerArabic = $this->uploadFile($organiserBannerArabic, $data['name'], '-banner-ar-');
        }

        $data['organiser_logo'] = $organiserLogo;
        $data['organiser_logo_arabic'] = $organiserLogoArabic;
        $data['banner'] = $organiserBanner;
        $data['banner_arabic'] = $organiserBannerArabic;

        $organiser = $user->account->organiser()->create($data);

        if (!$organiser) {
            $this->removeUploadedFile($organiserLogo);
            $this->removeUploadedFile($organiserLogoArabic);
            $this->removeUploadedFile($organiserNavBarLogo);
            $this->removeUploadedFile($organiserBanner);
            $this->removeUploadedFile($organiserBannerArabic);
        }

        return $organiser;
    }

    public function editOrganiserLogos(array $data, Organiser $organiser)
    {
        $oldOrganiserLogo = null;
        $oldOrganiserLogoArabic = null;
        $oldOrganiserBanner = null;
        $oldOrganiserBannerArabic = null;
        $organiserLogoArabic = null;
        $organiserLogo = null;
        $organiserBannerArabic = null;
        $organiserBanner = null;

        if (isset($data['organiser_logo']) && $data['organiser_logo'] instanceof UploadedFile) {
            $organiserLogo = $this->uploadFile($data['organiser_logo'], $organiser->name, '-logo-', 250);

            $oldOrganiserLogo = $organiser->organiser_logo;
            $organiser->organiser_logo = $organiserLogo;
        }

        if (isset($data['organiser_logo_arabic']) && $data['organiser_logo_arabic'] instanceof UploadedFile) {
            $organiserLogoArabic = $this->uploadFile($data['organiser_logo_arabic'], $organiser->name, '-logo-ar-', 250);

            $oldOrganiserLogoArabic = $organiser->organiser_logo_arabic;
            $organiser->organiser_logo_arabic = $organiserLogoArabic;
        }

        if (isset($data['organiser_banner']) && $data['organiser_banner'] instanceof UploadedFile) {
            $organiserBanner = $this->uploadFile($data['organiser_banner'], $organiser->name, '-banner-');

            $oldOrganiserBanner = $organiser->banner;
            $organiser->banner = $organiserBanner;
        }

        if (isset($data['organiser_banner_arabic']) && $data['organiser_banner_arabic'] instanceof UploadedFile) {
            $organiserBannerArabic = $this->uploadFile($data['organiser_banner_arabic'], $organiser->name, '-banner-ar-');

            $oldOrganiserBannerArabic = $organiser->banner_arabic;
            $organiser->banner_arabic = $organiserBannerArabic;
        }

        if (!$organiser->save()) {
            $this->removeUploadedFile($organiserLogo);
            $this->removeUploadedFile($organiserLogoArabic);
            $this->removeUploadedFile($organiserBanner);
            $this->removeUploadedFile($organiserBannerArabic);

            return null;
        }

        $this->removeUploadedFile($oldOrganiserLogo);
        $this->removeUploadedFile($oldOrganiserLogoArabic);

        return $organiser;
    }

    public function count(bool $all = false): int
    {
        return $this->model->query()
            ->when(!$all, function ($query) {
                $accountId = auth()->user()->account->id;
                $query->whereAccountId($accountId);
            })
            ->count();
    }
}
