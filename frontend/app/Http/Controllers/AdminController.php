<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExpressApiService;
use Exception;

class AdminController extends Controller
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
            $stats = $this->api->getAdminStats($token);
            $usersData = $this->api->getAdminUsers($token);

            return view('admin.index', [
                'user' => session('user'),
                'stats' => $stats,
                'users' => $usersData['users'],
                'tiers' => $usersData['tiers'],
                'expressUrl' => rtrim(env('EXPRESS_API_URL', 'http://127.0.0.1:5000'), '/'),
            ]);
        } catch (Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal memuat dashboard admin: ' . $e->getMessage());
        }
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
            'tier' => 'required|string',
            'token_limit' => 'nullable|numeric|min:1000',
        ]);

        $token = session('jwt_token');

        try {
            $this->api->createAdminUser($token, [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role ?? 'user',
                'tier' => $request->tier,
                'token_limit' => $request->token_limit ? (int)$request->token_limit : null,
            ]);

            return back()->with('success', "Member '{$request->name}' berhasil ditambahkan.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'tier' => 'required|string',
            'token_limit' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,suspended',
        ]);

        $token = session('jwt_token');

        try {
            $this->api->updateAdminUser($token, (int)$id, [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role ?? 'user',
                'tier' => $request->tier,
                'token_limit' => (int)$request->token_limit,
                'status' => $request->status,
                'reset_tokens' => $request->has('reset_tokens'),
            ]);

            return back()->with('success', "Status dan kuota member ID #{$id} berhasil diperbarui.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyUser($id)
    {
        $token = session('jwt_token');

        try {
            $this->api->deleteAdminUser($token, (int)$id);
            return back()->with('success', "Member ID #{$id} berhasil dihapus dari sistem.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateTier(Request $request, $tierName)
    {
        $request->validate([
            'display_name' => 'required|string|max:100',
            'default_token_limit' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
        ]);

        $token = session('jwt_token');

        try {
            $this->api->updateAdminTier($token, $tierName, [
                'display_name' => $request->display_name,
                'default_token_limit' => (int)$request->default_token_limit,
                'description' => $request->description,
            ]);

            return back()->with('success', "Konfigurasi tier {$tierName} berhasil diperbarui.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
