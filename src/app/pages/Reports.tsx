import { useState, useMemo } from "react";
import { FileText, Download, Calendar, Users, Wallet, TrendingUp, FileBarChart, X, Printer } from "lucide-react";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts";

type ReportType = "membership" | "share_capital" | "engagement" | "document_activity";

interface ReportCard {
  id: ReportType;
  title: string;
  description: string;
  icon: React.ElementType;
  color: string;
}

interface GeneratedReport {
  name: string;
  type: string;
  generatedBy: string;
  date: string;
}

export default function Reports() {
  const [selectedReport, setSelectedReport] = useState<ReportType | null>(null);
  const chartId = useMemo(() => `reports-${Date.now()}`, []);

  const reportCards: ReportCard[] = [
    {
      id: "membership",
      title: "Membership Report",
      description: "Total members, sector breakdown, and new member growth trends",
      icon: Users,
      color: "bg-blue-500"
    },
    {
      id: "share_capital",
      title: "Share Capital Report",
      description: "Total contributions, per member capital history, and sector-level totals",
      icon: Wallet,
      color: "bg-green-500"
    },
    {
      id: "engagement",
      title: "Member Engagement Report",
      description: "Active, At-Risk, and Inactive member counts with trend analysis",
      icon: TrendingUp,
      color: "bg-amber-500"
    },
    {
      id: "document_activity",
      title: "Document Activity Report",
      description: "Total documents uploaded, category breakdown, and upload activity trends",
      icon: FileBarChart,
      color: "bg-purple-500"
    }
  ];

  const recentReports: GeneratedReport[] = [
    { name: "Membership Report - Q1 2026", type: "Membership", generatedBy: "Maria Santos", date: "Apr 14, 2026" },
    { name: "Share Capital Summary - March 2026", type: "Share Capital", generatedBy: "Juan Dela Cruz", date: "Apr 10, 2026" },
    { name: "Member Engagement Analysis", type: "Engagement", generatedBy: "Maria Santos", date: "Apr 8, 2026" },
    { name: "Document Upload Activity Report", type: "Document Activity", generatedBy: "Rosa Garcia", date: "Apr 5, 2026" },
  ];

  // Sample data for different report types
  const getReportData = (type: ReportType) => {
    switch (type) {
      case "membership":
        return {
          stats: [
            { label: "Total Members", value: "1,247", change: "+12.5%" },
            { label: "New This Month", value: "42", change: "+8.2%" },
            { label: "Active Rate", value: "95.3%", change: "+2.1%" },
          ],
          chartData: [
            { month: "Oct", value: 1150 },
            { month: "Nov", value: 1175 },
            { month: "Dec", value: 1195 },
            { month: "Jan", value: 1210 },
            { month: "Feb", value: 1220 },
            { month: "Mar", value: 1235 },
            { month: "Apr", value: 1247 },
          ],
          tableData: [
            { sector: "Rice Farming", members: 312, percentage: "25.0%" },
            { sector: "Corn", members: 267, percentage: "21.4%" },
            { sector: "Fishery", members: 145, percentage: "11.6%" },
            { sector: "Livestock", members: 203, percentage: "16.3%" },
            { sector: "High-Value Crops", members: 320, percentage: "25.7%" },
          ]
        };
      case "share_capital":
        return {
          stats: [
            { label: "Total Share Capital", value: "₱32.5M", change: "+15.2%" },
            { label: "Average per Member", value: "₱26,061", change: "+3.8%" },
            { label: "Monthly Growth", value: "₱2.8M", change: "+12.5%" },
          ],
          chartData: [
            { month: "Oct", value: 25.2 },
            { month: "Nov", value: 26.8 },
            { month: "Dec", value: 28.1 },
            { month: "Jan", value: 29.5 },
            { month: "Feb", value: 30.2 },
            { month: "Mar", value: 31.7 },
            { month: "Apr", value: 32.5 },
          ],
          tableData: [
            { sector: "Rice Farming", capital: "₱8.1M", avgPerMember: "₱25,962" },
            { sector: "Corn", capital: "₱6.9M", avgPerMember: "₱25,843" },
            { sector: "Fishery", capital: "₱3.8M", avgPerMember: "₱26,207" },
            { sector: "Livestock", capital: "₱5.3M", avgPerMember: "₱26,108" },
            { sector: "High-Value Crops", capital: "₱8.4M", avgPerMember: "₱26,250" },
          ]
        };
      case "engagement":
        return {
          stats: [
            { label: "Active Members", value: "1,189", change: "+5.2%" },
            { label: "At-Risk Members", value: "46", change: "+2.1%" },
            { label: "Inactive Members", value: "12", change: "-1.3%" },
          ],
          chartData: [
            { month: "Oct", active: 1105, atRisk: 38, inactive: 7 },
            { month: "Nov", active: 1128, atRisk: 40, inactive: 7 },
            { month: "Dec", active: 1145, atRisk: 42, inactive: 8 },
            { month: "Jan", active: 1160, atRisk: 42, inactive: 8 },
            { month: "Feb", active: 1170, atRisk: 43, inactive: 7 },
            { month: "Mar", active: 1180, atRisk: 45, inactive: 10 },
            { month: "Apr", active: 1189, atRisk: 46, inactive: 12 },
          ],
          tableData: [
            { status: "Active", count: 1189, percentage: "95.3%", trend: "↑ +5.2%" },
            { status: "At-Risk", count: 46, percentage: "3.7%", trend: "↑ +2.1%" },
            { status: "Inactive", count: 12, percentage: "1.0%", trend: "↓ -1.3%" },
          ]
        };
      case "document_activity":
        return {
          stats: [
            { label: "Total Documents", value: "1,847", change: "+18.5%" },
            { label: "This Month", value: "156", change: "+12.3%" },
            { label: "Average per Day", value: "5.2", change: "+8.7%" },
          ],
          chartData: [
            { month: "Oct", value: 245 },
            { month: "Nov", value: 268 },
            { month: "Dec", value: 289 },
            { month: "Jan", value: 312 },
            { month: "Feb", value: 298 },
            { month: "Mar", value: 279 },
            { month: "Apr", value: 156 },
          ],
          tableData: [
            { category: "Membership Forms", count: 412, percentage: "22.3%" },
            { category: "Financial Records", count: 567, percentage: "30.7%" },
            { category: "Meeting Minutes", count: 289, percentage: "15.6%" },
            { category: "Project Documents", count: 334, percentage: "18.1%" },
            { category: "Compliance Documents", count: 245, percentage: "13.3%" },
          ]
        };
      default:
        return { stats: [], chartData: [], tableData: [] };
    }
  };

  const handleGenerateReport = (type: ReportType) => {
    setSelectedReport(type);
  };

  const handleDownloadPDF = () => {
    alert("Downloading PDF...");
  };

  const handlePrint = () => {
    window.print();
  };

  const reportData = selectedReport ? getReportData(selectedReport) : null;
  const selectedCard = reportCards.find(c => c.id === selectedReport);

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-display mb-2">Reports</h1>
        <p className="text-muted-foreground">Generate comprehensive reports and analytics for cooperative management</p>
      </div>

      {/* Report Type Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {reportCards.map((card) => {
          const Icon = card.icon;
          return (
            <div
              key={card.id}
              className="bg-card rounded-xl p-6 border border-border shadow-sm hover:shadow-lg transition-all group"
            >
              <div className={`${card.color} w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
                <Icon className="w-6 h-6 text-white" />
              </div>
              <h3 className="font-bold mb-2">{card.title}</h3>
              <p className="text-sm text-muted-foreground mb-4 min-h-[60px]">{card.description}</p>
              <button
                onClick={() => handleGenerateReport(card.id)}
                className="w-full px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all text-sm"
              >
                Generate Report
              </button>
            </div>
          );
        })}
      </div>

      {/* Recent Reports Table */}
      <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden mb-8">
        <div className="p-6 border-b border-border">
          <h2 className="text-xl font-display">Recent Reports</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-muted/50">
              <tr>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Report Name</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Type</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Generated By</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Date Generated</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Action</th>
              </tr>
            </thead>
            <tbody>
              {recentReports.map((report, index) => (
                <tr key={index} className="border-t border-border hover:bg-muted/30 transition-colors">
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <FileText className="w-5 h-5 text-primary" />
                      </div>
                      <span className="font-medium">{report.name}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className="px-3 py-1 bg-secondary text-secondary-foreground rounded-full text-sm">
                      {report.type}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-muted-foreground">{report.generatedBy}</td>
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-1 text-sm text-muted-foreground">
                      <Calendar className="w-4 h-4" />
                      <span>{report.date}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <button className="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all text-sm flex items-center gap-2">
                      <Download className="w-4 h-4" />
                      Download
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Report Preview Panel */}
      {selectedReport && reportData && selectedCard && (
        <div className="fixed inset-0 bg-black/50 z-50 flex justify-end" onClick={() => setSelectedReport(null)}>
          <div
            className="w-full max-w-4xl bg-background h-full overflow-y-auto shadow-2xl animate-in slide-in-from-right duration-300"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Header */}
            <div className="sticky top-0 bg-primary text-primary-foreground p-6 flex items-center justify-between z-10">
              <div>
                <h2 className="text-2xl font-display mb-1">{selectedCard.title}</h2>
                <p className="text-sm text-white/80">Generated on {new Date().toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</p>
              </div>
              <div className="flex items-center gap-2">
                <button
                  onClick={handleDownloadPDF}
                  className="px-4 py-2 bg-white text-primary rounded-lg hover:bg-white/90 transition-all text-sm flex items-center gap-2"
                >
                  <Download className="w-4 h-4" />
                  Download PDF
                </button>
                <button
                  onClick={handlePrint}
                  className="px-4 py-2 bg-white text-primary rounded-lg hover:bg-white/90 transition-all text-sm flex items-center gap-2"
                >
                  <Printer className="w-4 h-4" />
                  Print
                </button>
                <button
                  onClick={() => setSelectedReport(null)}
                  className="p-2 hover:bg-white/20 rounded-lg transition-colors"
                >
                  <X className="w-6 h-6" />
                </button>
              </div>
            </div>

            {/* Content */}
            <div className="p-6 space-y-6">
              {/* Key Stats */}
              <div>
                <h3 className="text-xl font-display mb-4">Key Highlights</h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  {reportData.stats.map((stat, index) => (
                    <div key={index} className="bg-card rounded-xl p-6 border border-border shadow-sm">
                      <div className="text-sm text-muted-foreground mb-1">{stat.label}</div>
                      <div className="text-3xl font-bold mb-1">{stat.value}</div>
                      <div className="text-sm text-green-600">{stat.change}</div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Line Chart */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h3 className="text-xl font-display mb-4">
                  {selectedReport === "engagement" ? "Member Status Trends" : "Growth Trend"}
                </h3>
                <div className="h-80" key={`report-chart-${chartId}`}>
                  <ResponsiveContainer width="100%" height="100%">
                    <LineChart data={reportData.chartData} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
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
                      />
                      <Tooltip
                        contentStyle={{
                          backgroundColor: "#fff",
                          border: "1px solid #e5e5e4",
                          borderRadius: "8px",
                        }}
                      />
                      {selectedReport === "engagement" ? (
                        <>
                          <Line type="monotone" dataKey="active" stroke="#22c55e" strokeWidth={2} dot={false} isAnimationActive={false} name="Active" />
                          <Line type="monotone" dataKey="atRisk" stroke="#f59e0b" strokeWidth={2} dot={false} isAnimationActive={false} name="At-Risk" />
                          <Line type="monotone" dataKey="inactive" stroke="#ef4444" strokeWidth={2} dot={false} isAnimationActive={false} name="Inactive" />
                        </>
                      ) : (
                        <Line
                          type="monotone"
                          dataKey="value"
                          stroke="#1b5e3f"
                          strokeWidth={3}
                          dot={false}
                          isAnimationActive={false}
                        />
                      )}
                    </LineChart>
                  </ResponsiveContainer>
                </div>
              </div>

              {/* Summary Table */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h3 className="text-xl font-display mb-4">
                  {selectedReport === "membership" ? "Sector Breakdown" :
                   selectedReport === "share_capital" ? "Sector-Level Capital" :
                   selectedReport === "engagement" ? "Status Distribution" :
                   "Category Breakdown"}
                </h3>
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead className="bg-muted/50">
                      <tr>
                        {Object.keys(reportData.tableData[0] || {}).map((key) => (
                          <th key={key} className="text-left px-4 py-3 text-sm font-medium text-muted-foreground capitalize">
                            {key.replace(/([A-Z])/g, ' $1').trim()}
                          </th>
                        ))}
                      </tr>
                    </thead>
                    <tbody>
                      {reportData.tableData.map((row: any, index) => (
                        <tr key={index} className="border-t border-border">
                          {Object.values(row).map((value: any, cellIndex) => (
                            <td key={cellIndex} className="px-4 py-3 text-sm">
                              {cellIndex === 0 ? <span className="font-medium">{value}</span> : value}
                            </td>
                          ))}
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>

              {/* Footer */}
              <div className="text-center text-sm text-muted-foreground pt-6 border-t border-border">
                <p>TrackCOOP Cooperative Management System</p>
                <p>Report generated on {new Date().toLocaleString()}</p>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
