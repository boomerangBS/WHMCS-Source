import { useState, useEffect } from "react";
import Header from "../../components/Header";
import { useAuth } from "../../api/auth";
import { domainsApi } from "../../api/client";

interface Domain {
  id: number;
  domain_name: string;
  type: string;
  status: string;
  registration_date: string;
  expiry_date: string;
  auto_renew: boolean;
  ns1: string;
  ns2: string;
  amount: number;
}

export default function ClientDomains() {
  const { user } = useAuth();
  const [domains, setDomains] = useState<Domain[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!user?.id) return;
    domainsApi.listByClient(user.id)
      .then((res) => setDomains((res.data as Domain[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [user?.id]);

  const statusBadge = (status: string) => {
    const map: Record<string, string> = {
      active: "badge-active", pending: "badge-pending", expired: "badge-overdue",
      cancelled: "badge-cancelled",
    };
    return <span className={`badge ${map[status] || "badge-pending"}`}>{status}</span>;
  };

  return (
    <>
      <Header title="My Domains" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">My Domains</h2>
            <p className="page-subtitle">{domains.length} domain(s)</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : domains.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">🌐</div>
              <div className="empty-state-title">No domains</div>
              <div className="empty-state-text">Your domains will appear here.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Domain</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Expires</th>
                    <th>Auto-Renew</th>
                    <th>Nameservers</th>
                  </tr>
                </thead>
                <tbody>
                  {domains.map((d) => (
                    <tr key={d.id}>
                      <td><strong>{d.domain_name}</strong></td>
                      <td>{statusBadge(d.status)}</td>
                      <td>{new Date(d.registration_date).toLocaleDateString()}</td>
                      <td>{new Date(d.expiry_date).toLocaleDateString()}</td>
                      <td>{d.auto_renew ? "Yes" : "No"}</td>
                      <td className="text-sm">{d.ns1}, {d.ns2}</td>
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
