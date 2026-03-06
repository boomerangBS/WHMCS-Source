package repository

import (
	"database/sql"
	"fmt"
	"whmcs-rebuild/internal/models"
)

type AdminRepo struct{ DB *sql.DB }

func NewAdminRepo(db *sql.DB) *AdminRepo { return &AdminRepo{DB: db} }

func (r *AdminRepo) GetByID(id int64) (*models.Admin, error) {
	a := &models.Admin{}
	err := r.DB.QueryRow(`SELECT id, username, email, password_hash, first_name, last_name, role_id, disabled, login_attempts, locked_until, last_login, two_factor_key, two_factor_on, created_at, updated_at FROM admins WHERE id = ?`, id).Scan(
		&a.ID, &a.Username, &a.Email, &a.PasswordHash, &a.FirstName, &a.LastName, &a.RoleID, &a.Disabled, &a.LoginAttempts, &a.LockedUntil, &a.LastLogin, &a.TwoFactorKey, &a.TwoFactorOn, &a.CreatedAt, &a.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return a, nil
}

func (r *AdminRepo) GetByUsername(username string) (*models.Admin, error) {
	a := &models.Admin{}
	err := r.DB.QueryRow(`SELECT id, username, email, password_hash, first_name, last_name, role_id, disabled, login_attempts, locked_until, last_login, two_factor_key, two_factor_on, created_at, updated_at FROM admins WHERE username = ?`, username).Scan(
		&a.ID, &a.Username, &a.Email, &a.PasswordHash, &a.FirstName, &a.LastName, &a.RoleID, &a.Disabled, &a.LoginAttempts, &a.LockedUntil, &a.LastLogin, &a.TwoFactorKey, &a.TwoFactorOn, &a.CreatedAt, &a.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return a, nil
}

func (r *AdminRepo) GetByEmail(email string) (*models.Admin, error) {
	a := &models.Admin{}
	err := r.DB.QueryRow(`SELECT id, username, email, password_hash, first_name, last_name, role_id, disabled, login_attempts, locked_until, last_login, two_factor_key, two_factor_on, created_at, updated_at FROM admins WHERE email = ?`, email).Scan(
		&a.ID, &a.Username, &a.Email, &a.PasswordHash, &a.FirstName, &a.LastName, &a.RoleID, &a.Disabled, &a.LoginAttempts, &a.LockedUntil, &a.LastLogin, &a.TwoFactorKey, &a.TwoFactorOn, &a.CreatedAt, &a.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return a, nil
}

func (r *AdminRepo) Create(a *models.Admin) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO admins (username, email, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?, ?)`,
		a.Username, a.Email, a.PasswordHash, a.FirstName, a.LastName, a.RoleID)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *AdminRepo) Update(a *models.Admin) error {
	_, err := r.DB.Exec(`UPDATE admins SET email = ?, first_name = ?, last_name = ?, role_id = ?, disabled = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
		a.Email, a.FirstName, a.LastName, a.RoleID, a.Disabled, a.ID)
	return err
}

func (r *AdminRepo) UpdateLoginAttempts(id int64, attempts int, lockedUntil *string) error {
	_, err := r.DB.Exec(`UPDATE admins SET login_attempts = ?, locked_until = ? WHERE id = ?`, attempts, lockedUntil, id)
	return err
}

func (r *AdminRepo) UpdateLastLogin(id int64) error {
	_, err := r.DB.Exec(`UPDATE admins SET last_login = CURRENT_TIMESTAMP, login_attempts = 0, locked_until = NULL WHERE id = ?`, id)
	return err
}

func (r *AdminRepo) List(page, perPage int) ([]models.Admin, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM admins`).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, username, email, first_name, last_name, role_id, disabled, last_login, created_at, updated_at FROM admins ORDER BY id ASC LIMIT ? OFFSET ?`, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var admins []models.Admin
	for rows.Next() {
		var a models.Admin
		if err := rows.Scan(&a.ID, &a.Username, &a.Email, &a.FirstName, &a.LastName, &a.RoleID, &a.Disabled, &a.LastLogin, &a.CreatedAt, &a.UpdatedAt); err != nil {
			return nil, 0, err
		}
		admins = append(admins, a)
	}
	return admins, total, nil
}

func (r *AdminRepo) Delete(id int64) error {
	_, err := r.DB.Exec(`DELETE FROM admins WHERE id = ?`, id)
	return err
}

// ---------- Client Repository ----------

type ClientRepo struct{ DB *sql.DB }

func NewClientRepo(db *sql.DB) *ClientRepo { return &ClientRepo{DB: db} }

func (r *ClientRepo) GetByID(id int64) (*models.Client, error) {
	c := &models.Client{}
	err := r.DB.QueryRow(`SELECT id, first_name, last_name, company_name, email, password_hash, address1, address2, city, state, postcode, country, phone_number, currency, group_id, status, credit, notes, two_factor_on, last_login, created_at, updated_at FROM clients WHERE id = ?`, id).Scan(
		&c.ID, &c.FirstName, &c.LastName, &c.CompanyName, &c.Email, &c.PasswordHash, &c.Address1, &c.Address2, &c.City, &c.State, &c.PostCode, &c.Country, &c.PhoneNumber, &c.Currency, &c.GroupID, &c.Status, &c.Credit, &c.Notes, &c.TwoFactorOn, &c.LastLogin, &c.CreatedAt, &c.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return c, nil
}

func (r *ClientRepo) GetByEmail(email string) (*models.Client, error) {
	c := &models.Client{}
	err := r.DB.QueryRow(`SELECT id, first_name, last_name, company_name, email, password_hash, address1, address2, city, state, postcode, country, phone_number, currency, group_id, status, credit, notes, two_factor_on, last_login, created_at, updated_at FROM clients WHERE email = ?`, email).Scan(
		&c.ID, &c.FirstName, &c.LastName, &c.CompanyName, &c.Email, &c.PasswordHash, &c.Address1, &c.Address2, &c.City, &c.State, &c.PostCode, &c.Country, &c.PhoneNumber, &c.Currency, &c.GroupID, &c.Status, &c.Credit, &c.Notes, &c.TwoFactorOn, &c.LastLogin, &c.CreatedAt, &c.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return c, nil
}

func (r *ClientRepo) Create(c *models.Client) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO clients (first_name, last_name, company_name, email, password_hash, address1, address2, city, state, postcode, country, phone_number, currency, group_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		c.FirstName, c.LastName, c.CompanyName, c.Email, c.PasswordHash, c.Address1, c.Address2, c.City, c.State, c.PostCode, c.Country, c.PhoneNumber, c.Currency, c.GroupID, c.Status)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *ClientRepo) Update(c *models.Client) error {
	_, err := r.DB.Exec(`UPDATE clients SET first_name = ?, last_name = ?, company_name = ?, email = ?, address1 = ?, address2 = ?, city = ?, state = ?, postcode = ?, country = ?, phone_number = ?, currency = ?, group_id = ?, status = ?, credit = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
		c.FirstName, c.LastName, c.CompanyName, c.Email, c.Address1, c.Address2, c.City, c.State, c.PostCode, c.Country, c.PhoneNumber, c.Currency, c.GroupID, c.Status, c.Credit, c.Notes, c.ID)
	return err
}

func (r *ClientRepo) Delete(id int64) error {
	_, err := r.DB.Exec(`DELETE FROM clients WHERE id = ?`, id)
	return err
}

func (r *ClientRepo) List(page, perPage int, search, status string) ([]models.Client, int64, error) {
	var total int64
	query := `FROM clients WHERE 1=1`
	args := []any{}
	if search != "" {
		query += ` AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR company_name LIKE ?)`
		s := "%" + search + "%"
		args = append(args, s, s, s, s)
	}
	if status != "" {
		query += ` AND status = ?`
		args = append(args, status)
	}

	r.DB.QueryRow(`SELECT COUNT(*) `+query, args...).Scan(&total)

	offset := (page - 1) * perPage
	args = append(args, perPage, offset)
	rows, err := r.DB.Query(`SELECT id, first_name, last_name, company_name, email, address1, city, state, country, phone_number, status, credit, created_at `+query+` ORDER BY id DESC LIMIT ? OFFSET ?`, args...)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var clients []models.Client
	for rows.Next() {
		var c models.Client
		if err := rows.Scan(&c.ID, &c.FirstName, &c.LastName, &c.CompanyName, &c.Email, &c.Address1, &c.City, &c.State, &c.Country, &c.PhoneNumber, &c.Status, &c.Credit, &c.CreatedAt); err != nil {
			return nil, 0, err
		}
		clients = append(clients, c)
	}
	return clients, total, nil
}

func (r *ClientRepo) UpdateLastLogin(id int64) error {
	_, err := r.DB.Exec(`UPDATE clients SET last_login = CURRENT_TIMESTAMP WHERE id = ?`, id)
	return err
}

func (r *ClientRepo) UpdateCredit(id int64, amount float64) error {
	_, err := r.DB.Exec(`UPDATE clients SET credit = credit + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, amount, id)
	return err
}

// ---------- Product Repository ----------

type ProductRepo struct{ DB *sql.DB }

func NewProductRepo(db *sql.DB) *ProductRepo { return &ProductRepo{DB: db} }

func (r *ProductRepo) GetByID(id int64) (*models.Product, error) {
	p := &models.Product{}
	err := r.DB.QueryRow(`SELECT id, group_id, type, name, description, price_monthly, price_quarterly, price_semiannual, price_annual, price_biennial, setup_fee, hidden, retired, stock_control, qty, sort_order, auto_setup, module_name, server_group_id, created_at, updated_at FROM products WHERE id = ?`, id).Scan(
		&p.ID, &p.GroupID, &p.Type, &p.Name, &p.Description, &p.PriceMonthly, &p.PriceQuarterly, &p.PriceSemiAnnual, &p.PriceAnnual, &p.PriceBiennial, &p.SetupFee, &p.Hidden, &p.Retired, &p.StockControl, &p.Qty, &p.SortOrder, &p.AutoSetup, &p.ModuleName, &p.ServerGroupID, &p.CreatedAt, &p.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return p, nil
}

func (r *ProductRepo) Create(p *models.Product) (int64, error) {
	if p.Type == "" {
		p.Type = "other"
	}
	if p.AutoSetup == "" {
		p.AutoSetup = "off"
	}
	res, err := r.DB.Exec(`INSERT INTO products (group_id, type, name, description, price_monthly, price_quarterly, price_semiannual, price_annual, price_biennial, setup_fee, hidden, stock_control, qty, sort_order, auto_setup, module_name, server_group_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		p.GroupID, p.Type, p.Name, p.Description, p.PriceMonthly, p.PriceQuarterly, p.PriceSemiAnnual, p.PriceAnnual, p.PriceBiennial, p.SetupFee, p.Hidden, p.StockControl, p.Qty, p.SortOrder, p.AutoSetup, p.ModuleName, p.ServerGroupID)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *ProductRepo) Update(p *models.Product) error {
	_, err := r.DB.Exec(`UPDATE products SET group_id = ?, type = ?, name = ?, description = ?, price_monthly = ?, price_quarterly = ?, price_semiannual = ?, price_annual = ?, price_biennial = ?, setup_fee = ?, hidden = ?, retired = ?, stock_control = ?, qty = ?, sort_order = ?, auto_setup = ?, module_name = ?, server_group_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
		p.GroupID, p.Type, p.Name, p.Description, p.PriceMonthly, p.PriceQuarterly, p.PriceSemiAnnual, p.PriceAnnual, p.PriceBiennial, p.SetupFee, p.Hidden, p.Retired, p.StockControl, p.Qty, p.SortOrder, p.AutoSetup, p.ModuleName, p.ServerGroupID, p.ID)
	return err
}

func (r *ProductRepo) Delete(id int64) error {
	_, err := r.DB.Exec(`DELETE FROM products WHERE id = ?`, id)
	return err
}

func (r *ProductRepo) List(page, perPage int, groupID int64) ([]models.Product, int64, error) {
	var total int64
	query := `FROM products WHERE 1=1`
	args := []any{}
	if groupID > 0 {
		query += ` AND group_id = ?`
		args = append(args, groupID)
	}

	r.DB.QueryRow(`SELECT COUNT(*) `+query, args...).Scan(&total)

	offset := (page - 1) * perPage
	args = append(args, perPage, offset)
	rows, err := r.DB.Query(`SELECT id, group_id, type, name, description, price_monthly, price_annual, setup_fee, hidden, retired, created_at `+query+` ORDER BY sort_order ASC, id ASC LIMIT ? OFFSET ?`, args...)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var products []models.Product
	for rows.Next() {
		var p models.Product
		if err := rows.Scan(&p.ID, &p.GroupID, &p.Type, &p.Name, &p.Description, &p.PriceMonthly, &p.PriceAnnual, &p.SetupFee, &p.Hidden, &p.Retired, &p.CreatedAt); err != nil {
			return nil, 0, err
		}
		products = append(products, p)
	}
	return products, total, nil
}

// ---------- Product Group Repository ----------

type ProductGroupRepo struct{ DB *sql.DB }

func NewProductGroupRepo(db *sql.DB) *ProductGroupRepo { return &ProductGroupRepo{DB: db} }

func (r *ProductGroupRepo) Create(pg *models.ProductGroup) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO product_groups (name, headline, order_num, hidden) VALUES (?, ?, ?, ?)`,
		pg.Name, pg.Headline, pg.OrderNum, pg.Hidden)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *ProductGroupRepo) List() ([]models.ProductGroup, error) {
	rows, err := r.DB.Query(`SELECT id, name, headline, order_num, hidden, created_at FROM product_groups ORDER BY order_num ASC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var groups []models.ProductGroup
	for rows.Next() {
		var g models.ProductGroup
		if err := rows.Scan(&g.ID, &g.Name, &g.Headline, &g.OrderNum, &g.Hidden, &g.CreatedAt); err != nil {
			return nil, err
		}
		groups = append(groups, g)
	}
	return groups, nil
}

// ---------- Invoice Repository ----------

type InvoiceRepo struct{ DB *sql.DB }

func NewInvoiceRepo(db *sql.DB) *InvoiceRepo { return &InvoiceRepo{DB: db} }

func (r *InvoiceRepo) GetByID(id int64) (*models.Invoice, error) {
	inv := &models.Invoice{}
	err := r.DB.QueryRow(`SELECT id, client_id, invoice_num, date_created, date_due, date_paid, subtotal, tax, tax_rate, total, credit, status, payment_method, notes, created_at, updated_at FROM invoices WHERE id = ?`, id).Scan(
		&inv.ID, &inv.ClientID, &inv.InvoiceNum, &inv.DateCreated, &inv.DateDue, &inv.DatePaid, &inv.SubTotal, &inv.Tax, &inv.TaxRate, &inv.Total, &inv.Credit, &inv.Status, &inv.PaymentMethod, &inv.Notes, &inv.CreatedAt, &inv.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return inv, nil
}

func (r *InvoiceRepo) Create(inv *models.Invoice) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO invoices (client_id, invoice_num, date_due, subtotal, tax, tax_rate, total, status, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		inv.ClientID, inv.InvoiceNum, inv.DateDue, inv.SubTotal, inv.Tax, inv.TaxRate, inv.Total, inv.Status, inv.PaymentMethod, inv.Notes)
	if err != nil {
		return 0, err
	}
	id, _ := res.LastInsertId()
	// Update invoice number with actual ID
	invNum := fmt.Sprintf("INV-%d-%06d", inv.DateCreated.Year(), id)
	r.DB.Exec(`UPDATE invoices SET invoice_num = ? WHERE id = ?`, invNum, id)
	return id, nil
}

func (r *InvoiceRepo) Update(inv *models.Invoice) error {
	_, err := r.DB.Exec(`UPDATE invoices SET date_due = ?, subtotal = ?, tax = ?, tax_rate = ?, total = ?, credit = ?, status = ?, payment_method = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
		inv.DateDue, inv.SubTotal, inv.Tax, inv.TaxRate, inv.Total, inv.Credit, inv.Status, inv.PaymentMethod, inv.Notes, inv.ID)
	return err
}

func (r *InvoiceRepo) MarkPaid(id int64) error {
	_, err := r.DB.Exec(`UPDATE invoices SET status = 'paid', date_paid = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, id)
	return err
}

func (r *InvoiceRepo) ListByClient(clientID int64, page, perPage int, status string) ([]models.Invoice, int64, error) {
	var total int64
	query := `FROM invoices WHERE client_id = ?`
	args := []any{clientID}
	if status != "" {
		query += ` AND status = ?`
		args = append(args, status)
	}

	r.DB.QueryRow(`SELECT COUNT(*) `+query, args...).Scan(&total)

	offset := (page - 1) * perPage
	args = append(args, perPage, offset)
	rows, err := r.DB.Query(`SELECT id, client_id, invoice_num, date_created, date_due, date_paid, total, status, payment_method `+query+` ORDER BY id DESC LIMIT ? OFFSET ?`, args...)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var invoices []models.Invoice
	for rows.Next() {
		var inv models.Invoice
		if err := rows.Scan(&inv.ID, &inv.ClientID, &inv.InvoiceNum, &inv.DateCreated, &inv.DateDue, &inv.DatePaid, &inv.Total, &inv.Status, &inv.PaymentMethod); err != nil {
			return nil, 0, err
		}
		invoices = append(invoices, inv)
	}
	return invoices, total, nil
}

func (r *InvoiceRepo) ListAll(page, perPage int, status string) ([]models.Invoice, int64, error) {
	var total int64
	query := `FROM invoices WHERE 1=1`
	args := []any{}
	if status != "" {
		query += ` AND status = ?`
		args = append(args, status)
	}

	r.DB.QueryRow(`SELECT COUNT(*) `+query, args...).Scan(&total)

	offset := (page - 1) * perPage
	args = append(args, perPage, offset)
	rows, err := r.DB.Query(`SELECT id, client_id, invoice_num, date_created, date_due, date_paid, total, status, payment_method `+query+` ORDER BY id DESC LIMIT ? OFFSET ?`, args...)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var invoices []models.Invoice
	for rows.Next() {
		var inv models.Invoice
		if err := rows.Scan(&inv.ID, &inv.ClientID, &inv.InvoiceNum, &inv.DateCreated, &inv.DateDue, &inv.DatePaid, &inv.Total, &inv.Status, &inv.PaymentMethod); err != nil {
			return nil, 0, err
		}
		invoices = append(invoices, inv)
	}
	return invoices, total, nil
}

// ---------- Invoice Item Repository ----------

type InvoiceItemRepo struct{ DB *sql.DB }

func NewInvoiceItemRepo(db *sql.DB) *InvoiceItemRepo { return &InvoiceItemRepo{DB: db} }

func (r *InvoiceItemRepo) Create(item *models.InvoiceItem) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO invoice_items (invoice_id, type, rel_id, description, amount, taxed) VALUES (?, ?, ?, ?, ?, ?)`,
		item.InvoiceID, item.Type, item.RelID, item.Description, item.Amount, item.Taxed)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *InvoiceItemRepo) ListByInvoice(invoiceID int64) ([]models.InvoiceItem, error) {
	rows, err := r.DB.Query(`SELECT id, invoice_id, type, rel_id, description, amount, taxed FROM invoice_items WHERE invoice_id = ?`, invoiceID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var items []models.InvoiceItem
	for rows.Next() {
		var i models.InvoiceItem
		if err := rows.Scan(&i.ID, &i.InvoiceID, &i.Type, &i.RelID, &i.Description, &i.Amount, &i.Taxed); err != nil {
			return nil, err
		}
		items = append(items, i)
	}
	return items, nil
}

func (r *InvoiceItemRepo) DeleteByInvoice(invoiceID int64) error {
	_, err := r.DB.Exec(`DELETE FROM invoice_items WHERE invoice_id = ?`, invoiceID)
	return err
}

// ---------- Order Repository ----------

type OrderRepo struct{ DB *sql.DB }

func NewOrderRepo(db *sql.DB) *OrderRepo { return &OrderRepo{DB: db} }

func (r *OrderRepo) GetByID(id int64) (*models.Order, error) {
	o := &models.Order{}
	err := r.DB.QueryRow(`SELECT id, client_id, order_number, invoice_id, amount, status, payment_method, ip_address, fraud_output, notes, promo_code, created_at, updated_at FROM orders WHERE id = ?`, id).Scan(
		&o.ID, &o.ClientID, &o.OrderNumber, &o.InvoiceID, &o.Amount, &o.Status, &o.PaymentMethod, &o.IPAddress, &o.FraudOutput, &o.Notes, &o.PromoCode, &o.CreatedAt, &o.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return o, nil
}

func (r *OrderRepo) Create(o *models.Order) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO orders (client_id, order_number, invoice_id, amount, status, payment_method, ip_address, notes, promo_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		o.ClientID, o.OrderNumber, o.InvoiceID, o.Amount, o.Status, o.PaymentMethod, o.IPAddress, o.Notes, o.PromoCode)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *OrderRepo) UpdateStatus(id int64, status string) error {
	_, err := r.DB.Exec(`UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, status, id)
	return err
}

