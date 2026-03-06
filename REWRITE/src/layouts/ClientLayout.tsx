import { Outlet, Navigate } from "react-router-dom";
import { useAuth } from "../api/auth";
import Sidebar from "../components/Sidebar";

export default function ClientLayout() {
  const { isAuthenticated } = useAuth();

  if (!isAuthenticated) return <Navigate to="/login" replace />;

  return (
    <div className="app-layout">
      <Sidebar />
      <div className="main-content">
        <Outlet />
      </div>
    </div>
  );
}
