<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Josantonius\MimeType\MimeType;

class UploadController extends Controller
{
    
    /**
     * Display the specified file resource.
     *
     * @param  Illuminate\Http\Request $request
     * @param string $folder
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $folder, $filename)
    {
        // Bulding file path
        $file = \public_path($folder)."/$filename";
        // getting file extension
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        // MimeType class instance
        $mimeType = new MimeType();
        // get mime from extension
        $mime = $mimeType->getMime($ext);
        // verifying if file exists
        if(!file_exists($file)) return response(null,404);
        // setting headers content type with mime type
        header("Content-type:$mime");
        // setting headers content length
        header("Content-Length:".filesize($file));
        // reading file
        readfile($file);
    }

}