func (r *OrderRepo) ListAll(page, perPage int, status string) ([]models.Order, int64, error) {
	var total int64
	query := `FROM orders WHERE 1=1`
	args := []any{}
	if status != "" {
		query += ` AND status = ?`
		args = append(args, status)
	}

	r.DB.QueryRow(`SELECT COUNT(*) `+query, args...).Scan(&total)

	offset := (page - 1) * perPage
	args = append(args, perPage, offset)
	rows, err := r.DB.Query(`SELECT id, client_id, order_number, invoice_id, amount, status, payment_method, ip_address, promo_code, created_at `+query+` ORDER BY id DESC LIMIT ? OFFSET ?`, args...)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var orders []models.Order
	for rows.Next() {
		var o models.Order
		if err := rows.Scan(&o.ID, &o.ClientID, &o.OrderNumber, &o.InvoiceID, &o.Amount, &o.Status, &o.PaymentMethod, &o.IPAddress, &o.PromoCode, &o.CreatedAt); err != nil {
			return nil, 0, err
		}
		orders = append(orders, o)
	}
	return orders, total, nil
}

// ---------- Ticket Repository ----------

type TicketRepo struct{ DB *sql.DB }

func NewTicketRepo(db *sql.DB) *TicketRepo { return &TicketRepo{DB: db} }

