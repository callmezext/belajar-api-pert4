<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ExpressApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.express.url', env('EXPRESS_API_URL', 'http://127.0.0.1:5000')), '/');
    }

    public function login(string $email, string $password): array
    {
        $response = Http::timeout(10)->post("{$this->baseUrl}/api/auth/login", [
            'email' => $email,
            'password' => $password,
        ]);

        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal login ke server.');
        }

        return $response->json();
    }

    public function register(string $name, string $email, string $password): array
    {
        $response = Http::timeout(10)->post("{$this->baseUrl}/api/auth/register", [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal registrasi akun.');
        }

        return $response->json();
    }

    public function getMe(string $token): array
    {
        $response = Http::withToken($token)->timeout(8)->get("{$this->baseUrl}/api/auth/me");
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Sesi berakhir.');
        }
        return $response->json();
    }

    public function getUserOverview(string $token): array
    {
        $response = Http::withToken($token)->timeout(8)->get("{$this->baseUrl}/api/user/overview");
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal memuat overview pengguna.');
        }
        return $response->json();
    }

    public function regenerateApiKey(string $token): array
    {
        $response = Http::withToken($token)->timeout(8)->post("{$this->baseUrl}/api/user/regenerate-key");
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal memperbarui API key.');
        }
        return $response->json();
    }

    public function upgradeTier(string $token, string $tier): array
    {
        $response = Http::withToken($token)->timeout(8)->post("{$this->baseUrl}/api/user/upgrade-tier", [
            'target_tier' => $tier,
        ]);
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal mengubah tier.');
        }
        return $response->json();
    }

    public function getAdminStats(string $token): array
    {
        $response = Http::withToken($token)->timeout(8)->get("{$this->baseUrl}/api/admin/stats");
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal memuat statistik admin.');
        }
        return $response->json();
    }

    public function getAdminUsers(string $token): array
    {
        $response = Http::withToken($token)->timeout(8)->get("{$this->baseUrl}/api/admin/users");
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal memuat daftar pengguna.');
        }
        return $response->json();
    }

    public function createAdminUser(string $token, array $data): array
    {
        $response = Http::withToken($token)->timeout(8)->post("{$this->baseUrl}/api/admin/users", $data);
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal membuat user baru.');
        }
        return $response->json();
    }

    public function updateAdminUser(string $token, int $id, array $data): array
    {
        $response = Http::withToken($token)->timeout(8)->put("{$this->baseUrl}/api/admin/users/{$id}", $data);
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal memperbarui data user.');
        }
        return $response->json();
    }

    public function deleteAdminUser(string $token, int $id): array
    {
        $response = Http::withToken($token)->timeout(8)->delete("{$this->baseUrl}/api/admin/users/{$id}");
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal menghapus user.');
        }
        return $response->json();
    }

    public function updateAdminTier(string $token, string $tierName, array $data): array
    {
        $response = Http::withToken($token)->timeout(8)->put("{$this->baseUrl}/api/admin/tiers/{$tierName}", $data);
        if ($response->failed()) {
            throw new Exception($response->json('error') ?? 'Gagal memperbarui konfigurasi tier.');
        }
        return $response->json();
    }
}
