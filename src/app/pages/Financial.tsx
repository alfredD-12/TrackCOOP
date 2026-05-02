import { useMemo, useState } from "react";
import {
  Wallet,
  TrendingUp,
  Calendar,
  Search,
  BarChart3,
  ArrowUpDown,
} from "lucide-react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

type RevenueSortKey = "date" | "type" | "description" | "amount";

export default function Financial() {
  const chartId = useMemo(() => `financial-${Date.now()}`, []);
  const [financialPeriod, setFinancialPeriod] = useState<"3m" | "6m" | "12m">("6m");
  const [transactionSearch, setTransactionSearch] = useState("");
  const [sourceFilter, setSourceFilter] = useState("all");
  const [amountFilter, setAmountFilter] = useState<
    "all" | "under_150000" | "150000_500000" | "above_500000"
  >("all");
  const [sortKey, setSortKey] = useState<RevenueSortKey>("date");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("desc");

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

  const financialData = financialPeriod === "3m"
    ? financialDataAll.slice(-3)
    : financialPeriod === "6m"
    ? financialDataAll.slice(-6)
    : financialDataAll;

  const transactions = [
    { id: "1", date: "Apr 14, 2026", dateIso: "2026-04-14", type: "Revenue", source: "Share Capital", description: "Share Capital Payments", amount: 850000 },
    { id: "2", date: "Apr 10, 2026", dateIso: "2026-04-10", type: "Revenue", source: "Service Fees", description: "Service Fees Collection", amount: 125000 },
    { id: "3", date: "Apr 8, 2026", dateIso: "2026-04-08", type: "Revenue", source: "Member Contributions", description: "Member Contributions", amount: 420000 },
    { id: "4", date: "Apr 5, 2026", dateIso: "2026-04-05", type: "Revenue", source: "Product Sales", description: "Agricultural Products Sales", amount: 680000 },
    { id: "5", date: "Apr 1, 2026", dateIso: "2026-04-01", type: "Revenue", source: "Training", description: "Training Workshop Fees", amount: 95000 },
  ];

  const totalRevenue = transactions.reduce((sum, t) => sum + t.amount, 0);
  const revenueSources = Array.from(new Set(transactions.map((transaction) => transaction.source)));

  const filteredTransactions = transactions.filter((transaction) => {
    const query = transactionSearch.trim().toLowerCase();
    const matchesSearch =
      !query ||
      transaction.description.toLowerCase().includes(query) ||
      transaction.date.toLowerCase().includes(query) ||
      transaction.source.toLowerCase().includes(query);
    const matchesSource =
      sourceFilter === "all" || transaction.source === sourceFilter;
    const matchesAmount =
      amountFilter === "all" ||
      (amountFilter === "under_150000" && transaction.amount < 150000) ||
      (amountFilter === "150000_500000" &&
        transaction.amount >= 150000 &&
        transaction.amount <= 500000) ||
      (amountFilter === "above_500000" && transaction.amount > 500000);

    return matchesSearch && matchesSource && matchesAmount;
  });

  const sortedTransactions = [...filteredTransactions].sort((left, right) => {
    const direction = sortDirection === "asc" ? 1 : -1;

    switch (sortKey) {
      case "date":
        return left.dateIso.localeCompare(right.dateIso) * direction;
      case "type":
      case "description":
        return left[sortKey].localeCompare(right[sortKey]) * direction;
      case "amount":
        return (left.amount - right.amount) * direction;
      default:
        return 0;
    }
  });

  const clearTransactionFilters = () => {
    setTransactionSearch("");
    setSourceFilter("all");
    setAmountFilter("all");
  };

  const handleSort = (nextKey: RevenueSortKey) => {
    if (sortKey === nextKey) {
      setSortDirection((current) => (current === "asc" ? "desc" : "asc"));
    } else {
      setSortKey(nextKey);
      setSortDirection(nextKey === "date" ? "desc" : "asc");
    }
  };

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
                Financial Records
              </p>
              <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">Income & Revenue</h1>
              <p className="mt-3 max-w-2xl text-lg text-white/85">Income, revenue, and financial performance tracking</p>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="flex items-center gap-3 mb-3">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <Wallet className="w-6 h-6 text-green-600" />
              </div>
            </div>
            <div className="text-3xl font-bold mb-1 text-green-600">{formatCurrency(totalRevenue)}</div>
            <div className="text-sm text-muted-foreground">Total Revenue (MTD)</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-border shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="flex items-center gap-3 mb-3">
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <BarChart3 className="w-6 h-6 text-blue-700" />
              </div>
            </div>
            <div className="text-3xl font-bold mb-1">{formatCurrency(3800000)}</div>
            <div className="text-sm text-muted-foreground">Monthly Income</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="flex items-center gap-3 mb-3">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <TrendingUp className="w-6 h-6 text-green-600" />
              </div>
            </div>
            <div className="text-3xl font-bold mb-1 text-green-600">{formatCurrency(2555000)}</div>
            <div className="text-sm text-muted-foreground">Net Income (MTD)</div>
          </div>
        </div>

        <div className="bg-card rounded-xl p-6 border border-border shadow-sm mb-8 animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500">
          <div className="mb-6 flex items-start justify-between">
            <div>
              <h2 className="text-xl font-display mb-1">Revenue & Net Income Trend</h2>
              <p className="text-sm text-muted-foreground">
                {financialPeriod === "3m" ? "3-month" : financialPeriod === "6m" ? "6-month" : "12-month"} financial performance overview
              </p>
            </div>
            <div className="flex items-center gap-2 bg-muted/30 rounded-lg p-1">
              {(["3m", "6m", "12m"] as const).map((p) => (
                <button key={p} onClick={() => setFinancialPeriod(p)} className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${financialPeriod === p ? "bg-primary text-primary-foreground shadow-sm" : "text-muted-foreground hover:text-foreground"}`}>
                  {p.toUpperCase()}
                </button>
              ))}
            </div>
          </div>
          <div className="h-80" key={`financial-chart-${chartId}`}>
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={financialData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#e5e5e4" vertical={false} />
                <XAxis dataKey="month" stroke="#666" fontSize={12} tickLine={false} axisLine={false} />
                <YAxis stroke="#666" fontSize={12} tickLine={false} axisLine={false} tickFormatter={(v) => `₱${(v / 1000000).toFixed(1)}M`} />
                <Tooltip contentStyle={{ backgroundColor: "#fff", border: "1px solid #e5e5e4", borderRadius: "8px" }} formatter={(value: number) => formatCurrency(value)} />
                <Line type="monotone" dataKey="revenue" stroke="#22c55e" strokeWidth={3} dot={false} isAnimationActive={false} name="Revenue" />
                <Line type="monotone" dataKey="netIncome" stroke="#1b5e3f" strokeWidth={3} dot={false} isAnimationActive={false} name="Net Income" />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Revenue Ledger
                </p>
                <h2 className="mt-1 text-xl font-display">Revenue Transactions</h2>
              </div>
              <div className="text-sm font-medium text-gray-500">
                {sortedTransactions.length} result
                {sortedTransactions.length === 1 ? "" : "s"}
              </div>
            </div>

            <div className="mt-5 border-t border-stone-100 pt-4">
              <div className="grid gap-3 xl:grid-cols-[minmax(280px,1fr)_200px_220px_auto] xl:items-center">
                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={transactionSearch}
                    onChange={(event) => setTransactionSearch(event.target.value)}
                    placeholder="Search revenue transactions"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <select
                  value={sourceFilter}
                  onChange={(event) => setSourceFilter(event.target.value)}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by revenue source"
                >
                  <option value="all">All Sources</option>
                  {revenueSources.map((source) => (
                    <option key={source} value={source}>
                      {source}
                    </option>
                  ))}
                </select>

                <select
                  value={amountFilter}
                  onChange={(event) =>
                    setAmountFilter(
                      event.target.value as
                        | "all"
                        | "under_150000"
                        | "150000_500000"
                        | "above_500000"
                    )
                  }
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by amount"
                >
                  <option value="all">All Amounts</option>
                  <option value="under_150000">Under 150,000</option>
                  <option value="150000_500000">150,000 to 500,000</option>
                  <option value="above_500000">Above 500,000</option>
                </select>

                <button
                  onClick={clearTransactionFilters}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-600 transition-all hover:-translate-y-0.5 hover:bg-stone-50 hover:text-primary"
                >
                  Clear
                </button>
              </div>
            </div>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-stone-50">
                <tr>
                  {(
                    [
                      ["date", "Date"],
                      ["type", "Type"],
                      ["description", "Description"],
                      ["amount", "Amount"],
                    ] as Array<[RevenueSortKey, string]>
                  ).map(([key, label]) => (
                    <th
                      key={key}
                      className="px-6 py-4 text-left text-sm font-semibold text-gray-600"
                    >
                      <button
                        onClick={() => handleSort(key)}
                        className="inline-flex items-center gap-2 transition-colors hover:text-gray-950"
                      >
                        {label}
                        <ArrowUpDown className="h-4 w-4" />
                        {sortKey === key && (
                          <span className="text-xs uppercase tracking-[0.12em] text-primary">
                            {sortDirection}
                          </span>
                        )}
                      </button>
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody
                key={`${transactionSearch}-${sourceFilter}-${amountFilter}-${sortKey}-${sortDirection}`}
                className="animate-in fade-in duration-300"
              >
                {sortedTransactions.length === 0 ? (
                  <tr>
                    <td colSpan={4} className="px-6 py-14 text-center text-gray-500">
                      No revenue transactions found.
                    </td>
                  </tr>
                ) : (
                sortedTransactions.map((txn, index) => (
                  <tr key={txn.id} className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-green-50/50" style={{ animationDelay: `${Math.min(index * 40, 200)}ms` }}>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2 text-sm text-gray-500">
                        <Calendar className="w-4 h-4" /><span>{txn.date}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4"><span className="inline-flex rounded-full px-3 py-1 text-sm font-semibold bg-green-50 text-green-700">{txn.type}</span></td>
                    <td className="px-6 py-4 text-sm text-gray-950">
                      <div>
                        <p>{txn.description}</p>
                        <p className="mt-1 text-xs text-gray-500">{txn.source}</p>
                      </div>
                    </td>
                    <td className="px-6 py-4"><span className="font-bold text-green-600">+{formatCurrency(txn.amount)}</span></td>
                  </tr>
                ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  );
}
