package com.example.workproject1.model;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.Map;

import com.fasterxml.jackson.annotation.JsonBackReference;
import com.fasterxml.jackson.annotation.JsonProperty;

import jakarta.persistence.*;

@Entity
@Table(name = "reimburse")
public class Reimburse {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "new_name")
    @JsonProperty("new_name")
    private String newName;

    @Column(name = "reimburse_type")
    @JsonProperty("reimburse_type")
    private String reimburseType;

    @Column(name = "reimburse_date")
    @JsonProperty("reimburse_date")
    private String reimburseDate;

    @Column(name = "total_reimburse")
    @JsonProperty("total_reimburse")
    private BigDecimal totalReimburse;

    @Column(name = "description")
    @JsonProperty("description")
    private String description;

    @Column(name = "proof_letter")
    @JsonProperty("proof_letter")
    private String proofLetter;

    @Column(name = "payment_date")
    @JsonProperty("payment_date")
    private String paymentDate;

    @Column(name = "reimburse_proof")
    @JsonProperty("reimburse_proof")
    private String reimburseProof;

    @Column(name = "approval_status", nullable = false)
    @JsonProperty("approval_status")
    private String approvalStatus = "pending";

    @Column(name = "created_at")
    @JsonProperty("created_at")
    private LocalDateTime createdAt = LocalDateTime.now();

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "email", referencedColumnName = "email", nullable = false)
    @JsonBackReference
    private User user;

    // Getter & Setter
    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getNewName() { return newName; }
    public void setNewName(String newName) { this.newName = newName; }

    public String getReimburseType() { return reimburseType; }
    public void setReimburseType(String reimburseType) { this.reimburseType = reimburseType; }

    public String getReimburseDate() { return reimburseDate; }
    public void setReimburseDate(String reimburseDate) { this.reimburseDate = reimburseDate; }

    public BigDecimal getTotalReimburse() { return totalReimburse; }
    public void setTotalReimburse(BigDecimal totalReimburse) { this.totalReimburse = totalReimburse; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public String getProofLetter() { return proofLetter; }
    public void setProofLetter(String proofLetter) { this.proofLetter = proofLetter; }

    public String getPaymentDate() { return paymentDate; }
    public void setPaymentDate(String paymentDate) { this.paymentDate = paymentDate; }

    public String getReimburseProof() { return reimburseProof; }
    public void setReimburseProof(String reimburseProof) { this.reimburseProof = reimburseProof; }

    public String getApprovalStatus() { return approvalStatus; }
    public void setApprovalStatus(String approvalStatus) { this.approvalStatus = approvalStatus; }

    public LocalDateTime getCreatedAt() { return createdAt; }
    public void setCreatedAt(LocalDateTime createdAt) { this.createdAt = createdAt; }

    public User getUser() { return user; }
    public void setUser(User user) { this.user = user; }

    @JsonProperty("user")
    public void setUserFromJson(Map<String, String> userMap) {
        if (userMap != null && userMap.containsKey("email")) {
            User user = new User();
            user.setEmail(userMap.get("email"));
            this.user = user;
        }
    }
}
