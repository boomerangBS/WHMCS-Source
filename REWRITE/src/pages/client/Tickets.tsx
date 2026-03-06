import { useState, useEffect, type FormEvent } from "react";
import Header from "../../components/Header";
import Modal from "../../components/Modal";
import { useAuth } from "../../api/auth";
import { ticketsApi } from "../../api/client";

interface Ticket {
  id: number;
  tid: string;
  subject: string;
  status: string;
  priority: string;
  last_reply: string;
  created_at: string;
  message: string;
  replies?: { id: number; message: string; client_id: number; admin_id: number; created_at: string }[];
}

interface Dept {
  id: number;
  name: string;
}

export default function ClientTickets() {
  const { user } = useAuth();
  const [tickets, setTickets] = useState<Ticket[]>([]);
  const [departments, setDepartments] = useState<Dept[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [showCreate, setShowCreate] = useState(false);
  const [selected, setSelected] = useState<Ticket | null>(null);
  const [reply, setReply] = useState("");
  const [form, setForm] = useState({ department_id: 0, subject: "", message: "", priority: "medium" });

  const load = () => {
    setLoading(true);
    Promise.all([ticketsApi.listMy(), ticketsApi.listDepartments()])
      .then(([tRes, dRes]) => {
        setTickets((tRes.data as Ticket[]) || []);
        const depts = (dRes.data as Dept[]) || [];
        setDepartments(depts);
        if (depts.length > 0 && !form.department_id) {
          setForm((f) => ({ ...f, department_id: depts[0].id }));
        }
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    let active = true;
    Promise.all([ticketsApi.listMy(), ticketsApi.listDepartments()])
      .then(([tRes, dRes]) => {
        if (!active) return;
        setTickets((tRes.data as Ticket[]) || []);
        const depts = (dRes.data as Dept[]) || [];
        setDepartments(depts);
        if (depts.length > 0) {
          setForm((f) => ({ ...f, department_id: f.department_id || depts[0].id }));
        }
      })
      .catch((err) => { if (active) setError(err.message); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  const viewTicket = async (id: number) => {
    try {
      const res = await ticketsApi.get(id);
      setSelected(res.data as Ticket);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed");
    }
  };

  const handleCreate = async (e: FormEvent) => {
    e.preventDefault();
    try {
      await ticketsApi.open({ ...form, client_id: user?.id });
      setShowCreate(false);
      setForm({ ...form, subject: "", message: "" });
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed");
    }
  };

  const handleReply = async () => {
    if (!selected || !reply.trim()) return;
    try {
      await ticketsApi.reply(selected.id, reply);
      setReply("");
      viewTicket(selected.id);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed");
    }
  };

  const statusBadge = (status: string) => {
    const map: Record<string, string> = {
      open: "badge-open", answered: "badge-answered", closed: "badge-closed",
      "customer-reply": "badge-customer-reply", "on-hold": "badge-on-hold",
    };
    return <span className={`badge ${map[status] || "badge-pending"}`}>{status}</span>;
  };

  return (
    <>
      <Header title="Support Tickets" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">My Tickets</h2>
            <p className="page-subtitle">{tickets.length} ticket(s)</p>
          </div>
          <button className="btn btn-primary" onClick={() => setShowCreate(true)}>+ Open Ticket</button>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : tickets.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">🎫</div>
              <div className="empty-state-title">No tickets</div>
              <div className="empty-state-text">Need help? Open a support ticket.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Ticket ID</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Last Reply</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {tickets.map((t) => (
                    <tr key={t.id}>
                      <td><strong>{t.tid}</strong></td>
                      <td>{t.subject}</td>
                      <td>{statusBadge(t.status)}</td>
                      <td>{new Date(t.last_reply).toLocaleDateString()}</td>
                      <td>
                        <button className="btn btn-secondary btn-sm" onClick={() => viewTicket(t.id)}>View</button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* Create Ticket Modal */}
        <Modal open={showCreate} title="Open Support Ticket" onClose={() => setShowCreate(false)}>
          <form onSubmit={handleCreate}>
            <div className="form-group">
              <label className="form-label">Department</label>
              <select className="form-select" value={form.department_id} onChange={(e) => setForm({ ...form, department_id: parseInt(e.target.value) })}>
                {departments.map((d) => <option key={d.id} value={d.id}>{d.name}</option>)}
              </select>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Subject *</label>
                <input className="form-input" required value={form.subject} onChange={(e) => setForm({ ...form, subject: e.target.value })} />
              </div>
              <div className="form-group">
                <label className="form-label">Priority</label>
                <select className="form-select" value={form.priority} onChange={(e) => setForm({ ...form, priority: e.target.value })}>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Message *</label>
              <textarea className="form-textarea" required value={form.message} onChange={(e) => setForm({ ...form, message: e.target.value })}
                style={{ minHeight: 150 }} />
            </div>
            <div style={{ display: "flex", justifyContent: "flex-end", gap: 8 }}>
              <button type="button" className="btn btn-secondary" onClick={() => setShowCreate(false)}>Cancel</button>
              <button type="submit" className="btn btn-primary">Submit Ticket</button>
            </div>
          </form>
        </Modal>

        {/* View Ticket Modal */}
        <Modal open={!!selected} title={`Ticket ${selected?.tid || ""}`} onClose={() => setSelected(null)}
          footer={selected?.status !== "closed" ? (
            <button className="btn btn-primary" onClick={handleReply} disabled={!reply.trim()}>Send Reply</button>
          ) : undefined}>
          {selected && (
            <>
              <div className="mb-2">
                <strong>{selected.subject}</strong> — {statusBadge(selected.status)}
              </div>
              <div className="card mb-2">
                <div className="card-body">
                  <div className="text-sm text-muted">Your message</div>
                  <p style={{ whiteSpace: "pre-wrap", marginTop: 8 }}>{selected.message}</p>
                </div>
              </div>
              {selected.replies?.map((r) => (
                <div key={r.id} className="card mb-2">
                  <div className="card-body">
                    <div className="text-sm text-muted">
                      {r.admin_id ? "🛡️ Staff" : "You"} • {new Date(r.created_at).toLocaleString()}
                    </div>
                    <p style={{ whiteSpace: "pre-wrap", marginTop: 8 }}>{r.message}</p>
                  </div>
                </div>
              ))}
              {selected.status !== "closed" && (
                <div className="form-group mt-2">
                  <label className="form-label">Reply</label>
                  <textarea className="form-textarea" value={reply} onChange={(e) => setReply(e.target.value)} placeholder="Type your reply..." />
                </div>
              )}
            </>
          )}
        </Modal>
      </div>
    </>
  );
}
