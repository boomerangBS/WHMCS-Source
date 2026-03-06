package handlers

import (
	"net/http"
	"strconv"
	"time"
	"whmcs-rebuild/internal/middleware"
	"whmcs-rebuild/internal/models"
	"whmcs-rebuild/internal/repository"
	"whmcs-rebuild/internal/services"
	"whmcs-rebuild/internal/utils"
)

// ==================== Auth Handlers ====================

type AuthHandler struct {
	AuthSvc *services.AuthService
}

func NewAuthHandler(svc *services.AuthService) *AuthHandler {
	return &AuthHandler{AuthSvc: svc}
}

func (h *AuthHandler) AdminLogin(w http.ResponseWriter, r *http.Request) {
	var req models.LoginRequest
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if req.Email == "" || req.Password == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "email and password are required")
		return
	}
	resp, err := h.AuthSvc.AdminLogin(req.Email, req.Password, utils.GetClientIP(r))
	if err != nil {
		utils.ErrorJSON(w, http.StatusUnauthorized, err.Error())
		return
	}
	utils.SuccessJSON(w, resp)
}

func (h *AuthHandler) ClientLogin(w http.ResponseWriter, r *http.Request) {
	var req models.LoginRequest
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if req.Email == "" || req.Password == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "email and password are required")
		return
	}
	resp, err := h.AuthSvc.ClientLogin(req.Email, req.Password, utils.GetClientIP(r))
	if err != nil {
		utils.ErrorJSON(w, http.StatusUnauthorized, err.Error())
		return
	}
	utils.SuccessJSON(w, resp)
}

func (h *AuthHandler) RegisterAdmin(w http.ResponseWriter, r *http.Request) {
	var req struct {
		Username  string `json:"username"`
		Email     string `json:"email"`
		Password  string `json:"password"`
		FirstName string `json:"first_name"`
		LastName  string `json:"last_name"`
		RoleID    int64  `json:"role_id"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if req.Username == "" || req.Email == "" || req.Password == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "username, email and password are required")
		return
	}
	if req.RoleID == 0 {
		req.RoleID = 1
	}
	admin, err := h.AuthSvc.RegisterAdmin(req.Username, req.Email, req.Password, req.FirstName, req.LastName, req.RoleID)
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: admin})
}

// ==================== Client Handlers ====================

type ClientHandler struct {
	ClientSvc  *services.ClientService
	ClientRepo *repository.ClientRepo
}

func NewClientHandler(svc *services.ClientService, repo *repository.ClientRepo) *ClientHandler {
	return &ClientHandler{ClientSvc: svc, ClientRepo: repo}
}

func (h *ClientHandler) Register(w http.ResponseWriter, r *http.Request) {
	var req struct {
		FirstName   string `json:"first_name"`
		LastName    string `json:"last_name"`
		Email       string `json:"email"`
		Password    string `json:"password"`
		CompanyName string `json:"company_name"`
		Address1    string `json:"address1"`
		City        string `json:"city"`
		State       string `json:"state"`
		PostCode    string `json:"postcode"`
		Country     string `json:"country"`
		Phone       string `json:"phone"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	client, err := h.ClientSvc.RegisterClient(req.FirstName, req.LastName, req.Email, req.Password, req.CompanyName, req.Address1, req.City, req.State, req.PostCode, req.Country, req.Phone, utils.GetClientIP(r))
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: client})
}

func (h *ClientHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid client id")
		return
	}

	// Authorization: clients can only view their own data
	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != id {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	client, err := h.ClientRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "client not found")
		return
	}
	utils.SuccessJSON(w, client)
}

