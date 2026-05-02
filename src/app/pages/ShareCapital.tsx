import { Search, TrendingUp } from "lucide-react";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

export default function ShareCapital() {
  const members = [
    { id: "M001", name: "Maria Santos", sector: "Rice Farming", capital: 25000, lastPayment: "Apr 14, 2026", status: "Active" },
    { id: "M002", name: "Juan Dela Cruz", sector: "Corn", capital: 18000, lastPayment: "Apr 12, 2026", status: "Active" },
    { id: "M003", name: "Rosa Garcia", sector: "Fishery", capital: 32000, lastPayment: "Apr 10, 2026", status: "Active" },
    { id: "M004", name: "Pedro Reyes", sector: "Livestock", capital: 12000, lastPayment: "Mar 28, 2026", status: "Inactive" },
    { id: "M005", name: "Ana Lopez", sector: "High-Value Crops", capital: 45000, lastPayment: "Apr 13, 2026", status: "Active" },
    { id: "M006", name: "Carlos Ramos", sector: "Rice Farming", capital: 28000, lastPayment: "Apr 11, 2026", status: "Active" },
  ];

  const totalCapital = members.reduce((sum, m) => sum + m.capital, 0);

  return (
    <div className="min-h-full bg-stone-50 text-gray-950">
      <section className="relative overflow-hidden border-b border-stone-200">
        <img src={heroImage} alt="" aria-hidden="true" className="absolute inset-0 h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/15" />
        <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-stone-50 to-transparent" />
        <div className="relative mx-auto flex min-h-[280px] max-w-[1600px] flex-col justify-start px-6 py-8 md:min-h-[320px] md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="max-w-4xl">
              <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                Share Capital
              </p>
              <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">Member Share Capital</h1>
              <p className="mt-3 max-w-2xl text-lg text-white/85">Member share capital contributions and tracking</p>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        {/* Summary Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="flex items-center gap-3 mb-3">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <TrendingUp className="w-6 h-6 text-green-600" />
              </div>
            </div>
            <div className="text-3xl font-bold mb-1 text-green-600">{formatCurrency(totalCapital)}</div>
            <div className="text-sm text-muted-foreground">Total Share Capital</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-border shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="text-3xl font-bold mb-1">{members.length}</div>
            <div className="text-sm text-muted-foreground">Contributing Members</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-border shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="text-3xl font-bold mb-1">{formatCurrency(Math.round(totalCapital / members.length))}</div>
            <div className="text-sm text-muted-foreground">Average per Member</div>
          </div>
        </div>

        {/* Search */}
        <div className="bg-card rounded-xl p-4 border border-border shadow-sm mb-6 animate-in fade-in slide-in-from-bottom-3 delay-200 duration-300">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
            <input type="text" placeholder="Search members by name or ID..." className="w-full pl-10 pr-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring" />
          </div>
        </div>

        {/* Members Table */}
        <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-muted/50">
                <tr>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Member ID</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Name</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Sector</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Share Capital</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Last Payment</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Status</th>
                </tr>
              </thead>
              <tbody>
                {members.map((member, index) => (
                  <tr key={member.id} className="border-t border-border hover:bg-muted/30 transition-colors animate-in fade-in slide-in-from-bottom-2" style={{ animationDelay: `${Math.min(index * 40, 220)}ms` }}>
                    <td className="px-6 py-4 font-medium">{member.id}</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                          <span className="font-bold text-primary">{member.name.charAt(0)}</span>
                        </div>
                        <span className="font-medium">{member.name}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm">{member.sector}</td>
                    <td className="px-6 py-4"><span className="font-bold text-green-600">{formatCurrency(member.capital)}</span></td>
                    <td className="px-6 py-4 text-sm text-muted-foreground">{member.lastPayment}</td>
                    <td className="px-6 py-4">
                      <span className={`px-3 py-1 rounded-full text-sm ${member.status === "Active" ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"}`}>{member.status}</span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  );
}
