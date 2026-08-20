package com.example.workproject1.repository;

import com.example.workproject1.model.User;

import org.springframework.data.repository.query.Param;
import org.springframework.data.jpa.repository.Query;

import org.springframework.data.jpa.repository.JpaRepository;

import java.util.Optional;

public interface UserRepository extends JpaRepository<User, Long> {
	
	Optional<User> findByEmail(String email);
	
	 boolean existsByEmail(String email); 
	
    @Query("SELECT u FROM User u " +
            "LEFT JOIN FETCH u.userData " +
            "LEFT JOIN FETCH u.wfhData " +
            "LEFT JOIN FETCH u.permissionData " +
            "WHERE u.id = :id")
     Optional<User> findUserWithAllData(@Param("id") Long id);
    
}
 