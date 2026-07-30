<?php

namespace App\Http\Middleware;

use App\Services\WebsiteTrafficService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackWebsiteTrafficMiddleware
{
    public function __construct(private readonly WebsiteTrafficService $traffic) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->traffic->track($request, $response);

        return $response;
    }
}
