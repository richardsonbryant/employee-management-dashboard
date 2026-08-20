package com.example.workproject1.controller;

import com.example.workproject1.model.User;


import com.example.workproject1.repository.UserRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.validation.annotation.Validated;
import org.springframework.web.bind.annotation.*; 
import org.springframework.web.bind.annotation.RestController; 
import org.springframework.web.bind.annotation.RequestMapping;

import com.example.workproject1.dto.UserRegisterDTO;

import org.springframework.security.crypto.password.PasswordEncoder;

import com.example.workproject1.model.UserRole;

import java.util.List;
import java.util.Map;
import java.util.Optional;

@RestController
@RequestMapping("/api/users")
public class UserController {
    @Autowired
    private UserRepository userRepository;
    
    @Autowired
    private PasswordEncoder passwordEncoder; 

    @GetMapping
    public List<User> getAllUsers() {
        System.out.println("=== GET All Users Hit ===");
        return userRepository.findAll();
    }
    
    @PostMapping("/users")
    public ResponseEntity<?> createUser(@Validated @RequestBody UserRegisterDTO userDto) {
        if (userRepository.existsByEmail(userDto.getEmail())) {
            return ResponseEntity.status(HttpStatus.CONFLICT).body("Email already exists");
        }

        User user = new User();
        user.setName(userDto.getName());
        user.setEmail(userDto.getEmail());
        user.setPassword(passwordEncoder.encode(userDto.getPassword())); 
        user.setRole(UserRole.valueOf(userDto.getRole()));
        user.setLeaveQuota(12); // default quota

        return ResponseEntity.ok(userRepository.save(user));
    }
    
    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody Map<String, String> credentials) {
        String email = credentials.get("email");
        String password = credentials.get("password");
        
        // Log input data
        System.out.println("Received login request: email=" + email + ", password=" + password);


        Optional<User> userOptional = userRepository.findByEmail(email);
        if (userOptional.isEmpty()) {
        	System.out.println("User not found: " + email);
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid credentials");
        }

        User user = userOptional.get();

        if (!passwordEncoder.matches(password, user.getPassword())) {
            System.out.println("Password mismatch for user: " + email);

            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid credentials");
        }
        
        System.out.println("Login successful for user: " + email);

        // Return user data (tanpa password)
        user.setPassword(null);
        return ResponseEntity.ok(user);
    }


    @GetMapping("/{id}")
    public ResponseEntity<User> getUserById(@PathVariable Long id) {
        Optional<User> user = userRepository.findById(id);
        return user.map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }
    
    

    @GetMapping("/search")
    public ResponseEntity<?> getUserByEmailQuery(@RequestParam String email) {
        try {
            Optional<User> user = userRepository.findByEmail(email);
            if (user.isPresent()) {
                return ResponseEntity.ok(user.get());
            } else {
                return ResponseEntity.status(404).body("User not found");
            }
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Internal Error: " + e.getMessage());
        }
    }
    
    @PutMapping("/{userId}/leave_quota")
    public ResponseEntity<?> updateLeaveQuota(@PathVariable Long userId, @RequestBody Map<String, Integer> request) {
        try {
            User user = userRepository.findById(userId)
                .orElseThrow(() -> new RuntimeException("User not found"));

            int newLeaveQuota = request.get("leave_quota");
            
            System.out.println("=== PUT Update Leave Quota Hit === userId: " + userId + " leave_quota: " + newLeaveQuota);

            user.setLeaveQuota(newLeaveQuota);

            userRepository.save(user);
            
            return ResponseEntity.ok(user);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Failed to update leave quota: " + e.getMessage());
        }
   

    }

}
