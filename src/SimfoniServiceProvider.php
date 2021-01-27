<?php

namespace MBLSolutions\SimfoniLaravel;

use Exception;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use MBLSolutions\Simfoni\Exceptions\NotFoundException;
use MBLSolutions\Simfoni\Exceptions\PermissionDeniedException;
use MBLSolutions\Simfoni\Exceptions\ValidationException;
use MBLSolutions\Simfoni\Simfoni;
use MBLSolutions\SimfoniLaravel\Middleware\LoadSimfoniConfig;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SimfoniServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/simfoni.php' => config_path('simfoni.php'),
        ], 'config');

        $this->registerMiddleware(LoadSimfoniConfig::class);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Simfoni::class, static function () {
            // setup key configuration details on the base class automatically
            // to avoid keeping having to set this in integration code
            Simfoni::setBaseUri(Config::get('simfoni.endpoint'));
            Simfoni::setVerifySSL(Config::get('simfoni.verify_ssl', true));
            Simfoni::setWebhookSignature(Config::get('simfoni.webhook_signature'));

            return new Simfoni;
        });
    }

    /**
     * Register Middleware
     *
     * @param $middleware
     * @return void
     */
    public function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];

        $kernel->pushMiddleware($middleware);
    }

    /**
     * Inspired Deck Exception Handling
     *
     * @param $request
     * @param  Exception  $exception
     * @param  callable|null  $function
     * @return JsonResponse|RedirectResponse
     */
    public static function exceptionHandling($request, Exception $exception, callable $function = null)
    {
        if (route_contains('async') || route_contains('api')) {
            if ($exception instanceof ValidationException) {
                return JsonResponse::create([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->getValidationErrors()
                ], $exception->getCode());
            }
        }

        if ($exception instanceof HttpException) {
            if ($exception->getStatusCode() === 401) {
                return redirect()->route('login')->withErrors(['Please login to proceed.']);
            }
        }

        if ($exception instanceof PermissionDeniedException) {
            abort(403);
        }

        if ($exception instanceof NotFoundException) {
            abort(404);
        }

        if ($exception instanceof ValidationException) {
            return redirect()->back()->withInput()->withErrors($exception->getValidationErrors());
        }

        return $function();
    }

}