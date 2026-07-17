<?php

namespace App\Services\Shared;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function upload(UploadedFile $file, string $directory = 'files'): string
    {
        return $file->store($directory, 'public');
    }

    public function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    public function url(string $path): string
    {
        return Storage::url($path);
    }
}
