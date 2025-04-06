<?php

namespace App\Http\Controllers\Auth1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Change password of the currently authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request) {
        $header = [
            'User-Id' => auth()->id()
        ];

        $reqBody = $this->changePasswordRequestBody($request);

        $response =  Http::withHeaders($header)->post(config('pgi.api_base_url') . 'v1/user/pwd/change', $reqBody);
        
        if ($response->successful())
            return response([
                'success' => true,
                'message' => $response->object()->message
            ], $response->status());

        return response([
            'message' => $response->object()->message
        ], $response->status());
    }

    private function changePasswordRequestBody($request) {
        return [
            'current_password' => base64_encode($request->current_password),
            'new_password' => base64_encode($request->new_password),
            "confirm_password" => base64_encode($request->confirm_password),
            'username' =>  auth()->user()->username,
        ];
    }
}
