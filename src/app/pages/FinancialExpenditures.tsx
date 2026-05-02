import { Search, Calendar, Plus } from "lucide-react";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

export default function FinancialExpenditures() {
  const expenditures = [
    { id: "exp-1", date: "Apr 14, 2026", category: "Office Supplies", description: "Paper, pens, and stationery for main office", amount: 15000, approvedBy: "Board of Directors", status: "Paid" },
    { id: "exp-2", date: "Apr 11, 2026", category: "Utilities", description: "Electricity and water bills - April 2026", amount: 45000, approvedBy: "Finance Committee", status: "Paid" },
    { id: "exp-3", date: "Apr 8, 2026", category: "Agricultural Supplies", description: "Organic fertilizer distribution to members", amount: 85000, approvedBy: "Board of Directors", status: "Paid" },
    { id: "exp-4", date: "Apr 5, 2026", category: "Equipment Maintenance", description: "Repair of irrigation system", amount: 120000, approvedBy: "Board of Directors", status: "Paid" },
    { id: "exp-5", date: "Apr 3, 2026", category: "Training & Development", description: "Rice farming training workshop - facilitator fees", amount: 35000, approvedBy: "Education Committee", status: "Paid" },
    { id: "exp-6", date: "Apr 1, 2026", category: "Administrative", description: "Staff salaries - April 2026", amount: 180000, approvedBy: "Board of Directors", status: "Pending" },
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
            <div className="text-3xl font-bold mb-1 text-red-600">{formatCurrency(totalExpenses)}</div>
            <div className="text-sm text-muted-foreground">Total Expenditures (MTD)</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-border shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="text-3xl font-bold mb-1">{formatCurrency(paidExpenses)}</div>
            <div className="text-sm text-muted-foreground">Paid</div>
          </div>
          <div className="bg-card rounded-xl p-6 border border-amber-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 hover:-translate-y-1 hover:shadow-lg transition-all">
            <div className="text-3xl font-bold mb-1 text-amber-600">{formatCurrency(pendingExpenses)}</div>
            <div className="text-sm text-muted-foreground">Pending</div>
          </div>
        </div>

        {/* Filters */}
        <div className="bg-card rounded-xl p-4 border border-border shadow-sm mb-6 animate-in fade-in slide-in-from-bottom-3 delay-200 duration-300">
          <div className="flex flex-col md:flex-row gap-4">
            <div className="flex-1 relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
              <input type="text" placeholder="Search expenditures by description or category..." className="w-full pl-10 pr-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring" />
            </div>
            <select className="px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring">
              <option>All Categories</option>
              <option>Office Supplies</option>
              <option>Utilities</option>
              <option>Agricultural Supplies</option>
              <option>Equipment Maintenance</option>
              <option>Training & Development</option>
              <option>Administrative</option>
            </select>
            <select className="px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring">
              <option>All Status</option>
              <option>Paid</option>
              <option>Pending</option>
            </select>
          </div>
        </div>

        {/* Expenditures Table */}
        <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-3 delay-300 duration-500">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-muted/50">
                <tr>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Date</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Category</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Description</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Amount</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Approved By</th>
                  <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Status</th>
                </tr>
              </thead>
              <tbody>
                {expenditures.map((expense, index) => (
                  <tr key={expense.id} className="border-t border-border hover:bg-muted/30 transition-colors animate-in fade-in slide-in-from-bottom-2" style={{ animationDelay: `${Math.min(index * 40, 220)}ms` }}>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2 text-sm text-muted-foreground">
                        <Calendar className="w-4 h-4" /><span>{expense.date}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-3 py-1 rounded-full text-xs ${categoryColors[expense.category]}`}>{expense.category}</span>
                    </td>
                    <td className="px-6 py-4 text-sm">{expense.description}</td>
                    <td className="px-6 py-4"><span className="font-bold text-red-600">-{formatCurrency(expense.amount)}</span></td>
                    <td className="px-6 py-4 text-sm text-muted-foreground">{expense.approvedBy}</td>
                    <td className="px-6 py-4">
                      <span className={`px-3 py-1 rounded-full text-sm ${expense.status === "Paid" ? "bg-green-100 text-green-700" : "bg-amber-100 text-amber-700"}`}>{expense.status}</span>
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
