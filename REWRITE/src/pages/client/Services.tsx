import { useState, useEffect } from "react";
import Header from "../../components/Header";
import { useAuth } from "../../api/auth";
import { servicesApi } from "../../api/client";

interface Service {
  id: number;
  product_id: number;
  domain: string;
  amount: number;
  billing_cycle: string;
  status: string;
  next_due_date: string | null;
  created_at: string;
}

export default function ClientServices() {
  const { user } = useAuth();
  const [services, setServices] = useState<Service[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!user?.id) return;
    servicesApi.listByClient(user.id)
      .then((res) => setServices((res.data as Service[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [user?.id]);

  const statusBadge = (status: string) => {
    const map: Record<string, string> = {
      active: "badge-active", pending: "badge-pending", suspended: "badge-suspended",
      terminated: "badge-closed", cancelled: "badge-cancelled", fraud: "badge-fraud",
    };
    return <span className={`badge ${map[status] || "badge-pending"}`}>{status}</span>;
  };

  return (
    <>
      <Header title="My Services" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">My Services</h2>
            <p className="page-subtitle">{services.length} service(s)</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : services.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">⚙️</div>
              <div className="empty-state-title">No services</div>
              <div className="empty-state-text">Your active services will appear here.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Domain</th>
                    <th>Billing Cycle</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Next Due</th>
                  </tr>
                </thead>
                <tbody>
                  {services.map((s) => (
                    <tr key={s.id}>
                      <td>#{s.id}</td>
                      <td><strong>{s.domain || "—"}</strong></td>
                      <td>{s.billing_cycle}</td>
                      <td>${(s.amount || 0).toFixed(2)}</td>
                      <td>{statusBadge(s.status)}</td>
                      <td>{s.next_due_date ? new Date(s.next_due_date).toLocaleDateString() : "—"}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
