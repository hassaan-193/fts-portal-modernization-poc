<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect AMC Reporter users directly to the report form.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('AMC Reporter') && !$user->can('projects')) {
            return redirect()->route('projects.showForm');
        }

        if ($user->hasRole('Staff Requester') && !$user->can('stafprofile')) {
            return redirect()->route('own_requests.index');
        }
    }
}
