<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\User\Guards\Sentinel;
use Illuminate\Support\Facades\Auth;
use Cartalyst\Sentinel\Laravel\SentinelServiceProvider;

use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\User\Events\Handlers\RegisterUserSidebar;

use Modules\User\Contracts\Authentication;

use Modules\User\Http\Middleware\TokenCan;
use Modules\User\Http\Middleware\GuestMiddleware;
use Modules\Core\Http\Middleware\AuthorisedApiKey;
use Modules\User\Http\Middleware\AuthorisedApiToken;
use Modules\User\Http\Middleware\LoggedInMiddleware;
use Modules\User\Http\Middleware\AuthorisedApiTokenAdmin;

use Modules\User\Console\UserTagUpdaterCommand;
use Modules\User\Console\GrantModulePermissionsCommand;
use Modules\User\Console\RemoveModulePermissionsCommand;

use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Traits\CanGetSidebarClassForModule;

use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;

use Modules\User\Entities\UserToken;
use Modules\User\Repositories\UserTokenRepository;
use Modules\User\Repositories\Cache\CacheUserTokenDecorator;
use Modules\User\Repositories\Eloquent\EloquentUserTokenRepository;

use Modules\User\Entities\UserInvite;
use Modules\User\Repositories\UserInviteRepository;
use Modules\User\Repositories\Cache\CacheUserInviteDecorator;
use Modules\User\Repositories\Eloquent\EloquentUserInviteRepository;

use Modules\User\Entities\UserClient;
use Modules\User\Repositories\UserClientRepository;
use Modules\User\Repositories\Cache\CacheUserClientDecorator;
use Modules\User\Repositories\Eloquent\EloquentUserClientRepository;

use Modules\User\Entities\Favourite;
use Modules\User\Repositories\FavouriteRepository;
use Modules\User\Repositories\Cache\CacheFavouriteDecorator;
use Modules\User\Repositories\Eloquent\EloquentFavouriteRepository;

use Modules\User\Entities\Invoice;
use Modules\User\Repositories\InvoiceRepository;
use Modules\User\Repositories\Cache\CacheInvoiceDecorator;
use Modules\User\Repositories\Eloquent\EloquentInvoiceRepository;

use Modules\User\Entities\ChatMessage;
use Modules\User\Repositories\ChatMessageRepository;
use Modules\User\Repositories\Cache\CacheChatMessageDecorator;
use Modules\User\Repositories\Eloquent\EloquentChatMessageRepository;

use Modules\User\Entities\File;
use Modules\User\Repositories\FileRepository;
use Modules\User\Repositories\Cache\CacheFileDecorator;
use Modules\User\Repositories\Eloquent\EloquentFileRepository;

use Modules\User\Entities\ChatFile;
use Modules\User\Repositories\ChatFileRepository;
use Modules\User\Repositories\Cache\CacheChatFileDecorator;
use Modules\User\Repositories\Eloquent\EloquentChatFileRepository;

use Modules\User\Entities\UserFile;
use Modules\User\Repositories\UserFileRepository;
use Modules\User\Repositories\Cache\CacheUserFileDecorator;
use Modules\User\Repositories\Eloquent\EloquentUserFileRepository;

class UserServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration, CanGetSidebarClassForModule;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * @var array
     */
    protected $providers = [
        'Sentinel' => SentinelServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $middleware = [
        'auth.guest' => GuestMiddleware::class,
        'logged.in' => LoggedInMiddleware::class,
        'api.key' => AuthorisedApiKey::class,
        'api.token' => AuthorisedApiToken::class,
        'token-can' => TokenCan::class
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register($this->getUserPackageServiceProvider());

        $this->registerBindings();

        $this->app['events']->listen(
            BuildingSidebar::class,
            $this->getSidebarClassForModule('user', RegisterUserSidebar::class)
        );

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('users', array_dot(trans('user::users')));
            $event->load('roles', array_dot(trans('user::roles')));
        });
        $this->commands([
            GrantModulePermissionsCommand::class,
            RemoveModulePermissionsCommand::class,
            UserTagUpdaterCommand::class,
        ]);
    }

    /**
     */
    public function boot()
    {
        $this->registerMiddleware();

        $this->publishes([
            __DIR__ . '/../Resources/views' => base_path('resources/views/user'),
        ]);

        $this->publishConfig('user', 'permissions');
        $this->publishConfig('user', 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Auth::extend('sentinel-guard', function () {
            return new Sentinel();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $driver = config('user.config.driver', 'Sentinel');

        $this->app->bind(
            UserRepository::class,
            "Modules\\User\\Repositories\\{$driver}\\{$driver}UserRepository"
        );
        $this->app->bind(
            RoleRepository::class,
            "Modules\\User\\Repositories\\{$driver}\\{$driver}RoleRepository"
        );
        $this->app->bind(
            Authentication::class,
            "Modules\\User\\Repositories\\{$driver}\\{$driver}Authentication"
        );

        $this->app->bind(UserTokenRepository::class, function () {
            $repository = new EloquentUserTokenRepository(new UserToken());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheUserTokenDecorator($repository);
        });

        $this->app->bind(UserInviteRepository::class, function () {
            $repository = new EloquentUserInviteRepository(new UserInvite());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheUserInviteDecorator($repository);
        });

        $this->app->bind(UserClientRepository::class, function () {
            $repository = new EloquentUserClientRepository(new UserClient());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheUserClientDecorator($repository);
        });

        $this->app->bind(FavouriteRepository::class, function () {
            $repository = new EloquentFavouriteRepository(new Favourite());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheFavouriteDecorator($repository);
        });

        $this->app->bind(InvoiceRepository::class, function () {
            $repository = new EloquentInvoiceRepository(new Invoice());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheInvoiceDecorator($repository);
        });

        $this->app->bind(ChatMessageRepository::class, function () {
            $repository = new EloquentChatMessageRepository(new ChatMessage());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheChatMessageDecorator($repository);
        });

        $this->app->bind(FileRepository::class, function () {
            $repository = new EloquentFileRepository(new File());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheFileDecorator($repository);
        });

        $this->app->bind(ChatFileRepository::class, function () {
            $repository = new EloquentChatFileRepository(new ChatFile());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheChatFileDecorator($repository);
        });

        $this->app->bind(UserFileRepository::class, function () {
            $repository = new EloquentUserFileRepository(new UserFile());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheUserFileDecorator($repository);
        });
    }

    private function registerMiddleware()
    {
        foreach ($this->middleware as $name => $class) {
            $this->app['router']->aliasMiddleware($name, $class);
        }
    }

    private function getUserPackageServiceProvider()
    {
        $driver = config('user.config.driver', 'Sentinel');

        if (!isset($this->providers[$driver])) {
            throw new \Exception("Driver [{$driver}] does not exist");
        }

        return $this->providers[$driver];
    }
}
