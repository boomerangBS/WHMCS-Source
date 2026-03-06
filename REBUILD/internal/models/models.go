package models

import "time"

// ---------- Authentication & Users ----------

type Admin struct {
	ID             int64     `json:"id"`
	Username       string    `json:"username"`
	Email          string    `json:"email"`
	PasswordHash   string    `json:"-"`
	FirstName      string    `json:"first_name"`
	LastName       string    `json:"last_name"`
	RoleID         int64     `json:"role_id"`
	Disabled       bool      `json:"disabled"`
	LoginAttempts  int       `json:"-"`
	LockedUntil    *time.Time `json:"-"`
	LastLogin      *time.Time `json:"last_login"`
	TwoFactorKey   string    `json:"-"`
	TwoFactorOn    bool      `json:"two_factor_enabled"`
	CreatedAt      time.Time `json:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"`
}

type AdminRole struct {
	ID          int64     `json:"id"`
	Name        string    `json:"name"`
	Permissions string    `json:"permissions"` // JSON array of permission strings
	CreatedAt   time.Time `json:"created_at"`
}

// ---------- Clients ----------

type Client struct {
	ID           int64     `json:"id"`
	FirstName    string    `json:"first_name"`
	LastName     string    `json:"last_name"`
	CompanyName  string    `json:"company_name"`
	Email        string    `json:"email"`
	PasswordHash string    `json:"-"`
	Address1     string    `json:"address1"`
	Address2     string    `json:"address2"`
	City         string    `json:"city"`
	State        string    `json:"state"`
	PostCode     string    `json:"postcode"`
	Country      string    `json:"country"`
	PhoneNumber  string    `json:"phone_number"`
	Currency     int       `json:"currency"`
	GroupID      int64     `json:"group_id"`
	Status       string    `json:"status"` // active, inactive, closed
	Credit       float64   `json:"credit"`
	Notes        string    `json:"notes"`
	TwoFactorKey string    `json:"-"`
	TwoFactorOn  bool      `json:"two_factor_enabled"`
	LastLogin    *time.Time `json:"last_login"`
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}

type ClientGroup struct {
	ID           int64     `json:"id"`
	GroupName    string    `json:"group_name"`
	DiscountPerc float64   `json:"discount_percent"`
	CreatedAt    time.Time `json:"created_at"`
}

type Contact struct {
	ID          int64     `json:"id"`
	ClientID    int64     `json:"client_id"`
	FirstName   string    `json:"first_name"`
	LastName    string    `json:"last_name"`
	CompanyName string    `json:"company_name"`
	Email       string    `json:"email"`
	Phone       string    `json:"phone"`
	Address1    string    `json:"address1"`
	Address2    string    `json:"address2"`
	City        string    `json:"city"`
	State       string    `json:"state"`
	PostCode    string    `json:"postcode"`
	Country     string    `json:"country"`
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}

// ---------- Products & Services ----------

type ProductGroup struct {
	ID        int64     `json:"id"`
	Name      string    `json:"name"`
	Headline  string    `json:"headline"`
	OrderNum  int       `json:"order_num"`
	Hidden    bool      `json:"hidden"`
	CreatedAt time.Time `json:"created_at"`
}

type Product struct {
	ID              int64     `json:"id"`
	GroupID         int64     `json:"group_id"`
	Type            string    `json:"type"` // hosting, server, other
	Name            string    `json:"name"`
	Description     string    `json:"description"`
	PriceMonthly    float64   `json:"price_monthly"`
	PriceQuarterly  float64   `json:"price_quarterly"`
	PriceSemiAnnual float64   `json:"price_semiannual"`
	PriceAnnual     float64   `json:"price_annual"`
	PriceBiennial   float64   `json:"price_biennial"`
	SetupFee        float64   `json:"setup_fee"`
	Hidden          bool      `json:"hidden"`
	Retired         bool      `json:"retired"`
	StockControl    bool      `json:"stock_control"`
	Qty             int       `json:"qty"`
	SortOrder       int       `json:"sort_order"`
	AutoSetup       string    `json:"auto_setup"` // order, payment, on, off
	ModuleName      string    `json:"module_name"`
	ServerGroupID   int64     `json:"server_group_id"`
	CreatedAt       time.Time `json:"created_at"`
	UpdatedAt       time.Time `json:"updated_at"`
}

