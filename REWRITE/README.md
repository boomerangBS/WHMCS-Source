# WHMCS Rewrite — Frontend

Modern React frontend for the WHMCS Rebuild API backend. Built with **React 19**, **TypeScript**, **Vite**, and **React Router**.

## Tech Stack

| Technology | Purpose |
|------------|---------|
| React 19 | UI framework |
| TypeScript | Type-safe JavaScript |
| Vite 7 | Build tool & dev server |
| React Router 7 | Client-side routing |
| CSS (custom) | Modern responsive styling |

## Quick Start

```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Lint code
npm run lint
```

The development server starts at **http://localhost:5173** by default.

## Configuration

Set the API backend URL via environment variable:

```bash
# .env or .env.local
VITE_API_BASE=http://localhost:8080/api/v1
```

By default, the frontend connects to `http://localhost:8080/api/v1` (the REBUILD backend).

## Project Structure

```
REWRITE/
├── index.html                    # HTML entry point
├── package.json                  # Dependencies & scripts
├── vite.config.ts                # Vite configuration
├── tsconfig.json                 # TypeScript configuration
├── eslint.config.js              # ESLint configuration
├── public/                       # Static assets
└── src/
    ├── main.tsx                  # React entry point
    ├── App.tsx                   # Root component & routing
    ├── styles.css                # Global styles
    ├── api/
    │   ├── client.ts             # API client (all endpoint calls)
    │   └── auth.tsx              # Auth context & provider
    ├── components/
    │   ├── Header.tsx            # Page header with user info
    │   ├── Modal.tsx             # Reusable modal dialog
    │   └── Sidebar.tsx           # Navigation sidebar
    ├── layouts/
    │   ├── AdminLayout.tsx       # Admin area layout (auth guard)
    │   └── ClientLayout.tsx      # Client area layout (auth guard)
    └── pages/
        ├── public/
        │   └── LoginPage.tsx     # Login & registration
        ├── admin/
        │   ├── Dashboard.tsx     # Admin dashboard with stats
        │   ├── Clients.tsx       # Client management
        │   ├── Products.tsx      # Product CRUD
        │   ├── Orders.tsx        # Order management (accept/cancel)
        │   ├── Invoices.tsx      # Invoice management
        │   ├── Tickets.tsx       # Ticket management & replies
        │   ├── Domains.tsx       # Domain management
        │   ├── Announcements.tsx # Announcement CRUD
        │   ├── KnowledgeBase.tsx # KB categories & articles
        │   ├── Promotions.tsx    # Promo code management
        │   └── ActivityLog.tsx   # Audit trail viewer
        └── client/
            ├── Dashboard.tsx     # Client overview
            ├── Services.tsx      # My services list
            ├── Domains.tsx       # My domains list
            ├── Invoices.tsx      # My invoices list
            ├── Tickets.tsx       # My tickets & create new
            └── Profile.tsx       # Profile editor
```

## Features

### Authentication
- **Admin login** — Full administrative access
- **Client login** — Client area access
- **Client registration** — Self-service account creation
- JWT token management with automatic storage

### Admin Panel (`/admin`)
- **Dashboard** — Real-time statistics (clients, services, invoices, tickets, revenue)
- **Client Management** — List, view, and manage clients
- **Product Management** — Create, edit, and delete products with pricing
- **Order Management** — View orders, accept or cancel
- **Invoice Management** — View all invoices and their status
- **Ticket System** — View, reply to, and close support tickets
- **Domain Management** — Register and manage domains
- **Announcements** — Create and publish announcements
- **Knowledge Base** — Browse categories and articles
- **Promotions** — Create and manage promo codes
- **Activity Log** — Full audit trail of system actions

### Client Area (`/client`)
- **Dashboard** — Overview of services, invoices, domains, and tickets
- **My Services** — View active services and billing details
- **My Domains** — View registered domains and nameservers
- **My Invoices** — View invoice history and payment status
- **Support Tickets** — Open new tickets, reply to existing ones
- **Profile** — Edit personal information

## API Integration

The frontend connects to all REBUILD backend API endpoints:

- `POST /auth/admin/login` — Admin authentication
- `POST /auth/client/login` — Client authentication
- `POST /auth/client/register` — Client registration
- `GET /admin/dashboard` — Dashboard statistics
- `GET/POST /admin/clients` — Client management
- `GET/POST/PUT/DELETE /admin/products` — Product CRUD
- `GET/POST /admin/orders` — Order management
- `GET/POST /admin/invoices` — Invoice management
- `GET/POST /tickets` — Ticket operations
- `GET/POST /admin/domains` — Domain management
- `GET/POST /admin/announcements` — Announcement management
- `GET/POST /admin/promotions` — Promotion management
- `GET /admin/activity-log` — Activity log
- And more...

## Design

- **Responsive layout** — Sidebar navigation with collapsible design on mobile
- **Clean UI** — Modern card-based design with consistent spacing
- **Color-coded badges** — Status indicators for all entity types
- **Modal dialogs** — Create/view forms in overlay modals
- **Empty states** — Friendly messages when no data is available
- **Loading spinners** — Visual feedback during API calls
