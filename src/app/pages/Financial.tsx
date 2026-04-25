import { useMemo, useState } from "react";
import { Wallet, TrendingUp, Calendar } from "lucide-react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts";
import { formatCurrency } from "../../utils/formatters";

export default function Financial() {
  const chartId = useMemo(() => `financial-${Date.now()}`, []);
  const [financialPeriod, setFinancialPeriod] = useState<"3m" | "6m" | "12m">("6m");

  const financialDataAll = [
    { month: "May '25", revenue: 3900000, netIncome: 2950000 },
    { month: "Jun '25", revenue: 4100000, netIncome: 3180000 },
    { month: "Jul '25", revenue: 4000000, netIncome: 3050000 },
    { month: "Aug '25", revenue: 4300000, netIncome: 3320000 },
    { month: "Sep '25", revenue: 4150000, netIncome: 3190000 },
    { month: "Oct '25", revenue: 4250000, netIncome: 3260000 },
    { month: "Nov '25", revenue: 4200000, netIncome: 3220000 },
    { month: "Dec '25", revenue: 4500000, netIncome: 3380000 },
    { month: "Jan '26", revenue: 4800000, netIncome: 3750000 },
    { month: "Feb '26", revenue: 4600000, netIncome: 3420000 },
    { month: "Mar '26", revenue: 5100000, netIncome: 3880000 },
    { month: "Apr '26", revenue: 3800000, netIncome: 2555000 },
  ];

  // Filter financial data based on period selection
  const financialData = financialPeriod === "3m"
    ? financialDataAll.slice(-3)
    : financialPeriod === "6m"
    ? financialDataAll.slice(-6)
    : financialDataAll;

  const transactions = [
    { id: "1", date: "Apr 14, 2026", type: "Revenue", description: "Share Capital Payments", amount: 850000 },
    { id: "2", date: "Apr 10, 2026", type: "Revenue", description: "Service Fees Collection", amount: 125000 },
    { id: "3", date: "Apr 8, 2026", type: "Revenue", description: "Member Contributions", amount: 420000 },
    { id: "4", date: "Apr 5, 2026", type: "Revenue", description: "Agricultural Products Sales", amount: 680000 },
    { id: "5", date: "Apr 1, 2026", type: "Revenue", description: "Training Workshop Fees", amount: 95000 },
  ];

  const totalRevenue = transactions.reduce((sum, t) => sum + t.amount, 0);

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-display mb-2">Financial Records</h1>
        <p className="text-muted-foreground">Income, revenue, and financial performance tracking</p>
      </div>

      {/* Summary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm">
          <div className="flex items-center gap-3 mb-2">
            <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <Wallet className="w-6 h-6 text-green-600" />
            </div>
          </div>
          <div className="text-3xl font-bold mb-1 text-green-600">{formatCurrency(totalRevenue)}</div>
          <div className="text-sm text-muted-foreground">Total Revenue (MTD)</div>
        </div>

        <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
          <div className="text-3xl font-bold mb-1">{formatCurrency(3800000)}</div>
          <div className="text-sm text-muted-foreground">Monthly Income</div>
        </div>

        <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm">
          <div className="flex items-center gap-2 mb-2">
            <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <TrendingUp className="w-6 h-6 text-green-600" />
            </div>
          </div>
          <div className="text-3xl font-bold mb-1 text-green-600">{formatCurrency(2555000)}</div>
          <div className="text-sm text-muted-foreground">Net Income (MTD)</div>
        </div>
      </div>

      {/* Revenue Trend Chart */}
      <div className="bg-card rounded-xl p-6 border border-border shadow-sm mb-8">
        <div className="mb-6 flex items-start justify-between">
          <div>
            <h2 className="text-xl font-display mb-1">Revenue & Net Income Trend</h2>
            <p className="text-sm text-muted-foreground">
              {financialPeriod === "3m" ? "3-month" : financialPeriod === "6m" ? "6-month" : "12-month"} financial performance overview
            </p>
          </div>
          {/* Period Filter */}
          <div className="flex items-center gap-2 bg-muted/30 rounded-lg p-1">
            <button
              onClick={() => setFinancialPeriod("3m")}
              className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                financialPeriod === "3m"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              3M
            </button>
            <button
              onClick={() => setFinancialPeriod("6m")}
              className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                financialPeriod === "6m"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              6M
            </button>
            <button
              onClick={() => setFinancialPeriod("12m")}
              className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                financialPeriod === "12m"
                  ? "bg-primary text-primary-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
            >
              12M
            </button>
          </div>
        </div>
        <div className="h-80" key={`financial-chart-${chartId}`}>
          <ResponsiveContainer width="100%" height="100%">
            <LineChart data={financialData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
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
                formatter={(value: number) => formatCurrency(value)}
              />
              <Line
                type="monotone"
                dataKey="revenue"
                stroke="#22c55e"
                strokeWidth={3}
                dot={false}
                isAnimationActive={false}
                name="Revenue"
              />
              <Line
                type="monotone"
                dataKey="netIncome"
                stroke="#1b5e3f"
                strokeWidth={3}
                dot={false}
                isAnimationActive={false}
                name="Net Income"
              />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Revenue Transactions */}
      <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <div className="p-6 border-b border-border">
          <h2 className="text-xl font-display">Revenue Transactions</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-muted/50">
              <tr>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Date</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Type</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Description</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Amount</th>
              </tr>
            </thead>
            <tbody>
              {transactions.map((transaction) => (
                <tr key={transaction.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                      <Calendar className="w-4 h-4" />
                      <span>{transaction.date}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">
                      {transaction.type}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-sm">{transaction.description}</td>
                  <td className="px-6 py-4">
                    <span className="font-bold text-green-600">+{formatCurrency(transaction.amount)}</span>
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
