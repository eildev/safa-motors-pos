<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageService
{
    public function resizeAndOptimize($imageFile, $destinationPath, $width = 800, $height = 600, $quality = 75)
    {
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        // Generate unique image name
        $imageName = rand() . '.' . $imageFile->extension();
        $imagePath = $destinationPath . '/' . $imageName;

        // Resize and save image using Intervention
        $image = Image::make($imageFile)
            ->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio(); // Maintain aspect ratio
                $constraint->upsize(); // Prevent upsizing
            })
            ->save($imagePath, $quality);

        // Optimize the resized image using Spatie
        $optimizer = OptimizerChainFactory::create();
        $optimizer->optimize($imagePath);

        return $imageName; // Return image name to save in the database
    }
}
