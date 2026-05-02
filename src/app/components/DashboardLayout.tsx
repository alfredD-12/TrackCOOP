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
      <aside className="w-64 md:w-72 bg-[#f6fcf8] text-green-950 flex flex-col border-r border-green-100 shadow-sm z-10">
        {/* Logo */}
        <div className="p-6">
          <div className="flex flex-col gap-1">
            <TrackCoopLogo tone="dark" markClassName="h-9 w-9 shadow-sm ring-1 ring-green-900/5" titleClassName="text-xl" />
            <span className="mt-2 ml-1 inline-flex w-fit items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold tracking-wide text-green-700 capitalize">
              {userRole}
            </span>
          </div>
        </div>

        {/* Navigation */}
        <nav className="flex-1 px-4 py-2 overflow-y-auto">
          <div className="mb-3 px-3 text-xs font-bold text-green-800/40 uppercase tracking-widest">
            Menu
          </div>
          <ul className="space-y-1.5">
            {filteredNavItems.map((item) => {
              const Icon = item.icon;
              const active = isActive(item.path);

              return (
                <li key={item.path}>
                  <Link
                    to={item.path}
                    className={`group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all relative ${
                      active
                        ? "bg-white text-green-950 font-semibold shadow-sm ring-1 ring-green-900/5"
                        : "text-green-900/60 hover:bg-green-100/50 hover:text-green-950 font-medium"
                    }`}
                  >
                    {active && (
                      <div className="absolute -left-4 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-green-600 rounded-r-full shadow-sm" />
                    )}
                    <Icon className={`w-5 h-5 transition-colors ${active ? "text-green-600" : "text-green-800/40 group-hover:text-green-700"}`} />
                    <span>{item.name}</span>
                  </Link>
                </li>
              );
            })}
          </ul>
        </nav>

        {/* Logout */}
        <div className="p-4 border-t border-green-100">
          <button
            onClick={handleLogout}
            className="flex items-center gap-3 px-3 py-2.5 rounded-xl w-full text-green-900/60 hover:bg-red-50 hover:text-red-700 transition-all font-medium group"
          >
            <LogOut className="w-5 h-5 text-green-800/40 group-hover:text-red-500 transition-colors" />
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
