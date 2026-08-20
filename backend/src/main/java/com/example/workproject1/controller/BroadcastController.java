package com.example.workproject1.controller;

import com.example.workproject1.model.Broadcast;
import com.example.workproject1.repository.BroadcastRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/broadcasts")
public class BroadcastController {

    @Autowired
    private BroadcastRepository broadcastRepository;

    @GetMapping
    public List<Broadcast> getAllBroadcasts() {
        return broadcastRepository.findAll();
    }	

    @GetMapping("/{id}")
    public Broadcast getBroadcastById(@PathVariable Long id) {
        return broadcastRepository.findById(id).orElse(null);
    }

    @PostMapping
    public Broadcast createBroadcast(@RequestBody Broadcast broadcast) {
        return broadcastRepository.save(broadcast);
    }

    @PutMapping("/{id}")
    public Broadcast updateBroadcast(@PathVariable Long id, @RequestBody Broadcast updated) {
        return broadcastRepository.findById(id).map(broadcast -> {
            broadcast.setStartOffDate(updated.getStartOffDate());
            broadcast.setEndOffDate(updated.getEndOffDate());
            broadcast.setTotalOffDay(updated.getTotalOffDay());
            broadcast.setMessage(updated.getMessage());
            return broadcastRepository.save(broadcast);
        }).orElse(null);
    }

    @DeleteMapping("/{id}")
    public void deleteBroadcast(@PathVariable Long id) {
        broadcastRepository.deleteById(id);
    }
}
