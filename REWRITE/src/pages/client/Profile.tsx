import { useState, useEffect, type FormEvent } from "react";
import Header from "../../components/Header";
import { useAuth } from "../../api/auth";
import { clientsApi } from "../../api/client";

export default function ClientProfile() {
  const { user } = useAuth();
  const [form, setForm] = useState({
    first_name: "", last_name: "", company_name: "", email: "",
    address1: "", address2: "", city: "", state: "", postcode: "",
    country: "", phone_number: "",
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  useEffect(() => {
    if (!user?.id) return;
    clientsApi.get(user.id)
      .then((res) => {
        const data = res.data as Record<string, unknown>;
        setForm({
          first_name: (data.first_name as string) || "",
          last_name: (data.last_name as string) || "",
          company_name: (data.company_name as string) || "",
          email: (data.email as string) || "",
          address1: (data.address1 as string) || "",
          address2: (data.address2 as string) || "",
          city: (data.city as string) || "",
          state: (data.state as string) || "",
          postcode: (data.postcode as string) || "",
          country: (data.country as string) || "",
          phone_number: (data.phone_number as string) || "",
        });
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [user?.id]);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    if (!user?.id) return;
    setError(""); setSuccess("");
    setSaving(true);
    try {
      await clientsApi.update(user.id, form);
      setSuccess("Profile updated successfully!");
    } catch (err) {
      setError(err instanceof Error ? err.message : "Failed to update profile");
    } finally {
      setSaving(false);
    }
  };

  return (
    <>
      <Header title="My Profile" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Profile Settings</h2>
            <p className="page-subtitle">Manage your personal information</p>
          </div>
        </div>
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : (
          <div className="card">
            <div className="card-body">
              {error && <div className="alert alert-error">{error}</div>}
              {success && <div className="alert alert-success">{success}</div>}
              <form onSubmit={handleSubmit}>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">First Name</label>
                    <input className="form-input" value={form.first_name} onChange={(e) => setForm({ ...form, first_name: e.target.value })} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Last Name</label>
                    <input className="form-input" value={form.last_name} onChange={(e) => setForm({ ...form, last_name: e.target.value })} />
                  </div>
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Email</label>
                    <input className="form-input" type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Company</label>
                    <input className="form-input" value={form.company_name} onChange={(e) => setForm({ ...form, company_name: e.target.value })} />
                  </div>
                </div>
                <div className="form-group">
                  <label className="form-label">Address</label>
                  <input className="form-input" value={form.address1} onChange={(e) => setForm({ ...form, address1: e.target.value })} />
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">City</label>
                    <input className="form-input" value={form.city} onChange={(e) => setForm({ ...form, city: e.target.value })} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">State</label>
                    <input className="form-input" value={form.state} onChange={(e) => setForm({ ...form, state: e.target.value })} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Postcode</label>
                    <input className="form-input" value={form.postcode} onChange={(e) => setForm({ ...form, postcode: e.target.value })} />
                  </div>
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-label">Country</label>
                    <input className="form-input" value={form.country} onChange={(e) => setForm({ ...form, country: e.target.value })} />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Phone</label>
                    <input className="form-input" value={form.phone_number} onChange={(e) => setForm({ ...form, phone_number: e.target.value })} />
                  </div>
                </div>
                <div style={{ display: "flex", justifyContent: "flex-end", marginTop: 16 }}>
                  <button className="btn btn-primary" type="submit" disabled={saving}>
                    {saving ? "Saving..." : "Save Changes"}
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
