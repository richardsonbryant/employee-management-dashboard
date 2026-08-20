package com.example.workproject1.dto;


public class PermissionDataDTO {
    public String new_name;
    public String email;
    public String start_off_date;
    public String end_off_date;
    public int total_off_day;
    public String reason;
    public String has_doctor_letter;
    public String permission_letter;
    public String request_type;
    private String created_at;
    
    
    // Getters and Setters
    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getNew_name() {
        return new_name;
    }

    public void setNew_name(String new_name) {
        this.new_name = new_name;
    }

    public String getStart_off_date() {
        return start_off_date;
    }

    public void setStart_off_date(String start_off_date) {
        this.start_off_date = start_off_date;
    }

    public String getEnd_off_date() {
        return end_off_date;
    }

    public void setEnd_off_date(String end_off_date) {
        this.end_off_date = end_off_date;
    }

    public int getTotal_off_day() {
        return total_off_day;
    }

    public void setTotal_off_day(int total_off_day) {
        this.total_off_day = total_off_day;
    }

    public String getReason() {
        return reason;
    }

    public void setReason(String reason) {
        this.reason = reason;
    }
    
    public String getHas_doctor_letter() {
    	return has_doctor_letter;
    }
    
    public void setHas_doctor_letter(String has_doctor_letter) {
    	this.has_doctor_letter = has_doctor_letter;
    }
    
    public String getPermission_letter() {
    	return permission_letter;
    }
    
    public void setPermission_letter(String permission_letter) {
    	this.permission_letter = permission_letter;
    }

    public String getRequest_type() {
        return request_type;
    }

    public void setRequest_type(String request_type) {
        this.request_type = request_type;
    }
    
    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }
}