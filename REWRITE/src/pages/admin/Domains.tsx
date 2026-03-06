import { useState, useEffect } from "react";
import Header from "../../components/Header";
import Modal from "../../components/Modal";
import { domainsApi, clientsApi } from "../../api/client";

interface Domain {
  id: number;
  client_id: number;
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

export default function DomainsPage() {
  const [domains, setDomains] = useState<Domain[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [showCreate, setShowCreate] = useState(false);
  const [form, setForm] = useState({
    client_id: 0, domain_name: "", type: "register",
    registration_date: new Date().toISOString().split("T")[0],
    expiry_date: "", ns1: "ns1.example.com", ns2: "ns2.example.com",
    amount: 0, recurring_amount: 0,
  });

  const load = () => {
    setLoading(true);
    clientsApi.list()
      .then(async (res) => {
        const clients = (res.data as { id: number }[]) || [];
        const allDomains: Domain[] = [];
        for (const c of clients.slice(0, 20)) {
          try {
            const dr = await domainsApi.listByClient(c.id);
            allDomains.push(...((dr.data as Domain[]) || []));
          } catch { /* skip */ }
        }
        setDomains(allDomains);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    let active = true;
    clientsApi.list()
      .then(async (res) => {
        const clients = (res.data as { id: number }[]) || [];
        const allDomains: Domain[] = [];
        for (const c of clients.slice(0, 20)) {
          try {
            const dr = await domainsApi.listByClient(c.id);
            allDomains.push(...((dr.data as Domain[]) || []));
          } catch { /* skip */ }
        }
        if (active) setDomains(allDomains);
      })
      .catch((err) => { if (active) setError(err.message); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  const handleCreate = async () => {
    try {
      await domainsApi.create(form);
      setShowCreate(false);
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to create domain");
    }
  };

  const statusBadge = (status: string) => {
    const map: Record<string, string> = {
      active: "badge-active", pending: "badge-pending", expired: "badge-overdue",
      cancelled: "badge-cancelled", fraud: "badge-fraud", transferring: "badge-pending",
    };
    return <span className={`badge ${map[status] || "badge-pending"}`}>{status}</span>;
  };

  return (
    <>
      <Header title="Domains" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Domain Management</h2>
            <p className="page-subtitle">{domains.length} domain(s)</p>
          </div>
          <button className="btn btn-primary" onClick={() => setShowCreate(true)}>+ New Domain</button>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : domains.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">🌐</div>
              <div className="empty-state-title">No domains</div>
              <div className="empty-state-text">Domains will appear here.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Domain</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Expires</th>
                    <th>Auto-Renew</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  {domains.map((d) => (
                    <tr key={d.id}>
                      <td><strong>{d.domain_name}</strong></td>
                      <td>#{d.client_id}</td>
                      <td>{d.type}</td>
                      <td>{statusBadge(d.status)}</td>
                      <td>{new Date(d.registration_date).toLocaleDateString()}</td>
                      <td>{new Date(d.expiry_date).toLocaleDateString()}</td>
                      <td>{d.auto_renew ? "Yes" : "No"}</td>
                      <td>${(d.amount || 0).toFixed(2)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        <Modal open={showCreate} title="Register Domain" onClose={() => setShowCreate(false)}
          footer={<><button className="btn btn-secondary" onClick={() => setShowCreate(false)}>Cancel</button><button className="btn btn-primary" onClick={handleCreate}>Create</button></>}>
          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Client ID *</label>
              <input className="form-input" type="number" value={form.client_id} onChange={(e) => setForm({ ...form, client_id: parseInt(e.target.value) || 0 })} />
            </div>
            <div className="form-group">
              <label className="form-label">Type</label>
              <select className="form-select" value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}>
                <option value="register">Register</option>
                <option value="transfer">Transfer</option>
              </select>
            </div>
          </div>
          <div className="form-group">
            <label className="form-label">Domain Name *</label>
            <input className="form-input" value={form.domain_name} placeholder="example.com" onChange={(e) => setForm({ ...form, domain_name: e.target.value })} />
          </div>
          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Nameserver 1</label>
              <input className="form-input" value={form.ns1} onChange={(e) => setForm({ ...form, ns1: e.target.value })} />
            </div>
            <div className="form-group">
              <label className="form-label">Nameserver 2</label>
              <input className="form-input" value={form.ns2} onChange={(e) => setForm({ ...form, ns2: e.target.value })} />
            </div>
          </div>
          <div className="form-group">
            <label className="form-label">Amount</label>
            <input className="form-input" type="number" step="0.01" value={form.amount} onChange={(e) => setForm({ ...form, amount: parseFloat(e.target.value) || 0 })} />
          </div>
        </Modal>
      </div>
    </>
  );
}
