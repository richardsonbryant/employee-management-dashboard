package com.example.workproject1.repository;

import java.util.List;

import com.example.workproject1.model.Reimburse;
import org.springframework.data.jpa.repository.JpaRepository;

public interface ReimburseRepository extends JpaRepository<Reimburse, Long> {
    List<Reimburse> findByUserEmail(String email);
}
