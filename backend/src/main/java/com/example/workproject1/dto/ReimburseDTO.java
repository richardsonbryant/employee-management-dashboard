package com.example.workproject1.dto;

import java.math.BigDecimal;

import com.fasterxml.jackson.annotation.JsonProperty;

public class ReimburseDTO {

    @JsonProperty("new_name")
    private String newName;

    @JsonProperty("email")
    private String email;

    @JsonProperty("reimburse_type")
    private String reimburseType;

    @JsonProperty("reimburse_date")
    private String reimburseDate;

    @JsonProperty("total_reimburse")
    private BigDecimal totalReimburse;

    @JsonProperty("description")
    private String description;

    @JsonProperty("proof_letter")
    private String proofLetter;

    @JsonProperty("payment_date")
    private String paymentDate;

    @JsonProperty("reimburse_proof")
    private String reimburseProof;

    @JsonProperty("created_at")
    private String createdAt;

    // Getter & Setter
    public String getNewName() { return newName; }
    public void setNewName(String newName) { this.newName = newName; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

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

    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }
}

