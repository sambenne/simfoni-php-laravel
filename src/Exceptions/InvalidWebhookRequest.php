<?php

namespace MBLSolutions\SimfoniLaravel\Exceptions;

class InvalidWebhookRequest extends \Exception
{

    /**
     * InvalidWebhookRequest constructor.
     *
     */
    public function __construct()
    {
        parent::__construct('The webhook request did not match the expected signature.');
    }

}