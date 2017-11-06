<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vendor\Cloudinary;

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
            return response()->json(\Cloudinary\Uploader::upload($request->file('image')));
        }
    }

    //
}
