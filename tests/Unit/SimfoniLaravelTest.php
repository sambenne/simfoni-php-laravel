<?php

namespace MBLSolutions\SimfoniLaravel\Tests\Unit;

use Illuminate\Support\Facades\Config;
use MBLSolutions\SimfoniLaravel\SimfoniLaravel;
use MBLSolutions\SimfoniLaravel\Tests\LaravelTestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class SimfoniLaravelTest extends LaravelTestCase
{

    /** {@inheritdoc} **/
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test * */
    public function does_bootstrap_configuration()
    {
        self::assertEquals('https://simfoni.co.uk', Config::get('simfoni.endpoint'));
        self::assertEquals(true, Config::get('simfoni.verify_ssl'));
        self::assertEquals(false, Config::get('simfoni.enable_webhooks'));
        self::assertEquals('default', Config::get('simfoni.webhook_signature'));
    }

    /** @test * */
    public function does_not_bootstrap_webhook_routes()
    {
        $this->withExceptionHandling();

        $this->expectException(RouteNotFoundException::class);
        
        self::assertIsString(route('simfoni.webhook'));
    }

    /** @test * */
    public function does_bootstrap_webhook_routes_when_requested()
    {
        SimfoniLaravel::routes();

        self::assertIsString(route('simfoni.webhook'));
    }

}