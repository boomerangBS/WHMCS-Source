package services

import (
	"database/sql"
	"fmt"
	"time"
	"whmcs-rebuild/internal/config"
	"whmcs-rebuild/internal/models"
	"whmcs-rebuild/internal/repository"
	"whmcs-rebuild/internal/utils"
)

// ---------- Auth Service ----------

type AuthService struct {
	AdminRepo  *repository.AdminRepo
	ClientRepo *repository.ClientRepo
	LogRepo    *repository.ActivityLogRepo
	Config     *config.Config
}

func NewAuthService(cfg *config.Config, adminRepo *repository.AdminRepo, clientRepo *repository.ClientRepo, logRepo *repository.ActivityLogRepo) *AuthService {
	return &AuthService{AdminRepo: adminRepo, ClientRepo: clientRepo, LogRepo: logRepo, Config: cfg}
}

func (s *AuthService) AdminLogin(usernameOrEmail, password, ip string) (*models.LoginResponse, error) {
	admin, err := s.AdminRepo.GetByEmail(usernameOrEmail)
	if err != nil {
		admin, err = s.AdminRepo.GetByUsername(usernameOrEmail)
		if err != nil {
			return nil, fmt.Errorf("invalid credentials")
		}
	}

	if admin.Disabled {
		return nil, fmt.Errorf("account is disabled")
	}

	// Check lockout
	if admin.LockedUntil != nil && time.Now().Before(*admin.LockedUntil) {
		return nil, fmt.Errorf("account is locked, try again later")
	}

	if !utils.CheckPassword(password, admin.PasswordHash) {
		attempts := admin.LoginAttempts + 1
		var lockStr *string
		if attempts >= s.Config.Security.MaxLoginAttempts {
			lock := time.Now().Add(s.Config.Security.LockoutDuration).UTC().Format(time.RFC3339)
			lockStr = &lock
		}
		s.AdminRepo.UpdateLoginAttempts(admin.ID, attempts, lockStr)
		return nil, fmt.Errorf("invalid credentials")
	}

	s.AdminRepo.UpdateLastLogin(admin.ID)
	s.LogRepo.Create(&models.ActivityLog{AdminID: admin.ID, Description: "Admin login", IPAddress: ip})

	token, expiresAt, err := utils.GenerateJWT(s.Config.JWT.Secret, admin.ID, admin.Email, "admin", admin.RoleID, s.Config.JWT.ExpirationHours, s.Config.JWT.Issuer)
	if err != nil {
		return nil, fmt.Errorf("failed to generate token")
	}

	return &models.LoginResponse{Token: token, ExpiresAt: expiresAt, User: admin}, nil
}

func (s *AuthService) ClientLogin(email, password, ip string) (*models.LoginResponse, error) {
	client, err := s.ClientRepo.GetByEmail(email)
	if err != nil {
		return nil, fmt.Errorf("invalid credentials")
	}

	if client.Status != "active" {
		return nil, fmt.Errorf("account is not active")
	}

	if !utils.CheckPassword(password, client.PasswordHash) {
		return nil, fmt.Errorf("invalid credentials")
	}

	s.ClientRepo.UpdateLastLogin(client.ID)
	s.LogRepo.Create(&models.ActivityLog{ClientID: client.ID, Description: "Client login", IPAddress: ip})

	token, expiresAt, err := utils.GenerateJWT(s.Config.JWT.Secret, client.ID, client.Email, "client", 0, s.Config.JWT.ExpirationHours, s.Config.JWT.Issuer)
	if err != nil {
		return nil, fmt.Errorf("failed to generate token")
	}

	return &models.LoginResponse{Token: token, ExpiresAt: expiresAt, User: client}, nil
}

func (s *AuthService) RegisterAdmin(username, email, password, firstName, lastName string, roleID int64) (*models.Admin, error) {
	if !utils.ValidateEmail(email) {
		return nil, fmt.Errorf("invalid email address")
	}
	if err := utils.ValidatePassword(password); err != nil {
		return nil, err
	}

	hash, err := utils.HashPassword(password, s.Config.Security.BcryptCost)
	if err != nil {
		return nil, fmt.Errorf("failed to hash password")
	}

	admin := &models.Admin{
		Username:     username,
		Email:        email,
		PasswordHash: hash,
		FirstName:    firstName,
		LastName:     lastName,
		RoleID:       roleID,
	}

	id, err := s.AdminRepo.Create(admin)
	if err != nil {
		return nil, fmt.Errorf("failed to create admin: %w", err)
	}
	admin.ID = id
	return admin, nil
}

