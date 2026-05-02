import { useEffect } from "react";
import { useNavigate } from "react-router";
import {
  Activity,
  ArrowRight,
  Calendar,
  CheckCircle,
  Clock3,
  Megaphone,
  Wallet,
} from "lucide-react";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&q=80&w=2400";

export default function MemberDashboard() {
  const navigate = useNavigate();

  useEffect(() => {
    const userRole = localStorage.getItem("userRole");

    if (userRole === "chairman") {
      navigate("/dashboard");
    } else if (userRole === "bookkeeper") {
      navigate("/dashboard/bookkeeper");
    } else if (!userRole) {
      navigate("/");
    }
  }, [navigate]);

  const memberData = {
    name: "Maria Santos",
    id: "M001",
    status: "Active",
    shareCapital: 25000,
    lastContribution: "Apr 14, 2026",
    nextDueDate: "May 15, 2026",
    sector: "Rice Farming",
  };

  const recentAnnouncements = [
    {
      id: "ann-1",
      title: "Annual General Meeting - April 25, 2026",
      date: "Apr 12, 2026",
      excerpt: "All members are invited to attend the Annual General Meeting.",
    },
    {
      id: "ann-2",
      title: "New Mobile App Launch",
      date: "Apr 10, 2026",
      excerpt: "TrackCOOP mobile access is now available for members.",
    },
    {
      id: "ann-3",
      title: "Dividend Distribution Schedule",
      date: "Apr 8, 2026",
      excerpt: "Member dividends for Q1 2026 will be distributed on April 30.",
    },
    {
      id: "ann-4",
      title: "Training Workshop: Financial Literacy",
      date: "Apr 5, 2026",
      excerpt: "A free financial literacy workshop will be held on May 5, 2026.",
    },
  ];

  const recentActivity = [
    {
      id: "act-1",
      description: "Share Capital Payment Added",
      amount: 5000,
      date: "Apr 14, 2026",
      type: "Contribution",
      icon: Wallet,
      iconClassName: "bg-green-100 text-green-700",
    },
    {
      id: "act-2",
      description: "Attended Board Meeting",
      date: "Apr 10, 2026",
      type: "Attendance",
      icon: Activity,
      iconClassName: "bg-blue-100 text-blue-700",
    },
    {
      id: "act-3",
      description: "Viewed Annual Report 2025",
      date: "Apr 8, 2026",
      type: "Document",
      icon: Activity,
      iconClassName: "bg-purple-100 text-purple-700",
    },
    {
      id: "act-4",
      description: "Read Announcement: New App Launch",
      date: "Mar 10, 2026",
      type: "Announcement",
      icon: Megaphone,
      iconClassName: "bg-amber-100 text-amber-700",
    },
  ];

  return (
    <div className="min-h-full bg-stone-50 text-gray-950">
      <section className="relative overflow-hidden border-b border-stone-200">
        <img
          src={heroImage}
          alt=""
          aria-hidden="true"
          className="absolute inset-0 h-full w-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/15" />
        <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-stone-50 to-transparent" />
        <div className="relative mx-auto flex min-h-[280px] max-w-[1600px] flex-col justify-start px-6 py-8 md:min-h-[320px] md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
              <div className="max-w-4xl">
                <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                  Member Dashboard
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Welcome back, {memberData.name}
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Track your contributions, review updates, and stay aligned with cooperative activity.
                </p>
              </div>
              <div className="rounded-2xl border border-white/20 bg-white/10 px-5 py-4 text-white shadow-lg backdrop-blur">
                <div className="text-xs font-bold uppercase tracking-[0.18em] text-white/70">
                  Member Snapshot
                </div>
                <div className="mt-3 space-y-1 text-sm">
                  <p>{memberData.id}</p>
                  <p>{memberData.sector}</p>
                  <p>{memberData.status}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <CheckCircle className="h-6 w-6 text-green-600" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold text-green-600">{memberData.status}</div>
            <div className="text-sm text-muted-foreground">Membership Status</div>
          </div>

          <div className="rounded-xl border border-border bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <Wallet className="h-6 w-6 text-primary" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold">{formatCurrency(memberData.shareCapital)}</div>
            <div className="text-sm text-muted-foreground">Total Share Capital</div>
          </div>

          <div className="rounded-xl border border-blue-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                <Clock3 className="h-6 w-6 text-blue-700" />
              </div>
            </div>
            <div className="mb-1 text-2xl font-bold">{memberData.nextDueDate}</div>
            <div className="text-sm text-muted-foreground">Next Contribution Due</div>
          </div>
        </div>

        <div className="grid grid-cols-1 gap-6 xl:grid-cols-[1.15fr_0.85fr]">
          <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500">
            <div className="border-b border-stone-200 px-5 py-5 md:px-6">
              <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                  <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    Announcement Feed
                  </p>
                  <h2 className="mt-1 text-xl font-display">Recent Announcements</h2>
                </div>
                <button
                  onClick={() => navigate("/dashboard/member-announcements")}
                  className="inline-flex items-center gap-2 text-sm font-semibold text-primary transition-colors hover:text-green-800"
                >
                  View all
                  <ArrowRight className="h-4 w-4" />
                </button>
              </div>
            </div>

            <div className="divide-y divide-stone-100">
              {recentAnnouncements.map((announcement, index) => (
                <button
                  key={announcement.id}
                  onClick={() => navigate("/dashboard/member-announcements")}
                  className="flex w-full items-start gap-4 px-5 py-5 text-left transition-all hover:bg-green-50/40 md:px-6"
                  style={{ animationDelay: `${Math.min(index * 35, 160)}ms` }}
                >
                  <div className="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                    <Megaphone className="h-5 w-5 text-primary" />
                  </div>
                  <div className="min-w-0 flex-1">
                    <div className="flex flex-col justify-between gap-2 sm:flex-row sm:items-start">
                      <h3 className="font-semibold text-gray-950">{announcement.title}</h3>
                      <div className="flex items-center gap-2 text-xs text-gray-500">
                        <Calendar className="h-3.5 w-3.5" />
                        <span>{announcement.date}</span>
                      </div>
                    </div>
                    <p className="mt-2 text-sm leading-6 text-gray-500">{announcement.excerpt}</p>
                  </div>
                </button>
              ))}
            </div>
          </section>

          <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
            <div className="border-b border-stone-200 px-5 py-5 md:px-6">
              <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                Activity Log
              </p>
              <h2 className="mt-1 text-xl font-display">Recent Activity</h2>
            </div>

            <div className="px-5 py-5 md:px-6">
              <div className="space-y-4">
                {recentActivity.map((activity, index) => {
                  const Icon = activity.icon;

                  return (
                    <div
                      key={activity.id}
                      className="flex items-start gap-4 rounded-lg border border-stone-100 bg-stone-50/70 px-4 py-4"
                      style={{ animationDelay: `${Math.min(index * 40, 160)}ms` }}
                    >
                      <div className={`flex h-11 w-11 shrink-0 items-center justify-center rounded-lg ${activity.iconClassName}`}>
                        <Icon className="h-5 w-5" />
                      </div>
                      <div className="min-w-0 flex-1">
                        <div className="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                          <p className="font-semibold text-gray-950">{activity.description}</p>
                          <p className="text-xs text-gray-500">{activity.date}</p>
                        </div>
                        <div className="mt-2 flex flex-wrap items-center gap-3 text-sm">
                          <span className="rounded-full bg-white px-3 py-1 font-medium text-gray-600 ring-1 ring-stone-200">
                            {activity.type}
                          </span>
                          {activity.amount && (
                            <span className="font-bold text-green-600">
                              {formatCurrency(activity.amount)}
                            </span>
                          )}
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          </section>
        </div>
      </main>
    </div>
  );
}
