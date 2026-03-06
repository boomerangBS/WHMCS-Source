package utils

import (
	"net/http"
	"net/http/httptest"
	"strings"
	"testing"
)

func TestHashPassword(t *testing.T) {
	hash, err := HashPassword("Test1234", 10)
	if err != nil {
		t.Fatalf("HashPassword failed: %v", err)
	}
	if hash == "" {
		t.Fatal("hash should not be empty")
	}
	if hash == "Test1234" {
		t.Fatal("hash should not equal plaintext")
	}
}

func TestCheckPassword(t *testing.T) {
	hash, _ := HashPassword("Test1234", 10)
	if !CheckPassword("Test1234", hash) {
		t.Fatal("should match correct password")
	}
	if CheckPassword("Wrong123", hash) {
		t.Fatal("should not match wrong password")
	}
}

func TestValidateEmail(t *testing.T) {
	tests := []struct {
		email string
		valid bool
	}{
		{"user@example.com", true},
		{"test.user+tag@domain.co.uk", true},
		{"invalid", false},
		{"@no-user.com", false},
		{"no-at-sign", false},
		{"", false},
		{strings.Repeat("a", 250) + "@b.com", false},
	}
	for _, tt := range tests {
		if got := ValidateEmail(tt.email); got != tt.valid {
			t.Errorf("ValidateEmail(%q) = %v, want %v", tt.email, got, tt.valid)
		}
	}
}

func TestValidatePassword(t *testing.T) {
	tests := []struct {
		password string
		wantErr  bool
	}{
		{"Ab1cdefgh", false},
		{"short", true},
		{"alllowercase1", true},
		{"ALLUPPERCASE1", true},
		{"NoDigitsHere", true},
		{"Ab1", true},
		{strings.Repeat("A", 129), true},
	}
	for _, tt := range tests {
		err := ValidatePassword(tt.password)
		if (err != nil) != tt.wantErr {
			t.Errorf("ValidatePassword(%q) err=%v, wantErr=%v", tt.password, err, tt.wantErr)
		}
	}
}

func TestSanitizeString(t *testing.T) {
	tests := []struct {
		input    string
		expected string
	}{
		{"  hello  ", "hello"},
		{"<script>alert('xss')</script>", "&lt;script&gt;alert(&#39;xss&#39;)&lt;/script&gt;"},
		{"normal text", "normal text"},
		{`he said "hello"`, `he said &quot;hello&quot;`},
	}
	for _, tt := range tests {
		if got := SanitizeString(tt.input); got != tt.expected {
			t.Errorf("SanitizeString(%q) = %q, want %q", tt.input, got, tt.expected)
		}
	}
}

func TestGenerateTicketID(t *testing.T) {
	id := GenerateTicketID()
	if len(id) == 0 {
		t.Fatal("ticket ID should not be empty")
	}
	if !strings.Contains(id, "-") {
		t.Fatalf("ticket ID should contain a dash, got %q", id)
	}
	// Ensure uniqueness
	id2 := GenerateTicketID()
	if id == id2 {
		t.Fatal("two ticket IDs should not be identical")
	}
}

func TestGenerateOrderNumber(t *testing.T) {
	num := GenerateOrderNumber()
	if len(num) == 0 {
		t.Fatal("order number should not be empty")
	}
	num2 := GenerateOrderNumber()
	if num == num2 {
		t.Fatal("two order numbers should not be identical")
	}
}

func TestGenerateSecureToken(t *testing.T) {
	token := GenerateSecureToken(32)
	if len(token) != 64 { // 32 bytes = 64 hex chars
		t.Fatalf("expected 64 hex chars, got %d", len(token))
	}
	token2 := GenerateSecureToken(32)
	if token == token2 {
		t.Fatal("two tokens should not be identical")
	}
}

func TestGetPagination(t *testing.T) {
	tests := []struct {
		query       string
		wantPage    int
		wantPerPage int
	}{
		{"", 1, 25},
		{"?page=3&per_page=50", 3, 50},
		{"?page=-1&per_page=200", 1, 25},
		{"?page=abc", 1, 25},
	}
	for _, tt := range tests {
		r := httptest.NewRequest(http.MethodGet, "/test"+tt.query, nil)
		page, perPage := GetPagination(r)
		if page != tt.wantPage || perPage != tt.wantPerPage {
			t.Errorf("GetPagination(%q) = (%d, %d), want (%d, %d)", tt.query, page, perPage, tt.wantPage, tt.wantPerPage)
		}
	}
}

func TestGetClientIP(t *testing.T) {
	tests := []struct {
		name       string
		xff        string
		xri        string
		remoteAddr string
		want       string
	}{
		{"from X-Forwarded-For", "1.2.3.4, 5.6.7.8", "", "9.10.11.12:1234", "1.2.3.4"},
		{"from X-Real-IP", "", "10.0.0.1", "9.10.11.12:1234", "10.0.0.1"},
		{"from RemoteAddr", "", "", "192.168.1.1:4567", "192.168.1.1"},
	}
	for _, tt := range tests {
		r := httptest.NewRequest(http.MethodGet, "/", nil)
		r.RemoteAddr = tt.remoteAddr
		if tt.xff != "" {
			r.Header.Set("X-Forwarded-For", tt.xff)
		}
		if tt.xri != "" {
			r.Header.Set("X-Real-IP", tt.xri)
		}
		if got := GetClientIP(r); got != tt.want {
			t.Errorf("GetClientIP(%s) = %q, want %q", tt.name, got, tt.want)
		}
	}
}

func TestJSON(t *testing.T) {
	w := httptest.NewRecorder()
	JSON(w, http.StatusOK, map[string]string{"hello": "world"})
	if w.Code != http.StatusOK {
		t.Errorf("expected 200, got %d", w.Code)
	}
	if ct := w.Header().Get("Content-Type"); ct != "application/json" {
		t.Errorf("expected application/json, got %q", ct)
	}
	body := w.Body.String()
	if !strings.Contains(body, `"hello":"world"`) {
		t.Errorf("unexpected body: %s", body)
	}
}

func TestErrorJSON(t *testing.T) {
	w := httptest.NewRecorder()
	ErrorJSON(w, http.StatusBadRequest, "bad input")
	if w.Code != http.StatusBadRequest {
		t.Errorf("expected 400, got %d", w.Code)
	}
	body := w.Body.String()
	if !strings.Contains(body, `"error":"bad input"`) {
		t.Errorf("unexpected body: %s", body)
	}
	if !strings.Contains(body, `"success":false`) {
		t.Errorf("expected success:false in body: %s", body)
	}
}
