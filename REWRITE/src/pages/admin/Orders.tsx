import { useState, useEffect } from "react";
import Header from "../../components/Header";
import { ordersApi } from "../../api/client";

interface Order {
  id: number;
  client_id: number;
  order_number: string;
  amount: number;
  status: string;
  payment_method: string;
  promo_code: string;
  created_at: string;
}

export default function OrdersPage() {
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const load = () => {
    setLoading(true);
    ordersApi.listAll()
      .then((res) => setOrders((res.data as Order[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    let active = true;
    ordersApi.listAll()
      .then((res) => { if (active) setOrders((res.data as Order[]) || []); })
      .catch((err) => { if (active) setError(err.message); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, []);

  const handleAccept = async (id: number) => {
    try { await ordersApi.accept(id); load(); }
    catch (err) { setError(err instanceof Error ? err.message : "Failed"); }
  };

  const handleCancel = async (id: number) => {
    if (!confirm("Cancel this order?")) return;
    try { await ordersApi.cancel(id); load(); }
    catch (err) { setError(err instanceof Error ? err.message : "Failed"); }
  };

  const statusBadge = (status: string) => {
    const map: Record<string, string> = {
      active: "badge-active", pending: "badge-pending",
      cancelled: "badge-cancelled", fraud: "badge-fraud",
    };
    return <span className={`badge ${map[status] || "badge-pending"}`}>{status}</span>;
  };

  return (
    <>
      <Header title="Orders" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Order Management</h2>
            <p className="page-subtitle">{orders.length} order(s)</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : orders.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">🛒</div>
              <div className="empty-state-title">No orders</div>
              <div className="empty-state-text">Orders will appear here as clients make purchases.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Order #</th>
                    <th>Client ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Promo</th>
                    <th>Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {orders.map((o) => (
                    <tr key={o.id}>
                      <td><strong>{o.order_number || `#${o.id}`}</strong></td>
                      <td>#{o.client_id}</td>
                      <td>${(o.amount || 0).toFixed(2)}</td>
                      <td>{statusBadge(o.status)}</td>
                      <td>{o.payment_method || "—"}</td>
                      <td>{o.promo_code || "—"}</td>
                      <td>{new Date(o.created_at).toLocaleDateString()}</td>
                      <td>
                        <div style={{ display: "flex", gap: 4 }}>
                          {o.status === "pending" && (
                            <button className="btn btn-success btn-sm" onClick={() => handleAccept(o.id)}>Accept</button>
                          )}
                          {o.status !== "cancelled" && (
                            <button className="btn btn-danger btn-sm" onClick={() => handleCancel(o.id)}>Cancel</button>
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
      </div>
    </>
  );
}
