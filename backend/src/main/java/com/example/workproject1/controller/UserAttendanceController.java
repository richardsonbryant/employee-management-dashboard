package com.example.workproject1.controller;

import com.example.workproject1.dto.UserAttendanceDTO;

import com.example.workproject1.model.User;
import com.example.workproject1.model.UserAttendance;
import com.example.workproject1.repository.UserAttendanceRepository;
import com.example.workproject1.repository.UserRepository;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@RestController
@RequestMapping("/api/user-attendance")
public class UserAttendanceController {

    @Autowired
    private UserAttendanceRepository userAttendanceRepository;
    
    @Autowired
    private UserRepository userRepository;

    // Get all
    @GetMapping
    public List<UserAttendanceDTO> getAll() {
        List<UserAttendance> userAttendances = userAttendanceRepository.findAll();
        return userAttendances.stream()
                              .map(UserAttendanceDTO::new) 
                              .collect(Collectors.toList());
    }
    // Get by ID
    @GetMapping("/{id}")
    public UserAttendance getById(@PathVariable Long id) {
        return userAttendanceRepository.findById(id).orElse(null);
    }

    // Get by email
    @GetMapping("/email/{email}")
    public List<UserAttendance> getByEmail(@PathVariable String email) {
        return userAttendanceRepository.findByUserEmail(email);
    }

    // Create
    @PostMapping
    public ResponseEntity<?> create(@RequestBody UserAttendance userAttendance) {
        try {
            String email = userAttendance.getUser().getEmail();
            if (email == null || email.isEmpty()) {
                return ResponseEntity.badRequest().body("Email tidak boleh kosong");
            }

            // Ambil user dari database berdasarkan email
            Optional<User> userOptional = userRepository.findByEmail(email); 

            if (userOptional.isEmpty()) {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body("User tidak ditemukan");
            }

            // Set user yang valid ke attendance
            userAttendance.setUser(userOptional.get());

            // Simpan attendance
            UserAttendance saved = userAttendanceRepository.save(userAttendance);
            return ResponseEntity.ok(saved);

        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body("Error: " + e.getMessage());
        }
    }
    

    @PutMapping("/{id}")
    public ResponseEntity<?> update(@PathVariable Long id, @RequestBody UserAttendance updated) {
        Optional<UserAttendance> optionalAttendance = userAttendanceRepository.findById(id);

        if (optionalAttendance.isPresent()) {
            UserAttendance attendance = optionalAttendance.get();
            attendance.setClock_in(updated.getClock_in());
            attendance.setClock_out(updated.getClock_out());
            attendance.setTotal_hours(updated.getTotal_hours());
            attendance.setVerification(updated.getVerification());
            
            // JANGAN mengganti user, gunakan user yang sudah ada!
            // attendance.setUser(updated.getUser()); <-- Hapus baris ini!

            return ResponseEntity.ok(userAttendanceRepository.save(attendance));
        } else {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body("Attendance not found");
        }
    }

    // Delete
    @DeleteMapping("/{id}")
    public void delete(@PathVariable Long id) {
        userAttendanceRepository.deleteById(id);
    }
}
