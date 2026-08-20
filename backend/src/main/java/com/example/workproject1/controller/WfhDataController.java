package com.example.workproject1.controller;

import com.example.workproject1.dto.WfhDataDTO;

import com.example.workproject1.model.User;
import com.example.workproject1.model.UserData;
import com.example.workproject1.model.WfhData;
import com.example.workproject1.repository.UserRepository;
import com.example.workproject1.repository.WfhDataRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

import java.util.List;
import java.util.Map;
import java.util.Optional;

@RestController
@RequestMapping("/api/wfhdata")
public class WfhDataController {

    @Autowired
    private WfhDataRepository wfhDataRepository;
    
    @Autowired
    private UserRepository userRepository;

    @GetMapping
    public List<WfhData> getAllWfhData() {
        return wfhDataRepository.findAll();
    }
    
    @GetMapping("/{id}")
    public ResponseEntity<?> getWfhDataById(@PathVariable Long id) {
        try {
            WfhData data = wfhDataRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("WFH data not found"));
            return ResponseEntity.ok(data);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Failed to get WFH data: " + e.getMessage());
        }
    }

    @PostMapping
    public ResponseEntity<?> createWfhData(@RequestBody WfhDataDTO dto) {
        try {
            User user = userRepository.findByEmail(dto.getEmail())
                    .orElseThrow(() -> new RuntimeException("User with email not found"));

            WfhData wfhData = new WfhData();
            wfhData.setNewName(dto.getNew_name());
            wfhData.setStartOffDate(dto.getStart_off_date());
            wfhData.setEndOffDate(dto.getEnd_off_date());
            wfhData.setTotalOffDay(dto.getTotal_off_day());
            wfhData.setReason(dto.getReason());
            wfhData.setRequestType(dto.getRequest_type());
            wfhData.setApprovalStatus("pending"); // default value

            wfhData.setUser(user);

            if (dto.getCreated_at() != null && !dto.getCreated_at().isEmpty()) {
                DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");
                LocalDateTime createdAt = LocalDateTime.parse(dto.getCreated_at(), formatter);
                wfhData.setCreatedAt(createdAt);
            }

            WfhData savedData = wfhDataRepository.save(wfhData);
            return ResponseEntity.ok(savedData);

        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(500).body("Gagal menyimpan data: " + e.getMessage());
        }
    }
    
    @PutMapping("/{id}/status")
    public ResponseEntity<?> updateWfhStatus(@PathVariable Long id, @RequestBody Map<String, String> body) {
        try {
            String status = body.get("approval_status");

            if (status == null || (!status.equalsIgnoreCase("approved") && !status.equalsIgnoreCase("rejected"))) {
                return ResponseEntity.badRequest().body("Invalid status. Allowed: approved or rejected");
            }

            WfhData data = wfhDataRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("WFH data not found"));

            data.setApprovalStatus(status.toLowerCase());
            wfhDataRepository.save(data);

            return ResponseEntity.ok(data);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Failed to update WFH data status: " + e.getMessage());
        }
    }

}
