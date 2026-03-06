const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8080/api/v1";

interface RequestOptions {
  method?: string;
  body?: unknown;
  headers?: Record<string, string>;
}

class ApiError extends Error {
  status: number;
  data: unknown;
  constructor(message: string, status: number, data?: unknown) {
    super(message);
    this.status = status;
    this.data = data;
  }
}

function getToken(): string | null {
  return localStorage.getItem("token");
}

async function request<T>(endpoint: string, opts: RequestOptions = {}): Promise<T> {
  const { method = "GET", body, headers = {} } = opts;
  const token = getToken();

  const config: RequestInit = {
    method,
    headers: {
      "Content-Type": "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...headers,
    },
  };

  if (body) {
    config.body = JSON.stringify(body);
  }

  const res = await fetch(`${API_BASE}${endpoint}`, config);

  if (!res.ok) {
    let data: unknown;
    try {
      data = await res.json();
    } catch {
      data = null;
    }
    throw new ApiError(
      (data as { error?: string })?.error || res.statusText,
      res.status,
      data
    );
  }

  if (res.status === 204) return {} as T;
  return res.json();
}

// ─── Auth ────────────────────────────────────────
export const authApi = {
  adminLogin: (email: string, password: string) =>
    request<{ token: string; expires_at: number; user: unknown }>("/auth/admin/login", {
      method: "POST",
      body: { email, password },
    }),
  clientLogin: (email: string, password: string) =>
    request<{ token: string; expires_at: number; user: unknown }>("/auth/client/login", {
      method: "POST",
      body: { email, password },
    }),
  clientRegister: (data: Record<string, unknown>) =>
    request("/auth/client/register", { method: "POST", body: data }),
};

// ─── Dashboard ───────────────────────────────────
export const dashboardApi = {
  stats: () => request<{ data: unknown }>("/admin/dashboard"),
};

// ─── Clients ─────────────────────────────────────
export const clientsApi = {
  list: (page = 1) => request<{ data: unknown[]; total: number }>(`/admin/clients?page=${page}`),
  get: (id: number) => request<{ data: unknown }>(`/clients/${id}`),
  update: (id: number, data: Record<string, unknown>) =>
    request(`/clients/${id}`, { method: "PUT", body: data }),
  delete: (id: number) => request(`/admin/clients/${id}`, { method: "DELETE" }),
  addCredit: (id: number, amount: number, description: string) =>
    request(`/admin/clients/${id}/credit`, {
      method: "POST",
      body: { amount, description },
    }),
};

// ─── Products ────────────────────────────────────
export const productsApi = {
  list: () => request<{ data: unknown[] }>("/products"),
  get: (id: number) => request<{ data: unknown }>(`/products/${id}`),
  create: (data: Record<string, unknown>) =>
    request("/admin/products", { method: "POST", body: data }),
  update: (id: number, data: Record<string, unknown>) =>
    request(`/admin/products/${id}`, { method: "PUT", body: data }),
  delete: (id: number) => request(`/admin/products/${id}`, { method: "DELETE" }),
  listGroups: () => request<{ data: unknown[] }>("/product-groups"),
  createGroup: (data: Record<string, unknown>) =>
    request("/admin/product-groups", { method: "POST", body: data }),
};

// ─── Invoices ────────────────────────────────────
export const invoicesApi = {
  listAll: (page = 1) => request<{ data: unknown[]; total: number }>(`/admin/invoices?page=${page}`),
  listByClient: (clientId: number) => request<{ data: unknown[] }>(`/clients/${clientId}/invoices`),
  get: (id: number) => request<{ data: unknown }>(`/invoices/${id}`),
  create: (data: Record<string, unknown>) =>
    request("/admin/invoices", { method: "POST", body: data }),
  applyPayment: (id: number, data: Record<string, unknown>) =>
    request(`/admin/invoices/${id}/payment`, { method: "POST", body: data }),
  applyCredit: (id: number, data: Record<string, unknown>) =>
    request(`/admin/invoices/${id}/credit`, { method: "POST", body: data }),
};

// ─── Orders ──────────────────────────────────────
export const ordersApi = {
  listAll: (page = 1) => request<{ data: unknown[]; total: number }>(`/admin/orders?page=${page}`),
  get: (id: number) => request<{ data: unknown }>(`/orders/${id}`),
  create: (data: Record<string, unknown>) =>
    request("/orders", { method: "POST", body: data }),
  accept: (id: number) => request(`/admin/orders/${id}/accept`, { method: "POST" }),
  cancel: (id: number) => request(`/admin/orders/${id}/cancel`, { method: "POST" }),
};

// ─── Tickets ─────────────────────────────────────
export const ticketsApi = {
  listAll: (page = 1) => request<{ data: unknown[]; total: number }>(`/admin/tickets?page=${page}`),
  listMy: () => request<{ data: unknown[] }>("/my/tickets"),
  get: (id: number) => request<{ data: unknown }>(`/tickets/${id}`),
  open: (data: Record<string, unknown>) =>
    request("/tickets", { method: "POST", body: data }),
  reply: (id: number, message: string) =>
    request(`/tickets/${id}/reply`, { method: "POST", body: { message } }),
  close: (id: number) => request(`/tickets/${id}/close`, { method: "POST" }),
  listDepartments: () => request<{ data: unknown[] }>("/ticket-departments"),
};

// ─── Services ────────────────────────────────────
export const servicesApi = {
  listByClient: (clientId: number) => request<{ data: unknown[] }>(`/clients/${clientId}/services`),
  get: (id: number) => request<{ data: unknown }>(`/services/${id}`),
  updateStatus: (id: number, status: string) =>
    request(`/admin/services/${id}/status`, { method: "PUT", body: { status } }),
};

// ─── Domains ─────────────────────────────────────
export const domainsApi = {
  listByClient: (clientId: number) => request<{ data: unknown[] }>(`/clients/${clientId}/domains`),
  get: (id: number) => request<{ data: unknown }>(`/domains/${id}`),
  create: (data: Record<string, unknown>) =>
    request("/admin/domains", { method: "POST", body: data }),
};

// ─── Misc ────────────────────────────────────────
export const miscApi = {
  currencies: () => request<{ data: unknown[] }>("/currencies"),
  announcements: () => request<{ data: unknown[] }>("/announcements"),
  createAnnouncement: (data: Record<string, unknown>) =>
    request("/admin/announcements", { method: "POST", body: data }),
  promotions: () => request<{ data: unknown[] }>("/admin/promotions"),
  createPromotion: (data: Record<string, unknown>) =>
    request("/admin/promotions", { method: "POST", body: data }),
  validatePromo: (code: string) => request(`/promo/validate?code=${code}`),
  activityLog: (page = 1) => request<{ data: unknown[] }>(`/admin/activity-log?page=${page}`),
  affiliates: () => request<{ data: unknown[] }>("/admin/affiliates"),
  activateAffiliate: (data: Record<string, unknown>) =>
    request("/admin/affiliates", { method: "POST", body: data }),
  kbCategories: () => request<{ data: unknown[] }>("/kb/categories"),
  kbArticles: (categoryId?: number) =>
    request<{ data: unknown[] }>(`/kb/articles${categoryId ? `?category_id=${categoryId}` : ""}`),
  kbArticle: (id: number) => request<{ data: unknown }>(`/kb/articles/${id}`),
  health: () => request<{ status: string }>("/health"),
};

export { ApiError };