// ---------- Client Service (Business Logic) ----------

type ClientService struct {
	ClientRepo *repository.ClientRepo
	LogRepo    *repository.ActivityLogRepo
	Config     *config.Config
}

func NewClientService(cfg *config.Config, clientRepo *repository.ClientRepo, logRepo *repository.ActivityLogRepo) *ClientService {
	return &ClientService{ClientRepo: clientRepo, LogRepo: logRepo, Config: cfg}
}

func (s *ClientService) RegisterClient(firstName, lastName, email, password, companyName, address1, city, state, postcode, country, phone string, ip string) (*models.Client, error) {
	if firstName == "" || lastName == "" {
		return nil, fmt.Errorf("first name and last name are required")
	}
	if !utils.ValidateEmail(email) {
		return nil, fmt.Errorf("invalid email address")
	}
	if err := utils.ValidatePassword(password); err != nil {
		return nil, err
	}

	// Check duplicate
	if existing, _ := s.ClientRepo.GetByEmail(email); existing != nil {
		return nil, fmt.Errorf("email already registered")
	}

	hash, err := utils.HashPassword(password, s.Config.Security.BcryptCost)
	if err != nil {
		return nil, fmt.Errorf("failed to hash password")
	}

	client := &models.Client{
		FirstName:    utils.SanitizeString(firstName),
		LastName:     utils.SanitizeString(lastName),
		Email:        email,
		PasswordHash: hash,
		CompanyName:  utils.SanitizeString(companyName),
		Address1:     utils.SanitizeString(address1),
		City:         utils.SanitizeString(city),
		State:        utils.SanitizeString(state),
		PostCode:     utils.SanitizeString(postcode),
		Country:      utils.SanitizeString(country),
		PhoneNumber:  phone,
		Status:       "active",
		Currency:     1,
	}

	id, err := s.ClientRepo.Create(client)
	if err != nil {
		return nil, fmt.Errorf("failed to create client: %w", err)
	}
	client.ID = id

	s.LogRepo.Create(&models.ActivityLog{ClientID: id, Description: "New client registered", IPAddress: ip})
	return client, nil
}

func (s *ClientService) UpdateClient(client *models.Client, adminID int64, ip string) error {
	client.FirstName = utils.SanitizeString(client.FirstName)
	client.LastName = utils.SanitizeString(client.LastName)
	client.CompanyName = utils.SanitizeString(client.CompanyName)
	client.Address1 = utils.SanitizeString(client.Address1)
	client.Address2 = utils.SanitizeString(client.Address2)
	client.City = utils.SanitizeString(client.City)
	client.Notes = utils.SanitizeString(client.Notes)

	if err := s.ClientRepo.Update(client); err != nil {
		return fmt.Errorf("failed to update client: %w", err)
	}

	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: client.ID, Description: "Client updated", IPAddress: ip})
	return nil
}

func (s *ClientService) AddCredit(clientID int64, amount float64, adminID int64, ip string) error {
	if amount <= 0 {
		return fmt.Errorf("credit amount must be positive")
	}
	if err := s.ClientRepo.UpdateCredit(clientID, amount); err != nil {
		return fmt.Errorf("failed to add credit: %w", err)
	}
	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: clientID, Description: fmt.Sprintf("Added credit: %.2f", amount), IPAddress: ip})
	return nil
}

// ---------- Invoice Service ----------

type InvoiceService struct {
	InvoiceRepo     *repository.InvoiceRepo
	InvoiceItemRepo *repository.InvoiceItemRepo
	ClientRepo      *repository.ClientRepo
	TransactionRepo *repository.TransactionRepo
	LogRepo         *repository.ActivityLogRepo
}

