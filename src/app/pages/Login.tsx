import { useState } from "react";
import { useNavigate } from "react-router";
import { Eye, EyeOff, Mail, X } from "lucide-react";
import TrackCoopLogo from "../components/TrackCoopLogo";

export default function Login() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("chairman@trackcoop.com");
  const [password, setPassword] = useState("chairman123");
  const [role, setRole] = useState<"chairman" | "bookkeeper" | "member">("chairman");
  const [showPassword, setShowPassword] = useState(false);
  const [rememberMe, setRememberMe] = useState(true);
  const [forgotModalOpen, setForgotModalOpen] = useState(false);
  const [validationErrors, setValidationErrors] = useState<{ email?: string; password?: string }>({});

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

    const errors: { email?: string; password?: string } = {};
    if (!email.trim()) {
      errors.email = "Email address is required.";
    }
    if (!password.trim()) {
      errors.password = "Password is required.";
    }

    setValidationErrors(errors);

    if (Object.keys(errors).length > 0) {
      return;
    }

    // Store role in localStorage for dashboard navigation
    localStorage.setItem("userRole", role);
    localStorage.setItem("rememberTrackCoopLogin", rememberMe ? "true" : "false");

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
                onChange={(e) => {
                  setEmail(e.target.value);
                  setValidationErrors((current) => ({ ...current, email: undefined }));
                }}
                className={`w-full px-4 py-3 rounded-lg border bg-input-background focus:outline-none focus:ring-2 transition-all ${
                  validationErrors.email
                    ? "border-red-300 focus:ring-red-200"
                    : "border-input focus:ring-ring"
                }`}
                placeholder="you@example.com"
                aria-invalid={Boolean(validationErrors.email)}
              />
              {validationErrors.email && (
                <p className="mt-2 text-sm font-medium text-red-600">{validationErrors.email}</p>
              )}
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
                  onChange={(e) => {
                    setPassword(e.target.value);
                    setValidationErrors((current) => ({ ...current, password: undefined }));
                  }}
                  className={`w-full px-4 py-3 pr-12 rounded-lg border bg-input-background focus:outline-none focus:ring-2 transition-all ${
                    validationErrors.password
                      ? "border-red-300 focus:ring-red-200"
                      : "border-input focus:ring-ring"
                  }`}
                  placeholder="••••••••"
                  aria-invalid={Boolean(validationErrors.password)}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  aria-label={showPassword ? "Hide password" : "Show password"}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                >
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
              {validationErrors.password && (
                <p className="mt-2 text-sm font-medium text-red-600">{validationErrors.password}</p>
              )}
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
                Demo credentials auto-fill based on role.
              </p>
            </div>

            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <label className="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                <input
                  type="checkbox"
                  checked={rememberMe}
                  onChange={(event) => setRememberMe(event.target.checked)}
                  className="h-4 w-4 rounded border-stone-300 text-primary focus:ring-primary"
                />
                Remember me
              </label>
              <button
                type="button"
                onClick={() => setForgotModalOpen(true)}
                className="text-left text-sm font-semibold text-primary transition-colors hover:text-green-800 hover:underline sm:text-right"
              >
                Forgot your password?
              </button>
            </div>

            <button
              type="submit"
              className="w-full bg-primary text-primary-foreground py-3 rounded-lg hover:opacity-90 transition-all mt-6 shadow-sm hover:shadow-md"
            >
              Sign In
            </button>
          </form>
        </div>
      </div>

      {forgotModalOpen && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4"
          onClick={() => setForgotModalOpen(false)}
        >
          <div
            className="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-2xl"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between gap-4 border-b border-stone-200 px-6 py-5">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">Account Recovery</p>
                <h2 className="mt-1 text-2xl font-display text-gray-950">Forgot Password</h2>
              </div>
              <button
                type="button"
                onClick={() => setForgotModalOpen(false)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
                aria-label="Close forgot password modal"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="px-6 py-6">
              <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <Mail className="h-6 w-6 text-primary" />
              </div>
              <p className="text-sm leading-6 text-gray-600">
                For this prototype, password recovery is simulated. A cooperative staff member would send reset instructions to the email address on your member record.
              </p>
              <div className="mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                Demo account selected: {email || "No email entered yet"}
              </div>
            </div>

            <div className="flex justify-end gap-3 border-t border-stone-200 px-6 py-5">
              <button
                type="button"
                onClick={() => setForgotModalOpen(false)}
                className="inline-flex h-11 items-center justify-center rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
