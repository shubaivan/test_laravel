<?php


namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ValidateHeaders
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($request->hasHeader('Content-Type')
            && $request->header('Content-Type') === 'application/json'
        ) {
            return $next($request);
        }

        throw new AccessDeniedHttpException('Content-Type - application/json missing');
    }
}
