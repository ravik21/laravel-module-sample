<?php

namespace Modules\User\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Middleware\BaseApiMiddleware;

class AuthorisedApiToken extends BaseApiMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->header('Authorization') === null) {
            return $this->respondWithError('Please specify a User Token', 400);
        }

        if ($this->isValidToken($request->header('Authorization')) === false) {
            return $this->respondWithError('Invalid User Token', 401);
        }

        if ($this->userIsSuspended($request->header('Authorization'))) {
            return $this->respondWithError(trans('user::users.account is suspended'), 403);
        }

        return $next($request);
    }
}
