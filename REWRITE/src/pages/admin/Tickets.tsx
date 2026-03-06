import { useState, useEffect } from "react";
import Header from "../../components/Header";
import Modal from "../../components/Modal";
import { ticketsApi } from "../../api/client";

interface Ticket {
  id: number;
  tid: string;
  subject: string;
  client_id: number;
  department_id: number;
  status: string;
  priority: string;
  last_reply: string;
  created_at: string;
}

interface TicketDetail extends Ticket {
  message: string;
  replies?: { id: number; message: string; client_id: number; admin_id: number; created_at: string }[];
}

export default function TicketsPage() {
  const [tickets, setTickets] = useState<Ticket[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [selected, setSelected] = useState<TicketDetail | null>(null);
  const [reply, setReply] = useState("");

  const load = () => {
    setLoading(true);
    ticketsApi.listAll()
      .then((res) => setTickets((res.data as Ticket[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    let active = true;
    ticketsApi.listAll()
      .then((res) => { if (active) setTickets((res.data as Ticket[]) || []); })
      .catch((err) => { if (active) setError(err.message); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  const viewTicket = async (id: number) => {
    try {
      const res = await ticketsApi.get(id);
      setSelected(res.data as TicketDetail);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to load ticket");
    }
  };

  const handleReply = async () => {
    if (!selected || !reply.trim()) return;
    try {
      await ticketsApi.reply(selected.id, reply);
      setReply("");
      viewTicket(selected.id);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to reply");
    }
  };

  const handleClose = async (id: number) => {
    try { await ticketsApi.close(id); load(); setSelected(null); }
    catch (err) { setError(err instanceof Error ? err.message : "Failed"); }
  };

  const statusBadge = (status: string) => {
    const map: Record<string, string> = {
      open: "badge-open", answered: "badge-answered", closed: "badge-closed",
      "customer-reply": "badge-customer-reply", "on-hold": "badge-on-hold",
    };
    return <span className={`badge ${map[status] || "badge-pending"}`}>{status}</span>;
  };

  const priorityBadge = (priority: string) => {
    const map: Record<string, string> = {
      low: "badge-inactive", medium: "badge-pending",
      high: "badge-overdue", urgent: "badge-urgent",
    };
    return <span className={`badge ${map[priority] || "badge-pending"}`}>{priority}</span>;
  };

  return (
    <>
      <Header title="Support Tickets" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Ticket Management</h2>
            <p className="page-subtitle">{tickets.length} ticket(s)</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : tickets.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">🎫</div>
              <div className="empty-state-title">No tickets</div>
              <div className="empty-state-text">Support tickets will appear here.</div>
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
                    <th>Client</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Last Reply</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {tickets.map((t) => (
                    <tr key={t.id}>
                      <td><strong>{t.tid}</strong></td>
                      <td>{t.subject}</td>
                      <td>#{t.client_id}</td>
                      <td>{statusBadge(t.status)}</td>
                      <td>{priorityBadge(t.priority)}</td>
                      <td>{new Date(t.last_reply).toLocaleDateString()}</td>
                      <td>
                        <div style={{ display: "flex", gap: 4 }}>
                          <button className="btn btn-secondary btn-sm" onClick={() => viewTicket(t.id)}>View</button>
                          {t.status !== "closed" && (
                            <button className="btn btn-danger btn-sm" onClick={() => handleClose(t.id)}>Close</button>
                          )}
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        <Modal open={!!selected} title={`Ticket ${selected?.tid || ""}`} onClose={() => setSelected(null)}
          footer={selected?.status !== "closed" ? (
            <>
              <button className="btn btn-danger" onClick={() => selected && handleClose(selected.id)}>Close Ticket</button>
              <button className="btn btn-primary" onClick={handleReply} disabled={!reply.trim()}>Send Reply</button>
            </>
          ) : undefined}>
          {selected && (
            <>
              <div className="mb-2">
                <strong>Subject:</strong> {selected.subject}<br />
                <strong>Status:</strong> {statusBadge(selected.status)}{" "}
                <strong>Priority:</strong> {priorityBadge(selected.priority)}
              </div>
              <div className="card mb-2">
                <div className="card-body">
                  <div className="text-sm text-muted">Original Message</div>
                  <p style={{ whiteSpace: "pre-wrap", marginTop: 8 }}>{selected.message}</p>
                </div>
              </div>
              {selected.replies?.map((r) => (
                <div key={r.id} className="card mb-2">
                  <div className="card-body">
                    <div className="text-sm text-muted">
                      {r.admin_id ? "Admin" : "Client"} • {new Date(r.created_at).toLocaleString()}
                    </div>
                    <p style={{ whiteSpace: "pre-wrap", marginTop: 8 }}>{r.message}</p>
                  </div>
                </div>
              ))}
              {selected.status !== "closed" && (
                <div className="form-group mt-2">
                  <label className="form-label">Reply</label>
                  <textarea className="form-textarea" value={reply} onChange={(e) => setReply(e.target.value)}
                    placeholder="Type your reply..." />
                </div>
              )}
            </>
          )}
        </Modal>
      </div>
    </>
  );
}