func (h *ClientHandler) Update(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid client id")
		return
	}

	existing, err := h.ClientRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "client not found")
		return
	}

	// Authorization
	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != id {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	var req struct {
		FirstName   *string `json:"first_name"`
		LastName    *string `json:"last_name"`
		CompanyName *string `json:"company_name"`
		Address1    *string `json:"address1"`
		Address2    *string `json:"address2"`
		City        *string `json:"city"`
		State       *string `json:"state"`
		PostCode    *string `json:"postcode"`
		Country     *string `json:"country"`
		Phone       *string `json:"phone"`
		Status      *string `json:"status"`
		Notes       *string `json:"notes"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}

	if req.FirstName != nil {
		existing.FirstName = *req.FirstName
	}
	if req.LastName != nil {
		existing.LastName = *req.LastName
	}
	if req.CompanyName != nil {
		existing.CompanyName = *req.CompanyName
	}
	if req.Address1 != nil {
		existing.Address1 = *req.Address1
	}
	if req.Address2 != nil {
		existing.Address2 = *req.Address2
	}
	if req.City != nil {
		existing.City = *req.City
	}
	if req.State != nil {
		existing.State = *req.State
	}
	if req.PostCode != nil {
		existing.PostCode = *req.PostCode
	}
	if req.Country != nil {
		existing.Country = *req.Country
	}
	if req.Phone != nil {
		existing.PhoneNumber = *req.Phone
	}
	if req.Status != nil && claims.UserType == "admin" {
		existing.Status = *req.Status
	}
	if req.Notes != nil && claims.UserType == "admin" {
		existing.Notes = *req.Notes
	}

	adminID := int64(0)
	if claims.UserType == "admin" {
		adminID = claims.UserID
	}

	if err := h.ClientSvc.UpdateClient(existing, adminID, utils.GetClientIP(r)); err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, err.Error())
		return
	}
	utils.SuccessJSON(w, existing)
}

func (h *ClientHandler) Delete(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid client id")
		return
	}
	if err := h.ClientRepo.Delete(id); err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to delete client")
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "client deleted"})
}

func (h *ClientHandler) List(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	search := r.URL.Query().Get("search")
	status := r.URL.Query().Get("status")

	clients, total, err := h.ClientRepo.List(page, perPage, search, status)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list clients")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{
		Data:       clients,
		Total:      total,
		Page:       page,
		PerPage:    perPage,
		TotalPages: totalPages,
	})
}

func (h *ClientHandler) AddCredit(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid client id")
		return
	}
	var req struct {
		Amount float64 `json:"amount"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}

	claims := middleware.GetClaims(r)
	if err := h.ClientSvc.AddCredit(id, req.Amount, claims.UserID, utils.GetClientIP(r)); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "credit added"})
}

// ==================== Product Handlers ====================

type ProductHandler struct {
	ProductRepo      *repository.ProductRepo
	ProductGroupRepo *repository.ProductGroupRepo
}

func NewProductHandler(repo *repository.ProductRepo, grpRepo *repository.ProductGroupRepo) *ProductHandler {
	return &ProductHandler{ProductRepo: repo, ProductGroupRepo: grpRepo}
}

func (h *ProductHandler) Create(w http.ResponseWriter, r *http.Request) {
	var p models.Product
	if err := utils.DecodeJSON(r, &p); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if p.Name == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "product name is required")
		return
	}
	p.Name = utils.SanitizeString(p.Name)
	p.Description = utils.SanitizeString(p.Description)

	id, err := h.ProductRepo.Create(&p)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to create product")
		return
	}
	p.ID = id
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: p})
}

func (h *ProductHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid product id")
		return
	}
	product, err := h.ProductRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "product not found")
		return
	}
	utils.SuccessJSON(w, product)
}

func (h *ProductHandler) Update(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid product id")
		return
	}
	existing, err := h.ProductRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "product not found")
		return
	}

	var p models.Product
	if err := utils.DecodeJSON(r, &p); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	p.ID = existing.ID
	p.Name = utils.SanitizeString(p.Name)
	p.Description = utils.SanitizeString(p.Description)
	if p.Name == "" {
		p.Name = existing.Name
	}

	if err := h.ProductRepo.Update(&p); err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to update product")
		return
	}
	utils.SuccessJSON(w, p)
}

