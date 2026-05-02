import { useState } from "react";
import {
  Search,
  Calendar,
  Plus,
  Receipt,
  CheckCircle2,
  Clock3,
  ArrowUpDown,
} from "lucide-react";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

type ExpenditureSortKey =
  | "date"
  | "category"
  | "description"
  | "amount"
  | "approvedBy"
  | "status";

export default function FinancialExpenditures() {
  const [searchQuery, setSearchQuery] = useState("");
  const [categoryFilter, setCategoryFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState<"all" | "Paid" | "Pending">(
    "all"
  );
  const [amountFilter, setAmountFilter] = useState<
    "all" | "under_50000" | "50000_120000" | "above_120000"
  >("all");
  const [sortKey, setSortKey] = useState<ExpenditureSortKey>("date");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("desc");

  const expenditures = [
    { id: "exp-1", date: "Apr 14, 2026", dateIso: "2026-04-14", category: "Office Supplies", description: "Paper, pens, and stationery for main office", amount: 15000, approvedBy: "Board of Directors", status: "Paid" },
    { id: "exp-2", date: "Apr 11, 2026", dateIso: "2026-04-11", category: "Utilities", description: "Electricity and water bills - April 2026", amount: 45000, approvedBy: "Finance Committee", status: "Paid" },
    { id: "exp-3", date: "Apr 8, 2026", dateIso: "2026-04-08", category: "Agricultural Supplies", description: "Organic fertilizer distribution to members", amount: 85000, approvedBy: "Board of Directors", status: "Paid" },
    { id: "exp-4", date: "Apr 5, 2026", dateIso: "2026-04-05", category: "Equipment Maintenance", description: "Repair of irrigation system", amount: 120000, approvedBy: "Board of Directors", status: "Paid" },
    { id: "exp-5", date: "Apr 3, 2026", dateIso: "2026-04-03", category: "Training & Development", description: "Rice farming training workshop - facilitator fees", amount: 35000, approvedBy: "Education Committee", status: "Paid" },
    { id: "exp-6", date: "Apr 1, 2026", dateIso: "2026-04-01", category: "Administrative", description: "Staff salaries - April 2026", amount: 180000, approvedBy: "Board of Directors", status: "Pending" },
  ];

  const totalExpenses = expenditures.reduce((sum, e) => sum + e.amount, 0);
  const paidExpenses = expenditures.filter(e => e.status === "Paid").reduce((sum, e) => sum + e.amount, 0);
  const pendingExpenses = expenditures.filter(e => e.status === "Pending").reduce((sum, e) => sum + e.amount, 0);

  const categoryColors: Record<string, string> = {
    "Office Supplies": "bg-blue-100 text-blue-700",
    "Utilities": "bg-yellow-100 text-yellow-700",
    "Agricultural Supplies": "bg-green-100 text-green-700",
    "Equipment Maintenance": "bg-orange-100 text-orange-700",
    "Training & Development": "bg-purple-100 text-purple-700",
    "Administrative": "bg-gray-100 text-gray-700",
  };

  const categories = Object.keys(categoryColors);

  const filteredExpenditures = expenditures.filter((expense) => {
    const query = searchQuery.trim().toLowerCase();
    const matchesSearch =
      !query ||
      expense.description.toLowerCase().includes(query) ||
      expense.category.toLowerCase().includes(query) ||
      expense.approvedBy.toLowerCase().includes(query) ||
      expense.date.toLowerCase().includes(query);
    const matchesCategory =
      categoryFilter === "all" || expense.category === categoryFilter;
    const matchesStatus =
      statusFilter === "all" || expense.status === statusFilter;
    const matchesAmount =
      amountFilter === "all" ||
      (amountFilter === "under_50000" && expense.amount < 50000) ||
      (amountFilter === "50000_120000" &&
        expense.amount >= 50000 &&
        expense.amount <= 120000) ||
      (amountFilter === "above_120000" && expense.amount > 120000);

    return matchesSearch && matchesCategory && matchesStatus && matchesAmount;
  });

  const sortedExpenditures = [...filteredExpenditures].sort((left, right) => {
    const direction = sortDirection === "asc" ? 1 : -1;

    switch (sortKey) {
      case "date":
        return left.dateIso.localeCompare(right.dateIso) * direction;
      case "category":
      case "description":
      case "approvedBy":
      case "status":
        return left[sortKey].localeCompare(right[sortKey]) * direction;
      case "amount":
        return (left.amount - right.amount) * direction;
      default:
        return 0;
    }
  });

  const clearFilters = () => {
    setSearchQuery("");
    setCategoryFilter("all");
    setStatusFilter("all");
    setAmountFilter("all");
  };

  const handleSort = (nextKey: ExpenditureSortKey) => {
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
            <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
              <div className="max-w-4xl">
                <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                  Expenditures
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">Financial Expenditures</h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">Track and manage cooperative expenses and disbursements</p>
              </div>
              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <button className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl">
                  <Plus className="h-4 w-4" />
                  Record Expense
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        {/* Summary Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="bg-card rounded-xl p-6 border border-red-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="flex items-center gap-3 mb-3">
              <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <Receipt className="w-6 h-6 text-red-600" />
              </div>
            </div>
            <div className="text-3xl font-bold mb-1 text-red-600">{formatCurrency(totalExpenses)}</div>
            <div className="text-sm text-muted-foreground">Total Expenditures (MTD)</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-border shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="flex items-center gap-3 mb-3">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <CheckCircle2 className="w-6 h-6 text-green-600" />
              </div>
            </div>
            <div className="text-3xl font-bold mb-1">{formatCurrency(paidExpenses)}</div>
            <div className="text-sm text-muted-foreground">Paid</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-amber-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="flex items-center gap-3 mb-3">
              <div className="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <Clock3 className="w-6 h-6 text-amber-600" />
              </div>
            </div>
            <div className="text-3xl font-bold mb-1 text-amber-600">{formatCurrency(pendingExpenses)}</div>
            <div className="text-sm text-muted-foreground">Pending</div>
          </div>
        </div>

        {/* Expenditures Table */}
        <div className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Expenditure Ledger
                </p>
                <h2 className="mt-1 text-xl font-display">Expense Transactions</h2>
              </div>
              <div className="text-sm font-medium text-gray-500">
                {sortedExpenditures.length} result
                {sortedExpenditures.length === 1 ? "" : "s"}
              </div>
            </div>

            <div className="mt-5 border-t border-stone-100 pt-4">
              <div className="grid gap-3 xl:grid-cols-[minmax(280px,1fr)_220px_180px_220px_auto] xl:items-center">
                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(event) => setSearchQuery(event.target.value)}
                    placeholder="Search expenditures"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <select
                  value={categoryFilter}
                  onChange={(event) => setCategoryFilter(event.target.value)}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by expenditure category"
                >
                  <option value="all">All Categories</option>
                  {categories.map((category) => (
                    <option key={category} value={category}>
                      {category}
                    </option>
                  ))}
                </select>

                <select
                  value={statusFilter}
                  onChange={(event) =>
                    setStatusFilter(event.target.value as "all" | "Paid" | "Pending")
                  }
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by status"
                >
                  <option value="all">All Status</option>
                  <option value="Paid">Paid</option>
                  <option value="Pending">Pending</option>
                </select>

                <select
                  value={amountFilter}
                  onChange={(event) =>
                    setAmountFilter(
                      event.target.value as
                        | "all"
                        | "under_50000"
                        | "50000_120000"
                        | "above_120000"
                    )
                  }
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by amount"
                >
                  <option value="all">All Amounts</option>
                  <option value="under_50000">Under 50,000</option>
                  <option value="50000_120000">50,000 to 120,000</option>
                  <option value="above_120000">Above 120,000</option>
                </select>

                <button
                  onClick={clearFilters}
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
                      ["category", "Category"],
                      ["description", "Description"],
                      ["amount", "Amount"],
                      ["approvedBy", "Approved By"],
                      ["status", "Status"],
                    ] as Array<[ExpenditureSortKey, string]>
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
                key={`${searchQuery}-${categoryFilter}-${statusFilter}-${amountFilter}-${sortKey}-${sortDirection}`}
                className="animate-in fade-in duration-300"
              >
                {sortedExpenditures.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="px-6 py-14 text-center text-gray-500">
                      No expenditures found.
                    </td>
                  </tr>
                ) : (
                sortedExpenditures.map((expense, index) => (
                  <tr key={expense.id} className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-red-50/30" style={{ animationDelay: `${Math.min(index * 40, 220)}ms` }}>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2 text-sm text-gray-500">
                        <Calendar className="w-4 h-4" /><span>{expense.date}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${categoryColors[expense.category]}`}>{expense.category}</span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-950">{expense.description}</td>
                    <td className="px-6 py-4"><span className="font-bold text-red-600">-{formatCurrency(expense.amount)}</span></td>
                    <td className="px-6 py-4 text-sm text-gray-500">{expense.approvedBy}</td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex rounded-full px-3 py-1 text-sm font-semibold ${expense.status === "Paid" ? "bg-green-50 text-green-700" : "bg-amber-50 text-amber-700"}`}>{expense.status}</span>
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
