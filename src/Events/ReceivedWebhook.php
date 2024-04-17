<?php

namespace MBLSolutions\SimfoniLaravel\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReceivedWebhook
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string $event */
    public $event;

    /** @var array $data */
    public $data;

    public $signature;

    /**
     * Create a new event instance.
     *
     * @param  string  $event
     * @param  array  $data
     */
    public function __construct(string $event, $data, $signature)
    {
        $this->event = $event;

        $this->data = $data;

        $this->signature = $signature;
    }

}
