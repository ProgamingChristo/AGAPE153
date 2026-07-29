<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CatalogMediaController extends Controller
{
    public function show(string $filename): BinaryFileResponse
    {
        abort_unless(
            preg_match('/\A[a-z0-9][a-z0-9._-]*\.(?:jpe?g|png|webp)\z/i', $filename) === 1,
            404
        );

        $path = public_path('images/catalog/'.$filename);

        abort_unless(is_file($path), 404);

        return response()
            ->file($path)
            ->setMaxAge(604800)
            ->setPublic();
    }
}
