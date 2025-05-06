<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        $response = Http::withBasicAuth($user->sms_gateway_username, $user->sms_gateway_password)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($user->sms_gateway_url, [
                'message' => $request->message,
                'phoneNumbers' => $request->phoneNumbers,
            ]);

            Sms::create([
                'user_id' => $user->id,
                'phone_number' => $request->phoneNumbers,
                'message' => $request->message,
                'response' => $response->json(),
            ]);

        return response()->json([
            'status' => $response->status(),
            'body' => $response->json(),
        ]);
    }
}
