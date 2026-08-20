<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UserDataApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api/userdata');
    }

    public function getAllUserData()
    {
        try {
            $response = Http::get($this->baseUrl);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            // Kalau API gagal atau Java belum nyala
            return ['error' => 'Failed to fetch data from API: ' . $e->getMessage()];
        }
    }

    public function getUserDataById($id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$id}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch user data by ID from API: ' . $e->getMessage()];
        }
    }
    public function createUserData($data)
    {
        $response = Http::post($this->baseUrl, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }


    public function getUserRequests($email, $year = null, $month = null, $requestType = null, $page = 1, $perPage = 10)
    {
        $url = "{$this->baseUrl}"; // Mengambil semua data user dari Spring Boot

        $params = [
            'page' => $page,
            'perPage' => $perPage,
        ];

        try {
            // Mengambil data dari API
            $response = Http::get($url, $params);

            // Cek respons API
            // dd($response->json());

            if ($response->successful()) {
                $data = $response->json();

                // Filter data di Laravel berdasarkan parameter
                $filteredData = collect($data)->filter(function ($item) use ($email, $year, $month, $requestType) {
                    return (!$email || $item['email'] == $email) &&
                        (!$year || substr($item['start_off_date'], 0, 4) == $year) &&
                        (!$month || substr($item['start_off_date'], 5, 2) == $month) &&
                        (!$requestType || $item['request_type'] == $requestType);
                });

                // Debug untuk melihat data yang sudah difilter
                // dd($filteredData->values()->toArray()); 

                return [
                    'data' => $filteredData->values()->toArray(),
                    'total' => $filteredData->count(),
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'lastPage' => ceil($filteredData->count() / $perPage),
                ];
            }

            return [];
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch user data from API: ' . $e->getMessage()];
        }
    }

    public function updateApprovalStatus($id, $status)
    {
        // Debug URL yang akan digunakan
        $url = "{$this->baseUrl}/{$id}/status";
        // dd($url);  // Menampilkan URL yang dibentuk

        $response = Http::put($url, ['approval_status' => $status]);

        // dd($response->json());  // Menampilkan respons API dari Spring Boot

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
