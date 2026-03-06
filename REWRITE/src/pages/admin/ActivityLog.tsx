import { useState, useEffect } from "react";
import Header from "../../components/Header";
import { miscApi } from "../../api/client";

interface LogEntry {
  id: number;
  admin_id: number;
  client_id: number;
  description: string;
  ip_address: string;
  created_at: string;
}

export default function ActivityLogPage() {
  const [entries, setEntries] = useState<LogEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    miscApi.activityLog()
      .then((res) => setEntries((res.data as LogEntry[]) || []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return (
    <>
      <Header title="Activity Log" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Activity Log</h2>
            <p className="page-subtitle">Audit trail of all system actions</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : entries.length === 0 ? (
          <div className="card">
            <div className="empty-state">
              <div className="empty-state-icon">📋</div>
              <div className="empty-state-title">No activity yet</div>
              <div className="empty-state-text">Actions will be logged here.</div>
            </div>
          </div>
        ) : (
          <div className="card">
            <div className="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Admin</th>
                    <th>Client</th>
                    <th>IP Address</th>
                  </tr>
                </thead>
                <tbody>
                  {entries.map((e) => (
                    <tr key={e.id}>
                      <td className="text-sm">{new Date(e.created_at).toLocaleString()}</td>
                      <td>{e.description}</td>
                      <td>{e.admin_id ? `#${e.admin_id}` : "—"}</td>
                      <td>{e.client_id ? `#${e.client_id}` : "—"}</td>
                      <td className="text-sm text-muted">{e.ip_address}</td>
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
