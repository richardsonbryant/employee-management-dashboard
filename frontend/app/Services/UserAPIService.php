<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAPIService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api/users');
    }


    // public function attemptLogin(string $email, string $password)
    // {

    //     Log::debug('Sending login request to Spring Boot API', [
    //         'email' => $email,
    //         'password' => $password
    //     ]);

    //     $response = Http::post("{$this->baseUrl}/login", [
    //         'email' => $email,
    //         'password' => $password
    //     ]);

    //     Log::debug('Login API response', [
    //         'status' => $response->status(),
    //         'body' => $response->body()
    //     ]);

    //     if ($response->successful()) {
    //         return $response->json(); // Mengembalikan user data jika login berhasil
    //     }

    //     return null; // Jika login gagal, kembalikan null
    // }

    public function getAllUsers()
    {
        try {
            $response = Http::get($this->baseUrl);
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch users: ' . $e->getMessage()];
        }
    }

    public function getUserById($id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$id}");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch user: ' . $e->getMessage()];
        }
    }

    public function getUserByEmail($email)
    {
        // Sesuaikan URL sesuai dengan Spring Boot endpoint
        $url = "{$this->baseUrl}/search?email={$email}";  // Menggunakan query parameter

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json(); // atau sesuaikan dengan format responsenya
        }

        return null; // Kembalikan null jika tidak ditemukan
    }
    public function createUser(array $data)
    {
        try {
            $response = Http::post("{$this->baseUrl}/users", $data); // ← ini pakai $this->baseUrl

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'API error', 'status' => $response->status(), 'body' => $response->body()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function updateLeaveQuota($userId, $newLeaveQuota)
    {
        $url = "{$this->baseUrl}/{$userId}/leave_quota";

        $response = Http::put($url, [
            'leave_quota' => $newLeaveQuota
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
