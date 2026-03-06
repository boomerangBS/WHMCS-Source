package database

import (
	"database/sql"
	"fmt"
	"log"
)

func New(driver, dsn string) (*sql.DB, error) {
	db, err := sql.Open(driver, dsn)
	if err != nil {
		return nil, fmt.Errorf("failed to open database: %w", err)
	}

	db.SetMaxOpenConns(25)
	db.SetMaxIdleConns(5)

	if err := db.Ping(); err != nil {
		return nil, fmt.Errorf("failed to ping database: %w", err)
	}

	log.Println("[DB] Connected successfully")
	return db, nil
}

func RunMigrations(db *sql.DB) error {
	log.Println("[DB] Running migrations...")

	migrations := []string{
		migrationAdminRoles,
		migrationAdmins,
		migrationClientGroups,
		migrationClients,
		migrationContacts,
		migrationProductGroups,
		migrationProducts,
		migrationOrders,
		migrationClientServices,
		migrationInvoices,
		migrationInvoiceItems,
		migrationTransactions,
		migrationDomains,
		migrationTicketDepartments,
		migrationTickets,
		migrationTicketReplies,
		migrationTicketNotes,
		migrationAffiliates,
		migrationPromotions,
		migrationCurrencies,
		migrationActivityLog,
		migrationKBCategories,
		migrationKBArticles,
		migrationAnnouncements,
		seedDefaultData,
	}

	for i, m := range migrations {
		if _, err := db.Exec(m); err != nil {
			return fmt.Errorf("migration %d failed: %w", i+1, err)
		}
	}

	log.Println("[DB] Migrations completed successfully")
	return nil
}

// ---- Table Migrations ----

const migrationAdminRoles = `CREATE TABLE IF NOT EXISTS admin_roles (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL UNIQUE,
	permissions TEXT NOT NULL DEFAULT '[]',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);`

const migrationAdmins = `CREATE TABLE IF NOT EXISTS admins (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	username TEXT NOT NULL UNIQUE,
	email TEXT NOT NULL UNIQUE,
	password_hash TEXT NOT NULL,
	first_name TEXT NOT NULL DEFAULT '',
	last_name TEXT NOT NULL DEFAULT '',
	role_id INTEGER NOT NULL DEFAULT 1,
	disabled INTEGER NOT NULL DEFAULT 0,
	login_attempts INTEGER NOT NULL DEFAULT 0,
	locked_until DATETIME,
	last_login DATETIME,
	two_factor_key TEXT NOT NULL DEFAULT '',
	two_factor_on INTEGER NOT NULL DEFAULT 0,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (role_id) REFERENCES admin_roles(id)
);`

const migrationClientGroups = `CREATE TABLE IF NOT EXISTS client_groups (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	group_name TEXT NOT NULL,
	discount_percent REAL NOT NULL DEFAULT 0,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);`

const migrationClients = `CREATE TABLE IF NOT EXISTS clients (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	first_name TEXT NOT NULL,
	last_name TEXT NOT NULL,
	company_name TEXT NOT NULL DEFAULT '',
	email TEXT NOT NULL UNIQUE,
	password_hash TEXT NOT NULL,
	address1 TEXT NOT NULL DEFAULT '',
	address2 TEXT NOT NULL DEFAULT '',
	city TEXT NOT NULL DEFAULT '',
	state TEXT NOT NULL DEFAULT '',
	postcode TEXT NOT NULL DEFAULT '',
	country TEXT NOT NULL DEFAULT '',
	phone_number TEXT NOT NULL DEFAULT '',
	currency INTEGER NOT NULL DEFAULT 1,
	group_id INTEGER NOT NULL DEFAULT 0,
	status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active','inactive','closed')),
	credit REAL NOT NULL DEFAULT 0,
	notes TEXT NOT NULL DEFAULT '',
	two_factor_key TEXT NOT NULL DEFAULT '',
	two_factor_on INTEGER NOT NULL DEFAULT 0,
	last_login DATETIME,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);`

const migrationContacts = `CREATE TABLE IF NOT EXISTS contacts (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	client_id INTEGER NOT NULL,
	first_name TEXT NOT NULL,
	last_name TEXT NOT NULL,
	company_name TEXT NOT NULL DEFAULT '',
	email TEXT NOT NULL DEFAULT '',
	phone TEXT NOT NULL DEFAULT '',
	address1 TEXT NOT NULL DEFAULT '',
	address2 TEXT NOT NULL DEFAULT '',
	city TEXT NOT NULL DEFAULT '',
	state TEXT NOT NULL DEFAULT '',
	postcode TEXT NOT NULL DEFAULT '',
	country TEXT NOT NULL DEFAULT '',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);`

