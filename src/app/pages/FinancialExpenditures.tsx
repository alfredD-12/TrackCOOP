import { Search, Calendar, Plus } from "lucide-react";
import { formatCurrency } from "../../utils/formatters";

export default function FinancialExpenditures() {
  const expenditures = [
    {
      id: "exp-1",
      date: "Apr 14, 2026",
      category: "Office Supplies",
      description: "Paper, pens, and stationery for main office",
      amount: 15000,
      approvedBy: "Board of Directors",
      status: "Paid",
    },
    {
      id: "exp-2",
      date: "Apr 11, 2026",
      category: "Utilities",
      description: "Electricity and water bills - April 2026",
      amount: 45000,
      approvedBy: "Finance Committee",
      status: "Paid",
    },
    {
      id: "exp-3",
      date: "Apr 8, 2026",
      category: "Agricultural Supplies",
      description: "Organic fertilizer distribution to members",
      amount: 85000,
      approvedBy: "Board of Directors",
      status: "Paid",
    },
    {
      id: "exp-4",
      date: "Apr 5, 2026",
      category: "Equipment Maintenance",
      description: "Repair of irrigation system",
      amount: 120000,
      approvedBy: "Board of Directors",
      status: "Paid",
    },
    {
      id: "exp-5",
      date: "Apr 3, 2026",
      category: "Training & Development",
      description: "Rice farming training workshop - facilitator fees",
      amount: 35000,
      approvedBy: "Education Committee",
      status: "Paid",
    },
    {
      id: "exp-6",
      date: "Apr 1, 2026",
      category: "Administrative",
      description: "Staff salaries - April 2026",
      amount: 180000,
      approvedBy: "Board of Directors",
      status: "Pending",
    },
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
    <div className="p-8">
      <div className="mb-8 flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-display mb-2">Financial Expenditures</h1>
          <p className="text-muted-foreground">Track and manage cooperative expenses and disbursements</p>
        </div>
        <button className="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all flex items-center gap-2 shadow-sm">
          <Plus className="w-5 h-5" />
          Record Expense
        </button>
      </div>

      {/* Summary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-card rounded-xl p-6 border border-red-200 shadow-sm">
          <div className="text-3xl font-bold mb-1 text-red-600">{formatCurrency(totalExpenses)}</div>
          <div className="text-sm text-muted-foreground">Total Expenditures (MTD)</div>
        </div>

        <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
          <div className="text-3xl font-bold mb-1">{formatCurrency(paidExpenses)}</div>
          <div className="text-sm text-muted-foreground">Paid</div>
        </div>

        <div className="bg-card rounded-xl p-6 border border-amber-200 shadow-sm">
          <div className="text-3xl font-bold mb-1 text-amber-600">{formatCurrency(pendingExpenses)}</div>
          <div className="text-sm text-muted-foreground">Pending</div>
        </div>
      </div>

      {/* Search and Filters */}
      <div className="bg-card rounded-xl p-4 border border-border shadow-sm mb-6">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
            <input
              type="text"
              placeholder="Search expenditures by description or category..."
              className="w-full pl-10 pr-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
            />
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
      <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
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
              {expenditures.map((expense) => (
                <tr key={expense.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                      <Calendar className="w-4 h-4" />
                      <span>{expense.date}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`px-3 py-1 rounded-full text-xs ${categoryColors[expense.category]}`}>
                      {expense.category}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-sm">{expense.description}</td>
                  <td className="px-6 py-4">
                    <span className="font-bold text-red-600">-{formatCurrency(expense.amount)}</span>
                  </td>
                  <td className="px-6 py-4 text-sm text-muted-foreground">{expense.approvedBy}</td>
                  <td className="px-6 py-4">
                    <span className={`px-3 py-1 rounded-full text-sm ${
                      expense.status === "Paid"
                        ? "bg-green-100 text-green-700"
                        : "bg-amber-100 text-amber-700"
                    }`}>
                      {expense.status}
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
