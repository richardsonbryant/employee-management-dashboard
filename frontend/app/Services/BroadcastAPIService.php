<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BroadcastAPIService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api/broadcasts');
    }

    // Mengambil semua data broadcast
    public function getAllBroadcasts()
    {
        try {
            $response = Http::get($this->baseUrl);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            // Jika API gagal atau Spring Boot belum jalan
            return ['error' => 'Failed to fetch data from API: ' . $e->getMessage()];
        }
    }

    // Mengambil data broadcast berdasarkan ID
    public function getBroadcastById($id)
    {
        $response = Http::get("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    // Membuat broadcast baru
    public function createBroadcast($data)
    {
        $response = Http::post($this->baseUrl, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
