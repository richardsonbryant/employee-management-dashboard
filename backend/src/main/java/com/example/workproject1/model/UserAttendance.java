package com.example.workproject1.model;

import java.util.Map;

import com.fasterxml.jackson.annotation.JsonBackReference;
import com.fasterxml.jackson.annotation.JsonProperty;

import jakarta.persistence.*;

@Entity
@Table(name = "user_attendance")
public class UserAttendance {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

//    private String email;

    private String clock_in;
    private String clock_out;
    private Float total_hours;
    

    private String verification;

    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "email", referencedColumnName = "email", nullable = false)
    @JsonBackReference
    private User user;

    // Constructors
    public UserAttendance() {}
    
    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    // Getters and Setters
    public Long getId() { return id; }

    public void setId(Long id) { this.id = id; }

//    public String getEmail() { return email; }
//
//    public void setEmail(String email) { this.email = email; }

    public String getClock_in() { return clock_in; }

    public void setClock_in(String clock_in) { this.clock_in = clock_in; }

    public String getClock_out() { return clock_out; }

    public void setClock_out(String clock_out) { this.clock_out = clock_out; }


	public Float getTotal_hours() { 
	    return total_hours; 
	}
	
	public void setTotal_hours(Float total_hours) { 
	    this.total_hours = total_hours; 
	}
    public String getVerification() { return verification; }

    public void setVerification(String verification) { this.verification = verification; }
    
    @JsonProperty("name")
    public String getName() {
        return user != null ? user.getName() : null;
    }
    
    

    @JsonProperty("user")
    public void setUserFromJson(Map<String, String> userMap) {
        if (userMap != null && userMap.containsKey("email")) {
            User user = new User();
            user.setEmail(userMap.get("email"));
            this.user = user;
        }
    }

    
    
}
