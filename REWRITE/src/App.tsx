import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { AuthProvider } from "./api/auth";
import "./styles.css";

// Layouts
import AdminLayout from "./layouts/AdminLayout";
import ClientLayout from "./layouts/ClientLayout";

// Public pages
import LoginPage from "./pages/public/LoginPage";

// Admin pages
import AdminDashboard from "./pages/admin/Dashboard";
import ClientsPage from "./pages/admin/Clients";
import ProductsPage from "./pages/admin/Products";
import InvoicesPage from "./pages/admin/Invoices";
import OrdersPage from "./pages/admin/Orders";
import TicketsPage from "./pages/admin/Tickets";
import DomainsPage from "./pages/admin/Domains";
import AnnouncementsPage from "./pages/admin/Announcements";
import KnowledgeBasePage from "./pages/admin/KnowledgeBase";
import PromotionsPage from "./pages/admin/Promotions";
import ActivityLogPage from "./pages/admin/ActivityLog";

// Client pages
import ClientDashboard from "./pages/client/Dashboard";
import ClientServices from "./pages/client/Services";
import ClientDomains from "./pages/client/Domains";
import ClientInvoices from "./pages/client/Invoices";
import ClientTickets from "./pages/client/Tickets";
import ClientProfile from "./pages/client/Profile";

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          {/* Public */}
          <Route path="/login" element={<LoginPage />} />

          {/* Admin routes */}
          <Route path="/admin" element={<AdminLayout />}>
            <Route index element={<AdminDashboard />} />
            <Route path="clients" element={<ClientsPage />} />
            <Route path="clients/:id" element={<ClientsPage />} />
            <Route path="products" element={<ProductsPage />} />
            <Route path="invoices" element={<InvoicesPage />} />
            <Route path="orders" element={<OrdersPage />} />
            <Route path="tickets" element={<TicketsPage />} />
            <Route path="domains" element={<DomainsPage />} />
            <Route path="announcements" element={<AnnouncementsPage />} />
            <Route path="kb" element={<KnowledgeBasePage />} />
            <Route path="promotions" element={<PromotionsPage />} />
            <Route path="activity" element={<ActivityLogPage />} />
          </Route>

          {/* Client routes */}
          <Route path="/client" element={<ClientLayout />}>
            <Route index element={<ClientDashboard />} />
            <Route path="services" element={<ClientServices />} />
            <Route path="domains" element={<ClientDomains />} />
            <Route path="invoices" element={<ClientInvoices />} />
            <Route path="tickets" element={<ClientTickets />} />
            <Route path="profile" element={<ClientProfile />} />
          </Route>

          {/* Default redirect */}
          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  );
}
