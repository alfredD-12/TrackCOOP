import { useState } from "react";
import { Megaphone, Calendar, Pin, X, AlertTriangle, Send, Trash2, Edit } from "lucide-react";
import { useNavigate } from "react-router";
import ConfirmDialog from "../components/ConfirmDialog";

type Sector = "all" | "rice_farming" | "corn" | "fishery" | "livestock" | "high_value_crops";

interface Announcement {
  id: string;
  title: string;
  content: string;
  date: string;
  author: string;
  pinned: boolean;
  sector: Sector;
  readCount: number;
  totalRecipients: number;
}

interface AtRiskMember {
  id: string;
  name: string;
  sector: string;
  reasons: string[];
  lastActive: string;
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
  all: "bg-blue-100 text-blue-700",
  rice_farming: "bg-green-100 text-green-700",
  corn: "bg-yellow-100 text-yellow-700",
  fishery: "bg-cyan-100 text-cyan-700",
  livestock: "bg-orange-100 text-orange-700",
  high_value_crops: "bg-purple-100 text-purple-700",
};

const heroImage =
  "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=2400";

export default function Announcements() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState<"announcements" | "alerts">("announcements");
  const [showComposeModal, setShowComposeModal] = useState(false);
  const [modalMode, setModalMode] = useState<"compose" | "edit">("compose");
  const [viewingAnnouncement, setViewingAnnouncement] = useState<Announcement | null>(null);
  const [formData, setFormData] = useState({
    title: "",
    message: "",
    sector: "all" as Sector,
    scheduled: false,
    scheduleDate: "",
  });
  const [confirmDialog, setConfirmDialog] = useState<{
    isOpen: boolean;
    title: string;
    message: string;
    onConfirm: () => void;
    variant?: "danger" | "warning" | "info" | "success";
  }>({
    isOpen: false,
    title: "",
    message: "",
    onConfirm: () => {},
  });

  const announcements: Announcement[] = [
    {
      id: "ann-1",
      title: "Annual General Meeting - April 25, 2026",
      content: "All members are invited to attend the Annual General Meeting on April 25, 2026, at 2:00 PM in the Community Hall. We will discuss financial reports, elect new board members, and plan for the upcoming year.",
      date: "Apr 12, 2026",
      author: "Board of Directors",
      pinned: true,
      sector: "all",
      readCount: 1089,
      totalRecipients: 1247,
    },
    {
      id: "ann-2",
      title: "Rice Farming Best Practices Workshop",
      content: "Special workshop for rice farming members on modern cultivation techniques and pest management.",
      date: "Apr 10, 2026",
      author: "Agricultural Team",
      pinned: true,
      sector: "rice_farming",
      readCount: 234,
      totalRecipients: 312,
    },
    {
      id: "ann-3",
      title: "Dividend Distribution Schedule",
      content: "Member dividends for Q1 2026 will be distributed on April 30, 2026. Please ensure your bank account details are up to date in your profile.",
      date: "Apr 8, 2026",
      author: "Finance Team",
      pinned: false,
      sector: "all",
      readCount: 892,
      totalRecipients: 1247,
    },
    {
      id: "ann-4",
      title: "Fishery Sector Equipment Subsidy",
      content: "New subsidy program available for fishery equipment upgrades. Applications open until May 15.",
      date: "Apr 5, 2026",
      author: "Fishery Committee",
      pinned: false,
      sector: "fishery",
      readCount: 67,
      totalRecipients: 145,
    },
    {
      id: "ann-5",
      title: "Office Hours Update",
      content: "Starting May 1, 2026, our office hours will be Monday to Friday, 8:00 AM - 5:00 PM. Saturday hours remain 9:00 AM - 1:00 PM.",
      date: "Apr 2, 2026",
      author: "Administration",
      pinned: false,
      sector: "all",
      readCount: 756,
      totalRecipients: 1247,
    },
  ];

  const atRiskMembers: AtRiskMember[] = [
    {
      id: "M003",
      name: "Rosa Garcia",
      sector: "Fishery",
      reasons: ["Missed 3 meetings", "Low share capital growth", "Delayed payments"],
      lastActive: "Mar 28, 2026",
    },
    {
      id: "M004",
      name: "Pedro Reyes",
      sector: "Livestock",
      reasons: ["Missed 5 meetings", "No share capital in 2 months", "No recent activity"],
      lastActive: "Feb 15, 2026",
    },
    {
      id: "M007",
      name: "Elena Villanueva",
      sector: "Corn",
      reasons: ["Missed 2 meetings", "Declining engagement score", "Low participation"],
      lastActive: "Apr 1, 2026",
    },
  ];

  const handleSendReminder = (member: AtRiskMember) => {
    setConfirmDialog({
      isOpen: true,
      title: "Send Reminder?",
      message: `Are you sure you want to send a reminder to ${member.name}? They will receive a notification via email and SMS.`,
      variant: "info",
      onConfirm: () => {
        console.log(`Reminder sent to ${member.name}`);
      },
    });
  };

  const handleMemberClick = (memberId: string) => {
    navigate(`/dashboard/members`);
  };

  const handleEdit = (announcement: Announcement) => {
    setFormData({
      title: announcement.title,
      message: announcement.content,
      sector: announcement.sector,
      scheduled: false,
      scheduleDate: "",
    });
    setModalMode("edit");
    setShowComposeModal(true);
  };

  const handleSubmitAnnouncement = (e: React.FormEvent) => {
    e.preventDefault();
    setConfirmDialog({
      isOpen: true,
      title: formData.scheduled ? "Schedule Announcement?" : "Send Announcement Now?",
      message: formData.scheduled
        ? `This announcement will be scheduled for ${formData.scheduleDate}. Members will receive it at the scheduled time.`
        : "This announcement will be sent immediately to all selected members. Are you sure you want to proceed?",
      variant: "success",
      onConfirm: () => {
        setShowComposeModal(false);
        setFormData({
          title: "",
          message: "",
          sector: "all",
          scheduled: false,
          scheduleDate: "",
        });
      },
    });
  };

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
                  Announcements
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Announcements & Alerts
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Communicate with members and manage at-risk alerts
                </p>
              </div>

              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                {activeTab === "announcements" && (
                  <button
                    onClick={() => { setModalMode("compose"); setShowComposeModal(true); }}
                    data-tour="announcements-compose"
                    className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                  >
                    <Megaphone className="h-4 w-4" />
                    Compose Announcement
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        {/* Tabs */}
      <div className="mb-6 border-b border-border" data-tour="announcements-tabs">
        <div className="flex gap-1">
          <button
            onClick={() => setActiveTab("announcements")}
            className={`px-6 py-3 font-medium transition-all relative ${
              activeTab === "announcements"
                ? "text-primary"
                : "text-muted-foreground hover:text-foreground"
            }`}
          >
            <div className="flex items-center gap-2">
              <Megaphone className="w-5 h-5" />
              <span>Announcements</span>
            </div>
            {activeTab === "announcements" && (
              <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"></div>
            )}
          </button>
          <button
            onClick={() => setActiveTab("alerts")}
            className={`px-6 py-3 font-medium transition-all relative ${
              activeTab === "alerts"
                ? "text-amber-600"
                : "text-muted-foreground hover:text-foreground"
            }`}
          >
            <div className="flex items-center gap-2">
              <AlertTriangle className="w-5 h-5" />
              <span>Alerts</span>
              <span className="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs">
                {atRiskMembers.length}
              </span>
            </div>
            {activeTab === "alerts" && (
              <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-amber-600"></div>
            )}
          </button>
        </div>
      </div>

      {/* Announcements Tab Content */}
      {activeTab === "announcements" && (
        <>

          {/* Announcements List */}
          <div className="space-y-4" data-tour="announcements-table">
            {announcements.map((announcement, index) => (
              <div
                key={announcement.id}
                className={`bg-card rounded-xl p-6 border shadow-sm transition-all duration-300 animate-in fade-in slide-in-from-bottom-3 hover:-translate-y-1 hover:shadow-lg ${
                  announcement.pinned ? "border-primary" : "border-border"
                }`}
                style={{ animationDelay: `${Math.min(index * 50, 300)}ms` }}
              >
                <div className="flex items-start justify-between mb-4">
                  <div className="flex-1">
                    <div className="flex items-center gap-3 mb-2">
                      {announcement.pinned && (
                        <Pin className="w-4 h-4 text-primary" />
                      )}
                      <h3 className="font-bold text-lg">{announcement.title}</h3>
                    </div>
                    <div className="flex items-center gap-3 text-sm text-muted-foreground flex-wrap">
                      <div className="flex items-center gap-1">
                        <Calendar className="w-4 h-4" />
                        <span>{announcement.date}</span>
                      </div>
                      <span>•</span>
                      <span>By {announcement.author}</span>
                      <span>•</span>
                      <span className={`px-3 py-1 rounded-full text-xs ${sectorColors[announcement.sector]}`}>
                        {sectorLabels[announcement.sector]}
                      </span>
                      <span>•</span>
                      <span>{announcement.readCount}/{announcement.totalRecipients} read</span>
                    </div>
                  </div>
                </div>

                <p className="text-muted-foreground leading-relaxed mb-4">{announcement.content}</p>

                {/* Read Progress Bar */}
                <div className="mb-4">
                  <div className="flex items-center justify-between mb-1 text-xs text-muted-foreground">
                    <span>Read Rate</span>
                    <span>{Math.round((announcement.readCount / announcement.totalRecipients) * 100)}%</span>
                  </div>
                  <div className="w-full bg-muted rounded-full h-2 overflow-hidden">
                    <div
                      className="bg-primary h-full rounded-full transition-all"
                      style={{ width: `${(announcement.readCount / announcement.totalRecipients) * 100}%` }}
                    />
                  </div>
                </div>

                <div className="flex items-center gap-3 pt-4 border-t border-border">
                  <button
                    onClick={() => setViewingAnnouncement(announcement)}
                    className="text-sm text-primary hover:underline"
                  >
                    View Details
                  </button>
                  <span className="text-muted-foreground">•</span>
                  <button
                    onClick={() => handleEdit(announcement)}
                    className="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1"
                  >
                    <Edit className="w-3 h-3" />
                    Edit
                  </button>
                  <span className="text-muted-foreground">•</span>
                  <button
                    onClick={() => {
                      setConfirmDialog({
                        isOpen: true,
                        title: "Delete Announcement?",
                        message: `Are you sure you want to delete "${announcement.title}"? This action cannot be undone and the announcement will be permanently removed from all member feeds.`,
                        variant: "danger",
                        onConfirm: () => {
                          console.log("Delete announcement:", announcement.id);
                        },
                      });
                    }}
                    className="text-sm text-red-600 hover:text-red-700 flex items-center gap-1"
                  >
                    <Trash2 className="w-3 h-3" />
                    Delete
                  </button>
                </div>
              </div>
            ))}
          </div>
        </>
      )}

      {/* Alerts Tab Content */}
      {activeTab === "alerts" && (
        <div>
          <div className="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
            <div className="flex items-start gap-3">
              <AlertTriangle className="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
              <div>
                <h3 className="font-bold text-amber-900 mb-1">At-Risk Members Alert</h3>
                <p className="text-sm text-amber-800">
                  {atRiskMembers.length} members require attention based on AI predictions. Send reminders to re-engage them.
                </p>
              </div>
            </div>
          </div>

          {/* At-Risk Members Table */}
          <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-muted/50">
                  <tr>
                    <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Member Name</th>
                    <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Sector</th>
                    <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Risk Reasons</th>
                    <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Last Active</th>
                    <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {atRiskMembers.map((member, index) => (
                    <tr key={member.id} className="border-t border-border hover:bg-muted/30 transition-colors animate-in fade-in slide-in-from-bottom-2" style={{ animationDelay: `${Math.min(index * 35, 220)}ms` }}>
                      <td className="px-6 py-4">
                        <button
                          onClick={() => handleMemberClick(member.id)}
                          className="flex items-center gap-3 hover:text-primary transition-colors"
                        >
                          <div className="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
                            <span className="font-bold text-amber-600">{member.name.charAt(0)}</span>
                          </div>
                          <div className="text-left">
                            <div className="font-medium">{member.name}</div>
                            <div className="text-xs text-muted-foreground">{member.id}</div>
                          </div>
                        </button>
                      </td>
                      <td className="px-6 py-4">
                        <span className="text-sm">{member.sector}</span>
                      </td>
                      <td className="px-6 py-4">
                        <div className="space-y-1">
                          {member.reasons.map((reason, idx) => (
                            <div key={idx} className="flex items-center gap-2">
                              <div className="w-1.5 h-1.5 bg-amber-500 rounded-full"></div>
                              <span className="text-sm text-muted-foreground">{reason}</span>
                            </div>
                          ))}
                        </div>
                      </td>
                      <td className="px-6 py-4 text-sm text-muted-foreground">
                        {member.lastActive}
                      </td>
                      <td className="px-6 py-4">
                        <button
                          onClick={() => handleSendReminder(member)}
                          className="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all text-sm flex items-center gap-2"
                        >
                          <Send className="w-4 h-4" />
                          Send Reminder
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          {atRiskMembers.length === 0 && (
            <div className="bg-card rounded-xl border border-border shadow-sm p-12 text-center">
              <AlertTriangle className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
              <h3 className="font-bold mb-2">No At-Risk Members</h3>
              <p className="text-sm text-muted-foreground">All members are currently engaged and active!</p>
            </div>
          )}
        </div>
      )}

      {/* View Announcement Modal */}
      {viewingAnnouncement && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onClick={() => setViewingAnnouncement(null)}>
          <div
            className="bg-background rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-in fade-in zoom-in-95 duration-200 p-6"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="flex items-center justify-between mb-6">
              <div className="flex items-center gap-3">
                {viewingAnnouncement.pinned && <Pin className="w-5 h-5 text-primary" />}
                <h2 className="text-2xl font-bold">{viewingAnnouncement.title}</h2>
              </div>
              <button
                onClick={() => setViewingAnnouncement(null)}
                className="p-2 hover:bg-muted rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>
            
            <div className="flex items-center gap-3 text-sm text-muted-foreground mb-6 pb-6 border-b border-border">
              <div className="flex items-center gap-1">
                <Calendar className="w-4 h-4" />
                <span>{viewingAnnouncement.date}</span>
              </div>
              <span>•</span>
              <span>By {viewingAnnouncement.author}</span>
              <span>•</span>
              <span className={`px-3 py-1 rounded-full text-xs ${sectorColors[viewingAnnouncement.sector]}`}>
                {sectorLabels[viewingAnnouncement.sector]}
              </span>
            </div>

            <div className="prose prose-stone max-w-none mb-8">
              <p className="text-lg leading-relaxed">{viewingAnnouncement.content}</p>
            </div>

            <div className="bg-muted/50 rounded-lg p-4 mb-6">
               <h3 className="text-sm font-semibold mb-2">Read Progress</h3>
               <div className="flex items-center justify-between mb-1 text-xs text-muted-foreground">
                 <span>{viewingAnnouncement.readCount} of {viewingAnnouncement.totalRecipients} members have read this</span>
                 <span>{Math.round((viewingAnnouncement.readCount / viewingAnnouncement.totalRecipients) * 100)}%</span>
               </div>
               <div className="w-full bg-muted-foreground/20 rounded-full h-2 overflow-hidden">
                 <div
                   className="bg-primary h-full rounded-full transition-all"
                   style={{ width: `${(viewingAnnouncement.readCount / viewingAnnouncement.totalRecipients) * 100}%` }}
                 />
               </div>
            </div>

            <div className="flex justify-end gap-3 pt-4 border-t border-border">
              <button
                onClick={() => {
                  setViewingAnnouncement(null);
                  handleEdit(viewingAnnouncement);
                }}
                className="px-4 py-2 bg-muted hover:bg-muted/80 text-foreground rounded-lg transition-colors font-medium flex items-center gap-2"
              >
                <Edit className="w-4 h-4" />
                Edit
              </button>
              <button
                onClick={() => setViewingAnnouncement(null)}
                className="px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-colors font-medium"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Compose Announcement Modal */}
      {showComposeModal && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onClick={() => setShowComposeModal(false)}>
          <div
            className="bg-background rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-in fade-in zoom-in-95 duration-200"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Modal Header */}
            <div className="sticky top-0 bg-primary text-primary-foreground p-6 rounded-t-xl flex items-center justify-between">
              <h2 className="text-2xl font-display">{modalMode === "edit" ? "Edit Announcement" : "Compose Announcement"}</h2>
              <button
                onClick={() => setShowComposeModal(false)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>

            {/* Modal Content */}
            <div className="p-6">
              {/* Quick Examples */}
              {modalMode === "compose" && (
              <div className="mb-6 p-4 bg-muted/30 rounded-lg">
                <div className="flex items-center justify-between mb-3">
                  <h3 className="text-sm font-medium">Quick Examples (Click to use)</h3>
                  <button
                    type="button"
                    onClick={() => setFormData({
                      title: "",
                      message: "",
                      sector: "all",
                      scheduled: false,
                      scheduleDate: "",
                    })}
                    className="text-xs text-muted-foreground hover:text-foreground underline"
                  >
                    Clear Form
                  </button>
                </div>
                <div className="flex flex-wrap gap-2">
                  <button
                    type="button"
                    onClick={() => setFormData({
                      title: "Annual General Meeting - May 2026",
                      message: "All members are invited to attend the Annual General Meeting on May 15, 2026, at 2:00 PM in the Community Hall. We will discuss financial reports, elect new board members, and plan for the upcoming year. Your attendance is highly encouraged.",
                      sector: "all",
                      scheduled: false,
                      scheduleDate: "",
                    })}
                    className="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all text-sm"
                  >
                    General Meeting
                  </button>
                  <button
                    type="button"
                    onClick={() => setFormData({
                      title: "Rice Farming Training Seminar",
                      message: "Join us for a comprehensive training seminar on modern rice cultivation techniques on May 20, 2026, at 9:00 AM. Expert agricultural specialists will cover topics including pest management, organic fertilizer application, and water-efficient farming methods.",
                      sector: "rice_farming",
                      scheduled: false,
                      scheduleDate: "",
                    })}
                    className="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-all text-sm"
                  >
                    Training Seminar
                  </button>
                  <button
                    type="button"
                    onClick={() => setFormData({
                      title: "Corn Harvest Festival 2026",
                      message: "You and your family are invited to the Annual Corn Harvest Festival on June 1, 2026! Join us for a day of celebration with traditional food, games for children, product exhibitions, and prizes for the best corn producers. Festival starts at 10:00 AM.",
                      sector: "corn",
                      scheduled: true,
                      scheduleDate: "",
                    })}
                    className="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-all text-sm"
                  >
                    Community Event
                  </button>
                  <button
                    type="button"
                    onClick={() => setFormData({
                      title: "Dividend Distribution Schedule",
                      message: "Member dividends for Q1 2026 will be distributed on May 30, 2026. Please ensure your account information is up to date. Distribution will be based on share capital contributions. Contact the office if you have any questions.",
                      sector: "all",
                      scheduled: false,
                      scheduleDate: "",
                    })}
                    className="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-all text-sm"
                  >
                    Dividend Notice
                  </button>
                </div>
              </div>
              )}

              <form onSubmit={handleSubmitAnnouncement} className="space-y-5">
                <div>
                  <label className="block text-sm font-medium mb-2">Title</label>
                  <input
                    type="text"
                    value={formData.title}
                    onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    placeholder="e.g., Annual General Meeting - May 2026"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Message</label>
                  <textarea
                    value={formData.message}
                    onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring min-h-[150px]"
                    placeholder="e.g., All members are invited to attend... Include details like date, time, location, and what to expect."
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Target Sector</label>
                  <select
                    value={formData.sector}
                    onChange={(e) => setFormData({ ...formData, sector: e.target.value as Sector })}
                    className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                  >
                    {(Object.keys(sectorLabels) as Sector[]).map((sector) => (
                      <option key={sector} value={sector}>
                        {sectorLabels[sector]}
                      </option>
                    ))}
                  </select>
                  <p className="text-xs text-muted-foreground mt-2">
                    Select which sector members should receive this announcement
                  </p>
                </div>

                <div className="flex items-center gap-3 p-4 bg-muted/30 rounded-lg">
                  <input
                    type="checkbox"
                    id="scheduled"
                    checked={formData.scheduled}
                    onChange={(e) => setFormData({ ...formData, scheduled: e.target.checked })}
                    className="w-4 h-4 rounded border-input"
                  />
                  <label htmlFor="scheduled" className="text-sm font-medium cursor-pointer">
                    Schedule for later
                  </label>
                </div>

                {formData.scheduled && (
                  <div>
                    <label className="block text-sm font-medium mb-2">Schedule Date & Time</label>
                    <input
                      type="datetime-local"
                      value={formData.scheduleDate}
                      onChange={(e) => setFormData({ ...formData, scheduleDate: e.target.value })}
                      className="w-full px-4 py-3 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    />
                  </div>
                )}

                <div className="flex gap-3 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowComposeModal(false)}
                    className="flex-1 px-6 py-3 border border-border rounded-lg hover:bg-muted transition-all"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2"
                  >
                    <Send className="w-4 h-4" />
                    {modalMode === "edit" ? "Save Changes" : (formData.scheduled ? "Schedule Announcement" : "Send Now")}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Confirmation Dialog */}
      <ConfirmDialog
        isOpen={confirmDialog.isOpen}
        onClose={() => setConfirmDialog({ ...confirmDialog, isOpen: false })}
        onConfirm={confirmDialog.onConfirm}
        title={confirmDialog.title}
        message={confirmDialog.message}
        variant={confirmDialog.variant}
      />
      </main>
    </div>
  );
}
