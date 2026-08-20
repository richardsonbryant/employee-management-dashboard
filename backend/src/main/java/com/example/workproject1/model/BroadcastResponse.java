package com.example.workproject1.model;

import com.fasterxml.jackson.annotation.JsonBackReference;
import com.fasterxml.jackson.annotation.JsonProperty;

import jakarta.persistence.*;

@Entity
@Table(name = "broadcast_responses")
public class BroadcastResponse {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "broadcast_id", nullable = false)
    @JsonBackReference
    private Broadcast broadcast;

    @Column(name = "user_id")
    private Long userId;

    @Column(name = "response")
    private String response;
    
    @Column(name = "created_at")
    @JsonProperty("created_at")
    private String createdAt;

    // Getters & Setters
    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public Broadcast getBroadcast() { return broadcast; }
    public void setBroadcast(Broadcast broadcast) { this.broadcast = broadcast; }

    public Long getUserId() { return userId; }
    public void setUserId(Long userId) { this.userId = userId; }

    public String getResponse() { return response; }
    public void setResponse(String response) { this.response = response; }
    
    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }
    
    public void setBroadcastId(Long broadcastId) {
        this.broadcast = new Broadcast();
        this.broadcast.setId(broadcastId);
    }
}
