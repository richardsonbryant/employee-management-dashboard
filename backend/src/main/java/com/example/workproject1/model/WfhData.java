package com.example.workproject1.model;

import java.time.LocalDateTime;

import com.fasterxml.jackson.annotation.JsonBackReference;
import com.fasterxml.jackson.annotation.JsonProperty;

import jakarta.persistence.*;

@Entity
@Table(name = "user_wfh_data")

public class WfhData {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "new_name")
    @JsonProperty("new_name")
    private String newName;
    @Column(name = "start_off_date")
    @JsonProperty("start_off_date")
    private String startOffDate;
    @Column(name = "end_off_date")
    @JsonProperty("end_off_date")
    private String endOffDate;
    @Column(name = "total_off_day")
    @JsonProperty("total_off_day")
    private int totalOffDay;
    @Column(name = "reason")
    @JsonProperty("reason")
    private String reason;
    @Column(name = "approval_status")
    @JsonProperty("approval_status")
    private String approvalStatus;
    @Column(name = "request_type")
    @JsonProperty("request_type")
    private String requestType;
    @Column(name = "created_at")
    private LocalDateTime createdAt;
    
    
//    @Column(name = "email")
//    private String formEmail;  
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "email", referencedColumnName = "email", nullable = false)
    @JsonBackReference
    private User user;
    
    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }
    
    public WfhData() {}

    // Getters & Setters
    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getNewName() { return newName; }
    public void setNewName(String newName) { this.newName = newName; }

//    public String getFormEmail() { return formEmail; }
//    public void setFormEmail(String formEmail) { this.formEmail = formEmail; }


    public String getStartOffDate() { return startOffDate; }
    public void setStartOffDate(String startOffDate) { this.startOffDate = startOffDate; }

    public String getEndOffDate() { return endOffDate; }
    public void setEndOffDate(String endOffDate) { this.endOffDate = endOffDate; }

    public int getTotalOffDay() { return totalOffDay; }
    public void setTotalOffDay(int totalOffDay) { this.totalOffDay = totalOffDay; }

    public String getReason() { return reason; }
    public void setReason(String reason) { this.reason = reason; }

    public String getApprovalStatus() { return approvalStatus; }
    public void setApprovalStatus(String approvalStatus) { this.approvalStatus = approvalStatus; }
    
    public String getRequestType() { return requestType; }
    public void setRequestType(String requestType) { this.requestType = requestType; }
    
    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(LocalDateTime createdAt) {
        this.createdAt = createdAt;
    }
    
    
    @JsonProperty("email")
    public String getEmail() {
        return user != null ? user.getEmail() : null;
    }
}