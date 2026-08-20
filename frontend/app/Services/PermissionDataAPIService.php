<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PermissionDataApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api/permissiondata');
    }

    // Ambil semua data Permission
    public function getAllPermissionData()
    {
        $response = Http::get($this->baseUrl);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Simpan data Permission baru
    public function createPermissionData($data)
    {
        $response = Http::post($this->baseUrl, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
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



    public function getSickLeaveRequests($email, $year = null, $month = null, $requestType = null, $page = 1, $perPage = 10)
    {
        $url = "{$this->baseUrl}";  // API endpoint

        // Tambahkan parameter untuk paginasi
        $params = [
            'page' => $page,
            'perPage' => $perPage,
        ];

        try {
            // Mengambil data dari API
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

                // Buat LengthAwarePaginator dengan path yang benar
                return [
                    'data' => $filteredData->values()->toArray(),
                    'total' => $filteredData->count(),
                    'perPage' => $perPage,
                    'currentPage' => $page,
                    'lastPage' => ceil($filteredData->count() / $perPage),
                ];

                return [];
            }

            return [];  // Jika API gagal, kembalikan data kosong
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch sick leave data from API: ' . $e->getMessage()];
        }
    }

    public function updatePermissionApprovalStatus($id, $status)
    {
        $response = Http::put("{$this->baseUrl}/permissiondata/{$id}/status", ['approval_status' => $status]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
