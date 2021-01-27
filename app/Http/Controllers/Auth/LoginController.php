<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Validator,Redirect,Response;
use Session;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        request()->validate([
        'email' => 'required',
        'password' => 'required',
        ]);
 
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            session(['loginYear' => $request->year]);

            //check already logged in user exist or not (if exist, auto logout)
            /*if(Auth::user()->online_status == 1){
                Auth::logout();
                Session::forget('loginYear');
                return Redirect::to("login")->withErrors([
                                'email' => "Warning! There is currently logged in user with this email and password.",
                            ]);
            } else {*/

                //$user = User::find(Auth()->user()->id);
                if(Auth::user()->role->role_name != 'system') {
                    $user = Auth::user();
                    $user->online_status = 1;
                    $user->save();
                }
                //Auth()->user()->login_year = $request->year;
                //save activity to log table
                //\LoginActivity::addToLog('Login',$request->year);
                return redirect()->intended('/');  
            //}          
        }

         return Redirect::to("login")->withErrors([
                        'email' => "Oppes! You have entered invalid credentials",
                    ]);
    }

    public function logout() {
        //$user = User::find(Auth()->user()->id);
        if(Auth::user()) {
            $user = Auth::user();
            $user->online_status = 0;
            $user->save();
            Auth::logout();
            Session::forget('loginYear');            
        } 

        return redirect('/login');
    }
    
}
