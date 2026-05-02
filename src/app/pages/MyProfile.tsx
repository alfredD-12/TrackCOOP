import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router";
import {
  BarChart,
  Bar,
  CartesianGrid,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";
import {
  Calendar,
  Download,
  Edit,
  Mail,
  MapPin,
  Phone,
  Search,
  User,
  Wallet,
  X,
} from "lucide-react";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1521790797524-b2497295b8a0?auto=format&fit=crop&q=80&w=2400";

const memberProfile = {
  id: "M001",
  name: "Maria Santos",
  sector: "Rice Farming",
  email: "maria.santos@email.com",
  phone: "+63 912 345 6789",
  address: "Nasugbu, Batangas",
  joinDate: "Jan 15, 2024",
  status: "Active",
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
  { id: "month-12", month: "Apr '26", amount: 3000 },
];

const transactionHistory = [
  { id: "txn-1", date: "Apr 14, 2026", amount: 3000, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-2", date: "Mar 14, 2026", amount: 5500, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-3", date: "Feb 14, 2026", amount: 4500, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-4", date: "Jan 14, 2026", amount: 5000, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-5", date: "Dec 14, 2025", amount: 4000, type: "Monthly Contribution", status: "Completed" },
  { id: "txn-6", date: "Nov 14, 2025", amount: 3000, type: "Monthly Contribution", status: "Completed" },
];

const myDocuments = [
  { id: "doc-1", name: "Membership Application Form.pdf", category: "Membership", uploadDate: "Jan 15, 2024", size: "245 KB" },
  { id: "doc-2", name: "Share Capital Certificate.pdf", category: "Financial", uploadDate: "Jan 20, 2024", size: "180 KB" },
  { id: "doc-3", name: "ID Verification.pdf", category: "Membership", uploadDate: "Jan 15, 2024", size: "320 KB" },
  { id: "doc-4", name: "Annual Statement 2025.pdf", category: "Financial", uploadDate: "Dec 31, 2025", size: "420 KB" },
  { id: "doc-5", name: "Contribution Receipt Q1 2026.pdf", category: "Financial", uploadDate: "Mar 31, 2026", size: "156 KB" },
];

export default function MyProfile() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState<"share-capital" | "documents">("share-capital");
  const [editModalOpen, setEditModalOpen] = useState(false);
  const [shareCapitalPeriod, setShareCapitalPeriod] = useState<"3m" | "6m" | "12m">("6m");
  const [documentSearch, setDocumentSearch] = useState("");

  const shareCapitalData =
    shareCapitalPeriod === "3m"
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

  const totalContributions = transactionHistory.reduce((sum, transaction) => sum + transaction.amount, 0);
  const filteredDocuments = myDocuments.filter((document) => {
    const query = documentSearch.trim().toLowerCase();
    return (
      !query ||
      document.name.toLowerCase().includes(query) ||
      document.category.toLowerCase().includes(query) ||
      document.uploadDate.toLowerCase().includes(query)
    );
  });

  return (
    <div className="min-h-full bg-stone-50 text-gray-950">
      <section className="relative overflow-hidden border-b border-stone-200">
        <img src={heroImage} alt="" aria-hidden="true" className="absolute inset-0 h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/15" />
        <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-stone-50 to-transparent" />
        <div className="relative mx-auto flex min-h-[280px] max-w-[1600px] flex-col justify-start px-6 py-8 md:min-h-[320px] md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
              <div className="max-w-4xl">
                <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                  My Profile
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  {memberProfile.name}
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Review your profile, contribution history, and personal cooperative records.
                </p>
              </div>
              <button
                onClick={() => setEditModalOpen(true)}
                data-tour="member-profile-edit"
                className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
              >
                <Edit className="h-4 w-4" />
                Edit Profile
              </button>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-green-600">{formatCurrency(totalContributions)}</div>
            <div className="text-sm text-muted-foreground">Total Contributions</div>
          </div>
          <div className="rounded-xl border border-blue-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-2xl font-bold">{transactionHistory[0].date}</div>
            <div className="text-sm text-muted-foreground">Last Contribution</div>
          </div>
          <div className="rounded-xl border border-purple-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-2xl font-bold">{memberProfile.joinDate}</div>
            <div className="text-sm text-muted-foreground">Member Since</div>
          </div>
        </div>

        <section
          className="mb-8 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500"
          data-tour="member-profile-summary"
        >
          <div className="grid gap-0 lg:grid-cols-[300px_minmax(0,1fr)]">
            <div className="border-b border-stone-200 bg-stone-50 px-6 py-6 lg:border-b-0 lg:border-r">
              <div className="flex items-center gap-4">
                <div className="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10">
                  <User className="h-10 w-10 text-primary" />
                </div>
                <div>
                  <h2 className="text-2xl font-display">{memberProfile.name}</h2>
                  <p className="text-sm text-gray-500">{memberProfile.id}</p>
                  <span className="mt-2 inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">
                    {memberProfile.status}
                  </span>
                </div>
              </div>
            </div>

            <div className="grid gap-5 px-6 py-6 md:grid-cols-2">
              <div className="flex items-start gap-3 rounded-lg border border-stone-100 bg-stone-50/70 px-4 py-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                  <Wallet className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-gray-500">Sector</p>
                  <p className="font-semibold text-gray-950">{memberProfile.sector}</p>
                </div>
              </div>

              <div className="flex items-start gap-3 rounded-lg border border-stone-100 bg-stone-50/70 px-4 py-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                  <Mail className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-gray-500">Email Address</p>
                  <p className="font-semibold text-gray-950">{memberProfile.email}</p>
                </div>
              </div>

              <div className="flex items-start gap-3 rounded-lg border border-stone-100 bg-stone-50/70 px-4 py-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                  <Phone className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-gray-500">Phone Number</p>
                  <p className="font-semibold text-gray-950">{memberProfile.phone}</p>
                </div>
              </div>

              <div className="flex items-start gap-3 rounded-lg border border-stone-100 bg-stone-50/70 px-4 py-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                  <MapPin className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-sm text-gray-500">Address</p>
                  <p className="font-semibold text-gray-950">{memberProfile.address}</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Member Records
                </p>
                <h2 className="mt-1 text-xl font-display">Profile Activity</h2>
              </div>
              <div
                className="flex items-center gap-2 rounded-lg bg-muted/30 p-1"
                data-tour="member-profile-tabs"
              >
                <button
                  onClick={() => setActiveTab("share-capital")}
                  className={`rounded-md px-3 py-1.5 text-sm font-medium transition-all ${
                    activeTab === "share-capital"
                      ? "bg-primary text-primary-foreground shadow-sm"
                      : "text-muted-foreground hover:text-foreground"
                  }`}
                >
                  Share Capital
                </button>
                <button
                  onClick={() => setActiveTab("documents")}
                  className={`rounded-md px-3 py-1.5 text-sm font-medium transition-all ${
                    activeTab === "documents"
                      ? "bg-primary text-primary-foreground shadow-sm"
                      : "text-muted-foreground hover:text-foreground"
                  }`}
                >
                  Documents
                </button>
              </div>
            </div>
          </div>

          <div className="px-5 py-6 md:px-6">
            {activeTab === "share-capital" && (
              <div className="space-y-6">
                <div>
                  <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-display text-foreground">
                      Monthly Contributions
                    </h3>
                    <div className="flex items-center gap-2 rounded-lg bg-muted/30 p-1">
                      {(["3m", "6m", "12m"] as const).map((period) => (
                        <button
                          key={period}
                          onClick={() => setShareCapitalPeriod(period)}
                          className={`rounded-md px-3 py-1.5 text-sm font-medium transition-all ${
                            shareCapitalPeriod === period
                              ? "bg-primary text-primary-foreground shadow-sm"
                              : "text-muted-foreground hover:text-foreground"
                          }`}
                        >
                          {period.toUpperCase()}
                        </button>
                      ))}
                    </div>
                  </div>

                  <div className="rounded-lg border border-stone-200 bg-stone-50 p-4" key={chartId}>
                    <ResponsiveContainer width="100%" height={320}>
                      <BarChart data={shareCapitalData}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" vertical={false} />
                        <XAxis dataKey="month" stroke="#6b7280" tickLine={false} axisLine={false} />
                        <YAxis stroke="#6b7280" tickLine={false} axisLine={false} />
                        <Tooltip
                          contentStyle={{
                            backgroundColor: "white",
                            border: "1px solid #e5e7eb",
                            borderRadius: "8px",
                          }}
                          formatter={(value: number) => formatCurrency(value)}
                        />
                        <Bar dataKey="amount" fill="#1b5e3f" radius={[8, 8, 0, 0]} />
                      </BarChart>
                    </ResponsiveContainer>
                  </div>
                </div>

                <div className="overflow-hidden rounded-lg border border-stone-200 bg-white">
                  <div className="border-b border-stone-200 px-5 py-4">
                    <div className="flex items-center justify-between">
                      <h3 className="text-lg font-display">Transaction History</h3>
                      <div className="text-sm font-medium text-gray-500">
                        {transactionHistory.length} records
                      </div>
                    </div>
                  </div>
                  <div className="overflow-x-auto">
                    <table className="w-full">
                      <thead className="bg-stone-50">
                        <tr>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Date</th>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Type</th>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Amount</th>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        {transactionHistory.map((transaction, index) => (
                          <tr
                            key={transaction.id}
                            className="border-t border-stone-100 transition-all hover:bg-green-50/40"
                            style={{ animationDelay: `${Math.min(index * 35, 160)}ms` }}
                          >
                            <td className="px-6 py-4 text-sm text-gray-500">{transaction.date}</td>
                            <td className="px-6 py-4 text-sm text-gray-950">{transaction.type}</td>
                            <td className="px-6 py-4 text-sm font-bold text-green-600">{formatCurrency(transaction.amount)}</td>
                            <td className="px-6 py-4">
                              <span className="inline-flex rounded-full bg-green-50 px-3 py-1 text-sm font-semibold text-green-700">
                                {transaction.status}
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
              <div className="space-y-5">
                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                  <div className="text-sm font-medium text-gray-500">
                    {filteredDocuments.length} document{filteredDocuments.length === 1 ? "" : "s"}
                  </div>
                  <div className="relative w-full md:max-w-sm">
                    <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                    <input
                      type="text"
                      value={documentSearch}
                      onChange={(event) => setDocumentSearch(event.target.value)}
                      placeholder="Search documents"
                      className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                    />
                  </div>
                </div>

                <div className="overflow-hidden rounded-lg border border-stone-200 bg-white">
                  <div className="overflow-x-auto">
                    <table className="w-full">
                      <thead className="bg-stone-50">
                        <tr>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Document Name</th>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Category</th>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Upload Date</th>
                          <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Size</th>
                          <th className="px-6 py-4 text-right text-sm font-semibold text-gray-600">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        {filteredDocuments.map((document, index) => (
                          <tr
                            key={document.id}
                            className="border-t border-stone-100 transition-all hover:bg-green-50/30"
                            style={{ animationDelay: `${Math.min(index * 35, 160)}ms` }}
                          >
                            <td className="px-6 py-4 text-sm text-gray-950">{document.name}</td>
                            <td className="px-6 py-4">
                              <span
                                className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${
                                  document.category === "Membership"
                                    ? "bg-blue-100 text-blue-700"
                                    : "bg-green-100 text-green-700"
                                }`}
                              >
                                {document.category}
                              </span>
                            </td>
                            <td className="px-6 py-4 text-sm text-gray-500">{document.uploadDate}</td>
                            <td className="px-6 py-4 text-sm text-gray-500">{document.size}</td>
                            <td className="px-6 py-4">
                              <div className="flex justify-end">
                                <button className="inline-flex items-center gap-2 rounded-lg border border-stone-200 px-3 py-2 text-sm font-semibold text-primary transition-all hover:bg-green-50">
                                  <Download className="h-4 w-4" />
                                  Download
                                </button>
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            )}
          </div>
        </section>
      </main>

      {editModalOpen && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4"
          onClick={() => setEditModalOpen(false)}
        >
          <div
            className="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between gap-4 border-b border-stone-200 px-6 py-5">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">Profile Update</p>
                <h2 className="mt-1 text-2xl font-display">Edit Profile Information</h2>
              </div>
              <button
                onClick={() => setEditModalOpen(false)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="space-y-4 px-6 py-6">
              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">Full Name</label>
                <input
                  type="text"
                  defaultValue={memberProfile.name}
                  className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                />
              </div>

              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">Email Address</label>
                <input
                  type="email"
                  defaultValue={memberProfile.email}
                  className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                />
              </div>

              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">Phone Number</label>
                <input
                  type="tel"
                  defaultValue={memberProfile.phone}
                  className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                />
              </div>

              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">Address</label>
                <textarea
                  defaultValue={memberProfile.address}
                  rows={3}
                  className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                />
              </div>
            </div>

            <div className="flex justify-end gap-3 border-t border-stone-200 px-6 py-5">
              <button
                onClick={() => setEditModalOpen(false)}
                className="inline-flex h-11 items-center justify-center rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50"
              >
                Cancel
              </button>
              <button
                onClick={() => setEditModalOpen(false)}
                className="inline-flex h-11 items-center justify-center rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
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
