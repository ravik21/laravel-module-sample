<?php

namespace Modules\User\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Contracts\Authentication;
use Modules\User\Entities\UserInterface;
use Modules\User\Traits\CanFindUserWithBearerToken;

class TokenCan
{
    use CanFindUserWithBearerToken;

    /**
     * @var Authentication
     */
    private $auth;

    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @param string $permission
     * @return Response
     */
    public function handle(Request $request, \Closure $next, $permission)
    {
        if ($request->header('Authorization') === null) {
            return new Response('Forbidden', Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->findUserWithBearerToken($request->header('Authorization'));

        if ($user->hasAccess($permission) === false) {
            return response('Unauthorized.', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
