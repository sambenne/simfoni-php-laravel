<?php

namespace MBLSolutions\SimfoniLaravel\Tests;

use Illuminate\Foundation\Application;
use MBLSolutions\SimfoniLaravel\SimfoniServiceProvider;

class LaravelTestCase extends \Orchestra\Testbench\TestCase
{

    /** {@inheritdoc} **/
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEnvVariables();
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $config = include __DIR__.'/../config/simfoni.php';

        foreach ($config as $key => $value) {
            $app['config']->set('simfoni.'.$key, $value);
        }

        // register the class for testing
        $app->register(SimfoniServiceProvider::class);
    }


    /**
     * Setup environment variables
     *
     * @return void
     */
    private function setupEnvVariables(): void
    {
        $this->app['config']->set('app.key', 'base64:KMRokGdMt+pgOmbRD+oiKwmfZiKAVxR6KkZ4KuiIo90=');
    }

}