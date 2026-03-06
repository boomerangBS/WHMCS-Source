package main

import (
	"fmt"
	"log"
	"net/http"
	"time"

	_ "github.com/mattn/go-sqlite3"

	"whmcs-rebuild/internal/config"
	"whmcs-rebuild/internal/database"
	"whmcs-rebuild/internal/handlers"
	"whmcs-rebuild/internal/middleware"
	"whmcs-rebuild/internal/repository"
	"whmcs-rebuild/internal/services"
)

func main() {
	cfg := config.Load()

	// Database
	db, err := database.New(cfg.Database.Driver, cfg.Database.DSN)
	if err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}
	defer db.Close()

	if err := database.RunMigrations(db); err != nil {
		log.Fatalf("Failed to run migrations: %v", err)
	}

	// Repositories
	adminRepo := repository.NewAdminRepo(db)
	clientRepo := repository.NewClientRepo(db)
	productRepo := repository.NewProductRepo(db)
	productGroupRepo := repository.NewProductGroupRepo(db)
	invoiceRepo := repository.NewInvoiceRepo(db)
	invoiceItemRepo := repository.NewInvoiceItemRepo(db)
	orderRepo := repository.NewOrderRepo(db)
	ticketRepo := repository.NewTicketRepo(db)
	ticketReplyRepo := repository.NewTicketReplyRepo(db)
	ticketDeptRepo := repository.NewTicketDeptRepo(db)
	domainRepo := repository.NewDomainRepo(db)
	serviceRepo := repository.NewServiceRepo(db)
	transactionRepo := repository.NewTransactionRepo(db)
	activityLogRepo := repository.NewActivityLogRepo(db)
	currencyRepo := repository.NewCurrencyRepo(db)
	promotionRepo := repository.NewPromotionRepo(db)
	announcementRepo := repository.NewAnnouncementRepo(db)
	affiliateRepo := repository.NewAffiliateRepo(db)
	kbCategoryRepo := repository.NewKBCategoryRepo(db)
	kbArticleRepo := repository.NewKBArticleRepo(db)

	// Services
	authSvc := services.NewAuthService(cfg, adminRepo, clientRepo, activityLogRepo)
	clientSvc := services.NewClientService(cfg, clientRepo, activityLogRepo)
	invoiceSvc := services.NewInvoiceService(invoiceRepo, invoiceItemRepo, clientRepo, transactionRepo, activityLogRepo)
	orderSvc := services.NewOrderService(orderRepo, serviceRepo, productRepo, invoiceSvc, activityLogRepo)
	ticketSvc := services.NewTicketService(ticketRepo, ticketReplyRepo, activityLogRepo)
	dashboardSvc := services.NewDashboardService(db)

	// Handlers
	authHandler := handlers.NewAuthHandler(authSvc)
	clientHandler := handlers.NewClientHandler(clientSvc, clientRepo)
	productHandler := handlers.NewProductHandler(productRepo, productGroupRepo)
	invoiceHandler := handlers.NewInvoiceHandler(invoiceSvc, invoiceRepo, invoiceItemRepo)
	orderHandler := handlers.NewOrderHandler(orderSvc, orderRepo)
	ticketHandler := handlers.NewTicketHandler(ticketSvc, ticketRepo, ticketReplyRepo, ticketDeptRepo)
	domainHandler := handlers.NewDomainHandler(domainRepo)
	serviceHandler := handlers.NewServiceHandler(serviceRepo)
	dashboardHandler := handlers.NewDashboardHandler(dashboardSvc)
	miscHandler := handlers.NewMiscHandler(currencyRepo, promotionRepo, announcementRepo, activityLogRepo, affiliateRepo, kbCategoryRepo, kbArticleRepo)

	// Middleware
	rateLimiter := middleware.NewRateLimiter(cfg.Security.RateLimitPerMin, time.Minute)
	authMW := middleware.Auth(cfg.JWT.Secret)

	// Router (Go 1.22+ native ServeMux with patterns)
	mux := http.NewServeMux()

	// Health check
	mux.HandleFunc("GET /api/v1/health", handlers.HealthCheck)

	// ===== Public Routes =====
	mux.HandleFunc("POST /api/v1/auth/admin/login", authHandler.AdminLogin)
	mux.HandleFunc("POST /api/v1/auth/client/login", authHandler.ClientLogin)
	mux.HandleFunc("POST /api/v1/auth/client/register", clientHandler.Register)

	// Public content
	mux.HandleFunc("GET /api/v1/announcements", miscHandler.ListAnnouncements)
	mux.HandleFunc("GET /api/v1/products", productHandler.List)
	mux.HandleFunc("GET /api/v1/products/{id}", productHandler.Get)
	mux.HandleFunc("GET /api/v1/product-groups", productHandler.ListGroups)
	mux.HandleFunc("GET /api/v1/currencies", miscHandler.ListCurrencies)
	mux.HandleFunc("GET /api/v1/promo/validate", miscHandler.ValidatePromo)
	mux.HandleFunc("GET /api/v1/kb/categories", miscHandler.ListKBCategories)
	mux.HandleFunc("GET /api/v1/kb/articles", miscHandler.ListKBArticles)
	mux.HandleFunc("GET /api/v1/kb/articles/{id}", miscHandler.GetKBArticle)
	mux.HandleFunc("GET /api/v1/ticket-departments", ticketHandler.ListDepartments)

	// ===== Authenticated Routes =====
	// Client-facing endpoints (client or admin)
	mux.Handle("GET /api/v1/clients/{id}", authMW(http.HandlerFunc(clientHandler.Get)))
	mux.Handle("PUT /api/v1/clients/{id}", authMW(http.HandlerFunc(clientHandler.Update)))
	mux.Handle("GET /api/v1/clients/{client_id}/invoices", authMW(http.HandlerFunc(invoiceHandler.ListByClient)))
	mux.Handle("GET /api/v1/clients/{client_id}/services", authMW(http.HandlerFunc(serviceHandler.ListByClient)))
	mux.Handle("GET /api/v1/clients/{client_id}/domains", authMW(http.HandlerFunc(domainHandler.ListByClient)))

	// Invoices
	mux.Handle("GET /api/v1/invoices/{id}", authMW(http.HandlerFunc(invoiceHandler.Get)))

	// Orders
	mux.Handle("POST /api/v1/orders", authMW(http.HandlerFunc(orderHandler.Create)))
	mux.Handle("GET /api/v1/orders/{id}", authMW(http.HandlerFunc(orderHandler.Get)))

	// Tickets
	mux.Handle("POST /api/v1/tickets", authMW(http.HandlerFunc(ticketHandler.Open)))
	mux.Handle("GET /api/v1/tickets/{id}", authMW(http.HandlerFunc(ticketHandler.Get)))
	mux.Handle("POST /api/v1/tickets/{id}/reply", authMW(http.HandlerFunc(ticketHandler.Reply)))
	mux.Handle("POST /api/v1/tickets/{id}/close", authMW(http.HandlerFunc(ticketHandler.Close)))
	mux.Handle("GET /api/v1/my/tickets", authMW(http.HandlerFunc(ticketHandler.ListByClient)))

	// Services
	mux.Handle("GET /api/v1/services/{id}", authMW(http.HandlerFunc(serviceHandler.Get)))

	// Domains
	mux.Handle("GET /api/v1/domains/{id}", authMW(http.HandlerFunc(domainHandler.Get)))

	// ===== Admin-Only Routes =====
	adminMW := func(h http.Handler) http.Handler {
		return authMW(middleware.AdminOnly(h))
	}

	// Admin management
	mux.Handle("POST /api/v1/admin/register", adminMW(http.HandlerFunc(authHandler.RegisterAdmin)))

	// Dashboard
	mux.Handle("GET /api/v1/admin/dashboard", adminMW(http.HandlerFunc(dashboardHandler.Stats)))

	// Client management (admin)
	mux.Handle("GET /api/v1/admin/clients", adminMW(http.HandlerFunc(clientHandler.List)))
	mux.Handle("DELETE /api/v1/admin/clients/{id}", adminMW(http.HandlerFunc(clientHandler.Delete)))
	mux.Handle("POST /api/v1/admin/clients/{id}/credit", adminMW(http.HandlerFunc(clientHandler.AddCredit)))

	// Product management (admin)
	mux.Handle("POST /api/v1/admin/products", adminMW(http.HandlerFunc(productHandler.Create)))
	mux.Handle("PUT /api/v1/admin/products/{id}", adminMW(http.HandlerFunc(productHandler.Update)))
	mux.Handle("DELETE /api/v1/admin/products/{id}", adminMW(http.HandlerFunc(productHandler.Delete)))
	mux.Handle("POST /api/v1/admin/product-groups", adminMW(http.HandlerFunc(productHandler.CreateGroup)))

	// Invoice management (admin)
	mux.Handle("POST /api/v1/admin/invoices", adminMW(http.HandlerFunc(invoiceHandler.Create)))
	mux.Handle("GET /api/v1/admin/invoices", adminMW(http.HandlerFunc(invoiceHandler.ListAll)))
	mux.Handle("POST /api/v1/admin/invoices/{id}/payment", adminMW(http.HandlerFunc(invoiceHandler.ApplyPayment)))
	mux.Handle("POST /api/v1/admin/invoices/{id}/credit", adminMW(http.HandlerFunc(invoiceHandler.ApplyCredit)))

	// Order management (admin)
	mux.Handle("GET /api/v1/admin/orders", adminMW(http.HandlerFunc(orderHandler.ListAll)))
	mux.Handle("POST /api/v1/admin/orders/{id}/accept", adminMW(http.HandlerFunc(orderHandler.Accept)))
	mux.Handle("POST /api/v1/admin/orders/{id}/cancel", adminMW(http.HandlerFunc(orderHandler.Cancel)))

	// Ticket management (admin)
	mux.Handle("GET /api/v1/admin/tickets", adminMW(http.HandlerFunc(ticketHandler.ListAll)))

	// Service management (admin)
	mux.Handle("PUT /api/v1/admin/services/{id}/status", adminMW(http.HandlerFunc(serviceHandler.UpdateStatus)))

	// Domain management (admin)
	mux.Handle("POST /api/v1/admin/domains", adminMW(http.HandlerFunc(domainHandler.Create)))

	// Promotions (admin)
	mux.Handle("GET /api/v1/admin/promotions", adminMW(http.HandlerFunc(miscHandler.ListPromotions)))
	mux.Handle("POST /api/v1/admin/promotions", adminMW(http.HandlerFunc(miscHandler.CreatePromotion)))

	// Announcements (admin)
	mux.Handle("POST /api/v1/admin/announcements", adminMW(http.HandlerFunc(miscHandler.CreateAnnouncement)))

	// Activity log (admin)
	mux.Handle("GET /api/v1/admin/activity-log", adminMW(http.HandlerFunc(miscHandler.ListActivityLog)))

	// Affiliates (admin)
	mux.Handle("GET /api/v1/admin/affiliates", adminMW(http.HandlerFunc(miscHandler.ListAffiliates)))
	mux.Handle("POST /api/v1/admin/affiliates", adminMW(http.HandlerFunc(miscHandler.ActivateAffiliate)))

	// Apply middleware chain
	handler := middleware.Recovery(
		middleware.Logger(
			middleware.SecurityHeaders(
				middleware.CORS(
					rateLimiter.Middleware(mux),
				),
			),
		),
	)

	// Server
	addr := fmt.Sprintf("%s:%d", cfg.Server.Host, cfg.Server.Port)
	srv := &http.Server{
		Addr:         addr,
		Handler:      handler,
		ReadTimeout:  cfg.Server.ReadTimeout,
		WriteTimeout: cfg.Server.WriteTimeout,
		IdleTimeout:  cfg.Server.IdleTimeout,
	}

	log.Printf("=== WHMCS Rebuild API Server ===")
	log.Printf("Listening on %s", addr)
	log.Printf("API Base: http://%s/api/v1", addr)
	log.Printf("Health: http://%s/api/v1/health", addr)
	log.Printf("================================")

	if err := srv.ListenAndServe(); err != nil {
		log.Fatalf("Server failed: %v", err)
	}
}
