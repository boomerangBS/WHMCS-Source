# WHMCS Rebuild — Modern Go Implementation

A high-performance, secure rewrite of core WHMCS billing/hosting management functionality in **Go**.

## Architecture

```
REBUILD/
├── cmd/server/main.go         # Entry point, routing, DI wiring
├── internal/
│   ├── config/config.go       # Environment-based configuration
│   ├── database/database.go   # SQLite database, migrations, seed data
│   ├── middleware/middleware.go # Auth, CORS, logging, rate limiting, security headers
│   ├── models/models.go       # All data models and API types
│   ├── handlers/handlers.go   # HTTP request handlers (controllers)
│   ├── services/services.go   # Business logic layer
│   ├── repository/repository.go # Data access layer (SQL queries)
│   └── utils/
│       ├── utils.go           # Passwords, validation, sanitization, HTTP helpers
│       └── jwt.go             # JWT token generation/validation
├── go.mod
├── go.sum
└── README.md
```

## Quick Start

```bash
# Build
cd REBUILD
CGO_ENABLED=1 go build -o whmcs-rebuild ./cmd/server/

# Run (creates SQLite DB automatically)
./whmcs-rebuild

# Server starts at http://localhost:8080
# API base: http://localhost:8080/api/v1
```

## Configuration (Environment Variables)

| Variable | Default | Description |
|----------|---------|-------------|
| `SERVER_HOST` | `0.0.0.0` | Server bind address |
| `SERVER_PORT` | `8080` | Server port |
| `DB_DRIVER` | `sqlite3` | Database driver |
| `DB_DSN` | `whmcs_rebuild.db` | Database connection string |
| `JWT_SECRET` | (default) | **Change in production** |
| `JWT_EXPIRATION_HOURS` | `24` | Token lifetime |
| `BCRYPT_COST` | `12` | Password hashing cost |
| `RATE_LIMIT_PER_MIN` | `60` | API rate limit per IP |
| `MAX_LOGIN_ATTEMPTS` | `5` | Before account lockout |
| `LOCKOUT_DURATION_MIN` | `15` | Lockout duration in minutes |

## Core Features

### 🔐 Authentication & Security
- JWT-based authentication for admin and client sessions
- bcrypt password hashing (cost 12)
- Account lockout after failed login attempts
- Rate limiting per IP address
- Input sanitization (XSS prevention)
- Security headers (HSTS, CSP, X-Frame-Options, etc.)
- CORS support
- Panic recovery middleware
- Role-based access control (RBAC)

### 👥 Client Management
- Client registration and login
- Profile management (CRUD)
- Client groups with discount percentages
- Contact management
- Credit system
- Client search and filtering

### 📦 Product & Service Management
- Product groups and categories
- Multiple billing cycles (monthly, quarterly, semi-annual, annual, biennial, free)
- Setup fees
- Stock control
- Service provisioning status tracking (pending → active → suspended → terminated)

### 💰 Invoice & Billing
- Invoice creation with line items
- Tax calculation
- Payment application
- Credit application
- Invoice status tracking (draft, unpaid, paid, cancelled, refunded, overdue)
- Transaction recording

### 🛒 Order Processing
- Order placement with product selection
- Automatic invoice generation
- Order acceptance/cancellation workflow
- Promo code support
- Fraud tracking field

### 🎫 Support Tickets
- Multi-department ticket system
- Ticket creation, replies, and closing
- Priority levels (low, medium, high, urgent)
- Status tracking (open, answered, customer-reply, closed, on-hold)
- Admin assignment
- Ticket notes (internal)

### 🌐 Domain Management
- Domain registration and transfer tracking
- Nameserver management
- Auto-renewal control
- ID protection
- Expiry tracking

### 📢 Additional Features
- **Announcements**: Public announcements system
- **Knowledge Base**: Categories and articles with view tracking
- **Affiliates**: Commission tracking, visitor/conversion counting
- **Promotions**: Percentage and fixed-amount codes with usage limits and expiration
- **Activity Logging**: Full audit trail of all admin/client actions
- **Dashboard Stats**: Revenue, client counts, ticket counts, and more
- **Currency Support**: Multi-currency with exchange rates

## API Endpoints

### Public (No Auth)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/health` | Health check |
| POST | `/api/v1/auth/admin/login` | Admin login |
| POST | `/api/v1/auth/client/login` | Client login |
| POST | `/api/v1/auth/client/register` | Client registration |
| GET | `/api/v1/products` | List products |
| GET | `/api/v1/products/{id}` | Get product |
| GET | `/api/v1/product-groups` | List product groups |
| GET | `/api/v1/currencies` | List currencies |
| GET | `/api/v1/announcements` | List announcements |
| GET | `/api/v1/promo/validate?code=X` | Validate promo code |
| GET | `/api/v1/kb/categories` | List KB categories |
| GET | `/api/v1/kb/articles?category_id=X` | List KB articles |
| GET | `/api/v1/kb/articles/{id}` | Get KB article |
| GET | `/api/v1/ticket-departments` | List ticket departments |

