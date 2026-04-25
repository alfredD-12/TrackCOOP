import { useState, useEffect, useMemo } from "react";
import { useNavigate } from "react-router";
import { User, Mail, Phone, MapPin, Edit, Download } from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts";
import { formatCurrency } from "../../utils/formatters";

const memberProfile = {
  id: "M001",
  name: "Maria Santos",
  sector: "Rice Farming",
  email: "maria.santos@email.com",
  phone: "+63 912 345 6789",
  address: "Nasugbu, Batangas",
  joinDate: "Jan 15, 2024",
  status: "Active",
  photoUrl: ""
};

const shareCapitalDataAll = [
  { id: "month-1", month: "May '25", amount: 3500 },
  { id: "month-2", month: "Jun '25", amount: 3800 },
  { id: "month-3", month: "Jul '25", amount: 4200 },
  { id: "month-4", month: "Aug '25", amount: 3900 },
  { id: "month-5", month: "Sep '25", amount: 4100 },
  { id: "month-6", month: "Oct '25", amount: 4500 },
  { id: "month-7", month: "Nov '25", amount: 3000 },
  { id: "month-8", month: "Dec '25", amount: 4000 },
  { id: "month-9", month: "Jan '26", amount: 5000 },
  { id: "month-10", month: "Feb '26", amount: 4500 },
  { id: "month-11", month: "Mar '26", amount: 5500 },
  { id: "month-12", month: "Apr '26", amount: 3000 }
];

