import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Header from "../../components/Header";
import { useAuth } from "../../api/auth";
import { invoicesApi, servicesApi, domainsApi, ticketsApi } from "../../api/client";

export default function ClientDashboard() {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [stats, setStats] = useState({ services: 0, invoices: 0, domains: 0, tickets: 0 });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!user?.id) return;
    Promise.all([
      servicesApi.listByClient(user.id).catch(() => ({ data: [] })),
      invoicesApi.listByClient(user.id).catch(() => ({ data: [] })),
      domainsApi.listByClient(user.id).catch(() => ({ data: [] })),
      ticketsApi.listMy().catch(() => ({ data: [] })),
    ]).then(([svc, inv, dom, tkt]) => {
      setStats({
        services: ((svc.data as unknown[]) || []).length,
        invoices: ((inv.data as unknown[]) || []).length,
        domains: ((dom.data as unknown[]) || []).length,
        tickets: ((tkt.data as unknown[]) || []).length,
      });
    }).finally(() => setLoading(false));
  }, [user?.id]);

  return (
    <>
      <Header title="My Dashboard" />
      <div className="page">
        <div className="mb-3">
          <h2 className="page-title">Welcome, {user?.first_name}!</h2>
          <p className="page-subtitle">Here&apos;s an overview of your account.</p>
        </div>
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : (
          <div className="stats-grid">
            <div className="stat-card" style={{ cursor: "pointer" }} onClick={() => navigate("/client/services")}>
              <div className="stat-card-icon green">⚙️</div>
              <div className="stat-card-value">{stats.services}</div>
              <div className="stat-card-label">Active Services</div>
            </div>
            <div className="stat-card" style={{ cursor: "pointer" }} onClick={() => navigate("/client/invoices")}>
              <div className="stat-card-icon red">💳</div>
              <div className="stat-card-value">{stats.invoices}</div>
              <div className="stat-card-label">Invoices</div>
            </div>
            <div className="stat-card" style={{ cursor: "pointer" }} onClick={() => navigate("/client/domains")}>
              <div className="stat-card-icon blue">🌐</div>
              <div className="stat-card-value">{stats.domains}</div>
              <div className="stat-card-label">Domains</div>
            </div>
            <div className="stat-card" style={{ cursor: "pointer" }} onClick={() => navigate("/client/tickets")}>
              <div className="stat-card-icon orange">🎫</div>
              <div className="stat-card-value">{stats.tickets}</div>
              <div className="stat-card-label">Support Tickets</div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
