package com.example.workproject1.repository;

import com.example.workproject1.model.UserAttendance;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface UserAttendanceRepository extends JpaRepository<UserAttendance, Long> {
    List<UserAttendance> findByUserEmail(String email);
}
