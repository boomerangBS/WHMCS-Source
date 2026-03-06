import { useState, useEffect } from "react";
import Header from "../../components/Header";
import { useAuth } from "../../api/auth";
import { invoicesApi } from "../../api/client";

interface Invoice {
  id: number;
  invoice_num: string;
  date_created: string;
  date_due: string;
  date_paid: string | null;
  total: number;
  status: string;
  payment_method: string;
}

export default function ClientInvoices() {
  const { user } = useAuth();
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!user?.id) return;
    invoicesApi.listByClient(user.id)
      .then((res) => setInvoices((res.data as Invoice[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [user?.id]);

  const statusBadge = (status: string) => {
    const map: Record<string, string> = {
      paid: "badge-paid", unpaid: "badge-unpaid", draft: "badge-draft",
      cancelled: "badge-cancelled", overdue: "badge-overdue",
    };
    return <span className={`badge ${map[status] || "badge-pending"}`}>{status}</span>;
  };

  return (
    <>
      <Header title="My Invoices" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">My Invoices</h2>
            <p className="page-subtitle">{invoices.length} invoice(s)</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : invoices.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">💳</div>
              <div className="empty-state-title">No invoices</div>
              <div className="empty-state-text">Your invoices will appear here.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Invoice #</th>
                    <th>Created</th>
                    <th>Due</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Paid</th>
                  </tr>
                </thead>
                <tbody>
                  {invoices.map((inv) => (
                    <tr key={inv.id}>
                      <td><strong>{inv.invoice_num || `#${inv.id}`}</strong></td>
                      <td>{new Date(inv.date_created).toLocaleDateString()}</td>
                      <td>{new Date(inv.date_due).toLocaleDateString()}</td>
                      <td><strong>${(inv.total || 0).toFixed(2)}</strong></td>
                      <td>{statusBadge(inv.status)}</td>
                      <td>{inv.date_paid ? new Date(inv.date_paid).toLocaleDateString() : "—"}</td>
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
