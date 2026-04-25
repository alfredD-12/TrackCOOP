import { useState, useEffect } from "react";
import { useNavigate } from "react-router";
import { Search, Calendar, CheckCircle2, Circle } from "lucide-react";

type Sector = "all" | "rice_farming" | "corn" | "fishery" | "livestock" | "high_value_crops";

interface Announcement {
  id: string;
  title: string;
  content: string;
  date: string;
  sender: string;
  sector: Sector;
  unread: boolean;
}

const sectorLabels: Record<Sector, string> = {
  all: "All Members",
  rice_farming: "Rice Farming",
  corn: "Corn",
  fishery: "Fishery",
  livestock: "Livestock",
  high_value_crops: "High-Value Crops",
};

const sectorColors: Record<Sector, string> = {
  all: "bg-blue-100 text-blue-700 border-blue-200",
  rice_farming: "bg-green-100 text-green-700 border-green-200",
  corn: "bg-yellow-100 text-yellow-700 border-yellow-200",
  fishery: "bg-cyan-100 text-cyan-700 border-cyan-200",
  livestock: "bg-orange-100 text-orange-700 border-orange-200",
  high_value_crops: "bg-purple-100 text-purple-700 border-purple-200",
};

const memberSector: Sector = "rice_farming";

export default function MemberAnnouncements() {
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState("");
  const [announcements, setAnnouncements] = useState<Announcement[]>([
    {
      id: "ann-1",
      title: "Annual General Meeting - April 25, 2026",
      content: "All members are invited to attend the Annual General Meeting on April 25, 2026, at 2:00 PM in the Community Hall. We will discuss financial reports, elect new board members, and plan for the upcoming year. Your participation is highly encouraged as we make important decisions about our cooperative's future.",
      date: "Apr 12, 2026",
      sender: "Board of Directors",
      sector: "all",
      unread: true,
    },
    {
      id: "ann-2",
      title: "Rice Farming Best Practices Workshop",
      content: "Special workshop for rice farming members on modern cultivation techniques and pest management. Join us on April 20, 2026, at 9:00 AM for a comprehensive session led by agricultural experts. Topics include sustainable farming practices, water management, and organic pest control methods.",
      date: "Apr 10, 2026",
      sender: "Agricultural Team",
      sector: "rice_farming",
      unread: true,
    },
    {
      id: "ann-3",
      title: "Dividend Distribution Schedule",
      content: "Member dividends for Q1 2026 will be distributed on April 30, 2026. Please ensure your bank account details are up to date in your profile. If you need to update your information, visit the office or update it through your member dashboard. The dividend amount is based on your share capital and participation.",
      date: "Apr 8, 2026",
      sender: "Finance Team",
      sector: "all",
      unread: false,
    },
    {
      id: "ann-4",
      title: "Rice Farming Equipment Loan Program",
      content: "We are pleased to announce a new equipment loan program specifically for rice farming members. Low-interest loans are now available for purchasing tractors, harvesters, and other essential farming equipment. Applications are being accepted until May 31, 2026.",
      date: "Apr 5, 2026",
      sender: "Loan Committee",
      sector: "rice_farming",
      unread: false,
    },
    {
      id: "ann-5",
      title: "Office Hours Update",
      content: "Starting May 1, 2026, our office hours will be Monday to Friday, 8:00 AM - 5:00 PM. Saturday hours remain 9:00 AM - 1:00 PM. Please plan your visits accordingly. For urgent matters outside office hours, you can reach us via our emergency hotline.",
      date: "Apr 2, 2026",
      sender: "Administration",
      sector: "all",
      unread: false,
    },
    {
      id: "ann-6",
      title: "Irrigation System Maintenance - Rice Farming Sector",
      content: "The cooperative's irrigation system will undergo scheduled maintenance from April 18-20, 2026. This may affect water supply to rice farming areas. We recommend adjusting your planting schedules accordingly. Our technical team will work to minimize disruption.",
      date: "Mar 30, 2026",
      sender: "Technical Services",
      sector: "rice_farming",
      unread: false,
    },
    {
      id: "ann-7",
      title: "Share Capital Contribution Reminder",
      content: "This is a friendly reminder that monthly share capital contributions are due by the 15th of each month. Regular contributions help strengthen our cooperative and increase your ownership stake. Thank you for your continued support and commitment.",
      date: "Mar 28, 2026",
      sender: "Finance Team",
      sector: "all",
      unread: false,
    },
  ]);

  useEffect(() => {
    const role = localStorage.getItem("userRole");
    if (role !== "member") {
      if (role === "chairman") {
        navigate("/dashboard");
      } else if (role === "bookkeeper") {
        navigate("/dashboard/bookkeeper");
      }
    }
  }, [navigate]);

  const relevantAnnouncements = announcements.filter(
    (ann) => ann.sector === "all" || ann.sector === memberSector
  );

  const filteredAnnouncements = relevantAnnouncements.filter((ann) =>
    ann.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
    ann.content.toLowerCase().includes(searchQuery.toLowerCase()) ||
    ann.sender.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const unreadCount = filteredAnnouncements.filter((ann) => ann.unread).length;

  const handleMarkAllAsRead = () => {
    setAnnouncements((prev) =>
      prev.map((ann) => ({ ...ann, unread: false }))
    );
  };

  const handleToggleRead = (id: string) => {
    setAnnouncements((prev) =>
      prev.map((ann) => (ann.id === id ? { ...ann, unread: !ann.unread } : ann))
    );
  };

  return (
    <div className="p-8 bg-background min-h-full">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-foreground mb-2">Announcements</h1>
        <p className="text-muted-foreground">
          Stay updated with cooperative news and important information
        </p>
      </div>

      {/* Search and Actions Bar */}
      <div className="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        {/* Search Bar */}
        <div className="relative flex-1 max-w-md">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-muted-foreground" />
          <input
            type="text"
            placeholder="Search announcements..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full pl-10 pr-4 py-2.5 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
          />
        </div>

        {/* Mark All as Read Button */}
        <button
          onClick={handleMarkAllAsRead}
          disabled={unreadCount === 0}
          className={`px-4 py-2.5 rounded-lg transition-all flex items-center gap-2 ${
            unreadCount > 0
              ? "bg-primary text-primary-foreground hover:opacity-90 shadow-sm"
              : "bg-muted text-muted-foreground cursor-not-allowed"
          }`}
        >
          <CheckCircle2 className="w-4 h-4" />
          Mark All as Read
          {unreadCount > 0 && (
            <span className="ml-1 px-2 py-0.5 bg-white/20 rounded-full text-xs">
              {unreadCount}
            </span>
          )}
        </button>
      </div>

      {/* Unread Count Summary */}
      {unreadCount > 0 && (
        <div className="mb-6 bg-primary/10 border border-primary/20 rounded-lg p-4">
          <p className="text-sm text-primary">
            You have <span className="font-bold">{unreadCount}</span> unread announcement{unreadCount !== 1 ? "s" : ""}
          </p>
        </div>
      )}

      {/* Announcements Feed */}
      <div className="space-y-4">
        {filteredAnnouncements.length === 0 && (
          <div className="bg-card rounded-lg border border-border p-12 text-center">
            <p className="text-muted-foreground">No announcements found</p>
          </div>
        )}

        {filteredAnnouncements.map((announcement) => (
          <div
            key={announcement.id}
            className={`bg-card rounded-lg border shadow-sm hover:shadow-md transition-all cursor-pointer ${
              announcement.unread
                ? "border-primary/40 bg-primary/5"
                : "border-border"
            }`}
            onClick={() => handleToggleRead(announcement.id)}
          >
            <div className="p-6">
              {/* Header Section */}
              <div className="flex items-start gap-4 mb-4">
                {/* Unread/Read Status Dot */}
                <div className="pt-1">
                  {announcement.unread ? (
                    <div className="w-3 h-3 bg-primary rounded-full animate-pulse"></div>
                  ) : (
                    <Circle className="w-3 h-3 text-muted-foreground/30" />
                  )}
                </div>

                {/* Content */}
                <div className="flex-1">
                  {/* Title and Badge */}
                  <div className="flex items-start justify-between gap-4 mb-3">
                    <h3 className={`text-lg font-display ${
                      announcement.unread ? "text-foreground font-bold" : "text-foreground"
                    }`}>
                      {announcement.title}
                    </h3>
                    <span className={`px-3 py-1 rounded-full text-xs border shrink-0 ${sectorColors[announcement.sector]}`}>
                      {sectorLabels[announcement.sector]}
                    </span>
                  </div>

                  {/* Meta Information */}
                  <div className="flex items-center gap-3 text-sm text-muted-foreground flex-wrap mb-4">
                    <div className="flex items-center gap-1.5">
                      <Calendar className="w-4 h-4" />
                      <span>{announcement.date}</span>
                    </div>
                    <span>•</span>
                    <span>From: {announcement.sender}</span>
                  </div>

                  {/* Message Body */}
                  <p className="text-muted-foreground leading-relaxed">
                    {announcement.content}
                  </p>
                </div>
              </div>

              {/* Footer */}
              <div className="flex items-center justify-between pt-4 border-t border-border ml-7">
                <div className="text-xs text-muted-foreground">
                  Click to mark as {announcement.unread ? "read" : "unread"}
                </div>
                {announcement.unread && (
                  <div className="text-xs font-medium text-primary">
                    NEW
                  </div>
                )}
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Info Box */}
      <div className="mt-8 bg-muted/30 rounded-lg p-4 border border-border">
        <p className="text-sm text-muted-foreground">
          <span className="font-medium text-foreground">Note:</span> You are viewing announcements for{" "}
          <span className="font-medium text-foreground">{sectorLabels[memberSector]}</span> sector and general announcements for all members.
        </p>
      </div>
    </div>
  );
}
