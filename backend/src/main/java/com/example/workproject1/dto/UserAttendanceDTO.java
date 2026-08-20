package com.example.workproject1.dto;

import com.example.workproject1.model.UserAttendance;

public class UserAttendanceDTO {

    private Long id;
    private String clock_in;
    private String clock_out;
    private Float total_hours;
    private String verification;
    private String name; // Nama user akan ditambahkan di sini
    private String email;
    

    // Constructor dari UserAttendance
    public UserAttendanceDTO(UserAttendance userAttendance) {
        this.id = userAttendance.getId();
        this.clock_in = userAttendance.getClock_in();
        this.clock_out = userAttendance.getClock_out();
        this.total_hours = userAttendance.getTotal_hours();
        this.verification = userAttendance.getVerification();
        this.name = userAttendance.getUser() != null ? userAttendance.getUser().getName() : null;
        this.email = userAttendance.getUser() != null ? userAttendance.getUser().getEmail() : null;
        
    }

    // Getters and Setters
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getClock_in() {
        return clock_in;
    }

    public void setClock_in(String clock_in) {
        this.clock_in = clock_in;
    }

    public String getClock_out() {
        return clock_out;
    }

    public void setClock_out(String clock_out) {
        this.clock_out = clock_out;
    }

    public Float getTotal_hours() {
        return total_hours;
    }

    public void setTotal_hours(Float total_hours) {
        this.total_hours = total_hours;
    }


    public String getVerification() {
        return verification;
    }

    public void setVerification(String verification) {
        this.verification = verification;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }
    

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }
}
