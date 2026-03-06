import { useState, useEffect } from "react";
import Header from "../../components/Header";
import Modal from "../../components/Modal";
import { miscApi } from "../../api/client";

interface Announcement {
  id: number;
  title: string;
  body: string;
  published: boolean;
  created_at: string;
}

export default function AnnouncementsPage() {
  const [items, setItems] = useState<Announcement[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [showCreate, setShowCreate] = useState(false);
  const [form, setForm] = useState({ title: "", body: "", published: true });

  const load = () => {
    setLoading(true);
    miscApi.announcements()
      .then((res) => setItems((res.data as Announcement[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    let active = true;
    miscApi.announcements()
      .then((res) => { if (active) setItems((res.data as Announcement[]) || []); })
      .catch((err) => { if (active) setError(err.message); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  const handleCreate = async () => {
    try {
      await miscApi.createAnnouncement(form);
      setShowCreate(false);
      setForm({ title: "", body: "", published: true });
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to create announcement");
    }
  };

  return (
    <>
      <Header title="Announcements" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Announcements</h2>
            <p className="page-subtitle">{items.length} announcement(s)</p>
          </div>
          <button className="btn btn-primary" onClick={() => setShowCreate(true)}>+ New Announcement</button>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : items.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">📢</div>
              <div className="empty-state-title">No announcements</div>
              <div className="empty-state-text">Create your first announcement.</div>
            </div>
          </div>
        ) : (
          <div style={{ display: "grid", gap: 16 }}>
            {items.map((a) => (
              <div key={a.id} className="card">
                <div className="card-header">
                  <div>
                    <h3 className="card-title">{a.title}</h3>
                    <span className="text-sm text-muted">{new Date(a.created_at).toLocaleDateString()}</span>
                  </div>
                  <span className={`badge ${a.published ? "badge-active" : "badge-draft"}`}>
                    {a.published ? "Published" : "Draft"}
                  </span>
                </div>
                <div className="card-body">
                  <p style={{ whiteSpace: "pre-wrap" }}>{a.body}</p>
                </div>
              </div>
            ))}
          </div>
        )}

        <Modal open={showCreate} title="Create Announcement" onClose={() => setShowCreate(false)}
          footer={<><button className="btn btn-secondary" onClick={() => setShowCreate(false)}>Cancel</button><button className="btn btn-primary" onClick={handleCreate}>Publish</button></>}>
          <div className="form-group">
            <label className="form-label">Title *</label>
            <input className="form-input" value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} />
          </div>
          <div className="form-group">
            <label className="form-label">Body *</label>
            <textarea className="form-textarea" value={form.body} onChange={(e) => setForm({ ...form, body: e.target.value })}
              style={{ minHeight: 200 }} />
          </div>
        </Modal>
      </div>
    </>
  );
}