func NewInvoiceService(invRepo *repository.InvoiceRepo, itemRepo *repository.InvoiceItemRepo, clientRepo *repository.ClientRepo, txnRepo *repository.TransactionRepo, logRepo *repository.ActivityLogRepo) *InvoiceService {
	return &InvoiceService{
		InvoiceRepo:     invRepo,
		InvoiceItemRepo: itemRepo,
		ClientRepo:      clientRepo,
		TransactionRepo: txnRepo,
		LogRepo:         logRepo,
	}
}

type CreateInvoiceInput struct {
	ClientID      int64
	DateDue       time.Time
	TaxRate       float64
	PaymentMethod string
	Notes         string
	Items         []CreateInvoiceItemInput
}

type CreateInvoiceItemInput struct {
	Type        string
	RelID       int64
	Description string
	Amount      float64
	Taxed       bool
}

func (s *InvoiceService) CreateInvoice(input CreateInvoiceInput, adminID int64, ip string) (*models.Invoice, error) {
	// Validate client exists
	if _, err := s.ClientRepo.GetByID(input.ClientID); err != nil {
		return nil, fmt.Errorf("client not found")
	}

	if len(input.Items) == 0 {
		return nil, fmt.Errorf("invoice must have at least one item")
	}

	// Calculate totals
	var subtotal float64
	var taxableTotal float64
	for _, item := range input.Items {
		subtotal += item.Amount
		if item.Taxed {
			taxableTotal += item.Amount
		}
	}

	tax := taxableTotal * (input.TaxRate / 100)
	total := subtotal + tax

	inv := &models.Invoice{
		ClientID:      input.ClientID,
		InvoiceNum:    "PENDING",
		DateCreated:   time.Now(),
		DateDue:       input.DateDue,
		SubTotal:      subtotal,
		Tax:           tax,
		TaxRate:       input.TaxRate,
		Total:         total,
		Status:        "unpaid",
		PaymentMethod: input.PaymentMethod,
		Notes:         utils.SanitizeString(input.Notes),
	}

	id, err := s.InvoiceRepo.Create(inv)
	if err != nil {
		return nil, fmt.Errorf("failed to create invoice: %w", err)
	}
	inv.ID = id

	// Create items
	for _, item := range input.Items {
		ii := &models.InvoiceItem{
			InvoiceID:   id,
			Type:        item.Type,
			RelID:       item.RelID,
			Description: utils.SanitizeString(item.Description),
			Amount:      item.Amount,
			Taxed:       item.Taxed,
		}
		s.InvoiceItemRepo.Create(ii)
	}

	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: input.ClientID, Description: fmt.Sprintf("Invoice #%d created: %.2f", id, total), IPAddress: ip})
	return inv, nil
}

func (s *InvoiceService) ApplyPayment(invoiceID int64, gateway, transactionID string, amount float64, adminID int64, ip string) error {
	inv, err := s.InvoiceRepo.GetByID(invoiceID)
	if err != nil {
		return fmt.Errorf("invoice not found")
	}

	if inv.Status == "paid" {
		return fmt.Errorf("invoice already paid")
	}

	if amount <= 0 {
		return fmt.Errorf("payment amount must be positive")
	}

	// Record transaction
	txn := &models.Transaction{
		ClientID:      inv.ClientID,
		InvoiceID:     invoiceID,
		TransactionID: transactionID,
		Gateway:       gateway,
		Amount:        amount,
		Currency:      "USD",
		Description:   fmt.Sprintf("Payment for invoice #%d", invoiceID),
	}
	s.TransactionRepo.Create(txn)

	// Mark paid if full amount received
	if amount >= inv.Total {
		s.InvoiceRepo.MarkPaid(invoiceID)
	}

	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: inv.ClientID, Description: fmt.Sprintf("Payment of %.2f applied to invoice #%d", amount, invoiceID), IPAddress: ip})
	return nil
}

