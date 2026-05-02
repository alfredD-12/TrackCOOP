import { useMemo, useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { Wallet, TrendingUp, TrendingDown, Calendar } from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from "recharts";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

export default function BookkeeperDashboard() {
  const navigate = useNavigate();
  const chartId = useMemo(() => `bookkeeper-${Date.now()}`, []);
  const [cashFlowPeriod, setCashFlowPeriod] = useState<"3m" | "6m" | "12m">("6m");

  useEffect(() => {
    const userRole = localStorage.getItem("userRole");
    if (userRole === "chairman") navigate("/dashboard");
    else if (userRole === "member") navigate("/dashboard/profile");
    else if (!userRole) navigate("/");
  }, [navigate]);

  const kpis = [
    { label: "Total Share Capital Collected", value: 32500000, change: 2800000, changePercent: "+9.4%", icon: Wallet, color: "bg-green-500", isPositive: true },
    { label: "Total Expenditures (MTD)", value: 1245000, change: 125000, changePercent: "+11.2%", icon: TrendingDown, color: "bg-red-500", isPositive: false },
    { label: "Current Net Balance", value: 31255000, change: 2675000, changePercent: "+9.4%", icon: TrendingUp, color: "bg-primary", isPositive: true },
  ];

  const cashFlowDataAll = [
    { month: "May '25", income: 3900000, expenses: 890000 },
    { month: "Jun '25", income: 4100000, expenses: 920000 },
    { month: "Jul '25", income: 4000000, expenses: 950000 },
    { month: "Aug '25", income: 4300000, expenses: 980000 },
    { month: "Sep '25", income: 4150000, expenses: 960000 },
    { month: "Oct '25", income: 4250000, expenses: 990000 },
    { month: "Nov '25", income: 4200000, expenses: 980000 },
    { month: "Dec '25", income: 4500000, expenses: 1120000 },
    { month: "Jan '26", income: 4800000, expenses: 1050000 },
    { month: "Feb '26", income: 4600000, expenses: 1180000 },
    { month: "Mar '26", income: 5100000, expenses: 1220000 },
    { month: "Apr '26", income: 3800000, expenses: 1245000 },
  ];

  const cashFlowData = cashFlowPeriod === "3m" ? cashFlowDataAll.slice(-3) : cashFlowPeriod === "6m" ? cashFlowDataAll.slice(-6) : cashFlowDataAll;

  const recentTransactions = [
    { id: "txn-1", date: "Apr 14, 2026", type: "Contribution", description: "Share Capital Payment - Maria Santos", amount: 25000, status: "Completed" },
    { id: "txn-2", date: "Apr 13, 2026", type: "Expense", description: "Office Supplies Purchase", amount: 15000, status: "Completed" },
    { id: "txn-3", date: "Apr 12, 2026", type: "Contribution", description: "Share Capital Payment - Juan Dela Cruz", amount: 18000, status: "Completed" },
    { id: "txn-4", date: "Apr 11, 2026", type: "Expense", description: "Utility Bills - April 2026", amount: 45000, status: "Completed" },
    { id: "txn-5", date: "Apr 10, 2026", type: "Contribution", description: "Share Capital Payment - Rosa Garcia", amount: 32000, status: "Pending" },
  ];

  return (
    <div className="min-h-full bg-stone-50 text-gray-950">
      {/* Hero Header */}
      <section className="relative overflow-hidden border-b border-stone-200">
        <img src={heroImage} alt="" aria-hidden="true" className="absolute inset-0 h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/15" />
        <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-stone-50 to-transparent" />

        <div className="relative mx-auto flex min-h-[280px] max-w-[1600px] flex-col justify-start px-6 py-8 md:min-h-[320px] md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="max-w-4xl">
              <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                Bookkeeper Dashboard
              </p>
              <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                Financial Overview
              </h1>
              <p className="mt-3 max-w-2xl text-lg text-white/85">
                Cooperative financial summary and cash flow analysis
              </p>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        {/* KPI Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          {kpis.map((kpi, index) => {
            const Icon = kpi.icon;
            return (
              <div
                key={index}
                className="bg-card rounded-xl p-6 border border-border shadow-sm transition-all duration-300 animate-in fade-in slide-in-from-bottom-3 hover:-translate-y-1 hover:shadow-lg"
                style={{ animationDelay: `${index * 75}ms` }}
              >
                <div className="flex items-start justify-between mb-4">
                  <div className={`${kpi.color} w-12 h-12 rounded-lg flex items-center justify-center`}>
                    <Icon className="w-6 h-6 text-white" />
                  </div>
                </div>
                <h3 className="text-sm text-muted-foreground mb-2">{kpi.label}</h3>
                <div className="text-3xl font-bold mb-2">{formatCurrency(kpi.value)}</div>
                <div className={`text-sm ${kpi.isPositive ? "text-green-600" : "text-red-600"}`}>
                  {kpi.isPositive ? "+" : ""}{formatCurrency(kpi.change)} ({kpi.changePercent})
                </div>
              </div>
            );
          })}
        </div>

        {/* Cash Flow Chart */}
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm mb-8 animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500">
          <div className="mb-6 flex items-start justify-between">
            <div>
              <h2 className="text-xl font-display mb-1">Monthly Cash Flow</h2>
              <p className="text-sm text-muted-foreground">
                Income vs. Expenses over the last {cashFlowPeriod === "3m" ? "3" : cashFlowPeriod === "6m" ? "6" : "12"} months
              </p>
            </div>
            <div className="flex items-center gap-2 bg-muted/30 rounded-lg p-1">
              {(["3m", "6m", "12m"] as const).map((p) => (
                <button
                  key={p}
                  onClick={() => setCashFlowPeriod(p)}
                  className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${cashFlowPeriod === p ? "bg-primary text-primary-foreground shadow-sm" : "text-muted-foreground hover:text-foreground"}`}
                >
                  {p.toUpperCase()}
                </button>
              ))}
            </div>
          </div>
          <div className="h-80" key={`cash-flow-chart-${chartId}`}>
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={cashFlowData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#e5e5e4" vertical={false} />
                <XAxis dataKey="month" stroke="#666" fontSize={12} tickLine={false} axisLine={false} />
                <YAxis stroke="#666" fontSize={12} tickLine={false} axisLine={false} tickFormatter={(v) => `₱${(v / 1000000).toFixed(1)}M`} />
                <Tooltip contentStyle={{ backgroundColor: "#fff", border: "1px solid #e5e5e4", borderRadius: "8px" }} formatter={(value: number) => `₱${value.toLocaleString()}`} />
                <Legend />
                <Bar dataKey="income" fill="#22c55e" radius={[4, 4, 0, 0]} name="Income" isAnimationActive={false} />
                <Bar dataKey="expenses" fill="#ef4444" radius={[4, 4, 0, 0]} name="Expenses" isAnimationActive={false} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Recent Transactions */}
        <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
          <div className="p-6 border-b border-border">
            <h2 className="text-xl font-display">Recent Transactions</h2>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-muted/50">
                <tr>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Date</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Type</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Description</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Amount</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Status</th>
                </tr>
              </thead>
              <tbody>
                {recentTransactions.map((txn, index) => (
                  <tr key={txn.id} className="border-t border-border hover:bg-muted/30 transition-colors animate-in fade-in slide-in-from-bottom-2" style={{ animationDelay: `${Math.min(index * 40, 220)}ms` }}>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2 text-sm text-muted-foreground">
                        <Calendar className="w-4 h-4" />
                        <span>{txn.date}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-3 py-1 rounded-full text-sm ${txn.type === "Contribution" ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"}`}>
                        {txn.type}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm">{txn.description}</td>
                    <td className="px-6 py-4">
                      <span className={`font-bold ${txn.type === "Contribution" ? "text-green-600" : "text-red-600"}`}>
                        {txn.type === "Contribution" ? "+" : "-"}₱{txn.amount.toLocaleString()}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-3 py-1 rounded-full text-xs ${txn.status === "Completed" ? "bg-blue-100 text-blue-700" : "bg-amber-100 text-amber-700"}`}>
                        {txn.status}
                      </span>
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
