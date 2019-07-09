<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Support\Facades\App;
use Modules\Setting\Contracts\Setting;
use Modules\User\Services\UserRegistration;
use Modules\User\Http\Requests\ResetRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\User\Repositories\UserRepository;
use Modules\User\Services\AdminOrUserResetter;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Http\Requests\Admin\LoginRequest;
use Modules\User\Repositories\UserTokenRepository;
use Modules\User\Exceptions\UserNotFoundException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Modules\User\Repositories\UserClientRepository;
use Modules\User\Http\Requests\AcceptInviteRequest;
use Modules\User\Http\Requests\ResetCompleteRequest;
use Modules\User\Exceptions\InvalidOrExpiredResetCode;
use Modules\Core\Http\Controllers\BasePublicController;

class AuthController extends BasePublicController
{
    /**
     * @var UserRepository
     */
    private $user;

    /**
     * @var UserClientRepository
     */
    private $userClient;

    /**
     * @var UserTokenRepository
     */
    private $userToken;


    use DispatchesJobs;

    public function __construct(UserRepository $user, UserClientRepository $userClient, UserTokenRepository $userToken)
    {
        $this->user       = $user;
        $this->userClient = $userClient;
        $this->userToken  = $userToken;

        parent::__construct();
    }

    public function getLogin()
    {
        return view('user::public.login');
    }

    public function postLogin(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $remember = (bool) $request->get('remember_me', false);

        $user       = $this->user->findByEmail($request->email);

        if ($user && ($user->hasRoleSlug('trader') || $user->hasRoleSlug('expert'))) {
          $status = $user->getStatus();
          if(isset($status['name'])) {
            if($status['name'] == 'Active') {
              return redirect()->route('login')->withSuccess(trans('user::messages.users.account already activated you can now login', [
                'sitename' => app(Setting::class)->get('core::site-name', App::getLocale()),
                'webUrl'  => env('WEB_LOGIN_URL')
              ]));
            } else {
              return redirect()->route('login')->withError(trans('user::messages.users.account '.strtolower($status['name']).' you cannot login', [
                'sitename' => app(Setting::class)->get('core::site-name', App::getLocale())
              ]));
            }
          }

          return view('user::public.verified', [
            'message' => trans('user::messages.users.not allowed')
          ]);
        }

        $error = $this->auth->login($credentials, $remember);

        if ($error) {
            return redirect()->back()->withInput()->withError($error);
        }

        $user = $this->auth->user();

        $this->userClient->loggedIn($request->get('client_id'), $user);
        $this->userToken->generateFor($user->id);

        return redirect()->intended(route(config('user.config.redirect_route_after_login')))
                ->withSuccess(trans('user::messages.successfully logged in'));
    }

    public function getRegister()
    {
        return view('user::public.register');
    }

    public function postRegister(RegisterRequest $request)
    {
        // app(UserRegistration::class)->register($request->all());

        return redirect()->route('register')
            ->withSuccess(trans('user::messages.account created check email for activation'));
    }

    public function getLogout()
    {
        $this->auth->logout();

        return redirect()->route('login');
    }

    public function getActivate($userId, $code)
    {
        $user       = $this->user->find($userId);
        $activation = $this->auth->findActivationByCode($user->id, $code);

        if ($activation) {
            if ($activation->completed) {
                if ($user->hasRoleSlug('trader') || $user->hasRoleSlug('expert')) {
                    return view('user::public.verified', ['message' => trans('user::messages.users.account already activated', ['sitename' => app(Setting::class)->get('core::site-name', App::getLocale())])]);
                } else {
                    return redirect()->route('login')->withSuccess(trans('user::messages.admins.account already activated'));
                }
            } else if ($activation->is_invite) {
                return redirect()->route('accept-invite.get', ['userId' => $userId, 'activationCode' => $code]);
            } else if ($this->auth->activate($userId, $code)) {
                if ($user->hasRoleSlug('trader') || $user->hasRoleSlug('expert')) {
                    return view('user::public.verified', [
                        'message' => trans('user::messages.users.account activated you can now login', ['sitename' => app(Setting::class)->get('core::site-name', App::getLocale())])
                    ]);
                } else {
                    app(\Modules\User\Repositories\UserTokenRepository::class)->generateFor($userId);
                    return redirect()->route('login')->withSuccess(trans('user::messages.admins.account activated you can now login'));
                }
            }
        }

        return redirect()->route('login')
            ->withError(trans('user::messages.there was an error with the activation'));
    }

    public function getAcceptInvite($userId, $code)
    {
        $user       = $this->user->find($userId);
        $activation = $this->auth->findActivationByCode($user->id, $code);

        if ($activation) {
             if ($activation->completed) {
                return redirect()->route('login')
                    ->withSuccess(trans('user::messages.admins.account already activated'));
            } else if (!$activation->is_invite) {
                return redirect()->route('activate', ['userId' => $userId, 'activationCode' => $code]);
            } else {
                return view('user::public.accept-invite', compact('activation', 'user'));
            }
        }

        return redirect()->route('login')
            ->withError(trans('user::messages.there was an error with the activation'));
    }

    public function postAcceptInvite(AcceptInviteRequest $request, $userId, $code)
    {
        $user       = $this->user->find($userId);
        $activation = $this->auth->findActivationByCode($user->id, $code);

        if ($activation) {
             if ($activation->completed) {
                return redirect()->route('login')
                    ->withSuccess(trans('user::messages.admins.account already activated'));
            } else if (!$activation->is_invite) {
                return redirect()->route('activate', ['userId' => $user->id, 'activationCode' => $code]);
            }
        }

        $this->auth->activate($user->id, $code);
        $this->user->update($user, $request->only('password'));

        app(\Modules\User\Repositories\UserTokenRepository::class)->generateFor($user->id);

        return redirect()->route('login')
                ->withSuccess(trans('user::messages.admins.account activated you can now login'));
    }

    public function getReset()
    {
        return view('user::public.reset.begin');
    }

    public function postReset(ResetRequest $request)
    {
        try {
            app(AdminOrUserResetter::class)->startReset($request->all());
        } catch (UserNotFoundException $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.no user found'));
        }

        return redirect()->route('reset')
            ->withSuccess(trans('user::messages.check email to reset password'));
    }

    public function getResetComplete()
    {
        return view('user::public.reset.complete');
    }

    public function postResetComplete($userId, $code, ResetCompleteRequest $request)
    {
        try {
            $user = app(AdminOrUserResetter::class)->finishReset(
                array_merge($request->all(), ['userId' => $userId, 'code' => $code])
            );
        } catch (UserNotFoundException $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.user no longer exists'));
        } catch (InvalidOrExpiredResetCode $e) {
            return redirect()->back()->withInput()
                ->withError(trans('user::messages.invalid reset code'));
        }

        if ($user->hasRoleSlug('trader') || $user->hasRoleSlug('expert')) {
            return view('user::public.reset.completed');
        } else {
            return redirect()->route('login')
                ->withSuccess(trans('user::messages.password reset'));
        }
    }

    public function getTerms()
    {
        return view('user::public.terms');
    }

    public function getPrivacy()
    {
        return view('user::public.privacy');
    }

    public function getCookies()
    {
        return view('user::public.cookies');
    }
}
