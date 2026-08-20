package com.example.workproject1.model;

import jakarta.persistence.*;


import com.fasterxml.jackson.annotation.JsonIgnore;
import com.fasterxml.jackson.annotation.JsonManagedReference;

import org.hibernate.annotations.CreationTimestamp;
import java.time.LocalDateTime;


import java.util.List;
@Entity
@Table(name = "users")
public class User {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    @Column(nullable = false)
    private String name;

    @Column(name = "email", updatable = false, unique = true)
    private String email;

    @Column(nullable = false)
    private String password;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false)
    private UserRole role;
    
    @Column(name = "leave_quota", nullable = false)
    private int leaveQuota = 12;
    
    @Column(name = "created_at", updatable = false)
    @CreationTimestamp
    private LocalDateTime createdAt;


    @OneToMany(mappedBy = "user", cascade = CascadeType.ALL, orphanRemoval = true,  fetch = FetchType.LAZY)
    @JsonManagedReference
    private List<UserData> userData;

    @OneToMany(mappedBy = "user", cascade = CascadeType.ALL, orphanRemoval = true,  fetch = FetchType.LAZY)
    @JsonManagedReference
    private List<WfhData> wfhData;

    @OneToMany(mappedBy = "user", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY )
    @JsonManagedReference
    private List<PermissionData> permissionData;

    @OneToMany(mappedBy = "user", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY )
    private List<UserAttendance> attendances;

    @OneToMany(mappedBy = "user", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY )
    private List<Reimburse> reimburse;

    public User() {}

    // Getters and Setters
    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getPassword() { return password; }
    public void setPassword(String password) { this.password = password; }

    public UserRole getRole() { return role; }
    public void setRole(UserRole role) { this.role = role; }

    public int getLeaveQuota() { return leaveQuota; }
    public void setLeaveQuota(int leaveQuota) { this.leaveQuota = leaveQuota; }

    public List<UserData> getUserData() { return userData; }
    public void setUserData(List<UserData> userData) { this.userData = userData; }

    public List<WfhData> getWfhData() { return wfhData; }
    public void setWfhData(List<WfhData> wfhData) { this.wfhData = wfhData; }

    public List<PermissionData> getPermissionData() { return permissionData; }
    public void setPermissionData(List<PermissionData> permissionData) { this.permissionData = permissionData; }
//
//    public List<UserAttendance> getAttendances() { return attendances; }
//    public void setAttendances(List<UserAttendance> attendances) { this.attendances = attendances; }
}