import { createContext, useContext, useState, useEffect, type ReactNode } from "react";
import { authApi } from "../api/client";

interface User {
  id: number;
  email: string;
  first_name: string;
  last_name: string;
  [key: string]: unknown;
}

interface AuthState {
  token: string | null;
  user: User | null;
  role: "admin" | "client" | null;
}

interface AuthContextType extends AuthState {
  loginAdmin: (email: string, password: string) => Promise<void>;
  loginClient: (email: string, password: string) => Promise<void>;
  logout: () => void;
  isAuthenticated: boolean;
  isAdmin: boolean;
}

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [state, setState] = useState<AuthState>(() => {
    const token = localStorage.getItem("token");
    const userStr = localStorage.getItem("user");
    const role = localStorage.getItem("role") as AuthState["role"];
    return {
      token,
      user: userStr ? JSON.parse(userStr) : null,
      role,
    };
  });

  useEffect(() => {
    if (state.token) {
      localStorage.setItem("token", state.token);
    } else {
      localStorage.removeItem("token");
    }
    if (state.user) {
      localStorage.setItem("user", JSON.stringify(state.user));
    } else {
      localStorage.removeItem("user");
    }
    if (state.role) {
      localStorage.setItem("role", state.role);
    } else {
      localStorage.removeItem("role");
    }
  }, [state]);

  const loginAdmin = async (email: string, password: string) => {
    const res = await authApi.adminLogin(email, password);
    setState({ token: res.token, user: res.user as User, role: "admin" });
  };

  const loginClient = async (email: string, password: string) => {
    const res = await authApi.clientLogin(email, password);
    setState({ token: res.token, user: res.user as User, role: "client" });
  };

  const logout = () => {
    setState({ token: null, user: null, role: null });
  };

  const value: AuthContextType = {
    ...state,
    loginAdmin,
    loginClient,
    logout,
    isAuthenticated: !!state.token,
    isAdmin: state.role === "admin",
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

// eslint-disable-next-line react-refresh/only-export-components
export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used within AuthProvider");
  return ctx;
}