const migrationProductGroups = `CREATE TABLE IF NOT EXISTS product_groups (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL,
	headline TEXT NOT NULL DEFAULT '',
	order_num INTEGER NOT NULL DEFAULT 0,
	hidden INTEGER NOT NULL DEFAULT 0,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);`

const migrationProducts = `CREATE TABLE IF NOT EXISTS products (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	group_id INTEGER NOT NULL DEFAULT 0,
	type TEXT NOT NULL DEFAULT 'other' CHECK(type IN ('hosting','server','other')),
	name TEXT NOT NULL,
	description TEXT NOT NULL DEFAULT '',
	price_monthly REAL NOT NULL DEFAULT 0,
	price_quarterly REAL NOT NULL DEFAULT 0,
	price_semiannual REAL NOT NULL DEFAULT 0,
	price_annual REAL NOT NULL DEFAULT 0,
	price_biennial REAL NOT NULL DEFAULT 0,
	setup_fee REAL NOT NULL DEFAULT 0,
	hidden INTEGER NOT NULL DEFAULT 0,
	retired INTEGER NOT NULL DEFAULT 0,
	stock_control INTEGER NOT NULL DEFAULT 0,
	qty INTEGER NOT NULL DEFAULT 0,
	sort_order INTEGER NOT NULL DEFAULT 0,
	auto_setup TEXT NOT NULL DEFAULT 'off' CHECK(auto_setup IN ('order','payment','on','off')),
	module_name TEXT NOT NULL DEFAULT '',
	server_group_id INTEGER NOT NULL DEFAULT 0,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (group_id) REFERENCES product_groups(id)
);`

const migrationOrders = `CREATE TABLE IF NOT EXISTS orders (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	client_id INTEGER NOT NULL,
	order_number TEXT NOT NULL UNIQUE,
	invoice_id INTEGER NOT NULL DEFAULT 0,
	amount REAL NOT NULL DEFAULT 0,
	status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','active','fraud','cancelled')),
	payment_method TEXT NOT NULL DEFAULT '',
	ip_address TEXT NOT NULL DEFAULT '',
	fraud_output TEXT NOT NULL DEFAULT '',
	notes TEXT NOT NULL DEFAULT '',
	promo_code TEXT NOT NULL DEFAULT '',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (client_id) REFERENCES clients(id)
);`

const migrationClientServices = `CREATE TABLE IF NOT EXISTS client_services (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	client_id INTEGER NOT NULL,
	order_id INTEGER NOT NULL DEFAULT 0,
	product_id INTEGER NOT NULL,
	server_id INTEGER NOT NULL DEFAULT 0,
	domain TEXT NOT NULL DEFAULT '',
	username TEXT NOT NULL DEFAULT '',
	password TEXT NOT NULL DEFAULT '',
	amount REAL NOT NULL DEFAULT 0,
	billing_cycle TEXT NOT NULL DEFAULT 'monthly' CHECK(billing_cycle IN ('monthly','quarterly','semiannual','annual','biennial','free')),
	next_due_date DATE,
	status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','active','suspended','terminated','cancelled','fraud')),
	suspend_reason TEXT NOT NULL DEFAULT '',
	dedicated_ip TEXT NOT NULL DEFAULT '',
	assigned_ips TEXT NOT NULL DEFAULT '',
	notes TEXT NOT NULL DEFAULT '',
	registration_date DATE DEFAULT CURRENT_DATE,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (client_id) REFERENCES clients(id),
	FOREIGN KEY (product_id) REFERENCES products(id)
);`

const migrationInvoices = `CREATE TABLE IF NOT EXISTS invoices (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	client_id INTEGER NOT NULL,
	invoice_num TEXT NOT NULL,
	date_created DATE NOT NULL DEFAULT CURRENT_DATE,
	date_due DATE NOT NULL,
	date_paid DATETIME,
	subtotal REAL NOT NULL DEFAULT 0,
	tax REAL NOT NULL DEFAULT 0,
	tax_rate REAL NOT NULL DEFAULT 0,
	total REAL NOT NULL DEFAULT 0,
	credit REAL NOT NULL DEFAULT 0,
	status TEXT NOT NULL DEFAULT 'unpaid' CHECK(status IN ('draft','unpaid','paid','cancelled','refunded','overdue')),
	payment_method TEXT NOT NULL DEFAULT '',
	notes TEXT NOT NULL DEFAULT '',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (client_id) REFERENCES clients(id)
);`