const transactionHistory = [
  { id: "txn-1", date: "Apr 14, 2026", amount: 3000, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-2", date: "Mar 14, 2026", amount: 5500, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-3", date: "Feb 14, 2026", amount: 4500, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-4", date: "Jan 14, 2026", amount: 5000, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-5", date: "Dec 14, 2025", amount: 4000, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-6", date: "Nov 14, 2025", amount: 3000, type: "Monthly Contribution", status: "Completed" }
];

const myDocuments = [
  { id: "doc-1", name: "Membership Application Form.pdf", category: "Membership", uploadDate: "Jan 15, 2024", size: "245 KB" },
  { id: "doc-2", name: "Share Capital Certificate.pdf", category: "Financial", uploadDate: "Jan 20, 2024", size: "180 KB" },
  { id: "doc-3", name: "ID Verification.pdf", category: "Membership", uploadDate: "Jan 15, 2024", size: "320 KB" },
  { id: "doc-4", name: "Annual Statement 2025.pdf", category: "Financial", uploadDate: "Dec 31, 2025", size: "420 KB" },
  { id: "doc-5", name: "Contribution Receipt Q1 2026.pdf", category: "Financial", uploadDate: "Mar 31, 2026", size: "156 KB" }
];

export default function MyProfile() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState<"share-capital" | "documents">("share-capital");
  const [editModalOpen, setEditModalOpen] = useState(false);
  const [shareCapitalPeriod, setShareCapitalPeriod] = useState<"3m" | "6m" | "12m">("6m");

  // Filter share capital data based on period
  const shareCapitalData = shareCapitalPeriod === "3m"
    ? shareCapitalDataAll.slice(-3)
    : shareCapitalPeriod === "6m"
    ? shareCapitalDataAll.slice(-6)
    : shareCapitalDataAll;

  const chartId = useMemo(() => `share-capital-chart-${Math.random()}`, []);

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

  const totalContributions = transactionHistory.reduce((sum, txn) => sum + txn.amount, 0);

  return (
    <div className="p-8 bg-background min-h-full">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-foreground mb-2">My Profile</h1>
        <p className="text-muted-foreground">View and manage your personal information and contributions</p>
      </div>

      {/* Profile Card */}
      <div className="bg-card rounded-lg shadow-sm border border-border p-8 mb-6">
        <div className="flex items-start gap-8">
          {/* Profile Photo */}
          <div className="shrink-0">
            <div className="w-32 h-32 bg-gradient-to-br from-primary/20 to-primary/40 rounded-full flex items-center justify-center">
              <User className="w-16 h-16 text-primary" />
            </div>
          </div>

          {/* Profile Info */}
          <div className="flex-1">
            <div className="flex items-start justify-between mb-6">
              <div>
                <h2 className="text-2xl font-display text-foreground mb-1">{memberProfile.name}</h2>
                <p className="text-muted-foreground">Member ID: {memberProfile.id}</p>
                <div className="mt-2">
                  <span className="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    {memberProfile.status}
                  </span>
                </div>
              </div>
              <button
                onClick={() => setEditModalOpen(true)}
                className="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all shadow-sm"
              >
                <Edit className="w-4 h-4" />
                Edit Info
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="flex items-start gap-3">
                <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                  <MapPin className="w-5 h-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground mb-1">Sector</p>
                  <p className="text-foreground font-medium">{memberProfile.sector}</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                  <Mail className="w-5 h-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground mb-1">Email Address</p>
                  <p className="text-foreground font-medium">{memberProfile.email}</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                  <Phone className="w-5 h-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground mb-1">Phone Number</p>
                  <p className="text-foreground font-medium">{memberProfile.phone}</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                  <MapPin className="w-5 h-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground mb-1">Address</p>
                  <p className="text-foreground font-medium">{memberProfile.address}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Tabs Section */}
      <div className="bg-card rounded-lg shadow-sm border border-border">
        {/* Tab Headers */}
        <div className="border-b border-border">
          <div className="flex gap-1 p-1">
            <button
              onClick={() => setActiveTab("share-capital")}
              className={`flex-1 px-6 py-3 rounded-lg transition-all ${
                activeTab === "share-capital"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:bg-accent hover:text-accent-foreground"
              }`}
            >
              Share Capital History
            </button>
            <button
              onClick={() => setActiveTab("documents")}
              className={`flex-1 px-6 py-3 rounded-lg transition-all ${
                activeTab === "documents"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:bg-accent hover:text-accent-foreground"
              }`}
            >
              My Documents
            </button>
          </div>
        </div>

        {/* Tab Content */}
        <div className="p-6">
          {activeTab === "share-capital" && (
            <div className="space-y-6">
              {/* Summary Stats */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                  <p className="text-sm text-green-700 mb-1">Total Contributions</p>
                  <p className="text-2xl font-display text-green-900">{formatCurrency(totalContributions)}</p>
                </div>
                <div className="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                  <p className="text-sm text-blue-700 mb-1">Last Contribution</p>
                  <p className="text-2xl font-display text-blue-900">{transactionHistory[0].date}</p>
                </div>
                <div className="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                  <p className="text-sm text-purple-700 mb-1">Member Since</p>
                  <p className="text-2xl font-display text-purple-900">{memberProfile.joinDate}</p>
                </div>
              </div>

              {/* Bar Chart */}
              <div>
                <div className="flex items-center justify-between mb-4">
                  <h3 className="text-lg font-display text-foreground">
                    Monthly Contributions ({shareCapitalPeriod === "3m" ? "Last 3 Months" : shareCapitalPeriod === "6m" ? "Last 6 Months" : "Last 12 Months"})
                  </h3>
                  {/* Period Filter */}
                  <div className="flex items-center gap-2 bg-muted/30 rounded-lg p-1">
                    <button
                      onClick={() => setShareCapitalPeriod("3m")}
                      className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                        shareCapitalPeriod === "3m"
                          ? "bg-primary text-primary-foreground shadow-sm"
                          : "text-muted-foreground hover:text-foreground"
                      }`}
                    >
                      3M
                    </button>
                    <button
                      onClick={() => setShareCapitalPeriod("6m")}
                      className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                        shareCapitalPeriod === "6m"
                          ? "bg-primary text-primary-foreground shadow-sm"
                          : "text-muted-foreground hover:text-foreground"
                      }`}
                    >
                      6M
                    </button>
                    <button
                      onClick={() => setShareCapitalPeriod("12m")}
                      className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                        shareCapitalPeriod === "12m"
                          ? "bg-primary text-primary-foreground shadow-sm"
                          : "text-muted-foreground hover:text-foreground"
                      }`}
                    >
                      12M
                    </button>
                  </div>
                </div>
                <div className="bg-background p-4 rounded-lg border border-border" key={chartId}>
                  <ResponsiveContainer width="100%" height={300}>
                    <BarChart data={shareCapitalData}>
                      <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
                      <XAxis dataKey="month" stroke="#6b7280" />
                      <YAxis stroke="#6b7280" />
                      <Tooltip
                        contentStyle={{
                          backgroundColor: "white",
                          border: "1px solid #e5e7eb",
                          borderRadius: "8px"
                        }}
                        formatter={(value: number) => formatCurrency(value)}
                      />
                      <Bar dataKey="amount" fill="#1b5e3f" radius={[8, 8, 0, 0]} />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
              </div>

              {/* Transaction Table */}
              <div>
                <h3 className="text-lg font-display text-foreground mb-4">Transaction History</h3>
                <div className="overflow-x-auto rounded-lg border border-border">
                  <table className="w-full">
                    <thead className="bg-accent">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                          Date
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                          Type
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                          Amount
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                          Status
                        </th>
                      </tr>
                    </thead>
                    <tbody className="bg-card divide-y divide-border">
                      {transactionHistory.map((txn) => (
                        <tr key={txn.id} className="hover:bg-accent/50 transition-colors">
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                            {txn.date}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                            {txn.type}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            {formatCurrency(txn.amount)}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs bg-green-100 text-green-800">
                              {txn.status}
                            </span>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {activeTab === "documents" && (
            <div>
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-display text-foreground">My Documents</h3>
                <p className="text-sm text-muted-foreground">{myDocuments.length} documents</p>
              </div>

              <div className="overflow-x-auto rounded-lg border border-border">
                <table className="w-full">
                  <thead className="bg-accent">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        Document Name
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        Category
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        Upload Date
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        Size
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        Action
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-card divide-y divide-border">
                    {myDocuments.map((doc) => (
                      <tr key={doc.id} className="hover:bg-accent/50 transition-colors">
                        <td className="px-6 py-4 text-sm text-foreground">
                          {doc.name}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs ${
                            doc.category === "Membership"
                              ? "bg-blue-100 text-blue-800"
                              : "bg-green-100 text-green-800"
                          }`}>
                            {doc.category}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                          {doc.uploadDate}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                          {doc.size}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <button className="flex items-center gap-2 text-primary hover:text-primary/80 transition-colors">
                            <Download className="w-4 h-4" />
                            <span className="text-sm">Download</span>
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Edit Modal */}
      {editModalOpen && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-card rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6 border-b border-border">
              <h2 className="text-xl font-display text-foreground">Edit Profile Information</h2>
            </div>

            <div className="p-6 space-y-4">
              <div>
                <label className="block text-sm text-foreground mb-2">Full Name</label>
                <input
                  type="text"
                  defaultValue={memberProfile.name}
                  className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                />
              </div>

              <div>
                <label className="block text-sm text-foreground mb-2">Email Address</label>
                <input
                  type="email"
                  defaultValue={memberProfile.email}
                  className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                />
              </div>

              <div>
                <label className="block text-sm text-foreground mb-2">Phone Number</label>
                <input
                  type="tel"
                  defaultValue={memberProfile.phone}
                  className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                />
              </div>

              <div>
                <label className="block text-sm text-foreground mb-2">Address</label>
                <textarea
                  defaultValue={memberProfile.address}
                  rows={3}
                  className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
                />
              </div>
            </div>

            <div className="p-6 border-t border-border flex justify-end gap-3">
              <button
                onClick={() => setEditModalOpen(false)}
                className="px-4 py-2 rounded-lg border border-input hover:bg-accent transition-colors"
              >
                Cancel
              </button>
              <button
                onClick={() => setEditModalOpen(false)}
                className="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all shadow-sm"
              >
                Save Changes
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