func (r *TicketRepo) GetByID(id int64) (*models.Ticket, error) {
	t := &models.Ticket{}
	err := r.DB.QueryRow(`SELECT id, tid, department_id, client_id, contact_id, admin_id, subject, message, status, priority, service_id, domain_id, last_reply, created_at, updated_at FROM tickets WHERE id = ?`, id).Scan(
		&t.ID, &t.TID, &t.DepartmentID, &t.ClientID, &t.ContactID, &t.AdminID, &t.Subject, &t.Message, &t.Status, &t.Priority, &t.ServiceID, &t.DomainID, &t.LastReply, &t.CreatedAt, &t.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return t, nil
}

func (r *TicketRepo) Create(t *models.Ticket) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO tickets (tid, department_id, client_id, contact_id, subject, message, status, priority, service_id, domain_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		t.TID, t.DepartmentID, t.ClientID, t.ContactID, t.Subject, t.Message, t.Status, t.Priority, t.ServiceID, t.DomainID)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *TicketRepo) UpdateStatus(id int64, status string) error {
	_, err := r.DB.Exec(`UPDATE tickets SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, status, id)
	return err
}

func (r *TicketRepo) AssignAdmin(id, adminID int64) error {
	_, err := r.DB.Exec(`UPDATE tickets SET admin_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, adminID, id)
	return err
}

