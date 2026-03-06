import { useState, type FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../../api/auth";
import { authApi } from "../../api/client";

export default function LoginPage() {
  const [tab, setTab] = useState<"admin" | "client">("admin");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  // Register state
  const [showRegister, setShowRegister] = useState(false);
  const [regData, setRegData] = useState({
    first_name: "",
    last_name: "",
    email: "",
    password: "",
    company_name: "",
    address1: "",
    city: "",
    state: "",
    postcode: "",
    country: "",
    phone_number: "",
  });
  const [regError, setRegError] = useState("");
  const [regSuccess, setRegSuccess] = useState("");

  const { loginAdmin, loginClient } = useAuth();
  const navigate = useNavigate();

  const handleLogin = async (e: FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);
    try {
      if (tab === "admin") {
        await loginAdmin(email, password);
        navigate("/admin");
      } else {
        await loginClient(email, password);
        navigate("/client");
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "Login failed");
    } finally {
      setLoading(false);
    }
  };

  const handleRegister = async (e: FormEvent) => {
    e.preventDefault();
    setRegError("");
    setRegSuccess("");
    setLoading(true);
    try {
      await authApi.clientRegister(regData);
      setRegSuccess("Account created! You can now log in.");
      setShowRegister(false);
      setTab("client");
      setEmail(regData.email);
    } catch (err) {
      setRegError(err instanceof Error ? err.message : "Registration failed");
    } finally {
      setLoading(false);
    }
  };

  if (showRegister) {
    return (
      <div className="login-page">
        <div className="login-card" style={{ maxWidth: 520 }}>
          <div className="login-logo">
            <h1>⚡ WHMCS Rewrite</h1>
            <p>Create your account</p>
          </div>
          {regError && <div className="alert alert-error">{regError}</div>}
          {regSuccess && <div className="alert alert-success">{regSuccess}</div>}
          <form onSubmit={handleRegister}>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">First Name *</label>
                <input className="form-input" required value={regData.first_name}
                  onChange={(e) => setRegData({ ...regData, first_name: e.target.value })} />
              </div>
              <div className="form-group">
                <label className="form-label">Last Name *</label>
                <input className="form-input" required value={regData.last_name}
                  onChange={(e) => setRegData({ ...regData, last_name: e.target.value })} />
              </div>
            </div>
            <div className="form-group">
              <label className="form-label">Email *</label>
              <input className="form-input" type="email" required value={regData.email}
                onChange={(e) => setRegData({ ...regData, email: e.target.value })} />
            </div>
            <div className="form-group">
              <label className="form-label">Password *</label>
              <input className="form-input" type="password" required value={regData.password}
                onChange={(e) => setRegData({ ...regData, password: e.target.value })} />
            </div>
            <div className="form-group">
              <label className="form-label">Company</label>
              <input className="form-input" value={regData.company_name}
                onChange={(e) => setRegData({ ...regData, company_name: e.target.value })} />
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Address</label>
                <input className="form-input" value={regData.address1}
                  onChange={(e) => setRegData({ ...regData, address1: e.target.value })} />
              </div>
              <div className="form-group">
                <label className="form-label">City</label>
                <input className="form-input" value={regData.city}
                  onChange={(e) => setRegData({ ...regData, city: e.target.value })} />
              </div>
            </div>
            <div className="form-row">
              <div className="form-group">
                <label className="form-label">Country</label>
                <input className="form-input" value={regData.country}
                  onChange={(e) => setRegData({ ...regData, country: e.target.value })} />
              </div>
              <div className="form-group">
                <label className="form-label">Phone</label>
                <input className="form-input" value={regData.phone_number}
                  onChange={(e) => setRegData({ ...regData, phone_number: e.target.value })} />
              </div>
            </div>
            <button className="btn btn-primary btn-lg" type="submit" disabled={loading} style={{ width: "100%" }}>
              {loading ? "Creating account..." : "Create Account"}
            </button>
          </form>
          <div className="login-footer">
            Already have an account?{" "}
            <a href="#" onClick={(e) => { e.preventDefault(); setShowRegister(false); }}>
              Sign in
            </a>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="login-page">
      <div className="login-card">
        <div className="login-logo">
          <h1>⚡ WHMCS Rewrite</h1>
          <p>Sign in to your account</p>
        </div>
        <div className="login-tabs">
          <button className={`login-tab${tab === "admin" ? " active" : ""}`} onClick={() => setTab("admin")}>
            Admin
          </button>
          <button className={`login-tab${tab === "client" ? " active" : ""}`} onClick={() => setTab("client")}>
            Client
          </button>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {regSuccess && <div className="alert alert-success">{regSuccess}</div>}
        <form onSubmit={handleLogin}>
          <div className="form-group">
            <label className="form-label">Email</label>
            <input className="form-input" type="email" required value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder={`${tab}@example.com`} />
          </div>
          <div className="form-group">
            <label className="form-label">Password</label>
            <input className="form-input" type="password" required value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="Enter your password" />
          </div>
          <button className="btn btn-primary btn-lg" type="submit" disabled={loading} style={{ width: "100%" }}>
            {loading ? "Signing in..." : `Sign in as ${tab === "admin" ? "Admin" : "Client"}`}
          </button>
        </form>
        {tab === "client" && (
          <div className="login-footer">
            Don&apos;t have an account?{" "}
            <a href="#" onClick={(e) => { e.preventDefault(); setShowRegister(true); }}>
              Register
            </a>
          </div>
        )}
      </div>
    </div>
  );
}
