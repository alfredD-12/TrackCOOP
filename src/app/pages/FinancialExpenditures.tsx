import type { FormEvent } from "react";
import { useState } from "react";
import {
  ArrowUpDown,
  Calendar,
  CheckCircle2,
  Clock3,
  Pencil,
  Plus,
  Receipt,
  Search,
  Trash2,
  X,
} from "lucide-react";
import ConfirmDialog from "../components/ConfirmDialog";
import { ImageWithFallback } from "../components/figma/ImageWithFallback";
import SimulationToast, {
  type SimulationToastState,
} from "../components/SimulationToast";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

const expenseEvidenceImages = {
  office:
    "https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&q=80&w=1200",
  utilities:
    "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=1200",
  field:
    "https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&q=80&w=1200",
};

type ExpenditureSortKey =
  | "date"
  | "category"
  | "description"
  | "amount"
  | "approvedBy"
  | "status";

interface ExpenditureRecord {
  id: string;
  date: string;
  dateIso: string;
  category: string;
  description: string;
  amount: number;
  approvedBy: string;
  status: "Paid" | "Pending";
  evidenceImage: string;
  evidenceName: string;
}

const initialExpenditures: ExpenditureRecord[] = [
  {
    id: "exp-1",
    date: "Apr 14, 2026",
    dateIso: "2026-04-14",
    category: "Office Supplies",
    description: "Paper, pens, and stationery for main office",
    amount: 15000,
    approvedBy: "Board of Directors",
    status: "Paid",
    evidenceImage: expenseEvidenceImages.office,
    evidenceName: "office-supplies-receipt-apr14.jpg",
  },
  {
    id: "exp-2",
    date: "Apr 11, 2026",
    dateIso: "2026-04-11",
    category: "Utilities",
    description: "Electricity and water bills - April 2026",
    amount: 45000,
    approvedBy: "Finance Committee",
    status: "Paid",
    evidenceImage: expenseEvidenceImages.utilities,
    evidenceName: "utilities-billing-proof-apr11.jpg",
  },
  {
    id: "exp-3",
    date: "Apr 8, 2026",
    dateIso: "2026-04-08",
    category: "Agricultural Supplies",
    description: "Organic fertilizer distribution to members",
    amount: 85000,
    approvedBy: "Board of Directors",
    status: "Paid",
    evidenceImage: expenseEvidenceImages.field,
    evidenceName: "fertilizer-purchase-slip-apr08.jpg",
  },
  {
    id: "exp-4",
    date: "Apr 5, 2026",
    dateIso: "2026-04-05",
    category: "Equipment Maintenance",
    description: "Repair of irrigation system",
    amount: 120000,
    approvedBy: "Board of Directors",
    status: "Paid",
    evidenceImage: expenseEvidenceImages.field,
    evidenceName: "irrigation-repair-proof-apr05.jpg",
  },
  {
    id: "exp-5",
    date: "Apr 3, 2026",
    dateIso: "2026-04-03",
    category: "Training & Development",
    description: "Rice farming training workshop - facilitator fees",
    amount: 35000,
    approvedBy: "Education Committee",
    status: "Paid",
    evidenceImage: expenseEvidenceImages.utilities,
    evidenceName: "training-facilitator-receipt-apr03.jpg",
  },
  {
    id: "exp-6",
    date: "Apr 1, 2026",
    dateIso: "2026-04-01",
    category: "Administrative",
    description: "Staff salaries - April 2026",
    amount: 180000,
    approvedBy: "Board of Directors",
    status: "Pending",
    evidenceImage: expenseEvidenceImages.office,
    evidenceName: "administrative-disbursement-apr01.jpg",
  },
];

const categoryOptions = [
  "Office Supplies",
  "Utilities",
  "Agricultural Supplies",
  "Equipment Maintenance",
  "Training & Development",
  "Administrative",
];

const approvalOptions = [
  "Board of Directors",
  "Finance Committee",
  "Education Committee",
  "Operations Team",
];

function formatDisplayDate(dateIso: string) {
  const date = new Date(`${dateIso}T12:00:00`);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}

function createExpenseForm(expense?: ExpenditureRecord) {
  if (expense) {
    return {
      category: expense.category,
      approvedBy: expense.approvedBy,
      dateIso: expense.dateIso,
      status: expense.status,
      amount: String(expense.amount),
      description: expense.description,
      evidenceImage: expense.evidenceImage,
      evidenceName: expense.evidenceName,
    };
  }

  return {
    category: "Agricultural Supplies",
    approvedBy: "Board of Directors",
    dateIso: "2026-04-18",
    status: "Pending",
    amount: "62500",
    description: "Advance purchase for vegetable seed packs and soil enhancers for the next planting cycle.",
    evidenceImage: expenseEvidenceImages.field,
    evidenceName: "seed-purchase-receipt-apr18.jpg",
  };
}

