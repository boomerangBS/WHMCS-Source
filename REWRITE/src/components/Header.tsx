import { useAuth } from "../api/auth";

interface HeaderProps {
  title: string;
}

export default function Header({ title }: HeaderProps) {
  const { user } = useAuth();
  const initials = `${(user?.first_name || "U")[0]}${(user?.last_name || "")[0] || ""}`.toUpperCase();

  return (
    <header className="header">
      <h1 className="header-title">{title}</h1>
      <div className="header-actions">
        <div className="header-user">
          <span>{user?.first_name} {user?.last_name}</span>
          <div className="header-avatar">{initials}</div>
        </div>
      </div>
    </header>
  );
}