func (h *ProductHandler) Delete(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid product id")
		return
	}
	if err := h.ProductRepo.Delete(id); err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to delete product")
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "product deleted"})
}

func (h *ProductHandler) List(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	var groupID int64
	if gid := r.URL.Query().Get("group_id"); gid != "" {
		groupID, _ = strconv.ParseInt(gid, 10, 64)
	}

	products, total, err := h.ProductRepo.List(page, perPage, groupID)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list products")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{
		Data:       products,
		Total:      total,
		Page:       page,
		PerPage:    perPage,
		TotalPages: totalPages,
	})
}

func (h *ProductHandler) ListGroups(w http.ResponseWriter, r *http.Request) {
	groups, err := h.ProductGroupRepo.List()
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list product groups")
		return
	}
	utils.SuccessJSON(w, groups)
}

func (h *ProductHandler) CreateGroup(w http.ResponseWriter, r *http.Request) {
	var pg models.ProductGroup
	if err := utils.DecodeJSON(r, &pg); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if pg.Name == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "group name is required")
		return
	}
	pg.Name = utils.SanitizeString(pg.Name)
	pg.Headline = utils.SanitizeString(pg.Headline)

	id, err := h.ProductGroupRepo.Create(&pg)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to create product group")
		return
	}
	pg.ID = id
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: pg})
}

// ==================== Invoice Handlers ====================

type InvoiceHandler struct {
	InvoiceSvc      *services.InvoiceService
	InvoiceRepo     *repository.InvoiceRepo
	InvoiceItemRepo *repository.InvoiceItemRepo
}

func NewInvoiceHandler(svc *services.InvoiceService, repo *repository.InvoiceRepo, itemRepo *repository.InvoiceItemRepo) *InvoiceHandler {
	return &InvoiceHandler{InvoiceSvc: svc, InvoiceRepo: repo, InvoiceItemRepo: itemRepo}
}

func (h *InvoiceHandler) Create(w http.ResponseWriter, r *http.Request) {
	var req struct {
		ClientID      int64  `json:"client_id"`
		DateDue       string `json:"date_due"`
		TaxRate       float64 `json:"tax_rate"`
		PaymentMethod string `json:"payment_method"`
		Notes         string `json:"notes"`
		Items         []struct {
			Type        string  `json:"type"`
			RelID       int64   `json:"rel_id"`
			Description string  `json:"description"`
			Amount      float64 `json:"amount"`
			Taxed       bool    `json:"taxed"`
		} `json:"items"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}

	var items []services.CreateInvoiceItemInput
	for _, i := range req.Items {
		items = append(items, services.CreateInvoiceItemInput{
			Type: i.Type, RelID: i.RelID, Description: i.Description, Amount: i.Amount, Taxed: i.Taxed,
		})
	}

	claims := middleware.GetClaims(r)
	inv, err := h.InvoiceSvc.CreateInvoice(services.CreateInvoiceInput{
		ClientID:      req.ClientID,
		TaxRate:       req.TaxRate,
		PaymentMethod: req.PaymentMethod,
		Notes:         req.Notes,
		Items:         items,
	}, claims.UserID, utils.GetClientIP(r))
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: inv})
}

func (h *InvoiceHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid invoice id")
		return
	}

	inv, err := h.InvoiceRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "invoice not found")
		return
	}

	// Authorization
	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != inv.ClientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	items, _ := h.InvoiceItemRepo.ListByInvoice(id)
	utils.SuccessJSON(w, map[string]any{"invoice": inv, "items": items})
}

func (h *InvoiceHandler) ListByClient(w http.ResponseWriter, r *http.Request) {
	clientID, err := utils.GetIntParam(r, "client_id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid client id")
		return
	}

	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != clientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	page, perPage := utils.GetPagination(r)
	status := r.URL.Query().Get("status")
	invoices, total, err := h.InvoiceRepo.ListByClient(clientID, page, perPage, status)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list invoices")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{Data: invoices, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

func (h *InvoiceHandler) ListAll(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	status := r.URL.Query().Get("status")
	invoices, total, err := h.InvoiceRepo.ListAll(page, perPage, status)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list invoices")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{Data: invoices, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

func (h *InvoiceHandler) ApplyPayment(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid invoice id")
		return
	}
	var req struct {
		Gateway       string  `json:"gateway"`
		TransactionID string  `json:"transaction_id"`
		Amount        float64 `json:"amount"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}

	claims := middleware.GetClaims(r)
	if err := h.InvoiceSvc.ApplyPayment(id, req.Gateway, req.TransactionID, req.Amount, claims.UserID, utils.GetClientIP(r)); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "payment applied"})
}

