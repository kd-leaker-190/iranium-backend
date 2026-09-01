<?php

namespace App;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class ModelBasedPathGenerator implements PathGenerator
{
    protected function getModelFolder(Media $media): string
    {
        return Str::plural(Str::snake(class_basename($media->model_type)));
    }

    public function getPath(Media $media): string
    {
        return $this->getModelFolder($media) . '/' . $media->model_id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getModelFolder($media) . '/' . $media->model_id . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getModelFolder($media) . '/' . $media->model_id . '/responsive/';
    }
}
