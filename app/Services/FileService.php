<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager as Image;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileService
{
    protected $filesystem;

    public function __construct(public Image $image)
    {
        $this->filesystem = config('filesystems.default');
    }

    public function upload(UploadedFile $file, string $path, string $filename, $imageSize = null)
    {
        $relativePath = $path . $filename;

        $fileContent = file_get_contents($file->getRealPath());

        if ($imageSize) {
            $fileContent = $this->resize($file, $imageSize);
        }

        Storage::disk($this->filesystem)->put($relativePath, $fileContent, 'public');

        if (!Storage::disk($this->filesystem)->exists($relativePath)) {
            return null;
        }

        return $relativePath;
    }

    public function deleteFile(string $path)
    {
        return Storage::disk($this->filesystem)->delete($path);
    }

    public function resize(UploadedFile $file, int $imgSize)
    {
        try {
            $image = $this->image->make($file);
            $image->resize($imgSize, $imgSize, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $image->stream()->detach();
        } catch (\Exception $e) {
            throw new BadRequestHttpException('An error occured');
        }
    }

    public function copy(string $path)
    {
        $newPath = "duplicate_{$path}";
        $copy = Storage::disk($this->filesystem)->copy($path, $newPath);

        if (!$copy) {
            return;
        }

        return $newPath;

    }
}
