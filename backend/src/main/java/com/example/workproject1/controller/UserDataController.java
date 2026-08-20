package com.example.workproject1.controller;

import java.util.List;


import java.util.Map;
import java.util.Optional;
import java.util.Set;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.example.workproject1.dto.UserDataDTO;
import com.example.workproject1.model.User;
import com.example.workproject1.model.UserData;
import com.example.workproject1.repository.UserDataRepository;
import com.example.workproject1.repository.UserRepository;
import com.example.workproject1.service.HolidayService;
import com.example.workproject1.service.LeaveCalculatorService;

import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

import jakarta.transaction.Transactional;

@RestController
@RequestMapping("/api/userdata")
public class UserDataController {

    @Autowired
    private UserDataRepository userDataRepository;
    
    @Autowired
    private UserRepository userRepository;
    
    @Autowired
    private HolidayService holidayService;

    @Autowired
    private LeaveCalculatorService leaveCalculatorService;


    @GetMapping
    @Transactional 
    public List<UserData> getAllUserData() {
        return userDataRepository.findAll();
    }
    
    @GetMapping("/user/{email}")
    public List<UserData> getUserDataByEmail(@PathVariable String email) {
        return userDataRepository.findByUserEmail(email);
    }
    
    @GetMapping("/{id}")
    public ResponseEntity<UserData> getUserDataById(@PathVariable Long id) {
        Optional<UserData> userData = userDataRepository.findById(id);
        return userData.map(ResponseEntity::ok)
                       .orElse(ResponseEntity.notFound().build());
    }

//    @PostMapping
//    public ResponseEntity<?> createUserData(@RequestBody UserDataDTO dto) {
//        try {
//            User user = userRepository.findByEmail(dto.getEmail())
//                    .orElseThrow(() -> new RuntimeException("User not found"));
//
//            LocalDate start = LocalDate.parse(dto.getStart_off_date());
//            LocalDate end = LocalDate.parse(dto.getEnd_off_date());
//
//            if (start.isAfter(end)) {
//                return ResponseEntity.badRequest().body("Start date must be before end date");
//            }
//
//            // 🔥 GET HOLIDAYS FROM GOOGLE
//            Set<LocalDate> holidays = holidayService.getHolidays(start, end);
//
//            // 🔥 CALCULATE WORKING DAYS
//            int totalOffDay = leaveCalculatorService
//                    .calculateWorkingDays(start, end, holidays);
//
//            UserData userData = new UserData();
//            userData.setUser(user);
//            userData.setNewName(dto.getNew_name());
//            userData.setStartOffDate(dto.getStart_off_date());
//            userData.setEndOffDate(dto.getEnd_off_date());
//            userData.setReason(dto.getReason());
//            userData.setRequestType("annual");
//            userData.setApprovalStatus("pending");
//            userData.setTotalOffDay(totalOffDay);
//            userData.setCreatedAt(LocalDateTime.now());
//
//            userDataRepository.save(userData);
//
//            return ResponseEntity.ok(userData);
//
//        } catch (Exception e) {
//            return ResponseEntity
//                    .status(500)
//                    .body("Failed to submit leave: " + e.getMessage());
//        }
//    }
    
    @PostMapping
    public ResponseEntity<?> createUserData(@RequestBody UserDataDTO dto) {
        try {
            // 1️⃣ Cari user berdasarkan email
            User user = userRepository.findByEmail(dto.getEmail())
                .orElseThrow(() -> new RuntimeException("User not found"));

            // 2️⃣ PARSE STRING → LocalDate (INI YANG KAMU TANYA)
            LocalDate startDate = LocalDate.parse(dto.getStart_off_date());
            LocalDate endDate = LocalDate.parse(dto.getEnd_off_date());

            // 3️⃣ Validasi tanggal
            if (startDate.isAfter(endDate)) {
                return ResponseEntity.badRequest()
                    .body("Start date tidak boleh lebih besar dari end date");
            }

            // 4️⃣ Ambil tanggal merah dari Google Calendar
            Set<LocalDate> holidays = holidayService.getHolidays(startDate, endDate);

            // 5️⃣ Hitung hari kerja
            int totalOffDay = leaveCalculatorService
                .calculateWorkingDays(startDate, endDate, holidays);

            // 6️⃣ Simpan ke entity
            UserData userData = new UserData();
            userData.setUser(user);
            userData.setNewName(dto.getNew_name());
            userData.setStartOffDate(dto.getStart_off_date());
            userData.setEndOffDate(dto.getEnd_off_date());
            userData.setReason(dto.getReason());
            userData.setRequestType("annual");
            userData.setApprovalStatus("pending");
            userData.setTotalOffDay(totalOffDay);
            userData.setCreatedAt(LocalDateTime.now());

            userDataRepository.save(userData);

            return ResponseEntity.ok(userData);

        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(500)
                .body("Gagal submit cuti: " + e.getMessage());
        }
    }


    
    @PutMapping("/{id}/status")
    public ResponseEntity<?> updateStatus(@PathVariable Long id, @RequestBody Map<String, String> body) {
        try {
        	
        	
            // Mendapatkan status dari body request
            String status = body.get("approval_status");

            // Validasi status
            if (status == null || (!status.equalsIgnoreCase("approved") && !status.equalsIgnoreCase("rejected"))) {
                return ResponseEntity.badRequest().body("Invalid status. Allowed: approved or rejected");
            }

            // Menemukan UserData berdasarkan ID
            Optional<UserData> userDataOptional = userDataRepository.findById(id);
            
            if (!userDataOptional.isPresent()) {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body("User data not found");
            }

            UserData userData = userDataOptional.get();
            userData.setApprovalStatus(status.toLowerCase());
            
            // Simpan perubahan
            userDataRepository.save(userData);

            // Mengembalikan response
            return ResponseEntity.ok(userData);
        } catch (Exception e) {
            return ResponseEntity.status(500).body("Failed to update status: " + e.getMessage());
        }
    }



    
    
}