import { useState } from "react";
import { useNavigate } from "react-router";
import { Eye, EyeOff } from "lucide-react";
import TrackCoopLogo from "../components/TrackCoopLogo";

export default function Login() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("chairman@trackcoop.com");
  const [password, setPassword] = useState("chairman123");
  const [role, setRole] = useState<"chairman" | "bookkeeper" | "member">("chairman");
  const [showPassword, setShowPassword] = useState(false);

  const handleRoleChange = (newRole: "chairman" | "bookkeeper" | "member") => {
    setRole(newRole);

    // Auto-fill email and password based on role
    switch (newRole) {
      case "chairman":
        setEmail("chairman@trackcoop.com");
        setPassword("chairman123");
        break;
      case "bookkeeper":
        setEmail("bookkeeper@trackcoop.com");
        setPassword("bookkeeper123");
        break;
      case "member":
        setEmail("member@trackcoop.com");
        setPassword("member123");
        break;
    }
  };

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();

    // Store role in localStorage for dashboard navigation
    localStorage.setItem("userRole", role);

    // Navigate based on role
    if (role === "chairman") {
      navigate("/dashboard");
    } else if (role === "bookkeeper") {
      navigate("/dashboard/bookkeeper");
    } else if (role === "member") {
      navigate("/dashboard/member");
    }
  };

  return (
    <div className="min-h-screen flex">
      {/* Left side - Branding */}
      <div className="hidden lg:flex lg:w-1/2 bg-primary p-12 flex-col justify-between relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
          <div className="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>

        <div className="relative z-10">
          <TrackCoopLogo className="mb-3" markClassName="h-12 w-12 shadow-sm" titleClassName="text-4xl" />
          <p className="text-lg text-white/80 font-light">Cooperative Management System</p>
        </div>

        <div className="relative z-10 text-white/90">
          <h2 className="text-3xl font-display mb-4">Empowering cooperatives through digital transformation</h2>
          <p className="text-white/70 leading-relaxed">
            Streamline your cooperative's operations with our comprehensive management platform.
            Track members, manage documents, and make data-driven decisions with confidence.
          </p>
        </div>
      </div>

      {/* Right side - Login Form */}
      <div className="flex-1 flex items-center justify-center p-8 bg-background">
        <div className="w-full max-w-md animate-in fade-in slide-in-from-right-8 duration-700">
          <div className="lg:hidden mb-8">
            <TrackCoopLogo markClassName="h-10 w-10 shadow-sm" titleClassName="text-3xl" tone="dark" />
          </div>

          <div className="mb-8">
            <h2 className="text-2xl font-display mb-2 text-foreground">Welcome back</h2>
            <p className="text-muted-foreground">Sign in to access your dashboard</p>
          </div>

          <form onSubmit={handleLogin} className="space-y-5">
            <div>
              <label htmlFor="email" className="block mb-2 text-sm text-foreground">
                Email Address
              </label>
              <input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring transition-all"
                placeholder="you@example.com"
                required
              />
            </div>

            <div>
              <label htmlFor="password" className="block mb-2 text-sm text-foreground">
                Password
              </label>
              <div className="relative">
                <input
                  id="password"
                  type={showPassword ? "text" : "password"}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full px-4 py-3 pr-12 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring transition-all"
                  placeholder="••••••••"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                >
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
            </div>

            <div>
              <label htmlFor="role" className="block mb-2 text-sm text-foreground">
                Select Role
              </label>
              <select
                id="role"
                value={role}
                onChange={(e) => handleRoleChange(e.target.value as "chairman" | "bookkeeper" | "member")}
                className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring transition-all cursor-pointer"
              >
                <option value="chairman">Chairman</option>
                <option value="bookkeeper">Bookkeeper</option>
                <option value="member">Member</option>
              </select>
              <p className="mt-2 text-xs text-muted-foreground">
                Credentials will auto-fill based on selected role
              </p>
            </div>

            <button
              type="submit"
              className="w-full bg-primary text-primary-foreground py-3 rounded-lg hover:opacity-90 transition-all mt-6 shadow-sm hover:shadow-md"
            >
              Sign In
            </button>
          </form>

          <div className="mt-6 text-center text-sm text-muted-foreground">
            <a href="#" className="text-primary hover:underline">
              Forgot your password?
            </a>
          </div>
        </div>
      </div>
    </div>
  );
}