type ClientService struct {
	ID              int64      `json:"id"`
	ClientID        int64      `json:"client_id"`
	OrderID         int64      `json:"order_id"`
	ProductID       int64      `json:"product_id"`
	ServerID        int64      `json:"server_id"`
	Domain          string     `json:"domain"`
	Username        string     `json:"username"`
	Password        string     `json:"-"`
	Amount          float64    `json:"amount"`
	BillingCycle    string     `json:"billing_cycle"` // monthly, quarterly, semiannual, annual, biennial, free
	NextDueDate     *time.Time `json:"next_due_date"`
	Status          string     `json:"status"` // pending, active, suspended, terminated, cancelled, fraud
	SuspendReason   string     `json:"suspend_reason"`
	DedicatedIP     string     `json:"dedicated_ip"`
	AssignedIPs     string     `json:"assigned_ips"`
	Notes           string     `json:"notes"`
	RegistrationDate time.Time `json:"registration_date"`
	CreatedAt       time.Time  `json:"created_at"`
	UpdatedAt       time.Time  `json:"updated_at"`
}

// ---------- Orders ----------

type Order struct {
	ID            int64     `json:"id"`
	ClientID      int64     `json:"client_id"`
	OrderNumber   string    `json:"order_number"`
	InvoiceID     int64     `json:"invoice_id"`
	Amount        float64   `json:"amount"`
	Status        string    `json:"status"` // pending, active, fraud, cancelled
	PaymentMethod string    `json:"payment_method"`
	IPAddress     string    `json:"ip_address"`
	FraudOutput   string    `json:"fraud_output"`
	Notes         string    `json:"notes"`
	PromoCode     string    `json:"promo_code"`
	CreatedAt     time.Time `json:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"`
}

// ---------- Invoices & Billing ----------

type Invoice struct {
	ID            int64      `json:"id"`
	ClientID      int64      `json:"client_id"`
	InvoiceNum    string     `json:"invoice_num"`
	DateCreated   time.Time  `json:"date_created"`
	DateDue       time.Time  `json:"date_due"`
	DatePaid      *time.Time `json:"date_paid"`
	SubTotal      float64    `json:"subtotal"`
	Tax           float64    `json:"tax"`
	TaxRate       float64    `json:"tax_rate"`
	Total         float64    `json:"total"`
	Credit        float64    `json:"credit"`
	Status        string     `json:"status"` // draft, unpaid, paid, cancelled, refunded, overdue
	PaymentMethod string     `json:"payment_method"`
	Notes         string     `json:"notes"`
	CreatedAt     time.Time  `json:"created_at"`
	UpdatedAt     time.Time  `json:"updated_at"`
}

type InvoiceItem struct {
	ID          int64   `json:"id"`
	InvoiceID   int64   `json:"invoice_id"`
	Type        string  `json:"type"` // service, domain, addon, setup, promo, custom
	RelID       int64   `json:"rel_id"`
	Description string  `json:"description"`
	Amount      float64 `json:"amount"`
	Taxed       bool    `json:"taxed"`
}

type Transaction struct {
	ID            int64     `json:"id"`
	ClientID      int64     `json:"client_id"`
	InvoiceID     int64     `json:"invoice_id"`
	TransactionID string    `json:"transaction_id"`
	Gateway       string    `json:"gateway"`
	Amount        float64   `json:"amount"`
	Fees          float64   `json:"fees"`
	Currency      string    `json:"currency"`
	Description   string    `json:"description"`
	CreatedAt     time.Time `json:"created_at"`
}

// ---------- Domains ----------

type Domain struct {
	ID              int64      `json:"id"`
	ClientID        int64      `json:"client_id"`
	OrderID         int64      `json:"order_id"`
	Type            string     `json:"type"` // register, transfer
	DomainName      string     `json:"domain_name"`
	RegistrarID     int64      `json:"registrar_id"`
	RegistrationDate time.Time `json:"registration_date"`
	ExpiryDate      time.Time  `json:"expiry_date"`
	NextDueDate     *time.Time `json:"next_due_date"`
	Status          string     `json:"status"` // pending, active, expired, cancelled, fraud, transferring
	Nameserver1     string     `json:"ns1"`
	Nameserver2     string     `json:"ns2"`
	Nameserver3     string     `json:"ns3"`
	Nameserver4     string     `json:"ns4"`
	AutoRenew       bool       `json:"auto_renew"`
	IDProtection    bool       `json:"id_protection"`
	Amount          float64    `json:"amount"`
	RecurringAmount float64    `json:"recurring_amount"`
	Notes           string     `json:"notes"`
	CreatedAt       time.Time  `json:"created_at"`
	UpdatedAt       time.Time  `json:"updated_at"`
}

// ---------- Support Tickets ----------

type TicketDepartment struct {
	ID          int64  `json:"id"`
	Name        string `json:"name"`
	Description string `json:"description"`
	Email       string `json:"email"`
	Hidden      bool   `json:"hidden"`
	SortOrder   int    `json:"sort_order"`
}

