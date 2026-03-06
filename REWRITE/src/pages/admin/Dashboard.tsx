import { useState, useEffect } from "react";
import Header from "../../components/Header";
import { dashboardApi } from "../../api/client";

interface DashboardStats {
  total_clients?: number;
  active_services?: number;
  open_tickets?: number;
  unpaid_invoices?: number;
  revenue_this_month?: number;
  pending_orders?: number;
  total_products?: number;
  total_domains?: number;
  [key: string]: unknown;
}

export default function AdminDashboard() {
  const [stats, setStats] = useState<DashboardStats>({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    dashboardApi.stats()
      .then((res) => setStats((res.data as DashboardStats) || {}))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <><Header title="Dashboard" /><div className="page"><div className="loading"><div className="spinner"></div></div></div></>;

  return (
    <>
      <Header title="Dashboard" />
      <div className="page">
        {error && <div className="alert alert-error">{error}</div>}
        <div className="stats-grid">
          <div className="stat-card">
            <div className="stat-card-icon blue">👥</div>
            <div className="stat-card-value">{stats.total_clients ?? 0}</div>
            <div className="stat-card-label">Total Clients</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-icon green">⚙️</div>
            <div className="stat-card-value">{stats.active_services ?? 0}</div>
            <div className="stat-card-label">Active Services</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-icon orange">🎫</div>
            <div className="stat-card-value">{stats.open_tickets ?? 0}</div>
            <div className="stat-card-label">Open Tickets</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-icon red">💳</div>
            <div className="stat-card-value">{stats.unpaid_invoices ?? 0}</div>
            <div className="stat-card-label">Unpaid Invoices</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-icon cyan">💰</div>
            <div className="stat-card-value">${(stats.revenue_this_month ?? 0).toLocaleString()}</div>
            <div className="stat-card-label">Revenue This Month</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-icon orange">🛒</div>
            <div className="stat-card-value">{stats.pending_orders ?? 0}</div>
            <div className="stat-card-label">Pending Orders</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-icon blue">📦</div>
            <div className="stat-card-value">{stats.total_products ?? 0}</div>
            <div className="stat-card-label">Products</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-icon green">🌐</div>
            <div className="stat-card-value">{stats.total_domains ?? 0}</div>
            <div className="stat-card-label">Domains</div>
          </div>
        </div>
      </div>
    </>
  );
}
