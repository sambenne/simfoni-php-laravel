<?php

namespace MBLSolutions\SimfoniLaravel;

use Illuminate\Support\Facades\Route;
use MBLSolutions\SimfoniLaravel\App\Http\Controllers\WebhookController;

class SimfoniLaravel
{

    /**
     * Binds the Report routes into the controller.
     *
     * @param  array  $options
     * @return void
     */
    public static function routes(array $options = [])
    {
        static::webhookRoutes($options);
    }

    /**
     * Register Webhook routes
     *
     * @param  array  $options
     * @return void
     */
    public static function webhookRoutes(array $options = [])
    {
        $defaultOptions = [
            'namespace' => '\MBLSolutions\SimfoniLaravel\App\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, static function () {

            Route::post('/simfoni/webhook', [WebhookController::class, 'webhook'])->name('simfoni.webhook');

        });
    }

}