func (r *TicketRepo) ListByClient(clientID int64, page, perPage int) ([]models.Ticket, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM tickets WHERE client_id = ?`, clientID).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, tid, department_id, client_id, admin_id, subject, status, priority, last_reply, created_at FROM tickets WHERE client_id = ? ORDER BY last_reply DESC LIMIT ? OFFSET ?`, clientID, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var tickets []models.Ticket
	for rows.Next() {
		var t models.Ticket
		if err := rows.Scan(&t.ID, &t.TID, &t.DepartmentID, &t.ClientID, &t.AdminID, &t.Subject, &t.Status, &t.Priority, &t.LastReply, &t.CreatedAt); err != nil {
			return nil, 0, err
		}
		tickets = append(tickets, t)
	}
	return tickets, total, nil
}

func (r *TicketRepo) ListAll(page, perPage int, status, priority string, deptID int64) ([]models.Ticket, int64, error) {
	var total int64
	query := `FROM tickets WHERE 1=1`
	args := []any{}
	if status != "" {
		query += ` AND status = ?`
		args = append(args, status)
	}
	if priority != "" {
		query += ` AND priority = ?`
		args = append(args, priority)
	}
	if deptID > 0 {
		query += ` AND department_id = ?`
		args = append(args, deptID)
	}

	r.DB.QueryRow(`SELECT COUNT(*) `+query, args...).Scan(&total)

	offset := (page - 1) * perPage
	args = append(args, perPage, offset)
	rows, err := r.DB.Query(`SELECT id, tid, department_id, client_id, admin_id, subject, status, priority, last_reply, created_at `+query+` ORDER BY last_reply DESC LIMIT ? OFFSET ?`, args...)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var tickets []models.Ticket
	for rows.Next() {
		var t models.Ticket
		if err := rows.Scan(&t.ID, &t.TID, &t.DepartmentID, &t.ClientID, &t.AdminID, &t.Subject, &t.Status, &t.Priority, &t.LastReply, &t.CreatedAt); err != nil {
			return nil, 0, err
		}
		tickets = append(tickets, t)
	}
	return tickets, total, nil
}

