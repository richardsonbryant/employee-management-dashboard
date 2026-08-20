<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class BroadcastResponseAPIService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SPRINGBOOT_API_URL', 'http://localhost:8080/api/broadcast-responses');
    }

    // Mengambil semua data respons broadcast
    public function getAllBroadcastResponses()
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

    // Mengambil respons berdasarkan ID broadcast
    public function getResponsesByBroadcastId($broadcastId)
    {
        $response = Http::get("{$this->baseUrl}/broadcast/{$broadcastId}");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    // Membuat respons broadcast baru
    public function createBroadcastResponse($data)
    {
        $response = Http::post($this->baseUrl, $data);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
