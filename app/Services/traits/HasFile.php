<?php

namespace App\Services\traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

trait HasFile
{
    public function getFile(Request $request, string $fileName = null)
    {
        if (!$request->hasFile($fileName)) {
            return;
        }

        return $request->file($fileName);
    }

    protected function uploadFile(UploadedFile|File $file, string $name, string $type, int $resize = null): ?string
    {
        $filename = strtolower(Str::slug(($name) . $type . strtotime(now())) . '.' . ($file->getClientOriginalExtension() ?: 'png'));
        return $this->file->upload($file, $this->images_path, $filename, $resize);
    }

    protected function removeUploadedFile(string $path = null)
    {
        if (is_null($path)) {
            return;
        }

        $this->file->deleteFile($path);
    }

    protected function uploadPdfFile($file, string $name, string $type)
    {
        $filesystem = config('filesystems.default');
        $filename = strtolower(Str::slug(($name) . $type . strtotime(now())) . '.' . 'pdf');
        $relativePath = $this->pdf_path . $filename;

        Storage::disk($filesystem)->put($relativePath, $file, 'public');

        if (!Storage::disk($filesystem)->exists($relativePath)) {
            return null;
        }

        return $relativePath;
    }

    protected function uploadBase64File($file, $name): ?string
    {
        $filesystem = config('filesystems.default');
        $filename = $name . '-' . strtotime(now()) . '.' . 'png';
        $relativePath = $this->images_path . $filename;

        Storage::disk($filesystem)->put($relativePath, $file, 'public');

        if (!Storage::disk($filesystem)->exists($relativePath)) {
            return null;
        }

        return $relativePath;
    }
}
