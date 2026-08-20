package com.example.workproject1.repository;

import com.example.workproject1.model.BroadcastResponse;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface BroadcastResponseRepository extends JpaRepository<BroadcastResponse, Long> {
	List<BroadcastResponse> findByBroadcast_Id(Long broadcastId);
}