// ---------- Ticket Reply Repository ----------

type TicketReplyRepo struct{ DB *sql.DB }

func NewTicketReplyRepo(db *sql.DB) *TicketReplyRepo { return &TicketReplyRepo{DB: db} }

func (r *TicketReplyRepo) Create(reply *models.TicketReply) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO ticket_replies (ticket_id, client_id, admin_id, contact_id, message) VALUES (?, ?, ?, ?, ?)`,
		reply.TicketID, reply.ClientID, reply.AdminID, reply.ContactID, reply.Message)
	if err != nil {
		return 0, err
	}
	// Update ticket last_reply
	r.DB.Exec(`UPDATE tickets SET last_reply = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, reply.TicketID)
	return res.LastInsertId()
}

func (r *TicketReplyRepo) ListByTicket(ticketID int64) ([]models.TicketReply, error) {
	rows, err := r.DB.Query(`SELECT id, ticket_id, client_id, admin_id, contact_id, message, created_at FROM ticket_replies WHERE ticket_id = ? ORDER BY created_at ASC`, ticketID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var replies []models.TicketReply
	for rows.Next() {
		var rp models.TicketReply
		if err := rows.Scan(&rp.ID, &rp.TicketID, &rp.ClientID, &rp.AdminID, &rp.ContactID, &rp.Message, &rp.CreatedAt); err != nil {
			return nil, err
		}
		replies = append(replies, rp)
	}
	return replies, nil
}

// ---------- Domain Repository ----------

type DomainRepo struct{ DB *sql.DB }

func NewDomainRepo(db *sql.DB) *DomainRepo { return &DomainRepo{DB: db} }

func (r *DomainRepo) GetByID(id int64) (*models.Domain, error) {
	d := &models.Domain{}
	err := r.DB.QueryRow(`SELECT id, client_id, order_id, type, domain_name, registrar_id, registration_date, expiry_date, next_due_date, status, ns1, ns2, ns3, ns4, auto_renew, id_protection, amount, recurring_amount, notes, created_at, updated_at FROM domains WHERE id = ?`, id).Scan(
		&d.ID, &d.ClientID, &d.OrderID, &d.Type, &d.DomainName, &d.RegistrarID, &d.RegistrationDate, &d.ExpiryDate, &d.NextDueDate, &d.Status, &d.Nameserver1, &d.Nameserver2, &d.Nameserver3, &d.Nameserver4, &d.AutoRenew, &d.IDProtection, &d.Amount, &d.RecurringAmount, &d.Notes, &d.CreatedAt, &d.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return d, nil
}

func (r *DomainRepo) Create(d *models.Domain) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO domains (client_id, order_id, type, domain_name, registrar_id, registration_date, expiry_date, next_due_date, status, ns1, ns2, ns3, ns4, auto_renew, id_protection, amount, recurring_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		d.ClientID, d.OrderID, d.Type, d.DomainName, d.RegistrarID, d.RegistrationDate, d.ExpiryDate, d.NextDueDate, d.Status, d.Nameserver1, d.Nameserver2, d.Nameserver3, d.Nameserver4, d.AutoRenew, d.IDProtection, d.Amount, d.RecurringAmount)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *DomainRepo) Update(d *models.Domain) error {
	_, err := r.DB.Exec(`UPDATE domains SET status = ?, ns1 = ?, ns2 = ?, ns3 = ?, ns4 = ?, auto_renew = ?, id_protection = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`,
		d.Status, d.Nameserver1, d.Nameserver2, d.Nameserver3, d.Nameserver4, d.AutoRenew, d.IDProtection, d.Notes, d.ID)
	return err
}

func (r *DomainRepo) ListByClient(clientID int64, page, perPage int) ([]models.Domain, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM domains WHERE client_id = ?`, clientID).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, client_id, domain_name, status, registration_date, expiry_date, auto_renew, created_at FROM domains WHERE client_id = ? ORDER BY id DESC LIMIT ? OFFSET ?`, clientID, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var domains []models.Domain
	for rows.Next() {
		var d models.Domain
		if err := rows.Scan(&d.ID, &d.ClientID, &d.DomainName, &d.Status, &d.RegistrationDate, &d.ExpiryDate, &d.AutoRenew, &d.CreatedAt); err != nil {
			return nil, 0, err
		}
		domains = append(domains, d)
	}
	return domains, total, nil
}

// ---------- Client Service Repository ----------

type ServiceRepo struct{ DB *sql.DB }

func NewServiceRepo(db *sql.DB) *ServiceRepo { return &ServiceRepo{DB: db} }

func (r *ServiceRepo) GetByID(id int64) (*models.ClientService, error) {
	s := &models.ClientService{}
	err := r.DB.QueryRow(`SELECT id, client_id, order_id, product_id, server_id, domain, username, amount, billing_cycle, next_due_date, status, suspend_reason, dedicated_ip, notes, registration_date, created_at, updated_at FROM client_services WHERE id = ?`, id).Scan(
		&s.ID, &s.ClientID, &s.OrderID, &s.ProductID, &s.ServerID, &s.Domain, &s.Username, &s.Amount, &s.BillingCycle, &s.NextDueDate, &s.Status, &s.SuspendReason, &s.DedicatedIP, &s.Notes, &s.RegistrationDate, &s.CreatedAt, &s.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return s, nil
}

func (r *ServiceRepo) Create(s *models.ClientService) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO client_services (client_id, order_id, product_id, domain, username, amount, billing_cycle, next_due_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
		s.ClientID, s.OrderID, s.ProductID, s.Domain, s.Username, s.Amount, s.BillingCycle, s.NextDueDate, s.Status)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *ServiceRepo) UpdateStatus(id int64, status, reason string) error {
	_, err := r.DB.Exec(`UPDATE client_services SET status = ?, suspend_reason = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, status, reason, id)
	return err
}

func (r *ServiceRepo) ListByClient(clientID int64, page, perPage int) ([]models.ClientService, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM client_services WHERE client_id = ?`, clientID).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, client_id, product_id, domain, amount, billing_cycle, next_due_date, status, created_at FROM client_services WHERE client_id = ? ORDER BY id DESC LIMIT ? OFFSET ?`, clientID, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var services []models.ClientService
	for rows.Next() {
		var s models.ClientService
		if err := rows.Scan(&s.ID, &s.ClientID, &s.ProductID, &s.Domain, &s.Amount, &s.BillingCycle, &s.NextDueDate, &s.Status, &s.CreatedAt); err != nil {
			return nil, 0, err
		}
		services = append(services, s)
	}
	return services, total, nil
}

// ---------- Transaction Repository ----------

type TransactionRepo struct{ DB *sql.DB }

func NewTransactionRepo(db *sql.DB) *TransactionRepo { return &TransactionRepo{DB: db} }

func (r *TransactionRepo) Create(t *models.Transaction) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO transactions (client_id, invoice_id, transaction_id, gateway, amount, fees, currency, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
		t.ClientID, t.InvoiceID, t.TransactionID, t.Gateway, t.Amount, t.Fees, t.Currency, t.Description)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *TransactionRepo) ListByClient(clientID int64, page, perPage int) ([]models.Transaction, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM transactions WHERE client_id = ?`, clientID).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, client_id, invoice_id, transaction_id, gateway, amount, fees, currency, description, created_at FROM transactions WHERE client_id = ? ORDER BY id DESC LIMIT ? OFFSET ?`, clientID, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var txns []models.Transaction
	for rows.Next() {
		var t models.Transaction
		if err := rows.Scan(&t.ID, &t.ClientID, &t.InvoiceID, &t.TransactionID, &t.Gateway, &t.Amount, &t.Fees, &t.Currency, &t.Description, &t.CreatedAt); err != nil {
			return nil, 0, err
		}
		txns = append(txns, t)
	}
	return txns, total, nil
}

// ---------- Activity Log Repository ----------

type ActivityLogRepo struct{ DB *sql.DB }

func NewActivityLogRepo(db *sql.DB) *ActivityLogRepo { return &ActivityLogRepo{DB: db} }

func (r *ActivityLogRepo) Create(entry *models.ActivityLog) error {
	_, err := r.DB.Exec(`INSERT INTO activity_log (admin_id, client_id, description, ip_address) VALUES (?, ?, ?, ?)`,
		entry.AdminID, entry.ClientID, entry.Description, entry.IPAddress)
	return err
}

func (r *ActivityLogRepo) List(page, perPage int) ([]models.ActivityLog, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM activity_log`).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, admin_id, client_id, description, ip_address, created_at FROM activity_log ORDER BY id DESC LIMIT ? OFFSET ?`, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var logs []models.ActivityLog
	for rows.Next() {
		var l models.ActivityLog
		if err := rows.Scan(&l.ID, &l.AdminID, &l.ClientID, &l.Description, &l.IPAddress, &l.CreatedAt); err != nil {
			return nil, 0, err
		}
		logs = append(logs, l)
	}
	return logs, total, nil
}

// ---------- Promotion Repository ----------

type PromotionRepo struct{ DB *sql.DB }

func NewPromotionRepo(db *sql.DB) *PromotionRepo { return &PromotionRepo{DB: db} }

func (r *PromotionRepo) GetByCode(code string) (*models.Promotion, error) {
	p := &models.Promotion{}
	err := r.DB.QueryRow(`SELECT id, code, type, value, recurring, max_uses, uses, expiration_date, applies_to, created_at FROM promotions WHERE code = ?`, code).Scan(
		&p.ID, &p.Code, &p.Type, &p.Value, &p.Recurring, &p.MaxUses, &p.Uses, &p.ExpirationDate, &p.AppliesTo, &p.CreatedAt)
	if err != nil {
		return nil, err
	}
	return p, nil
}

func (r *PromotionRepo) Create(p *models.Promotion) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO promotions (code, type, value, recurring, max_uses, expiration_date, applies_to) VALUES (?, ?, ?, ?, ?, ?, ?)`,
		p.Code, p.Type, p.Value, p.Recurring, p.MaxUses, p.ExpirationDate, p.AppliesTo)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *PromotionRepo) IncrementUses(id int64) error {
	_, err := r.DB.Exec(`UPDATE promotions SET uses = uses + 1 WHERE id = ?`, id)
	return err
}