type Ticket struct {
	ID           int64     `json:"id"`
	TID          string    `json:"tid"` // human-readable ticket ID (e.g., ABC-123456)
	DepartmentID int64     `json:"department_id"`
	ClientID     int64     `json:"client_id"`
	ContactID    int64     `json:"contact_id"`
	AdminID      int64     `json:"admin_id"`
	Subject      string    `json:"subject"`
	Message      string    `json:"message"`
	Status       string    `json:"status"` // open, answered, customer-reply, closed, on-hold
	Priority     string    `json:"priority"` // low, medium, high, urgent
	ServiceID    int64     `json:"service_id"`
	DomainID     int64     `json:"domain_id"`
	LastReply    time.Time `json:"last_reply"`
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}

type TicketReply struct {
	ID        int64     `json:"id"`
	TicketID  int64     `json:"ticket_id"`
	ClientID  int64     `json:"client_id"`
	AdminID   int64     `json:"admin_id"`
	ContactID int64     `json:"contact_id"`
	Message   string    `json:"message"`
	CreatedAt time.Time `json:"created_at"`
}

type TicketNote struct {
	ID        int64     `json:"id"`
	TicketID  int64     `json:"ticket_id"`
	AdminID   int64     `json:"admin_id"`
	Message   string    `json:"message"`
	CreatedAt time.Time `json:"created_at"`
}

// ---------- Affiliates ----------

type Affiliate struct {
	ID           int64     `json:"id"`
	ClientID     int64     `json:"client_id"`
	PayType      string    `json:"pay_type"` // percentage, fixed
	PayAmount    float64   `json:"pay_amount"`
	Balance      float64   `json:"balance"`
	Withdrawn    float64   `json:"withdrawn"`
	Visitors     int       `json:"visitors"`
	Conversions  int       `json:"conversions"`
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}

// ---------- Promotions ----------

type Promotion struct {
	ID             int64     `json:"id"`
	Code           string    `json:"code"`
	Type           string    `json:"type"` // percentage, fixed
	Value          float64   `json:"value"`
	Recurring      bool      `json:"recurring"`
	MaxUses        int       `json:"max_uses"`
	Uses           int       `json:"uses"`
	ExpirationDate *time.Time `json:"expiration_date"`
	AppliesTo      string    `json:"applies_to"` // JSON array of product IDs
	CreatedAt      time.Time `json:"created_at"`
}

// ---------- Currencies ----------

type Currency struct {
	ID     int64   `json:"id"`
	Code   string  `json:"code"`
	Prefix string  `json:"prefix"`
	Suffix string  `json:"suffix"`
	Rate   float64 `json:"rate"`
	IsDefault bool `json:"is_default"`
}

// ---------- Activity Log ----------

type ActivityLog struct {
	ID          int64     `json:"id"`
	AdminID     int64     `json:"admin_id"`
	ClientID    int64     `json:"client_id"`
	Description string    `json:"description"`
	IPAddress   string    `json:"ip_address"`
	CreatedAt   time.Time `json:"created_at"`
}

// ---------- Knowledge Base ----------

type KBCategory struct {
	ID        int64  `json:"id"`
	Name      string `json:"name"`
	ParentID  int64  `json:"parent_id"`
	SortOrder int    `json:"sort_order"`
	Hidden    bool   `json:"hidden"`
}

type KBArticle struct {
	ID         int64     `json:"id"`
	CategoryID int64     `json:"category_id"`
	Title      string    `json:"title"`
	Content    string    `json:"content"`
	Views      int       `json:"views"`
	Useful     int       `json:"useful"`
	SortOrder  int       `json:"sort_order"`
	Hidden     bool      `json:"hidden"`
	CreatedAt  time.Time `json:"created_at"`
	UpdatedAt  time.Time `json:"updated_at"`
}

// ---------- Announcements ----------

type Announcement struct {
	ID        int64     `json:"id"`
	Title     string    `json:"title"`
	Body      string    `json:"body"`
	Published bool      `json:"published"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

// ---------- API Request/Response ----------

type LoginRequest struct {
	Email    string `json:"email"`
	Password string `json:"password"`
}

type LoginResponse struct {
	Token     string `json:"token"`
	ExpiresAt int64  `json:"expires_at"`
	User      any    `json:"user"`
}

type PaginatedResponse struct {
	Data       any   `json:"data"`
	Total      int64 `json:"total"`
	Page       int   `json:"page"`
	PerPage    int   `json:"per_page"`
	TotalPages int   `json:"total_pages"`
}

type APIResponse struct {
	Success bool   `json:"success"`
	Message string `json:"message,omitempty"`
	Data    any    `json:"data,omitempty"`
	Error   string `json:"error,omitempty"`
}
