package utils

import (
	"crypto/rand"
	"encoding/hex"
	"encoding/json"
	"fmt"
	"math/big"
	"net"
	"net/http"
	"regexp"
	"strconv"
	"strings"
	"time"
	"unicode"

	"golang.org/x/crypto/bcrypt"
)

// ---------- Password Hashing ----------

func HashPassword(password string, cost int) (string, error) {
	bytes, err := bcrypt.GenerateFromPassword([]byte(password), cost)
	return string(bytes), err
}

func CheckPassword(password, hash string) bool {
	return bcrypt.CompareHashAndPassword([]byte(hash), []byte(password)) == nil
}

// ---------- Input Validation ----------

var emailRegex = regexp.MustCompile(`^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$`)

func ValidateEmail(email string) bool {
	if len(email) > 254 {
		return false
	}
	return emailRegex.MatchString(email)
}

func ValidatePassword(password string) error {
	if len(password) < 8 {
		return fmt.Errorf("password must be at least 8 characters")
	}
	if len(password) > 128 {
		return fmt.Errorf("password must be at most 128 characters")
	}
	var hasUpper, hasLower, hasDigit bool
	for _, c := range password {
		switch {
		case unicode.IsUpper(c):
			hasUpper = true
		case unicode.IsLower(c):
			hasLower = true
		case unicode.IsDigit(c):
			hasDigit = true
		}
	}
	if !hasUpper || !hasLower || !hasDigit {
		return fmt.Errorf("password must contain uppercase, lowercase and digit")
	}
	return nil
}

func SanitizeString(s string) string {
	s = strings.TrimSpace(s)
	s = strings.ReplaceAll(s, "<", "&lt;")
	s = strings.ReplaceAll(s, ">", "&gt;")
	s = strings.ReplaceAll(s, "\"", "&quot;")
	s = strings.ReplaceAll(s, "'", "&#39;")
	return s
}

// ---------- ID Generation ----------

func GenerateTicketID() string {
	const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
	prefix := make([]byte, 3)
	for i := range prefix {
		n, _ := rand.Int(rand.Reader, big.NewInt(int64(len(chars))))
		prefix[i] = chars[n.Int64()]
	}
	num, _ := rand.Int(rand.Reader, big.NewInt(999999))
	return fmt.Sprintf("%s-%06d", string(prefix), num.Int64())
}

func GenerateOrderNumber() string {
	ts := time.Now().UnixMilli()
	b := make([]byte, 4)
	rand.Read(b)
	return fmt.Sprintf("%d%s", ts, hex.EncodeToString(b))
}

func GenerateInvoiceNumber(id int64) string {
	year := time.Now().Year()
	return fmt.Sprintf("INV-%d-%06d", year, id)
}

func GenerateSecureToken(length int) string {
	b := make([]byte, length)
	rand.Read(b)
	return hex.EncodeToString(b)
}

// ---------- HTTP Helpers ----------

func JSON(w http.ResponseWriter, status int, data any) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	json.NewEncoder(w).Encode(data)
}

func ErrorJSON(w http.ResponseWriter, status int, message string) {
	JSON(w, status, map[string]any{
		"success": false,
		"error":   message,
	})
}

func SuccessJSON(w http.ResponseWriter, data any) {
	JSON(w, http.StatusOK, map[string]any{
		"success": true,
		"data":    data,
	})
}

func DecodeJSON(r *http.Request, v any) error {
	if r.Body == nil {
		return fmt.Errorf("request body is empty")
	}
	decoder := json.NewDecoder(r.Body)
	return decoder.Decode(v)
}

func GetIntParam(r *http.Request, key string) (int64, error) {
	val := r.PathValue(key)
	if val == "" {
		return 0, fmt.Errorf("missing parameter: %s", key)
	}
	return strconv.ParseInt(val, 10, 64)
}

func GetPagination(r *http.Request) (page int, perPage int) {
	page = 1
	perPage = 25

	if p := r.URL.Query().Get("page"); p != "" {
		if v, err := strconv.Atoi(p); err == nil && v > 0 {
			page = v
		}
	}
	if pp := r.URL.Query().Get("per_page"); pp != "" {
		if v, err := strconv.Atoi(pp); err == nil && v > 0 && v <= 100 {
			perPage = v
		}
	}
	return
}

func GetClientIP(r *http.Request) string {
	if xff := r.Header.Get("X-Forwarded-For"); xff != "" {
		parts := strings.Split(xff, ",")
		ip := strings.TrimSpace(parts[0])
		if net.ParseIP(ip) != nil {
			return ip
		}
	}
	if xri := r.Header.Get("X-Real-IP"); xri != "" {
		if net.ParseIP(xri) != nil {
			return xri
		}
	}
	host, _, err := net.SplitHostPort(r.RemoteAddr)
	if err != nil {
		return r.RemoteAddr
	}
	return host
}
