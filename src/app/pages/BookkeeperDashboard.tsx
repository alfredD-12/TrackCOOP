import { useMemo, useEffect, useState } from "react";
import { useNavigate } from "react-router";
import {
  ArrowUpDown,
  Wallet,
  TrendingUp,
  TrendingDown,
  Calendar,
  Search,
} from "lucide-react";
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Legend,
} from "recharts";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

type TransactionType = "Contribution" | "Expense";
type TransactionStatus = "Completed" | "Pending";
type TransactionSortKey = "date" | "type" | "description" | "amount" | "status";

interface Transaction {
  id: string;
  date: string;
  dateIso: string;
  type: TransactionType;
  description: string;
  amount: number;
  status: TransactionStatus;
}

export default function BookkeeperDashboard() {
  const navigate = useNavigate();
  const chartId = useMemo(() => `bookkeeper-${Date.now()}`, []);
  const [cashFlowPeriod, setCashFlowPeriod] = useState<"3m" | "6m" | "12m">(
    "6m"
  );
  const [transactionSearch, setTransactionSearch] = useState("");
  const [transactionTypeFilter, setTransactionTypeFilter] = useState<
    "all" | TransactionType
  >("all");
  const [transactionStatusFilter, setTransactionStatusFilter] = useState<
    "all" | TransactionStatus
  >("all");
  const [transactionSortKey, setTransactionSortKey] =
    useState<TransactionSortKey>("date");
  const [transactionSortDirection, setTransactionSortDirection] = useState<
    "asc" | "desc"
  >("desc");

  useEffect(() => {
    const userRole = localStorage.getItem("userRole");
    if (userRole === "chairman") navigate("/dashboard");
    else if (userRole === "member") navigate("/dashboard/profile");
    else if (!userRole) navigate("/");
  }, [navigate]);

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

  const cashFlowData =
    cashFlowPeriod === "3m"
      ? cashFlowDataAll.slice(-3)
      : cashFlowPeriod === "6m"
        ? cashFlowDataAll.slice(-6)
        : cashFlowDataAll;

  const recentTransactions: Transaction[] = [
    {
      id: "txn-1",
      date: "Apr 14, 2026",
      dateIso: "2026-04-14",
      type: "Contribution",
      description: "Share Capital Payment - Maria Santos",
      amount: 25000,
      status: "Completed",
    },
    {
      id: "txn-2",
      date: "Apr 13, 2026",
      dateIso: "2026-04-13",
      type: "Expense",
      description: "Office Supplies Purchase",
      amount: 15000,
      status: "Completed",
    },
    {
      id: "txn-3",
      date: "Apr 12, 2026",
      dateIso: "2026-04-12",
      type: "Contribution",
      description: "Share Capital Payment - Juan Dela Cruz",
      amount: 18000,
      status: "Completed",
    },
    {
      id: "txn-4",
      date: "Apr 11, 2026",
      dateIso: "2026-04-11",
      type: "Expense",
      description: "Utility Bills - April 2026",
      amount: 45000,
      status: "Completed",
    },
    {
      id: "txn-5",
      date: "Apr 10, 2026",
      dateIso: "2026-04-10",
      type: "Contribution",
      description: "Share Capital Payment - Rosa Garcia",
      amount: 32000,
      status: "Pending",
    },
  ];

  const filteredTransactions = recentTransactions.filter((transaction) => {
    const query = transactionSearch.trim().toLowerCase();
    const matchesSearch =
      !query ||
      transaction.description.toLowerCase().includes(query) ||
      transaction.type.toLowerCase().includes(query) ||
      transaction.date.toLowerCase().includes(query) ||
      transaction.status.toLowerCase().includes(query);
    const matchesType =
      transactionTypeFilter === "all" || transaction.type === transactionTypeFilter;
    const matchesStatus =
      transactionStatusFilter === "all" ||
      transaction.status === transactionStatusFilter;

    return matchesSearch && matchesType && matchesStatus;
  });

  const sortedTransactions = [...filteredTransactions].sort((left, right) => {
    const direction = transactionSortDirection === "asc" ? 1 : -1;

    switch (transactionSortKey) {
      case "date":
        return left.dateIso.localeCompare(right.dateIso) * direction;
      case "type":
      case "description":
      case "status":
        return left[transactionSortKey].localeCompare(right[transactionSortKey]) * direction;
      case "amount":
        return (left.amount - right.amount) * direction;
      default:
        return 0;
    }
  });

  const clearTransactionFilters = () => {
    setTransactionSearch("");
    setTransactionTypeFilter("all");
    setTransactionStatusFilter("all");
  };

  const handleTransactionSort = (nextKey: TransactionSortKey) => {
    if (transactionSortKey === nextKey) {
      setTransactionSortDirection((current) =>
        current === "asc" ? "desc" : "asc"
      );
    } else {
      setTransactionSortKey(nextKey);
      setTransactionSortDirection(nextKey === "date" ? "desc" : "asc");
    }
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
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          {kpis.map((kpi, index) => {
            const Icon = kpi.icon;
            return (
              <div
                key={kpi.label}
                className="rounded-xl border border-border bg-card p-6 shadow-sm transition-all duration-300 animate-in fade-in slide-in-from-bottom-3 hover:-translate-y-1 hover:shadow-lg"
                style={{ animationDelay: `${index * 75}ms` }}
              >
                <div className="mb-4 flex items-start justify-between">
                  <div
                    className={`${kpi.color} flex h-12 w-12 items-center justify-center rounded-lg`}
                  >
                    <Icon className="h-6 w-6 text-white" />
                  </div>
                </div>
                <h3 className="mb-2 text-sm text-muted-foreground">{kpi.label}</h3>
                <div className="mb-2 text-3xl font-bold">
                  {formatCurrency(kpi.value)}
                </div>
                <div
                  className={`text-sm ${kpi.isPositive ? "text-green-600" : "text-red-600"}`}
                >
                  {kpi.isPositive ? "+" : ""}
                  {formatCurrency(kpi.change)} ({kpi.changePercent})
                </div>
              </div>
            );
          })}
        </div>

        <div
          className="mb-8 rounded-xl border border-border bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500"
          data-tour="bookkeeper-cashflow"
        >
          <div className="mb-6 flex items-start justify-between">
            <div>
              <h2 className="mb-1 text-xl font-display">Monthly Cash Flow</h2>
              <p className="text-sm text-muted-foreground">
                Income vs. Expenses over the last{" "}
                {cashFlowPeriod === "3m"
                  ? "3"
                  : cashFlowPeriod === "6m"
                    ? "6"
                    : "12"}{" "}
                months
              </p>
            </div>
            <div className="flex items-center gap-2 rounded-lg bg-muted/30 p-1">
              {(["3m", "6m", "12m"] as const).map((period) => (
                <button
                  key={period}
                  onClick={() => setCashFlowPeriod(period)}
                  className={`rounded-md px-3 py-1.5 text-sm font-medium transition-all ${
                    cashFlowPeriod === period
                      ? "bg-primary text-primary-foreground shadow-sm"
                      : "text-muted-foreground hover:text-foreground"
                  }`}
                >
                  {period.toUpperCase()}
                </button>
              ))}
            </div>
          </div>
          <div className="h-80" key={`cash-flow-chart-${chartId}`}>
            <ResponsiveContainer width="100%" height="100%">
              <BarChart
                data={cashFlowData}
                margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
              >
                <CartesianGrid
                  strokeDasharray="3 3"
                  stroke="#e5e5e4"
                  vertical={false}
                />
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
                  tickFormatter={(value) => `PHP ${(value / 1000000).toFixed(1)}M`}
                />
                <Tooltip
                  contentStyle={{
                    backgroundColor: "#fff",
                    border: "1px solid #e5e5e4",
                    borderRadius: "8px",
                  }}
                  formatter={(value: number) => formatCurrency(value)}
                />
                <Legend />
                <Bar
                  dataKey="income"
                  fill="#22c55e"
                  radius={[4, 4, 0, 0]}
                  name="Income"
                  isAnimationActive={false}
                />
                <Bar
                  dataKey="expenses"
                  fill="#ef4444"
                  radius={[4, 4, 0, 0]}
                  name="Expenses"
                  isAnimationActive={false}
                />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Transactions
                </p>
                <h2 className="mt-1 font-display text-2xl font-bold text-gray-950">
                  Recent Transactions
                </h2>
              </div>
              <div className="text-sm font-medium text-gray-500">
                {sortedTransactions.length} result
                {sortedTransactions.length === 1 ? "" : "s"}
              </div>
            </div>

            <div
              className="mt-5 border-t border-stone-100 pt-4"
              data-tour="bookkeeper-transactions-filters"
            >
              <div className="grid gap-3 xl:grid-cols-[minmax(280px,1fr)_180px_180px_auto] xl:items-center">
                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={transactionSearch}
                    onChange={(event) => setTransactionSearch(event.target.value)}
                    placeholder="Search transactions"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <select
                  value={transactionTypeFilter}
                  onChange={(event) =>
                    setTransactionTypeFilter(
                      event.target.value as "all" | TransactionType
                    )
                  }
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by transaction type"
                >
                  <option value="all">All Types</option>
                  <option value="Contribution">Contribution</option>
                  <option value="Expense">Expense</option>
                </select>

                <select
                  value={transactionStatusFilter}
                  onChange={(event) =>
                    setTransactionStatusFilter(
                      event.target.value as "all" | TransactionStatus
                    )
                  }
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by transaction status"
                >
                  <option value="all">All Status</option>
                  <option value="Completed">Completed</option>
                  <option value="Pending">Pending</option>
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

          <div
            className="overflow-x-auto"
            data-tour="bookkeeper-transactions-table"
          >
            <table className="w-full">
              <thead className="bg-stone-50">
                <tr>
                  {(
                    [
                      ["date", "Date"],
                      ["type", "Type"],
                      ["description", "Description"],
                      ["amount", "Amount"],
                      ["status", "Status"],
                    ] as Array<[TransactionSortKey, string]>
                  ).map(([key, label]) => (
                    <th
                      key={key}
                      className="px-6 py-4 text-left text-sm font-semibold text-gray-600"
                    >
                      <button
                        onClick={() => handleTransactionSort(key)}
                        className="inline-flex items-center gap-2 transition-colors hover:text-gray-950"
                      >
                        {label}
                        <ArrowUpDown className="h-4 w-4" />
                        {transactionSortKey === key && (
                          <span className="text-xs uppercase tracking-[0.12em] text-primary">
                            {transactionSortDirection}
                          </span>
                        )}
                      </button>
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody
                key={`${transactionSearch}-${transactionTypeFilter}-${transactionStatusFilter}-${transactionSortKey}-${transactionSortDirection}`}
                className="animate-in fade-in duration-300"
              >
                {sortedTransactions.length === 0 ? (
                  <tr>
                    <td
                      colSpan={5}
                      className="px-6 py-14 text-center text-gray-500"
                    >
                      No transactions found.
                    </td>
                  </tr>
                ) : (
                  sortedTransactions.map((transaction, index) => (
                    <tr
                      key={transaction.id}
                      className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-green-50/50"
                      style={{ animationDelay: `${Math.min(index * 40, 220)}ms` }}
                    >
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-2 text-sm text-gray-500">
                          <Calendar className="h-4 w-4" />
                          <span>{transaction.date}</span>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <span
                          className={`inline-flex rounded-full px-3 py-1 text-sm font-semibold ${
                            transaction.type === "Contribution"
                              ? "bg-green-50 text-green-700"
                              : "bg-red-50 text-red-700"
                          }`}
                        >
                          {transaction.type}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-950">
                        {transaction.description}
                      </td>
                      <td className="px-6 py-4">
                        <span
                          className={`font-bold ${
                            transaction.type === "Contribution"
                              ? "text-green-600"
                              : "text-red-600"
                          }`}
                        >
                          {transaction.type === "Contribution" ? "+" : "-"}
                          {formatCurrency(transaction.amount)}
                        </span>
                      </td>
                      <td className="px-6 py-4">
                        <span
                          className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${
                            transaction.status === "Completed"
                              ? "bg-blue-50 text-blue-700"
                              : "bg-amber-50 text-amber-700"
                          }`}
                        >
                          {transaction.status}
                        </span>
                      </td>
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
