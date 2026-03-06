package middleware

import (
	"context"
	"log"
	"net/http"
	"strings"
	"sync"
	"time"
	"whmcs-rebuild/internal/utils"
)

type contextKey string

const (
	ClaimsKey contextKey = "claims"
)

// ---------- Logging Middleware ----------

func Logger(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		start := time.Now()
		rw := &responseWriter{ResponseWriter: w, statusCode: http.StatusOK}
		next.ServeHTTP(rw, r)
		log.Printf("[%s] %s %s %d %s", r.Method, r.URL.Path, utils.GetClientIP(r), rw.statusCode, time.Since(start))
	})
}

type responseWriter struct {
	http.ResponseWriter
	statusCode int
}

func (rw *responseWriter) WriteHeader(code int) {
	rw.statusCode = code
	rw.ResponseWriter.WriteHeader(code)
}

// ---------- CORS Middleware ----------

func CORS(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Access-Control-Allow-Origin", "*")
		w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, PATCH, OPTIONS")
		w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Request-ID")
		w.Header().Set("Access-Control-Max-Age", "86400")

		if r.Method == http.MethodOptions {
			w.WriteHeader(http.StatusNoContent)
			return
		}

		next.ServeHTTP(w, r)
	})
}

// ---------- Security Headers ----------

func SecurityHeaders(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("X-Content-Type-Options", "nosniff")
		w.Header().Set("X-Frame-Options", "DENY")
		w.Header().Set("X-XSS-Protection", "1; mode=block")
		w.Header().Set("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
		w.Header().Set("Content-Security-Policy", "default-src 'self'")
		w.Header().Set("Referrer-Policy", "strict-origin-when-cross-origin")
		next.ServeHTTP(w, r)
	})
}

// ---------- Panic Recovery ----------

func Recovery(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		defer func() {
			if err := recover(); err != nil {
				log.Printf("[PANIC] %s %s: %v", r.Method, r.URL.Path, err)
				utils.ErrorJSON(w, http.StatusInternalServerError, "internal server error")
			}
		}()
		next.ServeHTTP(w, r)
	})
}

// ---------- Rate Limiter ----------

type RateLimiter struct {
	mu       sync.Mutex
	visitors map[string]*visitor
	limit    int
	window   time.Duration
}

type visitor struct {
	count   int
	resetAt time.Time
}

func NewRateLimiter(limitPerWindow int, window time.Duration) *RateLimiter {
	rl := &RateLimiter{
		visitors: make(map[string]*visitor),
		limit:    limitPerWindow,
		window:   window,
	}
	go rl.cleanup()
	return rl
}

func (rl *RateLimiter) cleanup() {
	ticker := time.NewTicker(rl.window)
	defer ticker.Stop()
	for range ticker.C {
		rl.mu.Lock()
		now := time.Now()
		for ip, v := range rl.visitors {
			if now.After(v.resetAt) {
				delete(rl.visitors, ip)
			}
		}
		rl.mu.Unlock()
	}
}

func (rl *RateLimiter) Middleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		ip := utils.GetClientIP(r)

		rl.mu.Lock()
		v, exists := rl.visitors[ip]
		if !exists || time.Now().After(v.resetAt) {
			rl.visitors[ip] = &visitor{count: 1, resetAt: time.Now().Add(rl.window)}
			rl.mu.Unlock()
			next.ServeHTTP(w, r)
			return
		}
		v.count++
		count := v.count
		rl.mu.Unlock()

		if count > rl.limit {
			utils.ErrorJSON(w, http.StatusTooManyRequests, "rate limit exceeded")
			return
		}

		next.ServeHTTP(w, r)
	})
}

// ---------- JWT Auth Middleware ----------

func Auth(jwtSecret string) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			authHeader := r.Header.Get("Authorization")
			if authHeader == "" {
				utils.ErrorJSON(w, http.StatusUnauthorized, "missing authorization header")
				return
			}

			parts := strings.SplitN(authHeader, " ", 2)
			if len(parts) != 2 || !strings.EqualFold(parts[0], "bearer") {
				utils.ErrorJSON(w, http.StatusUnauthorized, "invalid authorization format")
				return
			}

			claims, err := utils.ValidateJWT(parts[1], jwtSecret)
			if err != nil {
				utils.ErrorJSON(w, http.StatusUnauthorized, "invalid or expired token")
				return
			}

			ctx := context.WithValue(r.Context(), ClaimsKey, claims)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

// ---------- Admin-Only Middleware ----------

func AdminOnly(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		claims := GetClaims(r)
		if claims == nil || claims.UserType != "admin" {
			utils.ErrorJSON(w, http.StatusForbidden, "admin access required")
			return
		}
		next.ServeHTTP(w, r)
	})
}

// ---------- Permission Check Middleware ----------

// RequirePermission checks whether the authenticated user has the given
// permission. Full Administrators (RoleID 1) always pass. For other roles,
// a production system should look up the role's permission set in the
// database and verify the required permission is present; non-admin roles
// are currently denied by default for safety.
func RequirePermission(permission string) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			claims := GetClaims(r)
			if claims == nil {
				utils.ErrorJSON(w, http.StatusUnauthorized, "authentication required")
				return
			}
			// Role ID 1 = Full Administrator — always allowed
			if claims.RoleID != 1 {
				// Non-admin roles are denied by default until a full
				// permission lookup (role → permissions DB table) is wired.
				utils.ErrorJSON(w, http.StatusForbidden, "permission denied: "+permission)
				return
			}
			next.ServeHTTP(w, r)
		})
	}
}

// ---------- Helper to extract claims ----------

func GetClaims(r *http.Request) *utils.JWTClaims {
	claims, _ := r.Context().Value(ClaimsKey).(*utils.JWTClaims)
	return claims
}
