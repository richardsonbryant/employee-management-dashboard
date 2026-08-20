<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class UserAttendanceAPIService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api/user-attendance');
    }

    // Mengambil semua data absensi
    public function getAllUserAttendances()
    {
        try {
            $response = Http::get($this->baseUrl);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch data from API: ' . $e->getMessage()];
        }
    }

    // Mengambil data absensi berdasarkan ID
    public function getUserAttendanceById($id)
    {
        $response = Http::get("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    // Mendapatkan user berdasarkan email
    // public function getUserByEmail($email)
    // {
    //     try {
    //         // Gunakan endpoint yang sudah ada di UserAttendanceController
    //         $response = Http::get("{$this->baseUrl}/email/" . urlencode($email));

    //         if ($response->successful()) {
    //             $attendances = $response->json();

    //             // Jika ada data attendance, ambil data user dari attendance pertama
    //             if (is_array($attendances) && !empty($attendances)) {
    //                 // Ekstrak data user dari attendance
    //                 $firstAttendance = $attendances[0];
    //                 if (isset($firstAttendance['user']) && isset($firstAttendance['user']['id'])) {
    //                     return $firstAttendance['user'];
    //                 } else {
    //                     // Jika tidak ada data user yang lengkap dalam attendance
    //                     return ['error' => 'User data incomplete in attendance record'];
    //                 }
    //             } else {
    //                 // Jika tidak ada attendance untuk user tersebut
    //                 return ['error' => 'No attendance records found for this email'];
    //             }
    //         }

    //         Log::error('Failed to get user by email', [
    //             'email' => $email,
    //             'status' => $response->status(),
    //             'body' => $response->body()
    //         ]);

    //         return ['error' => 'API returned status code ' . $response->status()];
    //     } catch (\Exception $e) {
    //         Log::error('Exception during API request', [
    //             'message' => $e->getMessage()
    //         ]);
    //         return ['error' => $e->getMessage()];
    //     }
    // }

    // Mendapatkan user berdasarkan email
    public function getUserByEmail($email)
    {
        try {
            // Gunakan endpoint users/search yang ada di UserController
            $baseApiUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api');
            $baseApiUrl = str_replace('/user-attendance', '', $baseApiUrl);
            $userApiUrl = $baseApiUrl . '/users/search?email=' . urlencode($email);

            Log::info('Fetching user data', ['url' => $userApiUrl]);

            $response = Http::get($userApiUrl);

            if ($response->successful()) {
                $userData = $response->json();
                Log::info('User data fetched successfully', ['userData' => $userData]);
                return $userData;
            }

            Log::error('Failed to get user by email', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['error' => 'API returned status code ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('Exception during API request', [
                'message' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }


    // Membuat data absensi baru
    public function createUserAttendance($data)
    {
        try {
            Log::info('Sending request to: ' . $this->baseUrl, [
                'data' => $data
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($this->baseUrl, $data);

            Log::info('Response received', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'request_data' => $data
            ]);

            return ['error' => 'API returned status code ' . $response->status() . ': ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('Exception during API request', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => $e->getMessage()];
        }
    }


    public function updateUserAttendance($id, $data)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->put("{$this->baseUrl}/{$id}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'API returned status code ' . $response->status() . ': ' . $response->body()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
