<?php

namespace App\Http\Controllers\Files;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ViewPDFFileController extends Controller
{

    /**
     * Shows a PDF file, from a file in the S3 Bucket.
     *
     * @param  Illuminate\Http\Request $request
     * @param  string  $fileName
     * @return Illuminate\Http\Response
     */
    public function show(Request $request, string $fileName)
    {
        return response(file_get_contents(config('app.s3_files_bucket_url') . urlencode($fileName)), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="'. substr($fileName, (strpos($fileName, '-') + 1)) .'"');
    }

}
