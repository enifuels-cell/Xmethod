<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockKnownCrawlers
{
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        $crawlerPattern = '/bot|crawler|spider|slurp|bingpreview|headless|scrapy|curl|wget|python-requests|go-http-client/i';

        if ($userAgent === '' || preg_match($crawlerPattern, $userAgent)) {
            abort(403, 'Automated access is not permitted.');
        }

        return $next($request);
    }
}
