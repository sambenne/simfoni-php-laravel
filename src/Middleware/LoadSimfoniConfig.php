<?php

namespace MBLSolutions\SimfoniLaravel\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use MBLSolutions\Simfoni\Simfoni;

class LoadSimfoniConfig
{
    /** @var Simfoni $config */
    protected $config;

    /**
     * Create a new middleware Instance
     *
     * @param  Simfoni  $simfoni
     */
    public function __construct(Simfoni $simfoni)
    {
        $this->config = $simfoni;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

}