package database

import (
	"database/sql"
	"os"
	"testing"

	_ "github.com/mattn/go-sqlite3"
)

func TestNewAndMigrate(t *testing.T) {
	dbPath := "/tmp/test_whmcs_rebuild.db"
	os.Remove(dbPath)
	defer os.Remove(dbPath)

	db, err := New("sqlite3", dbPath)
	if err != nil {
		t.Fatalf("failed to create database: %v", err)
	}
	defer db.Close()

	if err := RunMigrations(db); err != nil {
		t.Fatalf("migrations failed: %v", err)
	}

	// Verify tables exist
	tables := []string{
		"admin_roles", "admins", "client_groups", "clients", "contacts",
		"product_groups", "products", "orders", "client_services",
		"invoices", "invoice_items", "transactions", "domains",
		"ticket_departments", "tickets", "ticket_replies", "ticket_notes",
		"affiliates", "promotions", "currencies", "activity_log",
		"kb_categories", "kb_articles", "announcements",
	}

	for _, table := range tables {
		var count int
		err := db.QueryRow("SELECT COUNT(*) FROM " + table).Scan(&count)
		if err != nil {
			t.Errorf("table %s should exist: %v", table, err)
		}
	}
}

func TestSeedData(t *testing.T) {
	dbPath := "/tmp/test_seed_whmcs.db"
	os.Remove(dbPath)
	defer os.Remove(dbPath)

	db, err := New("sqlite3", dbPath)
	if err != nil {
		t.Fatalf("failed to create database: %v", err)
	}
	defer db.Close()

	if err := RunMigrations(db); err != nil {
		t.Fatalf("migrations failed: %v", err)
	}

	// Check seed data
	var roleCount int
	db.QueryRow("SELECT COUNT(*) FROM admin_roles").Scan(&roleCount)
	if roleCount < 2 {
		t.Errorf("expected at least 2 admin roles, got %d", roleCount)
	}

	var currCount int
	db.QueryRow("SELECT COUNT(*) FROM currencies").Scan(&currCount)
	if currCount < 3 {
		t.Errorf("expected at least 3 currencies, got %d", currCount)
	}

	var deptCount int
	db.QueryRow("SELECT COUNT(*) FROM ticket_departments").Scan(&deptCount)
	if deptCount < 4 {
		t.Errorf("expected at least 4 ticket departments, got %d", deptCount)
	}

	// Check default currency is USD
	var code string
	db.QueryRow("SELECT code FROM currencies WHERE is_default = 1").Scan(&code)
	if code != "USD" {
		t.Errorf("expected default currency USD, got %q", code)
	}
}

func TestIdempotentMigrations(t *testing.T) {
	dbPath := "/tmp/test_idempotent_whmcs.db"
	os.Remove(dbPath)
	defer os.Remove(dbPath)

	db, err := New("sqlite3", dbPath)
	if err != nil {
		t.Fatalf("failed to create database: %v", err)
	}
	defer db.Close()

	// Run migrations twice — should not error
	if err := RunMigrations(db); err != nil {
		t.Fatalf("first migration failed: %v", err)
	}
	if err := RunMigrations(db); err != nil {
		t.Fatalf("second migration should be idempotent: %v", err)
	}
}

func setupTestDB(t *testing.T) *sql.DB {
	t.Helper()
	db, err := sql.Open("sqlite3", ":memory:")
	if err != nil {
		t.Fatalf("failed to open in-memory db: %v", err)
	}
	if err := RunMigrations(db); err != nil {
		t.Fatalf("failed to run migrations: %v", err)
	}
	return db
}
