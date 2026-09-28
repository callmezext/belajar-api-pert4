<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExpressApiService;
use Exception;

class DashboardController extends Controller
{
    protected ExpressApiService $api;

    public function __construct(ExpressApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $token = session('jwt_token');

        try {
            $overview = $this->api->getUserOverview($token);
            // Refresh session user data
            session(['user' => $overview['user']]);

            return view('dashboard.index', [
                'user' => $overview['user'],
                'tierConfig' => $overview['tierConfig'],
                'allTiers' => $overview['allTiers'],
                'recentLogs' => $overview['recentLogs'],
                'expressUrl' => rtrim(env('EXPRESS_API_URL', 'http://127.0.0.1:5000'), '/'),
            ]);
        } catch (Exception $e) {
            session()->forget(['jwt_token', 'user']);
            return redirect()->route('login')->with('error', 'Sesi Anda telah kedaluwarsa. Silakan login kembali.');
        }
    }

    public function regenerateKey()
    {
        $token = session('jwt_token');

        try {
            $result = $this->api->regenerateApiKey($token);
            $user = session('user');
            $user['api_key'] = $result['api_key'];
            session(['user' => $user]);

            return back()->with('success', 'API Key baru Anda berhasil dibuat dan diaktifkan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function upgradeTier(Request $request)
    {
        $request->validate([
            'target_tier' => 'required|string|in:FREE,STARTER,SUPER',
        ]);

        $token = session('jwt_token');

        try {
            $result = $this->api->upgradeTier($token, $request->target_tier);
            return back()->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
