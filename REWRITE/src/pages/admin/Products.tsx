import { useState, useEffect } from "react";
import Header from "../../components/Header";
import Modal from "../../components/Modal";
import { productsApi } from "../../api/client";

interface Product {
  id: number;
  name: string;
  group_id: number;
  type: string;
  description: string;
  price_monthly: number;
  price_annual: number;
  setup_fee: number;
  hidden: boolean;
  retired: boolean;
  qty: number;
  stock_control: boolean;
}

export default function ProductsPage() {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [showCreate, setShowCreate] = useState(false);
  const [form, setForm] = useState({
    name: "", description: "", type: "hosting", group_id: 1,
    price_monthly: 0, price_quarterly: 0, price_semiannual: 0,
    price_annual: 0, price_biennial: 0, setup_fee: 0,
  });

  const load = () => {
    setLoading(true);
    productsApi.list()
      .then((res) => setProducts((res.data as Product[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    let active = true;
    productsApi.list()
      .then((res) => { if (active) setProducts((res.data as Product[]) || []); })
      .catch((err) => { if (active) setError(err.message); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  const handleCreate = async () => {
    try {
      await productsApi.create(form);
      setShowCreate(false);
      setForm({ name: "", description: "", type: "hosting", group_id: 1, price_monthly: 0, price_quarterly: 0, price_semiannual: 0, price_annual: 0, price_biennial: 0, setup_fee: 0 });
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to create product");
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm("Delete this product?")) return;
    try {
      await productsApi.delete(id);
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to delete");
    }
  };

  return (
    <>
      <Header title="Products" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Product Management</h2>
            <p className="page-subtitle">{products.length} product(s)</p>
          </div>
          <button className="btn btn-primary" onClick={() => setShowCreate(true)}>+ New Product</button>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : products.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">📦</div>
              <div className="empty-state-title">No products yet</div>
              <div className="empty-state-text">Create your first product to get started.</div>
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
                    <th>Type</th>
                    <th>Monthly</th>
                    <th>Annual</th>
                    <th>Setup</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {products.map((p) => (
                    <tr key={p.id}>
                      <td>#{p.id}</td>
                      <td><strong>{p.name}</strong></td>
                      <td>{p.type}</td>
                      <td>${(p.price_monthly || 0).toFixed(2)}</td>
                      <td>${(p.price_annual || 0).toFixed(2)}</td>
                      <td>${(p.setup_fee || 0).toFixed(2)}</td>
                      <td>
                        {p.retired ? <span className="badge badge-inactive">Retired</span> :
                         p.hidden ? <span className="badge badge-pending">Hidden</span> :
                         <span className="badge badge-active">Active</span>}
                      </td>
                      <td>
                        <button className="btn btn-danger btn-sm" onClick={() => handleDelete(p.id)}>Delete</button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        <Modal open={showCreate} title="Create Product" onClose={() => setShowCreate(false)}
          footer={<><button className="btn btn-secondary" onClick={() => setShowCreate(false)}>Cancel</button><button className="btn btn-primary" onClick={handleCreate}>Create</button></>}>
          <div className="form-group">
            <label className="form-label">Name *</label>
            <input className="form-input" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
          </div>
          <div className="form-group">
            <label className="form-label">Description</label>
            <textarea className="form-textarea" value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />
          </div>
          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Type</label>
              <select className="form-select" value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}>
                <option value="hosting">Hosting</option>
                <option value="server">Server</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Setup Fee</label>
              <input className="form-input" type="number" step="0.01" value={form.setup_fee} onChange={(e) => setForm({ ...form, setup_fee: parseFloat(e.target.value) || 0 })} />
            </div>
          </div>
          <div className="form-row">
            <div className="form-group">
              <label className="form-label">Monthly Price</label>
              <input className="form-input" type="number" step="0.01" value={form.price_monthly} onChange={(e) => setForm({ ...form, price_monthly: parseFloat(e.target.value) || 0 })} />
            </div>
            <div className="form-group">
              <label className="form-label">Annual Price</label>
              <input className="form-input" type="number" step="0.01" value={form.price_annual} onChange={(e) => setForm({ ...form, price_annual: parseFloat(e.target.value) || 0 })} />
            </div>
          </div>
        </Modal>
      </div>
    </>
  );
}
