<?php

namespace Larawatch\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddLarawatchNelHeader
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        return $next($request)
        ->header('Report-To', '{"group":"default","max_age":31536000,"endpoints":[{"url":"https://dev.larawatch.com/api/nel/'.config('larawatch.nel_key').'"}],"include_subdomains":true}')
        ->header('NEL', '{"report_to":"default","max_age":31536000,"include_subdomains":true}');
    }
}
