<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoginController extends Controller
{

    public function loginView(Request $request)
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);


        if ($user = $this->authenticateWithIdm($credentials)) {
            Auth::login($user, $request->get('remember'));
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response([
                    'success' => true,
                    'data' => [
                        'user' => $user->toArray(),
                    ]
                ]);
            }
            return redirect()->intended('/');
        }

        if ($request->expectsJson()) {
            return response([
                'success' => false,
                'message' => 'Invalid Username or Password',
            ], 401);
        }

        return back()->withErrors([
            'auth' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function user(Request $request)
    {
        $user = User::active()->where('username', $request->user()->username)->first();
        if (!$user) {
            abort(401);
        }

        $user->functions = $user->allFunctions->toArray();
        $counter = $user->counter;
        if (!$counter) {
            return $user->toArray();
        }

        return array_merge(
            $user->toArray(),
            [
                'counter_no' => $counter->no,
                'branch_code' => $counter->branch_code,
                'functions' => $user->allFunctions,
            ]
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($request->expectsJson()) {
            return response([
                'success' => true,
            ], 204);
        }
        return redirect('/');
    }

    protected function authenticateWithIdm(array $credentials): User | null
    {
        try {
            $user = User::active()->where('username', $credentials['username'])->first();
            if (!$user) {
                return null;
            }
            $response = Http::post(config('idm.idm_base_url') . 'idm/v1/login', [

                "app_code" => config('idm.idm_app_code'),
                "client_id" => config('idm.idm_client_id'),
                "client_secret" => config('idm.idm_client_secret'),
                "grant_type" => "password",
                "username" => $credentials['username'],
                "password" => base64_encode($credentials['password']),

            ]);
            $response->throw();
            $resBody = json_decode($response->body());
            if ((int)$user->id === (int)optional($resBody)->id) {
                return $user;
            }
        } catch (Throwable $e) {
            Log::error('Idm Login: ' . $e->getMessage());
        }

        return null;
    }
}
