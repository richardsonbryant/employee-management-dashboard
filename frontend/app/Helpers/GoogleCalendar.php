<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;



class GoogleCalendar
{
    /**
     * Ambil daftar tanggal merah dari Google Calendar API.
     *
     * @return array
     */
    public static function getHolidays()
    {
        $apiKey = env('GOOGLE_CALENDAR_API_KEY');
        $url = "https://www.googleapis.com/calendar/v3/calendars/id.indonesian%23holiday@group.v.calendar.google.com/events?key={$apiKey}";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }
        $events = $response->json();
        $holidays = collect($events['items'] ?? [])->pluck('start.date')->toArray();

        // $events = $response->json()['items'] ?? [];
        return $holidays;
    }
    /**
     *
     * @param string $startDate (format: YYYY-MM-DD)
     * @param string $endDate (format: YYYY-MM-DD)
     * @return int
     */
    public static function countLeaveDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $holidays = self::getHolidays();

        // Ambil semua event dari API
        $apiKey = env('GOOGLE_CALENDAR_API_KEY');
        $url = "https://www.googleapis.com/calendar/v3/calendars/id.indonesian%23holiday@group.v.calendar.google.com/events?key={$apiKey}   ";
        $response = Http::get($url);
        $events = $response->json()['items'] ?? [];

        $totalDays = 0;

        while ($start->lte($end)) {
            $currentDate = $start->format('Y-m-d');

            // Cek apakah hari ini adalah hari libur
            $isHoliday = in_array($currentDate, $holidays);

            // Cek apakah hari ini adalah cuti bersama
            $isCutiBersama = false;
            foreach ($events as $event) {
                if (
                    isset($event['start']['date']) &&
                    Carbon::parse($event['start']['date'])->format('Y-m-d') === $currentDate &&
                    strpos($event['summary'], 'Cuti Bersama') !== false
                ) {
                    $isCutiBersama = true;
                    break;
                }
            }

            // Hitung hari cuti
            if (!$isHoliday && !$start->isWeekend()) {
                // Jika hari ini bukan hari libur dan bukan akhir pekan
                $totalDays++;
            } else if ($isCutiBersama) {
                // Jika hari ini adalah cuti bersama, hitung sebagai hari cuti
                $totalDays++;
            }

            // Pindah ke tanggal berikutnya
            $start->addDay();
        }

        return $totalDays;
    }

    public static function getCutiBersamaDates($month = null, $year = null)
    {
        if (!$month) $month = Carbon::now()->month;
        if (!$year) $year = Carbon::now()->year;

        $apiKey = env('GOOGLE_CALENDAR_API_KEY');
        $url = "https://www.googleapis.com/calendar/v3/calendars/id.indonesian%23holiday@group.v.calendar.google.com/events?key={$apiKey}";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }

        $events = $response->json()['items'] ?? [];
        $cutiBersamaList = [];

        foreach ($events as $event) {
            if (isset($event['summary']) && isset($event['start']['date'])) {
                $eventDate = Carbon::parse($event['start']['date']);

                if (
                    strpos($event['summary'], 'Cuti Bersama') !== false &&
                    $eventDate->month == $month &&
                    $eventDate->year == $year
                ) {
                    // Simpan dalam format: ['date' => 'YYYY-MM-DD', 'event' => 'Nama Event']
                    $cutiBersamaList[] = [
                        'date' => $eventDate->format('Y-m-d'),
                        'event' => $event['summary']
                    ];
                }
            }
        }

        return $cutiBersamaList;
    }
}