### Authenticated (Client or Admin)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/clients/{id}` | Get client profile |
| PUT | `/api/v1/clients/{id}` | Update client profile |
| GET | `/api/v1/clients/{id}/invoices` | Client invoices |
| GET | `/api/v1/clients/{id}/services` | Client services |
| GET | `/api/v1/clients/{id}/domains` | Client domains |
| GET | `/api/v1/invoices/{id}` | Get invoice detail |
| POST | `/api/v1/orders` | Place order |
| GET | `/api/v1/orders/{id}` | Get order |
| POST | `/api/v1/tickets` | Open ticket |
| GET | `/api/v1/tickets/{id}` | Get ticket + replies |
| POST | `/api/v1/tickets/{id}/reply` | Reply to ticket |
| POST | `/api/v1/tickets/{id}/close` | Close ticket |
| GET | `/api/v1/my/tickets` | List my tickets |
| GET | `/api/v1/services/{id}` | Get service |
| GET | `/api/v1/domains/{id}` | Get domain |

### Admin Only
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/admin/register` | Register admin |
| GET | `/api/v1/admin/dashboard` | Dashboard stats |
| GET | `/api/v1/admin/clients` | List all clients |
| DELETE | `/api/v1/admin/clients/{id}` | Delete client |
| POST | `/api/v1/admin/clients/{id}/credit` | Add client credit |
| POST | `/api/v1/admin/products` | Create product |
| PUT | `/api/v1/admin/products/{id}` | Update product |
| DELETE | `/api/v1/admin/products/{id}` | Delete product |
| POST | `/api/v1/admin/product-groups` | Create product group |
| POST | `/api/v1/admin/invoices` | Create invoice |
| GET | `/api/v1/admin/invoices` | List all invoices |
| POST | `/api/v1/admin/invoices/{id}/payment` | Apply payment |
| POST | `/api/v1/admin/invoices/{id}/credit` | Apply credit |
| GET | `/api/v1/admin/orders` | List all orders |
| POST | `/api/v1/admin/orders/{id}/accept` | Accept order |
| POST | `/api/v1/admin/orders/{id}/cancel` | Cancel order |
| GET | `/api/v1/admin/tickets` | List all tickets |
| PUT | `/api/v1/admin/services/{id}/status` | Update service status |
| POST | `/api/v1/admin/domains` | Create domain |
| GET | `/api/v1/admin/promotions` | List promotions |
| POST | `/api/v1/admin/promotions` | Create promotion |
| POST | `/api/v1/admin/announcements` | Create announcement |
| GET | `/api/v1/admin/activity-log` | View audit log |
| GET | `/api/v1/admin/affiliates` | List affiliates |
| POST | `/api/v1/admin/affiliates` | Activate affiliate |

## Security Design

1. **Authentication**: JWT tokens with HMAC-SHA256 signing, configurable expiration
2. **Password Security**: bcrypt with configurable cost factor (default 12)
3. **Rate Limiting**: Per-IP sliding window with configurable limits
4. **Account Lockout**: Automatic lockout after N failed login attempts
5. **Input Sanitization**: All user input sanitized against XSS
6. **Authorization**: Resource-level checks (clients can only access own data)
7. **RBAC**: Admin roles with permission strings
8. **Security Headers**: HSTS, CSP, X-Frame-Options, X-Content-Type-Options
9. **SQL Safety**: Parameterized queries throughout (no string concatenation)
10. **Panic Recovery**: Server continues operating after unexpected panics

## Business Logic Rules

1. **Orders**: Product stock is checked before order creation; retired products cannot be ordered
2. **Invoices**: Tax is calculated only on taxable line items; credit application respects available balance
3. **Payments**: Double-payment prevention; invoices auto-marked as paid when full amount received
4. **Tickets**: Clients can only access/reply to their own tickets; admin replies set status to "answered"
5. **Services**: Follow lifecycle: pending → active → suspended → terminated → cancelled
6. **Promotions**: Usage count tracked; max uses and expiration dates enforced
7. **Affiliates**: Separate tracking of visitors and conversions with balance management

## Technology Stack

- **Language**: Go 1.22+
- **Database**: SQLite (zero-config, portable) — easily swappable to PostgreSQL/MySQL
- **Router**: Go 1.22 native `net/http` ServeMux with pattern matching
- **Auth**: JWT (github.com/golang-jwt/jwt/v5)
- **Password**: bcrypt (golang.org/x/crypto/bcrypt)
- **Database Driver**: github.com/mattn/go-sqlite3
