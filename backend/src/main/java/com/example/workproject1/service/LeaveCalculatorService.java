package com.example.workproject1.service;

import org.springframework.stereotype.Service;

import java.time.DayOfWeek;
import java.time.LocalDate;
import java.util.Set;

@Service
public class LeaveCalculatorService {

    public int calculateWorkingDays(
            LocalDate start,
            LocalDate end,
            Set<LocalDate> holidays
    ) {
        int total = 0;

        LocalDate date = start;
        while (!date.isAfter(end)) {

            boolean isWeekend =
                    date.getDayOfWeek() == DayOfWeek.SATURDAY ||
                    date.getDayOfWeek() == DayOfWeek.SUNDAY;

            boolean isHoliday = holidays.contains(date);

            if (!isWeekend && !isHoliday) {
                total++;
            }

            date = date.plusDays(1);
        }

        return total;
    }
}
