import { useState } from "react";
import { Search, UserPlus, Mail, Phone, X, Eye, MapPin, TrendingUp, Trash2, Edit } from "lucide-react";
import { Link } from "react-router";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer } from "recharts";
import { formatCurrency } from "../../utils/formatters";
import ConfirmDialog from "../components/ConfirmDialog";
import TooltipHint from "../components/Tooltip";

type MemberStatus = "Active" | "At-Risk" | "Inactive";
type Sector = "rice_farming" | "corn" | "fishery" | "livestock" | "high_value_crops";

interface Member {
  id: string;
  name: string;
  email: string;
  phone: string;
  address: string;
  status: MemberStatus;
  sector: Sector;
  shareCapital: number;
  joined: string;
  photo?: string;
}

const sectorLabels: Record<Sector, string> = {
  rice_farming: "Rice Farming",
  corn: "Corn",
  fishery: "Fishery",
  livestock: "Livestock",
  high_value_crops: "High-Value Crops",
};

const statusColors: Record<MemberStatus, string> = {
  Active: "bg-green-100 text-green-700",
  "At-Risk": "bg-amber-100 text-amber-700",
  Inactive: "bg-red-100 text-red-700",
};

const statusDescriptions: Record<MemberStatus, string> = {
  Active: "Members actively participating and contributing regularly",
  "At-Risk": "Members showing signs of disengagement - missed meetings or delayed payments",
  Inactive: "Members who have not participated or contributed in recent months",
};

