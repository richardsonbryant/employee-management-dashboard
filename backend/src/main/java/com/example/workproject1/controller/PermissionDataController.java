package com.example.workproject1.controller;

import com.example.workproject1.dto.PermissionDataDTO;

import com.example.workproject1.model.PermissionData;
import com.example.workproject1.model.User;
import com.example.workproject1.repository.PermissionDataRepository;
import com.example.workproject1.repository.UserRepository;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

@RestController
@RequestMapping("/api/permissiondata")
public class PermissionDataController {

    @Autowired
    private PermissionDataRepository permissionDataRepository;
    
    @Autowired
    private UserRepository userRepository;

    @GetMapping
    public List<PermissionData> getAllPermissionData() {
        return permissionDataRepository.findAll();
    }
    
    @GetMapping("/{id}")
    public ResponseEntity<?> getPermissionDataById(@PathVariable Long id) {
        try {
            PermissionData data = permissionDataRepository.findById(id)
                    .orElseThrow(() -> new RuntimeException("Permission data not found"));
            return ResponseEntity.ok(data);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Failed to get permission data: " + e.getMessage());
        }
    }

 
    @PostMapping
    public ResponseEntity<?> createPermissionData(@RequestBody PermissionDataDTO dto) {
        try {
            User user = userRepository.findByEmail(dto.getEmail())
                    .orElseThrow(() -> new RuntimeException("User with email not found"));

            PermissionData permissionData = new PermissionData();
            permissionData.setNewName(dto.getNew_name());
            permissionData.setStartOffDate(dto.getStart_off_date());
            permissionData.setEndOffDate(dto.getEnd_off_date());
            permissionData.setTotalOffDay(dto.getTotal_off_day());
            permissionData.setReason(dto.getReason());
            permissionData.setRequestType(dto.getRequest_type());
            permissionData.setApprovalStatus("pending"); // default
            permissionData.setHasDoctorLetter(Boolean.parseBoolean(dto.getHas_doctor_letter()));
            permissionData.setPermissionLetter(dto.getPermission_letter());

            permissionData.setUser(user);

            if (dto.getCreated_at() != null && !dto.getCreated_at().isEmpty()) {
                DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");
                LocalDateTime createdAt = LocalDateTime.parse(dto.getCreated_at(), formatter);
                permissionData.setCreatedAt(createdAt);
            }

            PermissionData savedData = permissionDataRepository.save(permissionData);
            return ResponseEntity.ok(savedData);

        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(500).body("Gagal menyimpan data: " + e.getMessage());
        }
    }
    
    @PutMapping("/{id}/status")
    public ResponseEntity<?> updatePermissionStatus(@PathVariable Long id, @RequestBody Map<String, String> body) {
        try {
            String status = body.get("approval_status");

            if (status == null || (!status.equalsIgnoreCase("approved") && !status.equalsIgnoreCase("rejected"))) {
                return ResponseEntity.badRequest().body("Invalid status. Allowed: approved or rejected");
            }

            PermissionData data = permissionDataRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Permission data not found"));

            data.setApprovalStatus(status.toLowerCase());
            permissionDataRepository.save(data);

            return ResponseEntity.ok(data);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Failed to update permission data status: " + e.getMessage());
        }
    }

}
