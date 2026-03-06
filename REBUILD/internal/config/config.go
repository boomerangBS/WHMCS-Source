package config

import (
	"os"
	"strconv"
	"time"
)

type Config struct {
	Server   ServerConfig
	Database DatabaseConfig
	JWT      JWTConfig
	Security SecurityConfig
}

type ServerConfig struct {
	Host         string
	Port         int
	ReadTimeout  time.Duration
	WriteTimeout time.Duration
	IdleTimeout  time.Duration
}

type DatabaseConfig struct {
	Driver string
	DSN    string
}

type JWTConfig struct {
	Secret          string
	ExpirationHours int
	Issuer          string
}

type SecurityConfig struct {
	BcryptCost       int
	RateLimitPerMin  int
	MaxLoginAttempts int
	LockoutDuration  time.Duration
}

func Load() *Config {
	return &Config{
		Server: ServerConfig{
			Host:         getEnv("SERVER_HOST", "0.0.0.0"),
			Port:         getEnvInt("SERVER_PORT", 8080),
			ReadTimeout:  time.Duration(getEnvInt("SERVER_READ_TIMEOUT", 15)) * time.Second,
			WriteTimeout: time.Duration(getEnvInt("SERVER_WRITE_TIMEOUT", 15)) * time.Second,
			IdleTimeout:  time.Duration(getEnvInt("SERVER_IDLE_TIMEOUT", 60)) * time.Second,
		},
		Database: DatabaseConfig{
			Driver: getEnv("DB_DRIVER", "sqlite3"),
			DSN:    getEnv("DB_DSN", "whmcs_rebuild.db"),
		},
		JWT: JWTConfig{
			Secret:          getEnv("JWT_SECRET", "change-this-in-production-to-a-random-string"),
			ExpirationHours: getEnvInt("JWT_EXPIRATION_HOURS", 24),
			Issuer:          getEnv("JWT_ISSUER", "whmcs-rebuild"),
		},
		Security: SecurityConfig{
			BcryptCost:       getEnvInt("BCRYPT_COST", 12),
			RateLimitPerMin:  getEnvInt("RATE_LIMIT_PER_MIN", 60),
			MaxLoginAttempts: getEnvInt("MAX_LOGIN_ATTEMPTS", 5),
			LockoutDuration:  time.Duration(getEnvInt("LOCKOUT_DURATION_MIN", 15)) * time.Minute,
		},
	}
}

func getEnv(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}

func getEnvInt(key string, fallback int) int {
	if v := os.Getenv(key); v != "" {
		if i, err := strconv.Atoi(v); err == nil {
			return i
		}
	}
	return fallback
}
