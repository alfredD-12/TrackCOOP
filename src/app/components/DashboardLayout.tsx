import { useState, useEffect } from "react";
import { Outlet, useNavigate, useLocation, Link } from "react-router";
import {
  LayoutDashboard,
  FileText,
  Users,
  TrendingUp,
  Megaphone,
  Bell,
  User,
  LogOut,
  Image,
  FileBarChart,
  Receipt,
  Wallet
} from "lucide-react";
import TrackCoopLogo from "./TrackCoopLogo";

type UserRole = "chairman" | "bookkeeper" | "member";

interface NavItem {
  name: string;
  path: string;
  icon: React.ElementType;
  roles: UserRole[];
}

const navItems: NavItem[] = [
  // Chairman pages
  { name: "Dashboard", path: "/dashboard", icon: LayoutDashboard, roles: ["chairman"] },
  { name: "Documents", path: "/dashboard/documents", icon: FileText, roles: ["chairman"] },
  { name: "Members", path: "/dashboard/members", icon: Users, roles: ["chairman"] },
  { name: "Predictions", path: "/dashboard/predictions", icon: TrendingUp, roles: ["chairman"] },
  { name: "Announcements", path: "/dashboard/announcements", icon: Megaphone, roles: ["chairman"] },
  { name: "Alerts", path: "/dashboard/alerts", icon: Bell, roles: ["chairman"] },
  { name: "Gallery", path: "/dashboard/gallery", icon: Image, roles: ["chairman"] },
  { name: "Reports", path: "/dashboard/reports", icon: FileBarChart, roles: ["chairman"] },

  // Bookkeeper pages
  { name: "Dashboard", path: "/dashboard/bookkeeper", icon: LayoutDashboard, roles: ["bookkeeper"] },
  { name: "Share Capital", path: "/dashboard/share-capital", icon: Wallet, roles: ["bookkeeper"] },
  { name: "Financial", path: "/dashboard/financial", icon: Wallet, roles: ["bookkeeper"] },
  { name: "Expenditures", path: "/dashboard/expenditures", icon: Receipt, roles: ["bookkeeper"] },

  // Member pages
  { name: "Dashboard", path: "/dashboard/member", icon: LayoutDashboard, roles: ["member"] },
  { name: "My Profile", path: "/dashboard/profile", icon: User, roles: ["member"] },
  { name: "Announcements", path: "/dashboard/member-announcements", icon: Megaphone, roles: ["member"] },
];

export default function DashboardLayout() {
  const navigate = useNavigate();
  const location = useLocation();
  const [userRole, setUserRole] = useState<UserRole>("chairman");

  useEffect(() => {
    const role = localStorage.getItem("userRole") as UserRole;
    if (role) {
      setUserRole(role);
    } else {
      navigate("/");
    }
  }, [navigate]);

  const handleLogout = () => {
    localStorage.removeItem("userRole");
    navigate("/");
  };

  const filteredNavItems = navItems.filter(item => item.roles.includes(userRole));

  const isActive = (path: string) => {
    if (path === "/dashboard" || path === "/dashboard/bookkeeper" || path === "/dashboard/member") {
      return location.pathname === path;
    }
    return location.pathname.startsWith(path);
  };

  return (
    <div className="flex h-screen bg-background">
      {/* Sidebar */}
      <aside className="w-64 bg-sidebar text-sidebar-foreground flex flex-col shadow-lg">
        {/* Logo */}
        <div className="p-6 border-b border-sidebar-border">
          <div>
            <TrackCoopLogo markClassName="h-10 w-10 shadow-sm" titleClassName="text-xl" />
            <p className="mt-2 text-xs text-sidebar-foreground/70 capitalize">{userRole}</p>
          </div>
        </div>

        {/* Navigation */}
        <nav className="flex-1 p-4 overflow-y-auto">
          <ul className="space-y-1">
            {filteredNavItems.map((item) => {
              const Icon = item.icon;
              const active = isActive(item.path);

              return (
                <li key={item.path}>
                  <Link
                    to={item.path}
                    className={`flex items-center gap-3 px-4 py-3 rounded-lg transition-all cursor-pointer ${
                      active
                        ? "bg-sidebar-primary text-sidebar-primary-foreground shadow-md font-medium"
                        : "text-sidebar-foreground/80 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground hover:shadow-sm font-normal"
                    }`}
                  >
                    <Icon className="w-5 h-5" />
                    <span className="font-medium">{item.name}</span>
                  </Link>
                </li>
              );
            })}
          </ul>
        </nav>

        {/* Logout */}
        <div className="p-4 border-t border-sidebar-border">
          <button
            onClick={handleLogout}
            className="flex items-center gap-3 px-4 py-3 rounded-lg w-full text-sidebar-foreground/80 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-all"
          >
            <LogOut className="w-5 h-5" />
            <span>Logout</span>
          </button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-auto">
        <Outlet />
      </main>
    </div>
  );
}
