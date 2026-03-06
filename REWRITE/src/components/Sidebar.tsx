import { NavLink, useLocation } from "react-router-dom";
import { useAuth } from "../api/auth";

const adminLinks = [
  { label: "Dashboard", path: "/admin", icon: "📊" },
  { label: "Clients", path: "/admin/clients", icon: "👥" },
  { label: "Products", path: "/admin/products", icon: "📦" },
  { label: "Orders", path: "/admin/orders", icon: "🛒" },
  { label: "Invoices", path: "/admin/invoices", icon: "💳" },
  { label: "Tickets", path: "/admin/tickets", icon: "🎫" },
  { label: "Domains", path: "/admin/domains", icon: "🌐" },
  { label: "Announcements", path: "/admin/announcements", icon: "📢" },
  { label: "Knowledge Base", path: "/admin/kb", icon: "📚" },
  { label: "Promotions", path: "/admin/promotions", icon: "🏷️" },
  { label: "Activity Log", path: "/admin/activity", icon: "📋" },
];

const clientLinks = [
  { label: "Dashboard", path: "/client", icon: "📊" },
  { label: "Services", path: "/client/services", icon: "⚙️" },
  { label: "Domains", path: "/client/domains", icon: "🌐" },
  { label: "Invoices", path: "/client/invoices", icon: "💳" },
  { label: "Tickets", path: "/client/tickets", icon: "🎫" },
  { label: "Profile", path: "/client/profile", icon: "👤" },
];

export default function Sidebar() {
  const { isAdmin, user, logout } = useAuth();
  const location = useLocation();
  const links = isAdmin ? adminLinks : clientLinks;
  const sectionLabel = isAdmin ? "Administration" : "Client Area";

  return (
    <nav className="sidebar">
      <div className="sidebar-brand">
        ⚡ <span>WHMCS</span> Rewrite
      </div>
      <div className="sidebar-nav">
        <div className="sidebar-section">{sectionLabel}</div>
        {links.map((link) => (
          <NavLink
            key={link.path}
            to={link.path}
            end={link.path === "/admin" || link.path === "/client"}
            className={({ isActive }) =>
              `sidebar-link${isActive || (link.path !== "/admin" && link.path !== "/client" && location.pathname.startsWith(link.path)) ? " active" : ""}`
            }
          >
            <span>{link.icon}</span>
            {link.label}
          </NavLink>
        ))}
      </div>
      <div className="sidebar-footer">
        <div style={{ marginBottom: 8 }}>
          <strong>{user?.first_name} {user?.last_name}</strong>
          <br />
          <span className="text-sm text-muted">{user?.email}</span>
        </div>
        <button className="btn btn-secondary btn-sm" onClick={logout} style={{ width: "100%" }}>
          Logout
        </button>
      </div>
    </nav>
  );
}