func (s *InvoiceService) ApplyCredit(invoiceID int64, adminID int64, ip string) error {
	inv, err := s.InvoiceRepo.GetByID(invoiceID)
	if err != nil {
		return fmt.Errorf("invoice not found")
	}

	client, err := s.ClientRepo.GetByID(inv.ClientID)
	if err != nil {
		return fmt.Errorf("client not found")
	}

	if client.Credit <= 0 {
		return fmt.Errorf("no credit available")
	}

	creditToApply := client.Credit
	if creditToApply > inv.Total {
		creditToApply = inv.Total
	}

	// Deduct credit from client
	s.ClientRepo.UpdateCredit(client.ID, -creditToApply)

	// Update invoice
	inv.Credit = creditToApply
	if creditToApply >= inv.Total {
		inv.Status = "paid"
	}
	s.InvoiceRepo.Update(inv)

	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: client.ID, Description: fmt.Sprintf("Credit of %.2f applied to invoice #%d", creditToApply, invoiceID), IPAddress: ip})
	return nil
}

// ---------- Order Service ----------

type OrderService struct {
	OrderRepo   *repository.OrderRepo
	ServiceRepo *repository.ServiceRepo
	ProductRepo *repository.ProductRepo
	InvoiceSvc  *InvoiceService
	LogRepo     *repository.ActivityLogRepo
}

func NewOrderService(orderRepo *repository.OrderRepo, svcRepo *repository.ServiceRepo, prodRepo *repository.ProductRepo, invSvc *InvoiceService, logRepo *repository.ActivityLogRepo) *OrderService {
	return &OrderService{
		OrderRepo:   orderRepo,
		ServiceRepo: svcRepo,
		ProductRepo: prodRepo,
		InvoiceSvc:  invSvc,
		LogRepo:     logRepo,
	}
}

type CreateOrderInput struct {
	ClientID      int64  `json:"client_id"`
	ProductID     int64  `json:"product_id"`
	BillingCycle  string `json:"billing_cycle"`
	Domain        string `json:"domain"`
	PaymentMethod string `json:"payment_method"`
	PromoCode     string `json:"promo_code"`
	IPAddress     string `json:"ip_address"`
}

func (s *OrderService) CreateOrder(input CreateOrderInput, adminID int64) (*models.Order, error) {
	product, err := s.ProductRepo.GetByID(input.ProductID)
	if err != nil {
		return nil, fmt.Errorf("product not found")
	}

	if product.Retired {
		return nil, fmt.Errorf("product is retired")
	}

	if product.StockControl && product.Qty <= 0 {
		return nil, fmt.Errorf("product out of stock")
	}

	// Determine price based on billing cycle
	var price float64
	switch input.BillingCycle {
	case "monthly":
		price = product.PriceMonthly
	case "quarterly":
		price = product.PriceQuarterly
	case "semiannual":
		price = product.PriceSemiAnnual
	case "annual":
		price = product.PriceAnnual
	case "biennial":
		price = product.PriceBiennial
	case "free":
		price = 0
	default:
		return nil, fmt.Errorf("invalid billing cycle")
	}

	totalAmount := price + product.SetupFee

	// Create order
	order := &models.Order{
		ClientID:      input.ClientID,
		OrderNumber:   utils.GenerateOrderNumber(),
		Amount:        totalAmount,
		Status:        "pending",
		PaymentMethod: input.PaymentMethod,
		IPAddress:     input.IPAddress,
		PromoCode:     input.PromoCode,
	}

	orderID, err := s.OrderRepo.Create(order)
	if err != nil {
		return nil, fmt.Errorf("failed to create order: %w", err)
	}
	order.ID = orderID

	// Create client service (pending)
	svc := &models.ClientService{
		ClientID:     input.ClientID,
		OrderID:      orderID,
		ProductID:    input.ProductID,
		Domain:       input.Domain,
		Amount:       price,
		BillingCycle: input.BillingCycle,
		Status:       "pending",
	}
	s.ServiceRepo.Create(svc)

	// Create invoice for the order
	items := []CreateInvoiceItemInput{
		{Type: "service", RelID: input.ProductID, Description: product.Name + " - " + input.BillingCycle, Amount: price, Taxed: true},
	}
	if product.SetupFee > 0 {
		items = append(items, CreateInvoiceItemInput{Type: "setup", Description: "Setup Fee", Amount: product.SetupFee, Taxed: true})
	}

	inv, err := s.InvoiceSvc.CreateInvoice(CreateInvoiceInput{
		ClientID:      input.ClientID,
		DateDue:       time.Now().Add(7 * 24 * time.Hour),
		TaxRate:       0,
		PaymentMethod: input.PaymentMethod,
		Items:         items,
	}, adminID, input.IPAddress)
	if err == nil && inv != nil {
		s.OrderRepo.DB.Exec(`UPDATE orders SET invoice_id = ? WHERE id = ?`, inv.ID, orderID)
	}

	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: input.ClientID, Description: fmt.Sprintf("New order #%s placed for %s", order.OrderNumber, product.Name), IPAddress: input.IPAddress})
	return order, nil
}