func (h *InvoiceHandler) ApplyCredit(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid invoice id")
		return
	}

	claims := middleware.GetClaims(r)
	if err := h.InvoiceSvc.ApplyCredit(id, claims.UserID, utils.GetClientIP(r)); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "credit applied"})
}

// ==================== Order Handlers ====================

type OrderHandler struct {
	OrderSvc  *services.OrderService
	OrderRepo *repository.OrderRepo
}

func NewOrderHandler(svc *services.OrderService, repo *repository.OrderRepo) *OrderHandler {
	return &OrderHandler{OrderSvc: svc, OrderRepo: repo}
}

func (h *OrderHandler) Create(w http.ResponseWriter, r *http.Request) {
	var req services.CreateOrderInput
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	req.IPAddress = utils.GetClientIP(r)

	claims := middleware.GetClaims(r)
	// If client is placing own order
	if claims.UserType == "client" {
		req.ClientID = claims.UserID
	}

	order, err := h.OrderSvc.CreateOrder(req, claims.UserID)
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: order})
}

func (h *OrderHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid order id")
		return
	}
	order, err := h.OrderRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "order not found")
		return
	}

	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != order.ClientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	utils.SuccessJSON(w, order)
}

func (h *OrderHandler) Accept(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid order id")
		return
	}
	claims := middleware.GetClaims(r)
	if err := h.OrderSvc.AcceptOrder(id, claims.UserID, utils.GetClientIP(r)); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "order accepted"})
}

func (h *OrderHandler) Cancel(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid order id")
		return
	}
	claims := middleware.GetClaims(r)
	if err := h.OrderSvc.CancelOrder(id, claims.UserID, utils.GetClientIP(r)); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "order cancelled"})
}

func (h *OrderHandler) ListAll(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	status := r.URL.Query().Get("status")
	orders, total, err := h.OrderRepo.ListAll(page, perPage, status)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list orders")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{Data: orders, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

// ==================== Ticket Handlers ====================

type TicketHandler struct {
	TicketSvc *services.TicketService
	TicketRepo *repository.TicketRepo
	ReplyRepo  *repository.TicketReplyRepo
	DeptRepo   *repository.TicketDeptRepo
}

func NewTicketHandler(svc *services.TicketService, repo *repository.TicketRepo, replyRepo *repository.TicketReplyRepo, deptRepo *repository.TicketDeptRepo) *TicketHandler {
	return &TicketHandler{TicketSvc: svc, TicketRepo: repo, ReplyRepo: replyRepo, DeptRepo: deptRepo}
}

func (h *TicketHandler) Open(w http.ResponseWriter, r *http.Request) {
	var req struct {
		DepartmentID int64  `json:"department_id"`
		Subject      string `json:"subject"`
		Message      string `json:"message"`
		Priority     string `json:"priority"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}

	claims := middleware.GetClaims(r)
	clientID := claims.UserID
	if claims.UserType == "admin" {
		// Admin can specify client_id in the request body; for now use 0
		clientID = 0
	}

	ticket, err := h.TicketSvc.OpenTicket(clientID, req.DepartmentID, req.Subject, req.Message, req.Priority, utils.GetClientIP(r))
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: ticket})
}

func (h *TicketHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid ticket id")
		return
	}

	ticket, err := h.TicketRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "ticket not found")
		return
	}

	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != ticket.ClientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	replies, _ := h.ReplyRepo.ListByTicket(id)
	utils.SuccessJSON(w, map[string]any{"ticket": ticket, "replies": replies})
}

func (h *TicketHandler) Reply(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid ticket id")
		return
	}

	var req struct {
		Message string `json:"message"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}

	claims := middleware.GetClaims(r)
	var clientID, adminID int64
	if claims.UserType == "admin" {
		adminID = claims.UserID
	} else {
		clientID = claims.UserID
	}

	reply, err := h.TicketSvc.ReplyToTicket(id, clientID, adminID, req.Message, utils.GetClientIP(r))
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: reply})
}

