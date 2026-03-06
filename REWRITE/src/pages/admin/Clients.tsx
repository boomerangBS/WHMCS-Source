import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Header from "../../components/Header";
import { clientsApi } from "../../api/client";

interface Client {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  company_name: string;
  status: string;
  credit: number;
  created_at: string;
}

export default function ClientsPage() {
  const [clients, setClients] = useState<Client[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    clientsApi.list()
      .then((res) => setClients((res.data as Client[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  const statusBadge = (status: string) => {
    const cls = status === "active" ? "badge-active" : status === "inactive" ? "badge-inactive" : "badge-closed";
    return <span className={`badge ${cls}`}>{status}</span>;
  };

  return (
    <>
      <Header title="Clients" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Client Management</h2>
            <p className="page-subtitle">{clients.length} client(s) found</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : clients.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">👥</div>
              <div className="empty-state-title">No clients yet</div>
              <div className="empty-state-text">Clients will appear here once they register.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Credit</th>
                    <th>Created</th>
                  </tr>
                </thead>
                <tbody>
                  {clients.map((c) => (
                    <tr key={c.id} style={{ cursor: "pointer" }} onClick={() => navigate(`/admin/clients/${c.id}`)}>
                      <td>#{c.id}</td>
                      <td><strong>{c.first_name} {c.last_name}</strong></td>
                      <td>{c.email}</td>
                      <td>{c.company_name || "—"}</td>
                      <td>{statusBadge(c.status)}</td>
                      <td>${(c.credit || 0).toFixed(2)}</td>
                      <td>{new Date(c.created_at).toLocaleDateString()}</td>
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
