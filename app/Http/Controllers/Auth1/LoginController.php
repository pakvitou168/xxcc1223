<?php

namespace App\Http\Controllers\Auth1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function attemptLogin(Request $request)
    {

        $reqBody = [
            'client_id' => config('pgi.idm_client_id'),
            'client_secret' => config('pgi.idm_client_secret'),
            'app_code' => config('pgi.idm_app_code'),
            'grant_type' => 'password',
            'username' => $request->username,
            'password' => base64_encode($request->password),
        ];
        try {

            $response = Http::timeout(12)->post(config('pgi.idm_base_url') . 'v1/login', $reqBody);
            $response->throw();

            $resBody = json_decode($response->body());
            if($userId = $resBody->id) {
                $user = User::find($userId);
                $functions = $user->authorizedFunctions;

                if(!$functions->isEmpty()) {
                    Auth::login($user, $request->get('remember'));
                    
                    return redirect(Session::get('url.intended'));
                }
                Log::error('No functions found.');
            }
        } catch(RequestException $e) {
            Log::error('LOGIN: '.$e->getMessage());
            $response = $e->response;
            if($response) {
                Log::info($response);
                if ($response->status() === 404) {
                    throw ValidationException::withMessages([
                        $this->username() => 'Invalid username or password!',
                    ]);
                }
            }
        }
        catch(ConnectionException $e) {
            Log::error('LOGIN: '.$e->getMessage());
        }

        throw ValidationException::withMessages([
            $this->username() => 'Oops, something went wrong!',
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }
}
