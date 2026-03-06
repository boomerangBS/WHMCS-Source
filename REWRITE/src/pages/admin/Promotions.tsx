import { useState, useEffect } from "react";
import Header from "../../components/Header";
import Modal from "../../components/Modal";
import { miscApi } from "../../api/client";

interface Promotion {
  id: number;
  code: string;
  type: string;
  value: number;
  recurring: boolean;
  max_uses: number;
  uses: number;
  expiration_date: string | null;
  created_at: string;
}

export default function PromotionsPage() {
  const [promos, setPromos] = useState<Promotion[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [showCreate, setShowCreate] = useState(false);
  const [form, setForm] = useState({
    code: "", type: "percentage", value: 0, recurring: false,
    max_uses: 0, expiration_date: "",
  });

  const load = () => {
    setLoading(true);
    miscApi.promotions()
      .then((res) => setPromos((res.data as Promotion[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    let active = true;
    miscApi.promotions()
      .then((res) => { if (active) setPromos((res.data as Promotion[]) || []); })
      .catch((err) => { if (active) setError(err.message); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  const handleCreate = async () => {
    try {
      const body: Record<string, unknown> = { ...form };
      if (!form.expiration_date) delete body.expiration_date;
      await miscApi.createPromotion(body);
      setShowCreate(false);
      setForm({ code: "", type: "percentage", value: 0, recurring: false, max_uses: 0, expiration_date: "" });
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to create promotion");
    }
  };

  return (
    <>
      <Header title="Promotions" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Promotional Codes</h2>
            <p className="page-subtitle">{promos.length} promotion(s)</p>
          </div>
          <button className="btn btn-primary" onClick={() => setShowCreate(true)}>+ New Promotion</button>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : promos.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">🏷️</div>
              <div className="empty-state-title">No promotions</div>
              <div className="empty-state-text">Create your first promo code.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Recurring</th>
                    <th>Uses</th>
                    <th>Expires</th>
                  </tr>
                </thead>
                <tbody>
                  {promos.map((p) => (
                    <tr key={p.id}>
                      <td><strong>{p.code}</strong></td>
                      <td>{p.type}</td>
                      <td>{p.type === "percentage" ? `${p.value}%` : `$${p.value.toFixed(2)}`}</td>
                      <td>{p.recurring ? "Yes" : "No"}</td>
                      <td>{p.uses} / {p.max_uses || "∞"}</td>
                      <td>{p.expiration_date ? new Date(p.expiration_date).toLocaleDateString() : "Never"}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        <Modal open={showCreate} title="Create Promotion" onClose={() => setShowCreate(false)}
          footer={<><button className="btn btn-secondary" onClick={() => setShowCreate(false)}>Cancel</button><button className="btn btn-primary" onClick={handleCreate}>Create</button></>}>
          <div className="form-group">
            <label className="form-label">Promo Code *</label>
            <input className="form-input" value={form.code} onChange={(e) => setForm({ ...form, code: e.target.value.toUpperCase() })} placeholder="SUMMER2024" />
          </div>
          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Discount Type</label>
              <select className="form-select" value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}>
                <option value="percentage">Percentage</option>
                <option value="fixed">Fixed Amount</option>
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Value</label>
              <input className="form-input" type="number" step="0.01" value={form.value} onChange={(e) => setForm({ ...form, value: parseFloat(e.target.value) || 0 })} />
            </div>
          </div>
          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Max Uses (0 = unlimited)</label>
              <input className="form-input" type="number" value={form.max_uses} onChange={(e) => setForm({ ...form, max_uses: parseInt(e.target.value) || 0 })} />
            </div>
            <div className="form-group">
              <label className="form-label">Expiration Date</label>
              <input className="form-input" type="date" value={form.expiration_date} onChange={(e) => setForm({ ...form, expiration_date: e.target.value })} />
            </div>
          </div>
        </Modal>
      </div>
    </>
  );
}
