import { useMemo, useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { Wallet, TrendingUp, TrendingDown, Calendar } from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from "recharts";
import { formatCurrency } from "../../utils/formatters";

export default function BookkeeperDashboard() {
  const navigate = useNavigate();
  const chartId = useMemo(() => `bookkeeper-${Date.now()}`, []);

  // Chart filters
  const [cashFlowPeriod, setCashFlowPeriod] = useState<"3m" | "6m" | "12m">("6m");

  // Check user role - only Bookkeeper can access this page
  useEffect(() => {
    const userRole = localStorage.getItem("userRole");

    if (userRole === "chairman") {
      navigate("/dashboard");
    } else if (userRole === "member") {
      navigate("/dashboard/profile");
    } else if (!userRole) {
      navigate("/");
    }
  }, [navigate]);

  // KPI Data
  const kpis = [
    {
      label: "Total Share Capital Collected",
      value: 32500000,
      change: 2800000,
      changePercent: "+9.4%",
      icon: Wallet,
      color: "bg-green-500",
      isPositive: true,
    },
    {
      label: "Total Expenditures (MTD)",
      value: 1245000,
      change: 125000,
      changePercent: "+11.2%",
      icon: TrendingDown,
      color: "bg-red-500",
      isPositive: false,
    },
    {
      label: "Current Net Balance",
      value: 31255000,
      change: 2675000,
      changePercent: "+9.4%",
      icon: TrendingUp,
      color: "bg-primary",
      isPositive: true,
    },
  ];

  // Monthly Cash Flow Data (Last 12 months)
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

  // Filter cash flow data based on period selection
  const cashFlowData = cashFlowPeriod === "3m"
    ? cashFlowDataAll.slice(-3)
    : cashFlowPeriod === "6m"
    ? cashFlowDataAll.slice(-6)
    : cashFlowDataAll;

  // Recent Transactions
  const recentTransactions = [
    {
      id: "txn-1",
      date: "Apr 14, 2026",
      type: "Contribution",
      description: "Share Capital Payment - Maria Santos",
      amount: 25000,
      status: "Completed",
    },
    {
      id: "txn-2",
      date: "Apr 13, 2026",
      type: "Expense",
      description: "Office Supplies Purchase",
      amount: 15000,
      status: "Completed",
    },
    {
      id: "txn-3",
      date: "Apr 12, 2026",
      type: "Contribution",
      description: "Share Capital Payment - Juan Dela Cruz",
      amount: 18000,
      status: "Completed",
    },
    {
      id: "txn-4",
      date: "Apr 11, 2026",
      type: "Expense",
      description: "Utility Bills - April 2026",
      amount: 45000,
      status: "Completed",
    },
    {
      id: "txn-5",
      date: "Apr 10, 2026",
      type: "Contribution",
      description: "Share Capital Payment - Rosa Garcia",
      amount: 32000,
      status: "Pending",
    },
  ];

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-display mb-2">Financial Overview</h1>
        <p className="text-muted-foreground">Cooperative financial summary and cash flow analysis</p>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {kpis.map((kpi, index) => {
          const Icon = kpi.icon;
          return (
            <div
              key={index}
              className="bg-card rounded-xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow"
            >
              <div className="flex items-start justify-between mb-4">
                <div className={`${kpi.color} w-12 h-12 rounded-lg flex items-center justify-center`}>
                  <Icon className="w-6 h-6 text-white" />
                </div>
              </div>
              <h3 className="text-sm text-muted-foreground mb-2">{kpi.label}</h3>
              <div className="text-3xl font-bold mb-2">{formatCurrency(kpi.value)}</div>
              <div className={`text-sm ${kpi.isPositive ? 'text-green-600' : 'text-red-600'}`}>
                {kpi.isPositive ? "+" : ""}{formatCurrency(kpi.change)} ({kpi.changePercent})
              </div>
            </div>
          );
        })}
      </div>

      {/* Monthly Cash Flow Chart */}
      <div className="bg-card rounded-xl p-6 border border-border shadow-sm mb-8">
        <div className="mb-6 flex items-start justify-between">
          <div>
            <h2 className="text-xl font-display mb-1">Monthly Cash Flow</h2>
            <p className="text-sm text-muted-foreground">
              Income vs. Expenses over the last {cashFlowPeriod === "3m" ? "3" : cashFlowPeriod === "6m" ? "6" : "12"} months
            </p>
          </div>
          {/* Period Filter */}
          <div className="flex items-center gap-2 bg-muted/30 rounded-lg p-1">
            <button
              onClick={() => setCashFlowPeriod("3m")}
              className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                cashFlowPeriod === "3m"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              3M
            </button>
            <button
              onClick={() => setCashFlowPeriod("6m")}
              className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                cashFlowPeriod === "6m"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              6M
            </button>
            <button
              onClick={() => setCashFlowPeriod("12m")}
              className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                cashFlowPeriod === "12m"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              12M
            </button>
          </div>
        </div>
        <div className="h-80" key={`cash-flow-chart-${chartId}`}>
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={cashFlowData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#e5e5e4" vertical={false} />
              <XAxis
                dataKey="month"
                stroke="#666"
                fontSize={12}
                tickLine={false}
                axisLine={false}
              />
              <YAxis
                stroke="#666"
                fontSize={12}
                tickLine={false}
                axisLine={false}
                tickFormatter={(value) => `₱${(value / 1000000).toFixed(1)}M`}
              />
              <Tooltip
                contentStyle={{
                  backgroundColor: "#fff",
                  border: "1px solid #e5e5e4",
                  borderRadius: "8px",
                }}
                formatter={(value: number) => `₱${value.toLocaleString()}`}
              />
              <Legend />
              <Bar dataKey="income" fill="#22c55e" radius={[4, 4, 0, 0]} name="Income" isAnimationActive={false} />
              <Bar dataKey="expenses" fill="#ef4444" radius={[4, 4, 0, 0]} name="Expenses" isAnimationActive={false} />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Recent Transactions */}
      <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
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
              {recentTransactions.map((transaction) => (
                <tr key={transaction.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                      <Calendar className="w-4 h-4" />
                      <span>{transaction.date}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`px-3 py-1 rounded-full text-sm ${
                      transaction.type === "Contribution"
                        ? "bg-green-100 text-green-700"
                        : "bg-red-100 text-red-700"
                    }`}>
                      {transaction.type}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-sm">{transaction.description}</td>
                  <td className="px-6 py-4">
                    <span className={`font-bold ${
                      transaction.type === "Contribution"
                        ? "text-green-600"
                        : "text-red-600"
                    }`}>
                      {transaction.type === "Contribution" ? "+" : "-"}₱{transaction.amount.toLocaleString()}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`px-3 py-1 rounded-full text-xs ${
                      transaction.status === "Completed"
                        ? "bg-blue-100 text-blue-700"
                        : "bg-amber-100 text-amber-700"
                    }`}>
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
  );
}
