<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WfhDataApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api/wfhdata');
    }

    // Ambil semua data WFH
    public function getAllWfhData()
    {
        $response = Http::get($this->baseUrl);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
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


    // Simpan data WFH baru
    public function createWfhData($data)
    {
        $response = Http::post($this->baseUrl, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }


    public function getWfhRequests($email, $year = null, $month = null, $requestType = null, $page = 1, $perPage = 10)
    {
        $url = "{$this->baseUrl}"; // Mengambil semua data user dari Spring Boot

        // Tambahkan parameter untuk paginasi
        $params = [
            'page' => $page,
            'perPage' => $perPage,
        ];

        try {
            $response = Http::get($url, $params);

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

            return [];  // Jika API gagal, kembalikan data kosong
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch WFH data from API: ' . $e->getMessage()];
        }
    }

    public function updateWfhApprovalStatus($id, $status)
    {
        $response = Http::put("{$this->baseUrl}/wfhdata/{$id}/status", ['approval_status' => $status]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
