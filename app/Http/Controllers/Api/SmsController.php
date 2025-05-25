<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendSmsJob;
use App\Models\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class SmsController extends Controller
{
    public function send(Request $request)
{
    $request->validate([
        'message' => 'required|string',
        'phoneNumbers' => 'required|array',
        'phoneNumbers.*' => 'required|string',
    ]);

    $user = $request->user();

    SendSmsJob::dispatch($user, $request->message, $request->phoneNumbers);

    return response()->json([
        'status' => 'queued',
        'message' => 'SMS is being sent in the background.',
    ]);
}
}
