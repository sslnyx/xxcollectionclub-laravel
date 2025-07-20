<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function __invoke()
    {
        $files = Storage::disk('public')->allFiles('gallery');
        $allImages = [];
        foreach ($files as $key => $image) {
            if(empty($allImages[$key])){
                $allImage[$key] = [];
            }
            $allImages[$key]["last_modify"] = Storage::disk('public')->lastModified($image);

            $allImages[$key]["path"] = $image;

            $allImages[$key]["type"] = Storage::disk('public')->mimeType($image);
        }

        return response()->json($allImages);
    }
}
