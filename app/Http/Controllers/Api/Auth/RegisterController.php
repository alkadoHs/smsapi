<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'sms_gateway_username' => 'required|string',
            'sms_gateway_password' => 'required|string',
            'sms_gateway_url' => 'required|url',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sms_gateway_username' => $request->sms_gateway_username,
            'sms_gateway_password' => $request->sms_gateway_password,
            'sms_gateway_url' => $request->sms_gateway_url,
        ]);

        $token = $user->createToken('sms-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
