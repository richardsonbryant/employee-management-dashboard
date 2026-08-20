package com.example.workproject1.service;

import org.springframework.stereotype.Service;
import org.springframework.web.client.RestTemplate;
import org.springframework.web.util.UriComponentsBuilder;

import java.time.LocalDate;
import java.util.*;

@Service
public class HolidayService {

    private static final String API_KEY = "AIzaSyChy3W44wvYQ4KnIsP7LBGwUKJx9jCdEQ4";
    private static final String CALENDAR_ID =
            "en.indonesian#holiday@group.v.calendar.google.com";

    public Set<LocalDate> getHolidays(LocalDate start, LocalDate end) {

        String url = UriComponentsBuilder
                .fromHttpUrl(
                  "https://www.googleapis.com/calendar/v3/calendars/{calendarId}/events"
                )
                .queryParam("key", API_KEY)
                .queryParam("timeMin", start + "T00:00:00Z")
                .queryParam("timeMax", end + "T23:59:59Z")
                .queryParam("singleEvents", true)
                .queryParam("orderBy", "startTime")
                .buildAndExpand(CALENDAR_ID)
                .toUriString();

        System.out.println("HOLIDAY API URL = " + url);

        RestTemplate restTemplate = new RestTemplate();
        Map<String, Object> response =
                restTemplate.getForObject(url, Map.class);

        Set<LocalDate> holidays = new HashSet<>();

        if (response != null && response.get("items") != null) {
            List<Map<String, Object>> items =
                    (List<Map<String, Object>>) response.get("items");

            for (Map<String, Object> item : items) {
                Map<String, Object> startMap =
                        (Map<String, Object>) item.get("start");

                if (startMap != null && startMap.get("date") != null) {
                    holidays.add(LocalDate.parse(startMap.get("date").toString()));
                }
            }
        }

        return holidays;
    }
}