const migrationInvoiceItems = `CREATE TABLE IF NOT EXISTS invoice_items (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	invoice_id INTEGER NOT NULL,
	type TEXT NOT NULL DEFAULT 'custom' CHECK(type IN ('service','domain','addon','setup','promo','custom')),
	rel_id INTEGER NOT NULL DEFAULT 0,
	description TEXT NOT NULL DEFAULT '',
	amount REAL NOT NULL DEFAULT 0,
	taxed INTEGER NOT NULL DEFAULT 0,
	FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);`

const migrationTransactions = `CREATE TABLE IF NOT EXISTS transactions (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	client_id INTEGER NOT NULL,
	invoice_id INTEGER NOT NULL DEFAULT 0,
	transaction_id TEXT NOT NULL DEFAULT '',
	gateway TEXT NOT NULL DEFAULT '',
	amount REAL NOT NULL DEFAULT 0,
	fees REAL NOT NULL DEFAULT 0,
	currency TEXT NOT NULL DEFAULT 'USD',
	description TEXT NOT NULL DEFAULT '',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (client_id) REFERENCES clients(id)
);`

const migrationDomains = `CREATE TABLE IF NOT EXISTS domains (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	client_id INTEGER NOT NULL,
	order_id INTEGER NOT NULL DEFAULT 0,
	type TEXT NOT NULL DEFAULT 'register' CHECK(type IN ('register','transfer')),
	domain_name TEXT NOT NULL,
	registrar_id INTEGER NOT NULL DEFAULT 0,
	registration_date DATE NOT NULL DEFAULT CURRENT_DATE,
	expiry_date DATE NOT NULL,
	next_due_date DATE,
	status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','active','expired','cancelled','fraud','transferring')),
	ns1 TEXT NOT NULL DEFAULT '',
	ns2 TEXT NOT NULL DEFAULT '',
	ns3 TEXT NOT NULL DEFAULT '',
	ns4 TEXT NOT NULL DEFAULT '',
	auto_renew INTEGER NOT NULL DEFAULT 1,
	id_protection INTEGER NOT NULL DEFAULT 0,
	amount REAL NOT NULL DEFAULT 0,
	recurring_amount REAL NOT NULL DEFAULT 0,
	notes TEXT NOT NULL DEFAULT '',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (client_id) REFERENCES clients(id)
);`

const migrationTicketDepartments = `CREATE TABLE IF NOT EXISTS ticket_departments (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL,
	description TEXT NOT NULL DEFAULT '',
	email TEXT NOT NULL DEFAULT '',
	hidden INTEGER NOT NULL DEFAULT 0,
	sort_order INTEGER NOT NULL DEFAULT 0
);`

const migrationTickets = `CREATE TABLE IF NOT EXISTS tickets (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	tid TEXT NOT NULL UNIQUE,
	department_id INTEGER NOT NULL,
	client_id INTEGER NOT NULL,
	contact_id INTEGER NOT NULL DEFAULT 0,
	admin_id INTEGER NOT NULL DEFAULT 0,
	subject TEXT NOT NULL,
	message TEXT NOT NULL,
	status TEXT NOT NULL DEFAULT 'open' CHECK(status IN ('open','answered','customer-reply','closed','on-hold')),
	priority TEXT NOT NULL DEFAULT 'medium' CHECK(priority IN ('low','medium','high','urgent')),
	service_id INTEGER NOT NULL DEFAULT 0,
	domain_id INTEGER NOT NULL DEFAULT 0,
	last_reply DATETIME DEFAULT CURRENT_TIMESTAMP,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (department_id) REFERENCES ticket_departments(id),
	FOREIGN KEY (client_id) REFERENCES clients(id)
);`

const migrationTicketReplies = `CREATE TABLE IF NOT EXISTS ticket_replies (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	ticket_id INTEGER NOT NULL,
	client_id INTEGER NOT NULL DEFAULT 0,
	admin_id INTEGER NOT NULL DEFAULT 0,
	contact_id INTEGER NOT NULL DEFAULT 0,
	message TEXT NOT NULL,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
);`

const migrationTicketNotes = `CREATE TABLE IF NOT EXISTS ticket_notes (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	ticket_id INTEGER NOT NULL,
	admin_id INTEGER NOT NULL,
	message TEXT NOT NULL,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
);`

