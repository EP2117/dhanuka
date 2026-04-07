<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Session as AppSession;
use App\Setting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Validator, Redirect, Response;
use Session;
use Closure;
use Config;
use DB;

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

    /***public function showLoginForm()
    {       
       $financial_years = DB::table('financial_years')->get();
       return view('auth.login',compact('financial_years'));
    }***/

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return redirect()->back()->withErrors([
                'email' => 'Invalid email or password. Please try again.',
            ]);
        }

        $user = Auth::user();

        // ─── Maintenance Mode Check (non-system users only) ──────────────────────
        if ($user->role->role_name !== 'system') {

            $setting = Setting::first();
            if ($setting && $setting->status === 'ACTIVATED') {
                Auth::logout();
                return redirect()->back()->with('error', $setting->description);
            }

            if (!$user->is_active) {
                Auth::logout();
                return redirect()->to('login')->withErrors([
                    'email' => 'Warning! This account is inactive.',
                ]);
            }

            $user->online_status = 1;
            $user->save();
        }

        // ─── Session Setup ───────────────────────────────────────────────────────
        session(['loginYear' => $request->year]);

        $firstBranch = $user->branches->first();
        if ($firstBranch) {
            session(['user_branch' => $firstBranch->branch_name]);
        }

        // ─── Kill Previous Sessions (single session per account) ─────────────────
        AppSession::query()
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();

        return redirect()->intended('/');
    }

    public function logout()
    {
        //$user = User::find(Auth()->user()->id);
        if (Auth::user()) {
            $user = Auth::user();
            $user->online_status = 0;
            $user->save();
            Auth::logout();
            Session::forget('loginYear');
            Session::forget('user_branch');
        }

        return redirect('/login');
    }
}
