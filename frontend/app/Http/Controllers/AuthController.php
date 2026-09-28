<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExpressApiService;
use Exception;

class AuthController extends Controller
{
    protected ExpressApiService $api;

    public function __construct(ExpressApiService $api)
    {
        $this->api = $api;
    }

    public function showLogin()
    {
        if (session()->has('jwt_token')) {
            $user = session('user');
            return redirect()->route(($user['role'] ?? '') === 'admin' ? 'admin.index' : 'dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $result = $this->api->login($request->email, $request->password);
            session([
                'jwt_token' => $result['token'],
                'user' => $result['user'],
            ]);

            $targetRoute = ($result['user']['role'] ?? '') === 'admin' ? 'admin.index' : 'dashboard';
            return redirect()->route($targetRoute)->with('success', 'Selamat datang kembali, ' . $result['user']['name'] . '!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showRegister()
    {
        if (session()->has('jwt_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
        ]);

        try {
            $result = $this->api->register($request->name, $request->email, $request->password);
            session([
                'jwt_token' => $result['token'],
                'user' => $result['user'],
            ]);

            return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Akun Anda terdaftar sebagai Member Biasa dengan 1.000.000 token gratis.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function logout()
    {
        session()->forget(['jwt_token', 'user']);
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
