package com.example.workproject1.controller;

import com.example.workproject1.dto.ReimburseDTO;
import com.example.workproject1.model.Reimburse;
import com.example.workproject1.model.User;
import com.example.workproject1.repository.ReimburseRepository;
import com.example.workproject1.repository.UserRepository;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.List;

@RestController
@RequestMapping("/api/reimburse")
public class ReimburseController {

    @Autowired
    private ReimburseRepository reimburseRepository;

    @Autowired
    private UserRepository userRepository;

    @GetMapping
    public List<Reimburse> getAllReimburse() {
        return reimburseRepository.findAll();
    }
    
    @GetMapping("/{id}")
    public ResponseEntity<?> getReimburseById(@PathVariable Long id) {
        try {
            Reimburse data = reimburseRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("Reimburse data not found"));
            return ResponseEntity.ok(data);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Failed to get reimburse data: " + e.getMessage());
        }
    }


    @PostMapping
    public ResponseEntity<?> createReimburse(@RequestBody ReimburseDTO dto) {
        try {
            User user = userRepository.findByEmail(dto.getEmail())
                    .orElseThrow(() -> new RuntimeException("User with email not found"));

            Reimburse reimburse = new Reimburse();
            reimburse.setNewName(dto.getNewName());
            reimburse.setReimburseType(dto.getReimburseType());
            reimburse.setReimburseDate(dto.getReimburseDate());
            reimburse.setTotalReimburse(dto.getTotalReimburse());
            reimburse.setDescription(dto.getDescription());
            reimburse.setProofLetter(dto.getProofLetter());
            reimburse.setApprovalStatus("pending"); // default
            reimburse.setUser(user);

            if (dto.getPaymentDate() != null && !dto.getPaymentDate().isEmpty()) {
                reimburse.setPaymentDate(dto.getPaymentDate());
            }

            if (dto.getReimburseProof() != null) {
                reimburse.setReimburseProof(dto.getReimburseProof());
            }

            if (dto.getCreatedAt() != null && !dto.getCreatedAt().isEmpty()) {
                DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");
                LocalDateTime createdAt = LocalDateTime.parse(dto.getCreatedAt(), formatter);
                reimburse.setCreatedAt(createdAt);
            }


            Reimburse savedData = reimburseRepository.save(reimburse);
            return ResponseEntity.ok(savedData);

        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(500).body("Gagal menyimpan data reimburse: " + e.getMessage());
        }
    }
}
