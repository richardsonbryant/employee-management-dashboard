package com.example.workproject1.repository;

import com.example.workproject1.model.Broadcast;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface BroadcastRepository extends JpaRepository<Broadcast, Long> {

}
