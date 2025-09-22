<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function gallery(){
        try{
            $files = File::files(public_path('system_images'));

            $images = collect($files)->map(function ($file) {
                return asset('system_images/' . $file->getFilename());
            });

            return response()->json([
                'result' => true,
                'galleryFiles' => $images
                
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result'  => false,
                'message' => 'Failed to get gellery. ' . $e->getMessage()
            ], 500);
        }
    }
}
