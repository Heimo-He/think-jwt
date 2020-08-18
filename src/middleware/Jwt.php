<?php
namespace heimo\auth\middleware;


use heimo\auth\Auth;
use heimo\auth\AuthException;

class Jwt
{
    /**
     * @param          $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        try {
            Auth::make()->verifyToken();
        }catch (AuthException $exception) {
            return json(['code' => 4001, 'message' => $exception->getMessage(), 'data' => null]);
        }

        return $next($request);
    }
}