export default function FinancialExpenditures() {
  const [expenditures, setExpenditures] = useState(initialExpenditures);
  const [searchQuery, setSearchQuery] = useState("");
  const [categoryFilter, setCategoryFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState<"all" | "Paid" | "Pending">(
    "all",
  );
  const [amountFilter, setAmountFilter] = useState<
    "all" | "under_50000" | "50000_120000" | "above_120000"
  >("all");
  const [sortKey, setSortKey] = useState<ExpenditureSortKey>("date");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("desc");
  const [dialogMode, setDialogMode] = useState<"record" | "edit">("record");
  const [dialogOpen, setDialogOpen] = useState(false);
  const [selectedExpenseId, setSelectedExpenseId] = useState<string | null>(null);
  const [expenseForm, setExpenseForm] = useState(createExpenseForm());
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

  const totalExpenses = expenditures.reduce((sum, expense) => sum + expense.amount, 0);
  const paidExpenses = expenditures
    .filter((expense) => expense.status === "Paid")
    .reduce((sum, expense) => sum + expense.amount, 0);
  const pendingExpenses = expenditures
    .filter((expense) => expense.status === "Pending")
    .reduce((sum, expense) => sum + expense.amount, 0);

  const categoryColors: Record<string, string> = {
    "Office Supplies": "bg-blue-100 text-blue-700",
    Utilities: "bg-yellow-100 text-yellow-700",
    "Agricultural Supplies": "bg-green-100 text-green-700",
    "Equipment Maintenance": "bg-orange-100 text-orange-700",
    "Training & Development": "bg-purple-100 text-purple-700",
    Administrative: "bg-gray-100 text-gray-700",
  };

  const categories = Array.from(new Set(expenditures.map((expense) => expense.category)));

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
    const matchesStatus = statusFilter === "all" || expense.status === statusFilter;
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

  const openRecordDialog = () => {
    setDialogMode("record");
    setSelectedExpenseId(null);
    setExpenseForm(createExpenseForm());
    setDialogOpen(true);
  };

  const openEditDialog = (expense: ExpenditureRecord) => {
    setDialogMode("edit");
    setSelectedExpenseId(expense.id);
    setExpenseForm(createExpenseForm(expense));
    setDialogOpen(true);
  };

  const handleExpenseFormChange = (name: string, value: string) => {
    setExpenseForm((current) => ({ ...current, [name]: value }));
  };

  const handleExpenseSubmit = (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const record: ExpenditureRecord = {
      id:
        dialogMode === "edit" && selectedExpenseId
          ? selectedExpenseId
          : `exp-${Date.now()}`,
      dateIso: expenseForm.dateIso,
      date: formatDisplayDate(expenseForm.dateIso),
      category: expenseForm.category,
      description: expenseForm.description,
      amount: Number(expenseForm.amount),
      approvedBy: expenseForm.approvedBy,
      status: expenseForm.status as "Paid" | "Pending",
      evidenceImage: expenseForm.evidenceImage,
      evidenceName: expenseForm.evidenceName,
    };

    setDialogOpen(false);

    runSimulation({
      processingTitle:
        dialogMode === "record" ? "Uploading expense receipt" : "Updating expense record",
      processingDescription:
        dialogMode === "record"
          ? `${record.evidenceName} is being attached to the expenditure ledger.`
          : `${record.evidenceName} is being re-attached while the expense is updated.`,
      completeTitle:
        dialogMode === "record" ? "Expense recorded" : "Expense record updated",
      completeDescription:
        dialogMode === "record"
          ? `${record.description} is now listed in the expenditure ledger.`
          : `${record.description} has been updated in the expenditure ledger.`,
      tone: "green",
      onComplete: () => {
        setExpenditures((current) =>
          dialogMode === "record"
            ? [record, ...current]
            : current.map((expense) => (expense.id === record.id ? record : expense)),
        );
      },
    });
  };

  const handleDeleteExpense = (expense: ExpenditureRecord) => {
    setConfirmDialog({
      isOpen: true,
      title: "Delete expense record?",
      message: `This will remove ${expense.description} from the expenditure ledger.`,
      onConfirm: () => {
        runSimulation({
          processingTitle: "Removing expense record",
          processingDescription: `${expense.description} is being removed from the ledger.`,
          completeTitle: "Expense record deleted",
          completeDescription: `${expense.description} has been removed.`,
          tone: "red",
          onComplete: () => {
            setExpenditures((current) =>
              current.filter((item) => item.id !== expense.id),
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
                  Expenditures
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">Financial Expenditures</h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">Track and manage cooperative expenses and disbursements</p>
              </div>
              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <button
                  onClick={openRecordDialog}
                  data-tour="expenditures-record"
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                >
                  <Plus className="h-4 w-4" />
                  Record Expense
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div className="rounded-xl border border-red-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100">
                <Receipt className="h-6 w-6 text-red-600" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold text-red-600">{formatCurrency(totalExpenses)}</div>
            <div className="text-sm text-muted-foreground">Total Expenditures (MTD)</div>
          </div>
          <div className="rounded-xl border border-border bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <CheckCircle2 className="h-6 w-6 text-green-600" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold">{formatCurrency(paidExpenses)}</div>
            <div className="text-sm text-muted-foreground">Paid</div>
          </div>
          <div className="rounded-xl border border-amber-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100">
                <Clock3 className="h-6 w-6 text-amber-600" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold text-amber-600">{formatCurrency(pendingExpenses)}</div>
            <div className="text-sm text-muted-foreground">Pending</div>
          </div>
        </div>

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

            <div
              className="mt-5 border-t border-stone-100 pt-4"
              data-tour="expenditures-filters"
            >
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
                        | "above_120000",
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

          <div
            className="overflow-x-auto"
            data-tour="expenditures-table"
          >
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
                  <th className="px-6 py-4 text-right text-sm font-semibold text-gray-600">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody
                key={`${searchQuery}-${categoryFilter}-${statusFilter}-${amountFilter}-${sortKey}-${sortDirection}`}
                className="animate-in fade-in duration-300"
              >
                {sortedExpenditures.length === 0 ? (
                  <tr>
                    <td colSpan={7} className="px-6 py-14 text-center text-gray-500">
                      No expenditures found.
                    </td>
                  </tr>
                ) : (
                  sortedExpenditures.map((expense, index) => (
                    <tr
                      key={expense.id}
                      className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-red-50/30"
                      style={{ animationDelay: `${Math.min(index * 40, 220)}ms` }}
                    >
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-2 text-sm text-gray-500">
                          <Calendar className="h-4 w-4" />
                          <span>{expense.date}</span>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <span className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${categoryColors[expense.category]}`}>
                          {expense.category}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-950">{expense.description}</td>
                      <td className="px-6 py-4">
                        <span className="font-bold text-red-600">
                          -{formatCurrency(expense.amount)}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">{expense.approvedBy}</td>
                      <td className="px-6 py-4">
                        <span className={`inline-flex rounded-full px-3 py-1 text-sm font-semibold ${expense.status === "Paid" ? "bg-green-50 text-green-700" : "bg-amber-50 text-amber-700"}`}>
                          {expense.status}
                        </span>
                      </td>
                      <td className="px-6 py-4">
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => openEditDialog(expense)}
                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-gray-600 transition-all hover:border-primary hover:bg-green-50 hover:text-primary"
                            aria-label={`Edit ${expense.description}`}
                            title="Edit entry"
                          >
                            <Pencil className="h-4 w-4" />
                          </button>
                          <button
                            onClick={() => handleDeleteExpense(expense)}
                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-gray-600 transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                            aria-label={`Delete ${expense.description}`}
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
                    {dialogMode === "record" ? "Record Expense" : "Edit Expense Record"}
                  </h2>
                </div>
                <button
                  onClick={() => setDialogOpen(false)}
                  className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
                  aria-label="Close dialog"
                >
                  <X className="h-5 w-5" />
                </button>
              </div>

              <form
                id="expense-record-form"
                onSubmit={handleExpenseSubmit}
                className="grid min-h-0 flex-1 lg:grid-cols-[minmax(0,1fr)_320px]"
              >
                <div className="min-h-0 overflow-y-auto px-6 py-6">
                  <div className="grid gap-4 md:grid-cols-2">
                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Category
                      </span>
                      <select
                        value={expenseForm.category}
                        onChange={(event) =>
                          handleExpenseFormChange("category", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      >
                        {categoryOptions.map((category) => (
                          <option key={category} value={category}>
                            {category}
                          </option>
                        ))}
                      </select>
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Approved By
                      </span>
                      <select
                        value={expenseForm.approvedBy}
                        onChange={(event) =>
                          handleExpenseFormChange("approvedBy", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      >
                        {approvalOptions.map((approver) => (
                          <option key={approver} value={approver}>
                            {approver}
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
                        value={expenseForm.dateIso}
                        onChange={(event) =>
                          handleExpenseFormChange("dateIso", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      />
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Status
                      </span>
                      <select
                        value={expenseForm.status}
                        onChange={(event) =>
                          handleExpenseFormChange("status", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      >
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                      </select>
                    </label>

                    <label className="md:col-span-2">
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Amount
                      </span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        value={expenseForm.amount}
                        onChange={(event) =>
                          handleExpenseFormChange("amount", event.target.value)
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
                        value={expenseForm.description}
                        onChange={(event) =>
                          handleExpenseFormChange("description", event.target.value)
                        }
                        className="min-h-[140px] w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      />
                    </label>

                    <div className="md:col-span-2 rounded-lg border border-dashed border-stone-300 bg-stone-50 px-4 py-4">
                      <div className="flex items-center gap-3">
                        <div className="flex h-11 w-11 items-center justify-center rounded-lg bg-red-100 text-red-700">
                          <Receipt className="h-5 w-5" />
                        </div>
                        <div>
                          <p className="text-sm font-semibold text-gray-950">
                            Receipt evidence attached
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
                      src={expenseForm.evidenceImage}
                      alt={expenseForm.evidenceName}
                      className="h-64 w-full object-cover"
                    />
                    <div className="border-t border-stone-200 px-4 py-4">
                      <p className="text-sm font-semibold text-gray-950">
                        {expenseForm.evidenceName}
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
                    form="expense-record-form"
                    className="inline-flex h-11 items-center justify-center rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
                  >
                    {dialogMode === "record" ? "Upload Expense Record" : "Save Expense Update"}
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
