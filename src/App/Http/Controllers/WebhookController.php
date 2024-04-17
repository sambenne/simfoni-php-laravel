<?php

namespace MBLSolutions\SimfoniLaravel\App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MBLSolutions\SimfoniLaravel\Events\ReceivedWebhook;

/**
 * Laravel controller for webhook requests
 *
 * Class WebhookController
 * @package MBLSolutions\SimfoniLaravel\App\Http\Controllers
 */
class WebhookController extends Controller
{
    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function webhook(Request $request)
    {
        try {
            $eventName = $request->get('event', 'unknown');

            event(new ReceivedWebhook($eventName, $request->all(), request()->header('Simfoni-Signature')));

            return response()->json([
                'success' => true,
                'message' => 'Received webhook '.$eventName
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}