func (s *OrderService) AcceptOrder(orderID int64, adminID int64, ip string) error {
	order, err := s.OrderRepo.GetByID(orderID)
	if err != nil {
		return fmt.Errorf("order not found")
	}

	if order.Status != "pending" {
		return fmt.Errorf("order is not pending")
	}

	if err := s.OrderRepo.UpdateStatus(orderID, "active"); err != nil {
		return err
	}

	// Activate associated services
	s.OrderRepo.DB.Exec(`UPDATE client_services SET status = 'active', updated_at = CURRENT_TIMESTAMP WHERE order_id = ?`, orderID)

	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: order.ClientID, Description: fmt.Sprintf("Order #%s accepted", order.OrderNumber), IPAddress: ip})
	return nil
}

func (s *OrderService) CancelOrder(orderID int64, adminID int64, ip string) error {
	order, err := s.OrderRepo.GetByID(orderID)
	if err != nil {
		return fmt.Errorf("order not found")
	}

	if err := s.OrderRepo.UpdateStatus(orderID, "cancelled"); err != nil {
		return err
	}

	s.OrderRepo.DB.Exec(`UPDATE client_services SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE order_id = ?`, orderID)

	s.LogRepo.Create(&models.ActivityLog{AdminID: adminID, ClientID: order.ClientID, Description: fmt.Sprintf("Order #%s cancelled", order.OrderNumber), IPAddress: ip})
	return nil
}

// ---------- Ticket Service ----------

type TicketService struct {
	TicketRepo *repository.TicketRepo
	ReplyRepo  *repository.TicketReplyRepo
	LogRepo    *repository.ActivityLogRepo
}

func NewTicketService(ticketRepo *repository.TicketRepo, replyRepo *repository.TicketReplyRepo, logRepo *repository.ActivityLogRepo) *TicketService {
	return &TicketService{TicketRepo: ticketRepo, ReplyRepo: replyRepo, LogRepo: logRepo}
}

func (s *TicketService) OpenTicket(clientID, departmentID int64, subject, message, priority string, ip string) (*models.Ticket, error) {
	if subject == "" || message == "" {
		return nil, fmt.Errorf("subject and message are required")
	}

	validPriorities := map[string]bool{"low": true, "medium": true, "high": true, "urgent": true}
	if !validPriorities[priority] {
		priority = "medium"
	}

	ticket := &models.Ticket{
		TID:          utils.GenerateTicketID(),
		DepartmentID: departmentID,
		ClientID:     clientID,
		Subject:      utils.SanitizeString(subject),
		Message:      utils.SanitizeString(message),
		Status:       "open",
		Priority:     priority,
	}

	id, err := s.TicketRepo.Create(ticket)
	if err != nil {
		return nil, fmt.Errorf("failed to create ticket: %w", err)
	}
	ticket.ID = id

	s.LogRepo.Create(&models.ActivityLog{ClientID: clientID, Description: fmt.Sprintf("Ticket #%s opened: %s", ticket.TID, subject), IPAddress: ip})
	return ticket, nil
}