func (r *PromotionRepo) List() ([]models.Promotion, error) {
	rows, err := r.DB.Query(`SELECT id, code, type, value, recurring, max_uses, uses, expiration_date, applies_to, created_at FROM promotions ORDER BY id DESC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var promos []models.Promotion
	for rows.Next() {
		var p models.Promotion
		if err := rows.Scan(&p.ID, &p.Code, &p.Type, &p.Value, &p.Recurring, &p.MaxUses, &p.Uses, &p.ExpirationDate, &p.AppliesTo, &p.CreatedAt); err != nil {
			return nil, err
		}
		promos = append(promos, p)
	}
	return promos, nil
}

// ---------- Announcement Repository ----------

type AnnouncementRepo struct{ DB *sql.DB }

func NewAnnouncementRepo(db *sql.DB) *AnnouncementRepo { return &AnnouncementRepo{DB: db} }

func (r *AnnouncementRepo) Create(a *models.Announcement) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO announcements (title, body, published) VALUES (?, ?, ?)`, a.Title, a.Body, a.Published)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *AnnouncementRepo) GetByID(id int64) (*models.Announcement, error) {
	a := &models.Announcement{}
	err := r.DB.QueryRow(`SELECT id, title, body, published, created_at, updated_at FROM announcements WHERE id = ?`, id).Scan(
		&a.ID, &a.Title, &a.Body, &a.Published, &a.CreatedAt, &a.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return a, nil
}

func (r *AnnouncementRepo) Update(a *models.Announcement) error {
	_, err := r.DB.Exec(`UPDATE announcements SET title = ?, body = ?, published = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, a.Title, a.Body, a.Published, a.ID)
	return err
}

func (r *AnnouncementRepo) Delete(id int64) error {
	_, err := r.DB.Exec(`DELETE FROM announcements WHERE id = ?`, id)
	return err
}

func (r *AnnouncementRepo) ListPublished(page, perPage int) ([]models.Announcement, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM announcements WHERE published = 1`).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, title, body, published, created_at, updated_at FROM announcements WHERE published = 1 ORDER BY id DESC LIMIT ? OFFSET ?`, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var anns []models.Announcement
	for rows.Next() {
		var a models.Announcement
		if err := rows.Scan(&a.ID, &a.Title, &a.Body, &a.Published, &a.CreatedAt, &a.UpdatedAt); err != nil {
			return nil, 0, err
		}
		anns = append(anns, a)
	}
	return anns, total, nil
}

func (r *AnnouncementRepo) ListAll(page, perPage int) ([]models.Announcement, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM announcements`).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, title, body, published, created_at, updated_at FROM announcements ORDER BY id DESC LIMIT ? OFFSET ?`, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var anns []models.Announcement
	for rows.Next() {
		var a models.Announcement
		if err := rows.Scan(&a.ID, &a.Title, &a.Body, &a.Published, &a.CreatedAt, &a.UpdatedAt); err != nil {
			return nil, 0, err
		}
		anns = append(anns, a)
	}
	return anns, total, nil
}

// ---------- KB Category Repository ----------

type KBCategoryRepo struct{ DB *sql.DB }

func NewKBCategoryRepo(db *sql.DB) *KBCategoryRepo { return &KBCategoryRepo{DB: db} }

func (r *KBCategoryRepo) Create(c *models.KBCategory) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO kb_categories (name, parent_id, sort_order, hidden) VALUES (?, ?, ?, ?)`, c.Name, c.ParentID, c.SortOrder, c.Hidden)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *KBCategoryRepo) List() ([]models.KBCategory, error) {
	rows, err := r.DB.Query(`SELECT id, name, parent_id, sort_order, hidden FROM kb_categories ORDER BY sort_order ASC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var cats []models.KBCategory
	for rows.Next() {
		var c models.KBCategory
		if err := rows.Scan(&c.ID, &c.Name, &c.ParentID, &c.SortOrder, &c.Hidden); err != nil {
			return nil, err
		}
		cats = append(cats, c)
	}
	return cats, nil
}

// ---------- KB Article Repository ----------

type KBArticleRepo struct{ DB *sql.DB }

func NewKBArticleRepo(db *sql.DB) *KBArticleRepo { return &KBArticleRepo{DB: db} }

func (r *KBArticleRepo) Create(a *models.KBArticle) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO kb_articles (category_id, title, content, sort_order, hidden) VALUES (?, ?, ?, ?, ?)`, a.CategoryID, a.Title, a.Content, a.SortOrder, a.Hidden)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *KBArticleRepo) GetByID(id int64) (*models.KBArticle, error) {
	a := &models.KBArticle{}
	err := r.DB.QueryRow(`SELECT id, category_id, title, content, views, useful, sort_order, hidden, created_at, updated_at FROM kb_articles WHERE id = ?`, id).Scan(
		&a.ID, &a.CategoryID, &a.Title, &a.Content, &a.Views, &a.Useful, &a.SortOrder, &a.Hidden, &a.CreatedAt, &a.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return a, nil
}

func (r *KBArticleRepo) IncrementViews(id int64) error {
	_, err := r.DB.Exec(`UPDATE kb_articles SET views = views + 1 WHERE id = ?`, id)
	return err
}

func (r *KBArticleRepo) ListByCategory(categoryID int64) ([]models.KBArticle, error) {
	rows, err := r.DB.Query(`SELECT id, category_id, title, views, useful, sort_order, hidden, created_at FROM kb_articles WHERE category_id = ? AND hidden = 0 ORDER BY sort_order ASC`, categoryID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var articles []models.KBArticle
	for rows.Next() {
		var a models.KBArticle
		if err := rows.Scan(&a.ID, &a.CategoryID, &a.Title, &a.Views, &a.Useful, &a.SortOrder, &a.Hidden, &a.CreatedAt); err != nil {
			return nil, err
		}
		articles = append(articles, a)
	}
	return articles, nil
}

// ---------- Currency Repository ----------

type CurrencyRepo struct{ DB *sql.DB }

func NewCurrencyRepo(db *sql.DB) *CurrencyRepo { return &CurrencyRepo{DB: db} }

func (r *CurrencyRepo) List() ([]models.Currency, error) {
	rows, err := r.DB.Query(`SELECT id, code, prefix, suffix, rate, is_default FROM currencies ORDER BY id ASC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var currencies []models.Currency
	for rows.Next() {
		var c models.Currency
		if err := rows.Scan(&c.ID, &c.Code, &c.Prefix, &c.Suffix, &c.Rate, &c.IsDefault); err != nil {
			return nil, err
		}
		currencies = append(currencies, c)
	}
	return currencies, nil
}

// ---------- Ticket Department Repository ----------

type TicketDeptRepo struct{ DB *sql.DB }

func NewTicketDeptRepo(db *sql.DB) *TicketDeptRepo { return &TicketDeptRepo{DB: db} }

func (r *TicketDeptRepo) List() ([]models.TicketDepartment, error) {
	rows, err := r.DB.Query(`SELECT id, name, description, email, hidden, sort_order FROM ticket_departments ORDER BY sort_order ASC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var depts []models.TicketDepartment
	for rows.Next() {
		var d models.TicketDepartment
		if err := rows.Scan(&d.ID, &d.Name, &d.Description, &d.Email, &d.Hidden, &d.SortOrder); err != nil {
			return nil, err
		}
		depts = append(depts, d)
	}
	return depts, nil
}

// ---------- Affiliate Repository ----------

type AffiliateRepo struct{ DB *sql.DB }

func NewAffiliateRepo(db *sql.DB) *AffiliateRepo { return &AffiliateRepo{DB: db} }

func (r *AffiliateRepo) GetByClientID(clientID int64) (*models.Affiliate, error) {
	a := &models.Affiliate{}
	err := r.DB.QueryRow(`SELECT id, client_id, pay_type, pay_amount, balance, withdrawn, visitors, conversions, created_at, updated_at FROM affiliates WHERE client_id = ?`, clientID).Scan(
		&a.ID, &a.ClientID, &a.PayType, &a.PayAmount, &a.Balance, &a.Withdrawn, &a.Visitors, &a.Conversions, &a.CreatedAt, &a.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return a, nil
}

func (r *AffiliateRepo) Create(a *models.Affiliate) (int64, error) {
	res, err := r.DB.Exec(`INSERT INTO affiliates (client_id, pay_type, pay_amount) VALUES (?, ?, ?)`, a.ClientID, a.PayType, a.PayAmount)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func (r *AffiliateRepo) AddConversion(id int64, amount float64) error {
	_, err := r.DB.Exec(`UPDATE affiliates SET balance = balance + ?, conversions = conversions + 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, amount, id)
	return err
}

func (r *AffiliateRepo) RecordVisit(id int64) error {
	_, err := r.DB.Exec(`UPDATE affiliates SET visitors = visitors + 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?`, id)
	return err
}

func (r *AffiliateRepo) List(page, perPage int) ([]models.Affiliate, int64, error) {
	var total int64
	r.DB.QueryRow(`SELECT COUNT(*) FROM affiliates`).Scan(&total)

	offset := (page - 1) * perPage
	rows, err := r.DB.Query(`SELECT id, client_id, pay_type, pay_amount, balance, withdrawn, visitors, conversions, created_at FROM affiliates ORDER BY id DESC LIMIT ? OFFSET ?`, perPage, offset)
	if err != nil {
		return nil, 0, err
	}
	defer rows.Close()

	var affiliates []models.Affiliate
	for rows.Next() {
		var a models.Affiliate
		if err := rows.Scan(&a.ID, &a.ClientID, &a.PayType, &a.PayAmount, &a.Balance, &a.Withdrawn, &a.Visitors, &a.Conversions, &a.CreatedAt); err != nil {
			return nil, 0, err
		}
		affiliates = append(affiliates, a)
	}
	return affiliates, total, nil
}
