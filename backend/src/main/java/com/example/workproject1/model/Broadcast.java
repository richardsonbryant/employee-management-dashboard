package com.example.workproject1.model;

import jakarta.persistence.*;
import java.time.LocalDate;
import java.util.List;

import com.fasterxml.jackson.annotation.JsonManagedReference;
import com.fasterxml.jackson.annotation.JsonProperty;


@Entity
@Table(name = "broadcasts")
public class Broadcast {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "start_off_date")
    @JsonProperty("start_off_date")
    private LocalDate startOffDate;

    @Column(name = "end_off_date")
    @JsonProperty("end_off_date")
    private LocalDate endOffDate;

    @Column(name = "total_off_day")
    @JsonProperty("total_off_day")
    private int totalOffDay;

    @Column(name = "message")
    @JsonProperty("message")
    private String message;

    @Column(name = "created_at")
    @JsonProperty("created_at")
    private String createdAt;
    
    // Getters & Setters
    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public LocalDate getStartOffDate() { return startOffDate; }
    public void setStartOffDate(LocalDate startOffDate) { this.startOffDate = startOffDate; }

    public LocalDate getEndOffDate() { return endOffDate; }
    public void setEndOffDate(LocalDate endOffDate) { this.endOffDate = endOffDate; }

    public int getTotalOffDay() { return totalOffDay; }
    public void setTotalOffDay(int totalOffDay) { this.totalOffDay = totalOffDay; }

    public String getMessage() { return message; }
    public void setMessage(String message) { this.message = message; }
    
    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }
    
    @OneToMany(mappedBy = "broadcast", cascade = CascadeType.ALL, fetch = FetchType.LAZY)
    @JsonManagedReference
    private List<BroadcastResponse> responses;


    public List<BroadcastResponse> getResponses() { return responses; }
    public void setResponses(List<BroadcastResponse> responses) { this.responses = responses; }

}
