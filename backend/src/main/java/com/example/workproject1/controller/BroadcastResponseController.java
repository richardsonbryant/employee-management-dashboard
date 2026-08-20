package com.example.workproject1.controller;

import com.example.workproject1.dto.BroadcastResponseDTO;
import com.example.workproject1.model.Broadcast;

import com.example.workproject1.model.BroadcastResponse;
import com.example.workproject1.repository.BroadcastRepository;
import com.example.workproject1.repository.BroadcastResponseRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.server.ResponseStatusException;

import java.time.LocalDateTime;
import java.util.List;
import java.util.Optional;

@RestController
@RequestMapping("/api/broadcast-responses")
public class BroadcastResponseController {

    @Autowired
    private BroadcastResponseRepository responseRepo;

    @Autowired
    private BroadcastRepository broadcastRepo;

    @GetMapping
    public List<BroadcastResponse> getAllResponses() {
        return responseRepo.findAll();
    }

    @GetMapping("/broadcast/{broadcastId}")
    public List<BroadcastResponse> getResponsesByBroadcast(@PathVariable Long broadcastId) {
        return responseRepo.findByBroadcast_Id(broadcastId);
    }
    
    @PostMapping
    public BroadcastResponse createResponse(@RequestBody BroadcastResponseDTO dto) {
        Optional<Broadcast> optionalBroadcast = broadcastRepo.findById(dto.getBroadcastId());

        if (optionalBroadcast.isPresent()) {
            BroadcastResponse response = new BroadcastResponse();
            response.setBroadcast(optionalBroadcast.get());
            response.setUserId(dto.getUserId());
            response.setResponse(dto.getResponse());
            response.setCreatedAt(LocalDateTime.now().toString());
            return responseRepo.save(response);
        } else {
            throw new ResponseStatusException(HttpStatus.NOT_FOUND, "Broadcast not found");
        }
    }
}