func (h *TicketHandler) Close(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid ticket id")
		return
	}

	claims := middleware.GetClaims(r)
	if err := h.TicketSvc.CloseTicket(id, claims.UserID, claims.UserType == "admin", utils.GetClientIP(r)); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, err.Error())
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "ticket closed"})
}

func (h *TicketHandler) ListByClient(w http.ResponseWriter, r *http.Request) {
	claims := middleware.GetClaims(r)
	clientID := claims.UserID

	page, perPage := utils.GetPagination(r)
	tickets, total, err := h.TicketRepo.ListByClient(clientID, page, perPage)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list tickets")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{Data: tickets, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

func (h *TicketHandler) ListAll(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	status := r.URL.Query().Get("status")
	priority := r.URL.Query().Get("priority")
	var deptID int64
	if d := r.URL.Query().Get("department_id"); d != "" {
		deptID, _ = strconv.ParseInt(d, 10, 64)
	}

	tickets, total, err := h.TicketRepo.ListAll(page, perPage, status, priority, deptID)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list tickets")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{Data: tickets, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

func (h *TicketHandler) ListDepartments(w http.ResponseWriter, r *http.Request) {
	depts, err := h.DeptRepo.List()
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list departments")
		return
	}
	utils.SuccessJSON(w, depts)
}

// ==================== Domain Handlers ====================

type DomainHandler struct {
	DomainRepo *repository.DomainRepo
}

func NewDomainHandler(repo *repository.DomainRepo) *DomainHandler {
	return &DomainHandler{DomainRepo: repo}
}

func (h *DomainHandler) Create(w http.ResponseWriter, r *http.Request) {
	var d models.Domain
	if err := utils.DecodeJSON(r, &d); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if d.DomainName == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "domain name is required")
		return
	}

	id, err := h.DomainRepo.Create(&d)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to create domain")
		return
	}
	d.ID = id
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: d})
}

func (h *DomainHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid domain id")
		return
	}
	domain, err := h.DomainRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "domain not found")
		return
	}

	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != domain.ClientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	utils.SuccessJSON(w, domain)
}

func (h *DomainHandler) ListByClient(w http.ResponseWriter, r *http.Request) {
	clientID, err := utils.GetIntParam(r, "client_id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid client id")
		return
	}

	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != clientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	page, perPage := utils.GetPagination(r)
	domains, total, err := h.DomainRepo.ListByClient(clientID, page, perPage)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list domains")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{Data: domains, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

// ==================== Service Handlers ====================

type ServiceHandler struct {
	ServiceRepo *repository.ServiceRepo
}

func NewServiceHandler(repo *repository.ServiceRepo) *ServiceHandler {
	return &ServiceHandler{ServiceRepo: repo}
}

func (h *ServiceHandler) Get(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid service id")
		return
	}
	svc, err := h.ServiceRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "service not found")
		return
	}

	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != svc.ClientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	utils.SuccessJSON(w, svc)
}