const migrationAffiliates = `CREATE TABLE IF NOT EXISTS affiliates (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	client_id INTEGER NOT NULL UNIQUE,
	pay_type TEXT NOT NULL DEFAULT 'percentage' CHECK(pay_type IN ('percentage','fixed')),
	pay_amount REAL NOT NULL DEFAULT 0,
	balance REAL NOT NULL DEFAULT 0,
	withdrawn REAL NOT NULL DEFAULT 0,
	visitors INTEGER NOT NULL DEFAULT 0,
	conversions INTEGER NOT NULL DEFAULT 0,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (client_id) REFERENCES clients(id)
);`

const migrationPromotions = `CREATE TABLE IF NOT EXISTS promotions (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	code TEXT NOT NULL UNIQUE,
	type TEXT NOT NULL DEFAULT 'percentage' CHECK(type IN ('percentage','fixed')),
	value REAL NOT NULL DEFAULT 0,
	recurring INTEGER NOT NULL DEFAULT 0,
	max_uses INTEGER NOT NULL DEFAULT 0,
	uses INTEGER NOT NULL DEFAULT 0,
	expiration_date DATE,
	applies_to TEXT NOT NULL DEFAULT '[]',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);`

const migrationCurrencies = `CREATE TABLE IF NOT EXISTS currencies (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	code TEXT NOT NULL UNIQUE,
	prefix TEXT NOT NULL DEFAULT '',
	suffix TEXT NOT NULL DEFAULT '',
	rate REAL NOT NULL DEFAULT 1.0,
	is_default INTEGER NOT NULL DEFAULT 0
);`

const migrationActivityLog = `CREATE TABLE IF NOT EXISTS activity_log (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	admin_id INTEGER NOT NULL DEFAULT 0,
	client_id INTEGER NOT NULL DEFAULT 0,
	description TEXT NOT NULL,
	ip_address TEXT NOT NULL DEFAULT '',
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);`

const migrationKBCategories = `CREATE TABLE IF NOT EXISTS kb_categories (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL,
	parent_id INTEGER NOT NULL DEFAULT 0,
	sort_order INTEGER NOT NULL DEFAULT 0,
	hidden INTEGER NOT NULL DEFAULT 0
);`

const migrationKBArticles = `CREATE TABLE IF NOT EXISTS kb_articles (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	category_id INTEGER NOT NULL,
	title TEXT NOT NULL,
	content TEXT NOT NULL DEFAULT '',
	views INTEGER NOT NULL DEFAULT 0,
	useful INTEGER NOT NULL DEFAULT 0,
	sort_order INTEGER NOT NULL DEFAULT 0,
	hidden INTEGER NOT NULL DEFAULT 0,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (category_id) REFERENCES kb_categories(id)
);`

const migrationAnnouncements = `CREATE TABLE IF NOT EXISTS announcements (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	title TEXT NOT NULL,
	body TEXT NOT NULL DEFAULT '',
	published INTEGER NOT NULL DEFAULT 0,
	created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);`

const seedDefaultData = `
INSERT OR IGNORE INTO admin_roles (id, name, permissions) VALUES (1, 'Full Administrator', '["all"]');
INSERT OR IGNORE INTO admin_roles (id, name, permissions) VALUES (2, 'Support Agent', '["tickets.view","tickets.reply","clients.view","kb.manage"]');
INSERT OR IGNORE INTO admin_roles (id, name, permissions) VALUES (3, 'Billing Agent', '["invoices.view","invoices.manage","clients.view","transactions.view"]');
INSERT OR IGNORE INTO currencies (id, code, prefix, suffix, rate, is_default) VALUES (1, 'USD', '$', '', 1.0, 1);
INSERT OR IGNORE INTO currencies (id, code, prefix, suffix, rate, is_default) VALUES (2, 'EUR', '€', '', 0.85, 0);
INSERT OR IGNORE INTO currencies (id, code, prefix, suffix, rate, is_default) VALUES (3, 'GBP', '£', '', 0.73, 0);
INSERT OR IGNORE INTO ticket_departments (id, name, description, email) VALUES (1, 'General', 'General inquiries', 'support@example.com');
INSERT OR IGNORE INTO ticket_departments (id, name, description, email) VALUES (2, 'Sales', 'Sales inquiries', 'sales@example.com');
INSERT OR IGNORE INTO ticket_departments (id, name, description, email) VALUES (3, 'Technical Support', 'Technical issues', 'tech@example.com');
INSERT OR IGNORE INTO ticket_departments (id, name, description, email) VALUES (4, 'Billing', 'Billing questions', 'billing@example.com');
`
