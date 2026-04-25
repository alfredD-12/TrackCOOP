import { useEffect } from "react";
import { useNavigate } from "react-router";
import { CheckCircle, Wallet, Calendar, Megaphone, Activity } from "lucide-react";
import { formatCurrency } from "../../utils/formatters";
import TooltipHint from "../components/Tooltip";

export default function MemberDashboard() {
  const navigate = useNavigate();

  // Check user role - only Member can access this page
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

  // Member data (this would come from an API in a real app)
  const memberData = {
    name: "Maria Santos",
    id: "M001",
    status: "Active",
    shareCapital: 25000,
    lastContribution: "Apr 14, 2026",
  };

  const recentAnnouncements = [
    {
      id: "ann-1",
      title: "Annual General Meeting - April 25, 2026",
      date: "Apr 12, 2026",
      excerpt: "All members are invited to attend the Annual General Meeting...",
    },
    {
      id: "ann-2",
      title: "New Mobile App Launch",
      date: "Apr 10, 2026",
      excerpt: "We're excited to announce the launch of our new TrackCOOP mobile app...",
    },
    {
      id: "ann-3",
      title: "Dividend Distribution Schedule",
      date: "Apr 8, 2026",
      excerpt: "Member dividends for Q1 2026 will be distributed on April 30...",
    },
    {
      id: "ann-4",
      title: "Training Workshop: Financial Literacy",
      date: "Apr 5, 2026",
      excerpt: "Join us for a free financial literacy workshop on May 5, 2026...",
    },
  ];

  const recentActivity = [
    {
      id: "act-1",
      type: "contribution",
      description: "Share Capital Payment Added",
      amount: 5000,
      date: "Apr 14, 2026",
      icon: Wallet,
      color: "text-green-600",
      bgColor: "bg-green-100",
    },
    {
      id: "act-2",
      type: "meeting",
      description: "Attended Board Meeting",
      date: "Apr 10, 2026",
      icon: Activity,
      color: "text-blue-600",
      bgColor: "bg-blue-100",
    },
    {
      id: "act-3",
      type: "document",
      description: "Viewed Annual Report 2025",
      date: "Apr 8, 2026",
      icon: Activity,
      color: "text-purple-600",
      bgColor: "bg-purple-100",
    },
    {
      id: "act-4",
      type: "contribution",
      description: "Share Capital Payment Added",
      amount: 5000,
      date: "Mar 14, 2026",
      icon: Wallet,
      color: "text-green-600",
      bgColor: "bg-green-100",
    },
    {
      id: "act-5",
      type: "announcement",
      description: "Read Announcement: New App Launch",
      date: "Mar 10, 2026",
      icon: Megaphone,
      color: "text-amber-600",
      bgColor: "bg-amber-100",
    },
  ];

  return (
    <div className="p-8">
      {/* Welcome Banner */}
      <div className="bg-primary rounded-xl p-8 text-white mb-8 shadow-lg">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-display mb-2">Welcome back, {memberData.name}!</h1>
            <p className="text-white/80">Member ID: {memberData.id} • Here's your cooperative dashboard overview</p>
          </div>
          <div className="hidden md:block">
            <div className="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
              <span className="text-5xl font-bold">{memberData.name.charAt(0)}</span>
            </div>
          </div>
        </div>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {/* My Status */}
        <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <CheckCircle className="w-6 h-6 text-green-600" />
            </div>
          </div>
          <h3 className="text-sm text-muted-foreground mb-2">My Status</h3>
          <div className="flex items-center gap-2">
            <TooltipHint content="You are actively participating and contributing regularly to the cooperative" position="top">
              <span className="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-lg font-bold cursor-help">
                {memberData.status}
              </span>
            </TooltipHint>
          </div>
        </div>

        {/* Total Share Capital */}
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
              <Wallet className="w-6 h-6 text-primary" />
            </div>
          </div>
          <h3 className="text-sm text-muted-foreground mb-2">Total Share Capital</h3>
          <div className="text-3xl font-bold text-primary">{formatCurrency(memberData.shareCapital)}</div>
        </div>

        {/* Last Contribution Date */}
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <Calendar className="w-6 h-6 text-blue-600" />
            </div>
          </div>
          <h3 className="text-sm text-muted-foreground mb-2">Last Contribution Date</h3>
          <div className="text-xl font-bold">{memberData.lastContribution}</div>
        </div>
      </div>

      {/* Two Column Layout */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Announcements */}
        <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
          <div className="p-6 border-b border-border">
            <div className="flex items-center justify-between">
              <h2 className="text-xl font-display">Recent Announcements</h2>
              <button
                onClick={() => navigate("/dashboard/member-announcements")}
                className="text-sm text-primary hover:underline"
              >
                View All
              </button>
            </div>
          </div>
          <div className="divide-y divide-border">
            {recentAnnouncements.map((announcement) => (
              <div
                key={announcement.id}
                className="p-6 hover:bg-muted/30 transition-colors cursor-pointer"
                onClick={() => navigate("/dashboard/member-announcements")}
              >
                <div className="flex items-start gap-3">
                  <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                    <Megaphone className="w-5 h-5 text-primary" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <h3 className="font-bold mb-1 line-clamp-1">{announcement.title}</h3>
                    <p className="text-sm text-muted-foreground mb-2 line-clamp-2">{announcement.excerpt}</p>
                    <div className="flex items-center gap-2 text-xs text-muted-foreground">
                      <Calendar className="w-3 h-3" />
                      <span>{announcement.date}</span>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* My Recent Activity */}
        <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
          <div className="p-6 border-b border-border">
            <h2 className="text-xl font-display">My Recent Activity</h2>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              {recentActivity.map((activity, index) => {
                const Icon = activity.icon;
                return (
                  <div key={activity.id} className="flex items-start gap-4">
                    {/* Timeline connector */}
                    <div className="flex flex-col items-center">
                      <div className={`w-10 h-10 ${activity.bgColor} rounded-full flex items-center justify-center shrink-0`}>
                        <Icon className={`w-5 h-5 ${activity.color}`} />
                      </div>
                      {index < recentActivity.length - 1 && (
                        <div className="w-0.5 h-8 bg-border mt-2"></div>
                      )}
                    </div>

                    {/* Activity content */}
                    <div className="flex-1 pb-4">
                      <div className="font-medium mb-1">{activity.description}</div>
                      {activity.amount && (
                        <div className="text-sm font-bold text-green-600 mb-1">{formatCurrency(activity.amount)}</div>
                      )}
                      <div className="flex items-center gap-2 text-xs text-muted-foreground">
                        <Calendar className="w-3 h-3" />
                        <span>{activity.date}</span>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
