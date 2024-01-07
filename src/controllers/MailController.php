<?php

namespace App\Controllers;

use App\Auth;
use App\Jobs\SendEmail;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Mail as MailModel;
use Exception;

class MailController
{
    public function create(Request $request)
    {
        if (empty($request->to)) {
            return new JsonResponse([
                'message' => 'to required'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        if (empty($request->subject)) {
            return new JsonResponse([
                'message' => 'subject required'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $params = $request->all();
        $params['user_id'] = Auth::user()->id;
        if (!isset($params['from'])) {
            $params['from'] = 'fandy.fadian@gmail.com';
        }

        try {
            $mail = MailModel::create($params);

            $jobData = [
                'name' => SendEmail::class,
                'payload' => json_encode($mail->toArray()),
                'attempts' => 1
            ];
            Job::create($jobData);

            return new JsonResponse([
                'message' => 'email created',
                'user' => $mail
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
