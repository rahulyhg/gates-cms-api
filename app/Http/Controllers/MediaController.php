<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vendor\Cloudinary;
use App\Models\Media;

class MediaController extends Controller
{

    public function index(Request $request)
    {
        \Cloudinary::config(array( 
        "cloud_name" => env('CLOUD_NAME'), 
        "api_key" => env('CLOUD_API_KEY'), 
        "api_secret" => env('CLOUD_API_SECRET')
        ));
        if ($request->hasFile('image')) {

            $cloudinary_id = \Cloudinary\Uploader::upload($request->file('image'));

            $media = new Media();
            $media->name = 'image';
            $media->cloudinary = $cloudinary_id['public_id'];
            $media->city_id = 0;
            $media->save();

            return response()->json($cloudinary_id);
        } elseif($request->hasFile('sheet')) {
            $cloudinary_id = \Cloudinary\Uploader::upload($request->file('sheet'), array("resource_type" => "raw"));

            $media = new Media();
            $media->name = 'csv';
            $media->cloudinary = $cloudinary_id['public_id'];
            $media->city_id = 0;
            $media->save();

            return response()->json($cloudinary_id);
        }
    }

    //
}
