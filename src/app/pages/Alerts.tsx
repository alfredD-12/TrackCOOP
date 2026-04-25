import { Bell, AlertCircle, CheckCircle, Info, XCircle } from "lucide-react";

export default function Alerts() {
  const alerts = [
    {
      type: "critical",
      title: "Payment Deadline Approaching",
      message: "23 members have pending payments due in 3 days",
      time: "2 hours ago",
      icon: AlertCircle,
    },
    {
      type: "warning",
      title: "Document Expiration",
      message: "5 legal documents will expire within 30 days",
      time: "5 hours ago",
      icon: Info,
    },
    {
      type: "success",
      title: "Monthly Target Achieved",
      message: "Membership goal of 1,200 members reached!",
      time: "1 day ago",
      icon: CheckCircle,
    },
    {
      type: "info",
      title: "System Maintenance Scheduled",
      message: "Platform will be unavailable on Apr 15, 2:00 AM - 4:00 AM",
      time: "1 day ago",
      icon: Bell,
    },
    {
      type: "critical",
      title: "Inactive Members Alert",
      message: "58 members have been inactive for over 6 months",
      time: "2 days ago",
      icon: XCircle,
    },
    {
      type: "warning",
      title: "Low Document Storage",
      message: "Storage space is 85% full. Consider archiving old documents.",
      time: "3 days ago",
      icon: Info,
    },
  ];

  const getAlertStyles = (type: string) => {
    switch (type) {
      case "critical":
        return {
          bg: "bg-red-50",
          border: "border-red-200",
          icon: "text-red-600",
          badge: "bg-red-100 text-red-700",
        };
      case "warning":
        return {
          bg: "bg-orange-50",
          border: "border-orange-200",
          icon: "text-orange-600",
          badge: "bg-orange-100 text-orange-700",
        };
      case "success":
        return {
          bg: "bg-green-50",
          border: "border-green-200",
          icon: "text-green-600",
          badge: "bg-green-100 text-green-700",
        };
      default:
        return {
          bg: "bg-blue-50",
          border: "border-blue-200",
          icon: "text-blue-600",
          badge: "bg-blue-100 text-blue-700",
        };
    }
  };

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-display mb-2">Alerts</h1>
        <p className="text-muted-foreground">System notifications and important updates</p>
      </div>

      {/* Alert Summary */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div className="bg-card rounded-xl p-6 border border-red-200 shadow-sm">
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm text-muted-foreground">Critical</span>
            <AlertCircle className="w-5 h-5 text-red-600" />
          </div>
          <div className="text-3xl font-bold text-red-600">2</div>
        </div>
        <div className="bg-card rounded-xl p-6 border border-orange-200 shadow-sm">
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm text-muted-foreground">Warning</span>
            <Info className="w-5 h-5 text-orange-600" />
          </div>
          <div className="text-3xl font-bold text-orange-600">2</div>
        </div>
        <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm">
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm text-muted-foreground">Success</span>
            <CheckCircle className="w-5 h-5 text-green-600" />
          </div>
          <div className="text-3xl font-bold text-green-600">1</div>
        </div>
        <div className="bg-card rounded-xl p-6 border border-blue-200 shadow-sm">
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm text-muted-foreground">Info</span>
            <Bell className="w-5 h-5 text-blue-600" />
          </div>
          <div className="text-3xl font-bold text-blue-600">1</div>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-card rounded-xl p-4 border border-border shadow-sm mb-6">
        <div className="flex flex-wrap gap-2">
          <button className="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm">
            All Alerts
          </button>
          <button className="px-4 py-2 bg-muted text-muted-foreground rounded-lg text-sm hover:bg-secondary transition-colors">
            Critical
          </button>
          <button className="px-4 py-2 bg-muted text-muted-foreground rounded-lg text-sm hover:bg-secondary transition-colors">
            Warning
          </button>
          <button className="px-4 py-2 bg-muted text-muted-foreground rounded-lg text-sm hover:bg-secondary transition-colors">
            Success
          </button>
          <button className="px-4 py-2 bg-muted text-muted-foreground rounded-lg text-sm hover:bg-secondary transition-colors">
            Info
          </button>
        </div>
      </div>

      {/* Alerts List */}
      <div className="space-y-4">
        {alerts.map((alert, index) => {
          const styles = getAlertStyles(alert.type);
          const Icon = alert.icon;

          return (
            <div
              key={index}
              className={`${styles.bg} ${styles.border} border rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow`}
            >
              <div className="flex items-start gap-4">
                <div className={`${styles.icon} mt-1`}>
                  <Icon className="w-6 h-6" />
                </div>

                <div className="flex-1">
                  <div className="flex items-start justify-between mb-2">
                    <h3 className="font-bold">{alert.title}</h3>
                    <span className={`${styles.badge} px-3 py-1 rounded-full text-xs uppercase tracking-wide`}>
                      {alert.type}
                    </span>
                  </div>
                  <p className="text-muted-foreground mb-3">{alert.message}</p>
                  <div className="flex items-center gap-4">
                    <span className="text-xs text-muted-foreground">{alert.time}</span>
                    <button className="text-sm text-primary hover:underline">Take Action</button>
                    <button className="text-sm text-muted-foreground hover:text-foreground">Dismiss</button>
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