func (h *ServiceHandler) UpdateStatus(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid service id")
		return
	}
	var req struct {
		Status string `json:"status"`
		Reason string `json:"reason"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if err := h.ServiceRepo.UpdateStatus(id, req.Status, req.Reason); err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to update service status")
		return
	}
	utils.SuccessJSON(w, map[string]string{"message": "service status updated"})
}

func (h *ServiceHandler) ListByClient(w http.ResponseWriter, r *http.Request) {
	clientID, err := utils.GetIntParam(r, "client_id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid client id")
		return
	}

	claims := middleware.GetClaims(r)
	if claims.UserType == "client" && claims.UserID != clientID {
		utils.ErrorJSON(w, http.StatusForbidden, "access denied")
		return
	}

	page, perPage := utils.GetPagination(r)
	services, total, err := h.ServiceRepo.ListByClient(clientID, page, perPage)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list services")
		return
	}

	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}

	utils.SuccessJSON(w, models.PaginatedResponse{Data: services, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

// ==================== Dashboard / Admin Handlers ====================

type DashboardHandler struct {
	DashSvc *services.DashboardService
}

func NewDashboardHandler(svc *services.DashboardService) *DashboardHandler {
	return &DashboardHandler{DashSvc: svc}
}

func (h *DashboardHandler) Stats(w http.ResponseWriter, r *http.Request) {
	stats, err := h.DashSvc.GetStats()
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to get stats")
		return
	}
	utils.SuccessJSON(w, stats)
}

// ==================== Misc Handlers ====================

type MiscHandler struct {
	CurrencyRepo     *repository.CurrencyRepo
	PromotionRepo    *repository.PromotionRepo
	AnnouncementRepo *repository.AnnouncementRepo
	ActivityLogRepo  *repository.ActivityLogRepo
	AffiliateRepo    *repository.AffiliateRepo
	KBCategoryRepo   *repository.KBCategoryRepo
	KBArticleRepo    *repository.KBArticleRepo
}

func NewMiscHandler(
	currRepo *repository.CurrencyRepo,
	promoRepo *repository.PromotionRepo,
	annRepo *repository.AnnouncementRepo,
	logRepo *repository.ActivityLogRepo,
	affRepo *repository.AffiliateRepo,
	kbCatRepo *repository.KBCategoryRepo,
	kbArtRepo *repository.KBArticleRepo,
) *MiscHandler {
	return &MiscHandler{
		CurrencyRepo:     currRepo,
		PromotionRepo:    promoRepo,
		AnnouncementRepo: annRepo,
		ActivityLogRepo:  logRepo,
		AffiliateRepo:    affRepo,
		KBCategoryRepo:   kbCatRepo,
		KBArticleRepo:    kbArtRepo,
	}
}

func (h *MiscHandler) ListCurrencies(w http.ResponseWriter, r *http.Request) {
	currencies, err := h.CurrencyRepo.List()
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list currencies")
		return
	}
	utils.SuccessJSON(w, currencies)
}

func (h *MiscHandler) ListPromotions(w http.ResponseWriter, r *http.Request) {
	promos, err := h.PromotionRepo.List()
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list promotions")
		return
	}
	utils.SuccessJSON(w, promos)
}

func (h *MiscHandler) CreatePromotion(w http.ResponseWriter, r *http.Request) {
	var p models.Promotion
	if err := utils.DecodeJSON(r, &p); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if p.Code == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "promotion code is required")
		return
	}
	id, err := h.PromotionRepo.Create(&p)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to create promotion")
		return
	}
	p.ID = id
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: p})
}

func (h *MiscHandler) ValidatePromo(w http.ResponseWriter, r *http.Request) {
	code := r.URL.Query().Get("code")
	if code == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "code is required")
		return
	}
	promo, err := h.PromotionRepo.GetByCode(code)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "invalid promo code")
		return
	}
	if promo.MaxUses > 0 && promo.Uses >= promo.MaxUses {
		utils.ErrorJSON(w, http.StatusBadRequest, "promo code usage limit reached")
		return
	}
	if promo.ExpirationDate != nil && promo.ExpirationDate.Before(time.Now()) {
		utils.ErrorJSON(w, http.StatusBadRequest, "promo code has expired")
		return
	}
	utils.SuccessJSON(w, promo)
}

func (h *MiscHandler) ListAnnouncements(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	anns, total, err := h.AnnouncementRepo.ListPublished(page, perPage)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list announcements")
		return
	}
	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}
	utils.SuccessJSON(w, models.PaginatedResponse{Data: anns, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

func (h *MiscHandler) CreateAnnouncement(w http.ResponseWriter, r *http.Request) {
	var a models.Announcement
	if err := utils.DecodeJSON(r, &a); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	if a.Title == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "title is required")
		return
	}
	a.Title = utils.SanitizeString(a.Title)
	a.Body = utils.SanitizeString(a.Body)

	id, err := h.AnnouncementRepo.Create(&a)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to create announcement")
		return
	}
	a.ID = id
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: a})
}

func (h *MiscHandler) ListActivityLog(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	logs, total, err := h.ActivityLogRepo.List(page, perPage)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list activity log")
		return
	}
	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}
	utils.SuccessJSON(w, models.PaginatedResponse{Data: logs, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

func (h *MiscHandler) ListAffiliates(w http.ResponseWriter, r *http.Request) {
	page, perPage := utils.GetPagination(r)
	affiliates, total, err := h.AffiliateRepo.List(page, perPage)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list affiliates")
		return
	}
	totalPages := int(total) / perPage
	if int(total)%perPage > 0 {
		totalPages++
	}
	utils.SuccessJSON(w, models.PaginatedResponse{Data: affiliates, Total: total, Page: page, PerPage: perPage, TotalPages: totalPages})
}

func (h *MiscHandler) ActivateAffiliate(w http.ResponseWriter, r *http.Request) {
	var req struct {
		ClientID  int64   `json:"client_id"`
		PayType   string  `json:"pay_type"`
		PayAmount float64 `json:"pay_amount"`
	}
	if err := utils.DecodeJSON(r, &req); err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid request body")
		return
	}
	aff := &models.Affiliate{ClientID: req.ClientID, PayType: req.PayType, PayAmount: req.PayAmount}
	id, err := h.AffiliateRepo.Create(aff)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to activate affiliate")
		return
	}
	aff.ID = id
	utils.JSON(w, http.StatusCreated, models.APIResponse{Success: true, Data: aff})
}

func (h *MiscHandler) ListKBCategories(w http.ResponseWriter, r *http.Request) {
	cats, err := h.KBCategoryRepo.List()
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list KB categories")
		return
	}
	utils.SuccessJSON(w, cats)
}

func (h *MiscHandler) GetKBArticle(w http.ResponseWriter, r *http.Request) {
	id, err := utils.GetIntParam(r, "id")
	if err != nil {
		utils.ErrorJSON(w, http.StatusBadRequest, "invalid article id")
		return
	}
	article, err := h.KBArticleRepo.GetByID(id)
	if err != nil {
		utils.ErrorJSON(w, http.StatusNotFound, "article not found")
		return
	}
	h.KBArticleRepo.IncrementViews(id)
	utils.SuccessJSON(w, article)
}

func (h *MiscHandler) ListKBArticles(w http.ResponseWriter, r *http.Request) {
	catIDStr := r.URL.Query().Get("category_id")
	if catIDStr == "" {
		utils.ErrorJSON(w, http.StatusBadRequest, "category_id is required")
		return
	}
	catID, _ := strconv.ParseInt(catIDStr, 10, 64)
	articles, err := h.KBArticleRepo.ListByCategory(catID)
	if err != nil {
		utils.ErrorJSON(w, http.StatusInternalServerError, "failed to list KB articles")
		return
	}
	utils.SuccessJSON(w, articles)
}

// ==================== Health Check ====================

func HealthCheck(w http.ResponseWriter, r *http.Request) {
	utils.SuccessJSON(w, map[string]string{
		"status":  "ok",
		"version": "1.0.0",
		"service": "whmcs-rebuild",
	})
}