export default function Members() {
  const [members] = useState<Member[]>([
    { id: "M001", name: "Maria Santos", email: "maria.santos@email.com", phone: "+63 912 345 6789", address: "Quezon City, Metro Manila", status: "Active", sector: "rice_farming", shareCapital: 25000, joined: "Jan 15, 2024" },
    { id: "M002", name: "Juan Dela Cruz", email: "juan.delacruz@email.com", phone: "+63 923 456 7890", address: "Makati City, Metro Manila", status: "Active", sector: "corn", shareCapital: 18000, joined: "Feb 3, 2024" },
    { id: "M003", name: "Rosa Garcia", email: "rosa.garcia@email.com", phone: "+63 934 567 8901", address: "Pasig City, Metro Manila", status: "At-Risk", sector: "fishery", shareCapital: 32000, joined: "Feb 20, 2024" },
    { id: "M004", name: "Pedro Reyes", email: "pedro.reyes@email.com", phone: "+63 945 678 9012", address: "Mandaluyong City, Metro Manila", status: "Inactive", sector: "livestock", shareCapital: 12000, joined: "Mar 5, 2024" },
    { id: "M005", name: "Ana Lopez", email: "ana.lopez@email.com", phone: "+63 956 789 0123", address: "Taguig City, Metro Manila", status: "Active", sector: "high_value_crops", shareCapital: 45000, joined: "Mar 18, 2024" },
    { id: "M006", name: "Carlos Ramos", email: "carlos.ramos@email.com", phone: "+63 967 890 1234", address: "Parañaque City, Metro Manila", status: "Active", sector: "rice_farming", shareCapital: 28000, joined: "Apr 2, 2026" },
    { id: "M007", name: "Elena Villanueva", email: "elena.v@email.com", phone: "+63 978 901 2345", address: "Las Piñas City, Metro Manila", status: "At-Risk", sector: "corn", shareCapital: 15000, joined: "Apr 5, 2026" },
    { id: "M008", name: "Roberto Aquino", email: "roberto.a@email.com", phone: "+63 989 012 3456", address: "Muntinlupa City, Metro Manila", status: "Active", sector: "fishery", shareCapital: 38000, joined: "Apr 8, 2026" },
  ]);

  const [searchQuery, setSearchQuery] = useState("");
  const [selectedSector, setSelectedSector] = useState<Sector | "all">("all");
  const [selectedMember, setSelectedMember] = useState<Member | null>(null);
  const [showAddModal, setShowAddModal] = useState(false);
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

  // Filter members
  const filteredMembers = members.filter(member => {
    const matchesSearch = member.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         member.id.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         member.email.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesSector = selectedSector === "all" || member.sector === selectedSector;
    return matchesSearch && matchesSector;
  });

  // Generate share capital history for selected member
  const getShareCapitalHistory = (member: Member) => {
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
    return months.map((month, index) => ({
      id: `${member.id}-${month}-${index}`,
      month,
      amount: Math.round(member.shareCapital * (0.7 + (index * 0.05))),
    }));
  };

  // Generate document history for selected member
  const getDocumentHistory = () => [
    { name: "Membership Application", date: "Jan 15, 2024", type: "PDF" },
    { name: "Share Certificate", date: "Jan 20, 2024", type: "PDF" },
    { name: "Annual Statement 2024", date: "Mar 10, 2024", type: "PDF" },
    { name: "Payment Receipt", date: "Apr 5, 2026", type: "PDF" },
  ];

  const stats = {
    total: members.length,
    active: members.filter(m => m.status === "Active").length,
    atRisk: members.filter(m => m.status === "At-Risk").length,
    inactive: members.filter(m => m.status === "Inactive").length,
  };

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-display mb-2">Members</h1>
        <p className="text-muted-foreground">Manage cooperative members and their information</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
          <div className="text-3xl font-bold mb-1">{stats.total}</div>
          <div className="text-sm text-muted-foreground">Total Members</div>
        </div>
        <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm">
          <div className="text-3xl font-bold mb-1 text-green-600">{stats.active}</div>
          <div className="text-sm text-muted-foreground">Active Members</div>
        </div>
        <div className="bg-card rounded-xl p-6 border border-amber-200 shadow-sm">
          <div className="text-3xl font-bold mb-1 text-amber-600">{stats.atRisk}</div>
          <div className="text-sm text-muted-foreground">At-Risk Members</div>
        </div>
        <div className="bg-card rounded-xl p-6 border border-red-200 shadow-sm">
          <div className="text-3xl font-bold mb-1 text-red-600">{stats.inactive}</div>
          <div className="text-sm text-muted-foreground">Inactive Members</div>
        </div>
      </div>

      {/* Search and Filters */}
      <div className="bg-card rounded-xl p-6 border border-border shadow-sm mb-6">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
            <input
              type="text"
              placeholder="Search members by name, email, or ID..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-10 pr-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
            />
          </div>
          <select
            value={selectedSector}
            onChange={(e) => setSelectedSector(e.target.value as Sector | "all")}
            className="px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
          >
            <option value="all">All Sectors</option>
            {(Object.keys(sectorLabels) as Sector[]).map(sector => (
              <option key={sector} value={sector}>{sectorLabels[sector]}</option>
            ))}
          </select>
          <button
            onClick={() => setShowAddModal(true)}
            className="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all whitespace-nowrap flex items-center gap-2"
          >
            <UserPlus className="w-4 h-4" />
            Add Member
          </button>
        </div>
      </div>

      {/* Members Table */}
      <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-muted/50">
              <tr>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Member Name</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Sector</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Status</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Share Capital</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Action</th>
              </tr>
            </thead>
            <tbody>
              {filteredMembers.length === 0 ? (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-muted-foreground">
                    No members found
                  </td>
                </tr>
              ) : (
                filteredMembers.map((member) => (
                  <tr key={member.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                          <span className="font-bold text-primary">{member.name.charAt(0)}</span>
                        </div>
                        <div>
                          <div className="font-medium">{member.name}</div>
                          <div className="text-xs text-muted-foreground">{member.id}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className="text-sm">{sectorLabels[member.sector]}</span>
                    </td>
                    <td className="px-6 py-4">
                      <TooltipHint content={statusDescriptions[member.status]} position="top">
                        <span className={`px-3 py-1 rounded-full text-sm ${statusColors[member.status]}`}>
                          {member.status}
                        </span>
                      </TooltipHint>
                    </td>
                    <td className="px-6 py-4">
                      <span className="font-medium">{formatCurrency(member.shareCapital)}</span>
                    </td>
                    <td className="px-6 py-4">
                      <button
                        onClick={() => setSelectedMember(member)}
                        className="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all text-sm flex items-center gap-2"
                      >
                        <Eye className="w-4 h-4" />
                        View
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Member Profile Side Panel */}
      {selectedMember && (
        <div className="fixed inset-0 bg-black/50 z-50 flex justify-end" onClick={() => setSelectedMember(null)}>
          <div
            className="w-full max-w-2xl bg-background h-full overflow-y-auto shadow-2xl animate-in slide-in-from-right duration-300"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Header */}
            <div className="sticky top-0 bg-primary text-primary-foreground p-6 flex items-center justify-between">
              <h2 className="text-2xl font-display">Member Profile</h2>
              <button
                onClick={() => setSelectedMember(null)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>

            {/* Profile Content */}
            <div className="p-6 space-y-6">
              {/* Profile Header */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <div className="flex items-start gap-6">
                  <div className="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                    <span className="text-4xl font-bold text-primary">{selectedMember.name.charAt(0)}</span>
                  </div>
                  <div className="flex-1">
                    <h3 className="text-2xl font-bold mb-1">{selectedMember.name}</h3>
                    <p className="text-muted-foreground mb-3">Member ID: {selectedMember.id}</p>
                    <div className="flex items-center gap-2 mb-3">
                      <span className={`px-3 py-1 rounded-full text-sm ${statusColors[selectedMember.status]}`}>
                        {selectedMember.status}
                      </span>
                      <span className="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                        {sectorLabels[selectedMember.sector]}
                      </span>
                    </div>
                    <Link
                      to="/dashboard/predictions"
                      className="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary to-green-700 text-white rounded-lg hover:opacity-90 transition-all text-sm shadow-sm"
                    >
                      <TrendingUp className="w-4 h-4" />
                      View Predictions
                    </Link>
                  </div>
                </div>
              </div>

              {/* Contact Information */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h4 className="font-bold mb-4">Contact Information</h4>
                <div className="space-y-3">
                  <div className="flex items-center gap-3">
                    <Mail className="w-5 h-5 text-muted-foreground" />
                    <div>
                      <div className="text-xs text-muted-foreground">Email</div>
                      <div>{selectedMember.email}</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <Phone className="w-5 h-5 text-muted-foreground" />
                    <div>
                      <div className="text-xs text-muted-foreground">Phone</div>
                      <div>{selectedMember.phone}</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <MapPin className="w-5 h-5 text-muted-foreground" />
                    <div>
                      <div className="text-xs text-muted-foreground">Address</div>
                      <div>{selectedMember.address}</div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Share Capital History */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h4 className="font-bold mb-4">Share Capital History</h4>
                <div className="mb-4">
                  <div className="text-3xl font-bold text-primary">{formatCurrency(selectedMember.shareCapital)}</div>
                  <div className="text-sm text-muted-foreground">Current Share Capital</div>
                </div>
                <div className="h-48" key={`bar-chart-${selectedMember.id}`}>
                  <ResponsiveContainer width="100%" height="100%">
                    <BarChart data={getShareCapitalHistory(selectedMember)} margin={{ top: 5, right: 20, left: 20, bottom: 5 }}>
                      <XAxis dataKey="month" stroke="#666" fontSize={12} tickLine={false} axisLine={false} />
                      <YAxis stroke="#666" fontSize={12} tickLine={false} axisLine={false} />
                      <Tooltip
                        contentStyle={{
                          backgroundColor: "#fff",
                          border: "1px solid #e5e5e4",
                          borderRadius: "8px",
                        }}
                        formatter={(value: number) => formatCurrency(value)}
                      />
                      <Bar dataKey="amount" fill="#1b5e3f" radius={[4, 4, 0, 0]} isAnimationActive={false} />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
              </div>

              {/* Document History */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h4 className="font-bold mb-4">Document History</h4>
                <div className="space-y-3">
                  {getDocumentHistory().map((doc, index) => (
                    <div key={index} className="flex items-center justify-between py-3 border-b border-border last:border-0">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                          <span className="text-xs font-bold text-primary">{doc.type}</span>
                        </div>
                        <div>
                          <div className="font-medium text-sm">{doc.name}</div>
                          <div className="text-xs text-muted-foreground">{doc.date}</div>
                        </div>
                      </div>
                      <button className="text-primary hover:underline text-sm">View</button>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Add Member Modal */}
      {showAddModal && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onClick={() => setShowAddModal(false)}>
          <div
            className="bg-background rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-in fade-in zoom-in-95 duration-200"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Modal Header */}
            <div className="sticky top-0 bg-primary text-primary-foreground p-6 rounded-t-xl flex items-center justify-between">
              <h2 className="text-2xl font-display">Add New Member</h2>
              <button
                onClick={() => setShowAddModal(false)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>

            {/* Modal Content */}
            <div className="p-6">
              <form className="space-y-4" onSubmit={(e) => {
                e.preventDefault();
                setConfirmDialog({
                  isOpen: true,
                  title: "Add New Member?",
                  message: "Are you sure you want to add this new member to the cooperative? They will be granted access to member services.",
                  variant: "success",
                  onConfirm: () => {
                    console.log("Member added");
                    setShowAddModal(false);
                  },
                });
              }}>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium mb-2">Full Name</label>
                    <input
                      type="text"
                      className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                      placeholder="Juan Dela Cruz"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium mb-2">Email</label>
                    <input
                      type="email"
                      className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                      placeholder="juan@email.com"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium mb-2">Phone</label>
                    <input
                      type="tel"
                      className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                      placeholder="+63 912 345 6789"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium mb-2">Sector</label>
                    <select className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring">
                      <option value="">Select Sector</option>
                      {(Object.keys(sectorLabels) as Sector[]).map(sector => (
                        <option key={sector} value={sector}>{sectorLabels[sector]}</option>
                      ))}
                    </select>
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Address</label>
                  <input
                    type="text"
                    className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                    placeholder="Street, City, Province"
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium mb-2">Initial Share Capital</label>
                    <input
                      type="number"
                      className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                      placeholder="25000"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium mb-2">Status</label>
                    <select className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring">
                      <option value="Active">Active</option>
                      <option value="At-Risk">At-Risk</option>
                      <option value="Inactive">Inactive</option>
                    </select>
                  </div>
                </div>

                <div className="flex gap-3 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowAddModal(false)}
                    className="flex-1 px-6 py-3 border border-border rounded-lg hover:bg-muted transition-all"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all"
                  >
                    Add Member
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
    </div>
  );
}
