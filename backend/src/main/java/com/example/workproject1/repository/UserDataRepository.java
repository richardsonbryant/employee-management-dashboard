package com.example.workproject1.repository;

import com.example.workproject1.model.UserData;
import org.springframework.data.jpa.repository.JpaRepository;
import java.util.List;

public interface UserDataRepository extends JpaRepository<UserData, Long> {
	
	
    List<UserData> findByUserEmail(String email);
}