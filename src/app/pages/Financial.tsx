import type { FormEvent } from "react";
import { useMemo, useState } from "react";
import {
  ArrowUpDown,
  BarChart3,
  Calendar,
  Pencil,
  Plus,
  Search,
  Trash2,
  TrendingUp,
  Wallet,
} from "lucide-react";
import {
  CartesianGrid,
  Line,
  LineChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";
import ConfirmDialog from "../components/ConfirmDialog";
import { ImageWithFallback } from "../components/figma/ImageWithFallback";
import SimulationToast, {
  type SimulationToastState,
} from "../components/SimulationToast";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

const evidenceImages = {
  capital:
    "https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&q=80&w=1200",
  fees:
    "https://images.unsplash.com/photo-1556740749-887f6717d7e4?auto=format&fit=crop&q=80&w=1200",
  produce:
    "https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&q=80&w=1200",
};

type RevenueSortKey = "date" | "type" | "description" | "amount";

interface RevenueTransaction {
  id: string;
  date: string;
  dateIso: string;
  type: string;
  source: string;
  description: string;
  amount: number;
  evidenceImage: string;
  evidenceName: string;
}

const initialTransactions: RevenueTransaction[] = [
  {
    id: "rev-1",
    date: "Apr 14, 2026",
    dateIso: "2026-04-14",
    type: "Revenue",
    source: "Share Capital",
    description: "Share Capital Payments",
    amount: 850000,
    evidenceImage: evidenceImages.capital,
    evidenceName: "share-capital-summary-apr14.jpg",
  },
  {
    id: "rev-2",
    date: "Apr 10, 2026",
    dateIso: "2026-04-10",
    type: "Revenue",
    source: "Service Fees",
    description: "Service Fees Collection",
    amount: 125000,
    evidenceImage: evidenceImages.fees,
    evidenceName: "service-fees-ledger-apr10.jpg",
  },
  {
    id: "rev-3",
    date: "Apr 8, 2026",
    dateIso: "2026-04-08",
    type: "Revenue",
    source: "Member Contributions",
    description: "Member Contributions",
    amount: 420000,
    evidenceImage: evidenceImages.capital,
    evidenceName: "member-contributions-proof-apr08.jpg",
  },
  {
    id: "rev-4",
    date: "Apr 5, 2026",
    dateIso: "2026-04-05",
    type: "Income",
    source: "Product Sales",
    description: "Agricultural Products Sales",
    amount: 680000,
    evidenceImage: evidenceImages.produce,
    evidenceName: "produce-sales-slip-apr05.jpg",
  },
  {
    id: "rev-5",
    date: "Apr 1, 2026",
    dateIso: "2026-04-01",
    type: "Income",
    source: "Training",
    description: "Training Workshop Fees",
    amount: 95000,
    evidenceImage: evidenceImages.fees,
    evidenceName: "training-fees-receipt-apr01.jpg",
  },
];

const sourceOptions = [
  "Share Capital",
  "Service Fees",
  "Member Contributions",
  "Product Sales",
  "Training",
  "Rice Harvest Proceeds",
];

function formatDisplayDate(dateIso: string) {
  const date = new Date(`${dateIso}T12:00:00`);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}

function createRevenueForm(transaction?: RevenueTransaction) {
  if (transaction) {
    return {
      type: transaction.type,
      source: transaction.source,
      dateIso: transaction.dateIso,
      amount: String(transaction.amount),
      description: transaction.description,
      evidenceImage: transaction.evidenceImage,
      evidenceName: transaction.evidenceName,
    };
  }

  return {
    type: "Income",
    source: "Rice Harvest Proceeds",
    dateIso: "2026-04-18",
    amount: "245000",
    description: "Rice harvest proceeds deposited after the San Jose market sale.",
    evidenceImage: evidenceImages.produce,
    evidenceName: "rice-harvest-deposit-slip-apr18.jpg",
  };
}

export default function Financial() {
  const chartId = useMemo(() => `financial-${Date.now()}`, []);
  const [transactions, setTransactions] = useState(initialTransactions);
  const [financialPeriod, setFinancialPeriod] = useState<"3m" | "6m" | "12m">("6m");
  const [transactionSearch, setTransactionSearch] = useState("");
  const [sourceFilter, setSourceFilter] = useState("all");
  const [amountFilter, setAmountFilter] = useState<
    "all" | "under_150000" | "150000_500000" | "above_500000"
  >("all");
  const [sortKey, setSortKey] = useState<RevenueSortKey>("date");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("desc");
  const [dialogMode, setDialogMode] = useState<"record" | "edit">("record");
  const [dialogOpen, setDialogOpen] = useState(false);
  const [selectedTransactionId, setSelectedTransactionId] = useState<string | null>(null);
  const [revenueForm, setRevenueForm] = useState(createRevenueForm());
  const [toast, setToast] = useState<SimulationToastState | null>(null);
  const [confirmDialog, setConfirmDialog] = useState<{
    isOpen: boolean;
    title: string;
    message: string;
    onConfirm: () => void;
  }>({
    isOpen: false,
    title: "",
    message: "",
    onConfirm: () => undefined,
  });

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

  const financialData =
    financialPeriod === "3m"
      ? financialDataAll.slice(-3)
      : financialPeriod === "6m"
        ? financialDataAll.slice(-6)
        : financialDataAll;

  const totalRevenue = transactions.reduce((sum, transaction) => sum + transaction.amount, 0);
  const revenueSources = Array.from(
    new Set(transactions.map((transaction) => transaction.source)),
  );

  const filteredTransactions = transactions.filter((transaction) => {
    const query = transactionSearch.trim().toLowerCase();
    const matchesSearch =
      !query ||
      transaction.description.toLowerCase().includes(query) ||
      transaction.date.toLowerCase().includes(query) ||
      transaction.source.toLowerCase().includes(query);
    const matchesSource = sourceFilter === "all" || transaction.source === sourceFilter;
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

  const runSimulation = ({
    processingTitle,
    processingDescription,
    completeTitle,
    completeDescription,
    tone = "green",
    onComplete,
  }: {
    processingTitle: string;
    processingDescription: string;
    completeTitle: string;
    completeDescription: string;
    tone?: "green" | "red" | "blue";
    onComplete: () => void;
  }) => {
    setToast({
      title: processingTitle,
      description: processingDescription,
      tone,
      status: "processing",
    });

    window.setTimeout(() => {
      onComplete();
      setToast({
        title: completeTitle,
        description: completeDescription,
        tone,
        status: "complete",
      });

      window.setTimeout(() => setToast(null), 2600);
    }, 1400);
  };

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

  const openRecordDialog = () => {
    setDialogMode("record");
    setSelectedTransactionId(null);
    setRevenueForm(createRevenueForm());
    setDialogOpen(true);
  };

  const openEditDialog = (transaction: RevenueTransaction) => {
    setDialogMode("edit");
    setSelectedTransactionId(transaction.id);
    setRevenueForm(createRevenueForm(transaction));
    setDialogOpen(true);
  };

  const handleRevenueFormChange = (name: string, value: string) => {
    setRevenueForm((current) => ({ ...current, [name]: value }));
  };

  const handleRevenueSubmit = (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const record: RevenueTransaction = {
      id:
        dialogMode === "edit" && selectedTransactionId
          ? selectedTransactionId
          : `rev-${Date.now()}`,
      dateIso: revenueForm.dateIso,
      date: formatDisplayDate(revenueForm.dateIso),
      type: revenueForm.type,
      source: revenueForm.source,
      description: revenueForm.description,
      amount: Number(revenueForm.amount),
      evidenceImage: revenueForm.evidenceImage,
      evidenceName: revenueForm.evidenceName,
    };

    setDialogOpen(false);

    runSimulation({
      processingTitle:
        dialogMode === "record" ? "Uploading revenue evidence" : "Updating revenue entry",
      processingDescription:
        dialogMode === "record"
          ? `${record.evidenceName} is being attached to the revenue ledger.`
          : `${record.evidenceName} is being re-attached while the entry is updated.`,
      completeTitle:
        dialogMode === "record" ? "Revenue recorded" : "Revenue entry updated",
      completeDescription:
        dialogMode === "record"
          ? `${record.description} is now listed in the revenue ledger.`
          : `${record.description} has been updated in the revenue ledger.`,
      tone: "green",
      onComplete: () => {
        setTransactions((current) =>
          dialogMode === "record"
            ? [record, ...current]
            : current.map((transaction) =>
                transaction.id === record.id ? record : transaction,
              ),
        );
      },
    });
  };

  const handleDeleteTransaction = (transaction: RevenueTransaction) => {
    setConfirmDialog({
      isOpen: true,
      title: "Delete revenue entry?",
      message: `This will remove ${transaction.description} from the revenue ledger.`,
      onConfirm: () => {
        runSimulation({
          processingTitle: "Removing revenue entry",
          processingDescription: `${transaction.description} is being removed from the ledger.`,
          completeTitle: "Revenue entry deleted",
          completeDescription: `${transaction.description} has been removed.`,
          tone: "red",
          onComplete: () => {
            setTransactions((current) =>
              current.filter((item) => item.id !== transaction.id),
            );
          },
        });
      },
    });
  };

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
                  Financial Records
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">Income & Revenue</h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">Income, revenue, and financial performance tracking</p>
              </div>
              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <button
                  onClick={openRecordDialog}
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                >
                  <Plus className="h-4 w-4" />
                  Record Income
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <Wallet className="h-6 w-6 text-green-600" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold text-green-600">{formatCurrency(totalRevenue)}</div>
            <div className="text-sm text-muted-foreground">Total Revenue (MTD)</div>
          </div>
          <div className="rounded-xl border border-border bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                <BarChart3 className="h-6 w-6 text-blue-700" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold">{formatCurrency(3800000)}</div>
            <div className="text-sm text-muted-foreground">Monthly Income</div>
          </div>
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <TrendingUp className="h-6 w-6 text-green-600" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold text-green-600">{formatCurrency(2555000)}</div>
            <div className="text-sm text-muted-foreground">Net Income (MTD)</div>
          </div>
        </div>

        <div className="mb-8 rounded-xl border border-border bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500">
          <div className="mb-6 flex items-start justify-between">
            <div>
              <h2 className="mb-1 text-xl font-display">Revenue & Net Income Trend</h2>
              <p className="text-sm text-muted-foreground">
                {financialPeriod === "3m" ? "3-month" : financialPeriod === "6m" ? "6-month" : "12-month"} financial performance overview
              </p>
            </div>
            <div className="flex items-center gap-2 rounded-lg bg-muted/30 p-1">
              {(["3m", "6m", "12m"] as const).map((period) => (
                <button
                  key={period}
                  onClick={() => setFinancialPeriod(period)}
                  className={`rounded-md px-3 py-1.5 text-sm font-medium transition-all ${
                    financialPeriod === period
                      ? "bg-primary text-primary-foreground shadow-sm"
                      : "text-muted-foreground hover:text-foreground"
                  }`}
                >
                  {period.toUpperCase()}
                </button>
              ))}
            </div>
          </div>
          <div className="h-80" key={`financial-chart-${chartId}`}>
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={financialData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#e5e5e4" vertical={false} />
                <XAxis dataKey="month" stroke="#666" fontSize={12} tickLine={false} axisLine={false} />
                <YAxis stroke="#666" fontSize={12} tickLine={false} axisLine={false} tickFormatter={(value) => `P${(value / 1000000).toFixed(1)}M`} />
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
                        | "above_500000",
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
                  <th className="px-6 py-4 text-right text-sm font-semibold text-gray-600">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody
                key={`${transactionSearch}-${sourceFilter}-${amountFilter}-${sortKey}-${sortDirection}`}
                className="animate-in fade-in duration-300"
              >
                {sortedTransactions.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="px-6 py-14 text-center text-gray-500">
                      No revenue transactions found.
                    </td>
                  </tr>
                ) : (
                  sortedTransactions.map((transaction, index) => (
                    <tr
                      key={transaction.id}
                      className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-green-50/50"
                      style={{ animationDelay: `${Math.min(index * 40, 200)}ms` }}
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
                            transaction.type === "Revenue"
                              ? "bg-green-50 text-green-700"
                              : "bg-blue-50 text-blue-700"
                          }`}
                        >
                          {transaction.type}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-950">
                        <div>
                          <p>{transaction.description}</p>
                          <p className="mt-1 text-xs text-gray-500">{transaction.source}</p>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <span className="font-bold text-green-600">
                          +{formatCurrency(transaction.amount)}
                        </span>
                      </td>
                      <td className="px-6 py-4">
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => openEditDialog(transaction)}
                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-gray-600 transition-all hover:border-primary hover:bg-green-50 hover:text-primary"
                            aria-label={`Edit ${transaction.description}`}
                            title="Edit entry"
                          >
                            <Pencil className="h-4 w-4" />
                          </button>
                          <button
                            onClick={() => handleDeleteTransaction(transaction)}
                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-gray-600 transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                            aria-label={`Delete ${transaction.description}`}
                            title="Delete entry"
                          >
                            <Trash2 className="h-4 w-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </main>

      {dialogOpen && (
        <div
          className="fixed inset-0 z-50 bg-black/55 p-4 sm:p-6"
          onClick={() => setDialogOpen(false)}
          role="dialog"
          aria-modal="true"
        >
          <div className="mx-auto flex h-full max-w-5xl items-center justify-center">
            <div
              className="flex max-h-full w-full flex-col overflow-hidden rounded-xl bg-white shadow-2xl"
              onClick={(event) => event.stopPropagation()}
            >
              <div className="flex items-start justify-between gap-4 border-b border-stone-200 px-6 py-5">
                <div>
                  <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    Simulated Upload
                  </p>
                  <h2 className="mt-1 text-2xl font-display text-gray-950">
                    {dialogMode === "record" ? "Record Income or Revenue" : "Edit Revenue Entry"}
                  </h2>
                </div>
                <button
                  onClick={() => setDialogOpen(false)}
                  className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
                  aria-label="Close dialog"
                >
                  <Trash2 className="h-0 w-0" />
                  <span className="text-3xl leading-none">×</span>
                </button>
              </div>

              <form
                id="financial-record-form"
                onSubmit={handleRevenueSubmit}
                className="grid min-h-0 flex-1 lg:grid-cols-[minmax(0,1fr)_320px]"
              >
                <div className="min-h-0 overflow-y-auto px-6 py-6">
                  <div className="grid gap-4 md:grid-cols-2">
                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Entry Type
                      </span>
                      <select
                        value={revenueForm.type}
                        onChange={(event) =>
                          handleRevenueFormChange("type", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      >
                        <option value="Revenue">Revenue</option>
                        <option value="Income">Income</option>
                      </select>
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Source
                      </span>
                      <select
                        value={revenueForm.source}
                        onChange={(event) =>
                          handleRevenueFormChange("source", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      >
                        {sourceOptions.map((source) => (
                          <option key={source} value={source}>
                            {source}
                          </option>
                        ))}
                      </select>
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Date
                      </span>
                      <input
                        type="date"
                        value={revenueForm.dateIso}
                        onChange={(event) =>
                          handleRevenueFormChange("dateIso", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      />
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Amount
                      </span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        value={revenueForm.amount}
                        onChange={(event) =>
                          handleRevenueFormChange("amount", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      />
                    </label>

                    <label className="md:col-span-2">
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Description
                      </span>
                      <textarea
                        rows={5}
                        value={revenueForm.description}
                        onChange={(event) =>
                          handleRevenueFormChange("description", event.target.value)
                        }
                        className="min-h-[140px] w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      />
                    </label>

                    <div className="md:col-span-2 rounded-lg border border-dashed border-stone-300 bg-stone-50 px-4 py-4">
                      <div className="flex items-center gap-3">
                        <div className="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100 text-green-700">
                          <Plus className="h-5 w-5" />
                        </div>
                        <div>
                          <p className="text-sm font-semibold text-gray-950">
                            Proof of income attached
                          </p>
                          <p className="text-xs text-gray-500">
                            Evidence file attached for this record.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <aside className="min-h-0 overflow-y-auto border-t border-stone-200 bg-stone-50 px-6 py-6 lg:border-t-0 lg:border-l">
                  <div className="text-sm font-semibold text-gray-700">Evidence Preview</div>
                  <div className="mt-4 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
                    <ImageWithFallback
                      src={revenueForm.evidenceImage}
                      alt={revenueForm.evidenceName}
                      className="h-64 w-full object-cover"
                    />
                    <div className="border-t border-stone-200 px-4 py-4">
                      <p className="text-sm font-semibold text-gray-950">
                        {revenueForm.evidenceName}
                      </p>
                      <p className="mt-1 text-xs leading-5 text-gray-500">
                        This preview is attached to the current record.
                      </p>
                    </div>
                  </div>
                </aside>
              </form>

              <div className="shrink-0 border-t border-stone-200 bg-white px-6 py-5">
                <div className="flex flex-col gap-3 sm:flex-row sm:justify-end">
                  <button
                    type="button"
                    onClick={() => setDialogOpen(false)}
                    className="inline-flex h-11 items-center justify-center rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    form="financial-record-form"
                    className="inline-flex h-11 items-center justify-center rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
                  >
                    {dialogMode === "record" ? "Upload Revenue Record" : "Save Revenue Update"}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      <ConfirmDialog
        isOpen={confirmDialog.isOpen}
        onClose={() => setConfirmDialog((current) => ({ ...current, isOpen: false }))}
        onConfirm={confirmDialog.onConfirm}
        title={confirmDialog.title}
        message={confirmDialog.message}
        confirmText="Delete"
        cancelText="Keep"
        variant="danger"
      />

      <SimulationToast toast={toast} />
    </div>
  );
}