func (s *TicketService) ReplyToTicket(ticketID, clientID, adminID int64, message, ip string) (*models.TicketReply, error) {
	if message == "" {
		return nil, fmt.Errorf("message is required")
	}

	ticket, err := s.TicketRepo.GetByID(ticketID)
	if err != nil {
		return nil, fmt.Errorf("ticket not found")
	}

	// Authorization: client can only reply to own tickets
	if clientID > 0 && ticket.ClientID != clientID {
		return nil, fmt.Errorf("access denied")
	}

	reply := &models.TicketReply{
		TicketID: ticketID,
		ClientID: clientID,
		AdminID:  adminID,
		Message:  utils.SanitizeString(message),
	}

	id, err := s.ReplyRepo.Create(reply)
	if err != nil {
		return nil, fmt.Errorf("failed to create reply: %w", err)
	}
	reply.ID = id

	// Update ticket status
	if adminID > 0 {
		s.TicketRepo.UpdateStatus(ticketID, "answered")
	} else {
		s.TicketRepo.UpdateStatus(ticketID, "customer-reply")
	}

	return reply, nil
}

func (s *TicketService) CloseTicket(ticketID int64, userID int64, isAdmin bool, ip string) error {
	ticket, err := s.TicketRepo.GetByID(ticketID)
	if err != nil {
		return fmt.Errorf("ticket not found")
	}

	if !isAdmin && ticket.ClientID != userID {
		return fmt.Errorf("access denied")
	}

	if err := s.TicketRepo.UpdateStatus(ticketID, "closed"); err != nil {
		return err
	}

	desc := fmt.Sprintf("Ticket #%s closed", ticket.TID)
	entry := &models.ActivityLog{Description: desc, IPAddress: ip}
	if isAdmin {
		entry.AdminID = userID
	} else {
		entry.ClientID = userID
	}
	s.LogRepo.Create(entry)
	return nil
}

// ---------- Dashboard Stats ----------

type DashboardService struct {
	DB *sql.DB
}

func NewDashboardService(db *sql.DB) *DashboardService {
	return &DashboardService{DB: db}
}

type DashboardStats struct {
	TotalClients       int64   `json:"total_clients"`
	ActiveClients      int64   `json:"active_clients"`
	TotalInvoices      int64   `json:"total_invoices"`
	UnpaidInvoices     int64   `json:"unpaid_invoices"`
	OverdueInvoices    int64   `json:"overdue_invoices"`
	TotalRevenue       float64 `json:"total_revenue"`
	MonthlyRevenue     float64 `json:"monthly_revenue"`
	OpenTickets        int64   `json:"open_tickets"`
	PendingOrders      int64   `json:"pending_orders"`
	ActiveServices     int64   `json:"active_services"`
	TotalDomains       int64   `json:"total_domains"`
	TotalProducts      int64   `json:"total_products"`
}

func (s *DashboardService) GetStats() (*DashboardStats, error) {
	stats := &DashboardStats{}

	s.DB.QueryRow(`SELECT COUNT(*) FROM clients`).Scan(&stats.TotalClients)
	s.DB.QueryRow(`SELECT COUNT(*) FROM clients WHERE status = 'active'`).Scan(&stats.ActiveClients)
	s.DB.QueryRow(`SELECT COUNT(*) FROM invoices`).Scan(&stats.TotalInvoices)
	s.DB.QueryRow(`SELECT COUNT(*) FROM invoices WHERE status = 'unpaid'`).Scan(&stats.UnpaidInvoices)
	s.DB.QueryRow(`SELECT COUNT(*) FROM invoices WHERE status = 'overdue'`).Scan(&stats.OverdueInvoices)
	s.DB.QueryRow(`SELECT COALESCE(SUM(amount), 0) FROM transactions`).Scan(&stats.TotalRevenue)
	s.DB.QueryRow(`SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE created_at >= date('now', 'start of month')`).Scan(&stats.MonthlyRevenue)
	s.DB.QueryRow(`SELECT COUNT(*) FROM tickets WHERE status IN ('open', 'customer-reply')`).Scan(&stats.OpenTickets)
	s.DB.QueryRow(`SELECT COUNT(*) FROM orders WHERE status = 'pending'`).Scan(&stats.PendingOrders)
	s.DB.QueryRow(`SELECT COUNT(*) FROM client_services WHERE status = 'active'`).Scan(&stats.ActiveServices)
	s.DB.QueryRow(`SELECT COUNT(*) FROM domains`).Scan(&stats.TotalDomains)
	s.DB.QueryRow(`SELECT COUNT(*) FROM products`).Scan(&stats.TotalProducts)

	return stats, nil
}
