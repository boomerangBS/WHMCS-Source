package utils

import (
	"testing"
)

func TestGenerateAndValidateJWT(t *testing.T) {
	secret := "test-secret-key-for-unit-tests"
	userID := int64(42)
	email := "admin@example.com"
	userType := "admin"
	roleID := int64(1)

	token, expiresAt, err := GenerateJWT(secret, userID, email, userType, roleID, 1, "test-issuer")
	if err != nil {
		t.Fatalf("GenerateJWT failed: %v", err)
	}
	if token == "" {
		t.Fatal("token should not be empty")
	}
	if expiresAt == 0 {
		t.Fatal("expiresAt should not be zero")
	}

	// Validate
	claims, err := ValidateJWT(token, secret)
	if err != nil {
		t.Fatalf("ValidateJWT failed: %v", err)
	}
	if claims.UserID != userID {
		t.Errorf("expected userID %d, got %d", userID, claims.UserID)
	}
	if claims.Email != email {
		t.Errorf("expected email %q, got %q", email, claims.Email)
	}
	if claims.UserType != userType {
		t.Errorf("expected userType %q, got %q", userType, claims.UserType)
	}
	if claims.RoleID != roleID {
		t.Errorf("expected roleID %d, got %d", roleID, claims.RoleID)
	}
	if claims.Issuer != "test-issuer" {
		t.Errorf("expected issuer %q, got %q", "test-issuer", claims.Issuer)
	}
}

func TestValidateJWT_WrongSecret(t *testing.T) {
	token, _, _ := GenerateJWT("correct-secret", 1, "a@b.com", "admin", 1, 1, "test")
	_, err := ValidateJWT(token, "wrong-secret")
	if err == nil {
		t.Fatal("should fail with wrong secret")
	}
}

func TestValidateJWT_InvalidToken(t *testing.T) {
	_, err := ValidateJWT("not.a.valid.token", "secret")
	if err == nil {
		t.Fatal("should fail with invalid token")
	}
}

func TestValidateJWT_EmptyToken(t *testing.T) {
	_, err := ValidateJWT("", "secret")
	if err == nil {
		t.Fatal("should fail with empty token")
	}
}
