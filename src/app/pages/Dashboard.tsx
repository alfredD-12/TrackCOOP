import { useState, useMemo, useEffect } from "react";
import { TrendingUp, Users, AlertTriangle, Wallet, ArrowRight, Filter, X, ChevronDown } from "lucide-react";
import { useNavigate } from "react-router";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell, Legend, BarChart, Bar } from "recharts";
import TooltipHint from "../components/Tooltip";

export default function Dashboard() {
  const navigate = useNavigate();
  const chartId = useMemo(() => `dashboard-${Date.now()}`, []);

  // State for filters
  const [selectedYear, setSelectedYear] = useState("2026");
  const [selectedSector, setSelectedSector] = useState<string>("all");
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [detailModalData, setDetailModalData] = useState<any>(null);
  const [showFilters, setShowFilters] = useState(false);

  // Individual chart filters
  const [lineChartPeriod, setLineChartPeriod] = useState<"6m" | "12m">("12m");
  const [pieChartFilter, setPieChartFilter] = useState<"all" | "active" | "at-risk" | "inactive">("all");
  const [barChartMetric, setBarChartMetric] = useState<"engagement" | "members" | "capital">("engagement");
  const [heatmapSector, setHeatmapSector] = useState<string>("all");

  // Check user role - only Chairman can access Dashboard
  useEffect(() => {
    const userRole = localStorage.getItem("userRole");

    if (userRole === "bookkeeper") {
      navigate("/dashboard/bookkeeper");
    } else if (userRole === "member") {
      navigate("/dashboard/profile");
    } else if (!userRole) {
      navigate("/");
    }
  }, [navigate]);

  // KPI Data
  const kpis = [
    {
      label: "Total Members",
      value: "1,247",
      change: "+12.5%",
      icon: Users,
      color: "bg-blue-500",
      route: "/dashboard/members"
    },
    {
      label: "Active Members",
      value: "1,189",
      change: "+5.2%",
      icon: TrendingUp,
      color: "bg-green-500",
      route: "/dashboard/predictions"
    },
    {
      label: "At-Risk Members",
      value: "58",
      change: "+2.1%",
      icon: AlertTriangle,
      color: "bg-amber-500",
      route: "/dashboard/predictions"
    },
    {
      label: "Total Share Capital",
      value: "₱32.5M",
      change: "+8.2%",
      icon: Wallet,
      color: "bg-primary",
      route: "/dashboard/members"
    },
  ];

  // Membership Participation Trend (12 months)
  const participationDataAll = [
    { id: "month-1", month: "May '25", members: 980 },
    { id: "month-2", month: "Jun '25", members: 1020 },
    { id: "month-3", month: "Jul '25", members: 1045 },
    { id: "month-4", month: "Aug '25", members: 1080 },
    { id: "month-5", month: "Sep '25", members: 1110 },
    { id: "month-6", month: "Oct '25", members: 1150 },
    { id: "month-7", month: "Nov '25", members: 1175 },
    { id: "month-8", month: "Dec '25", members: 1195 },
    { id: "month-9", month: "Jan '26", members: 1210 },
    { id: "month-10", month: "Feb '26", members: 1220 },
    { id: "month-11", month: "Mar '26", members: 1235 },
    { id: "month-12", month: "Apr '26", members: 1247 },
  ];

  // Filter participation data based on period selection
  const participationData = lineChartPeriod === "6m"
    ? participationDataAll.slice(-6)
    : participationDataAll;

  // Status Distribution for Donut Chart
  const statusDataAll = [
    {
      id: "status-active",
      name: "Active",
      value: 1189,
      color: "#22c55e",
      route: "/dashboard/predictions?filter=active",
      description: "Members actively participating and contributing regularly",
      members: ["Maria Santos", "Juan Dela Cruz", "Ana Lopez", "Carlos Ramos", "Roberto Aquino"]
    },
    {
      id: "status-at-risk",
      name: "At-Risk",
      value: 46,
      color: "#f59e0b",
      route: "/dashboard/predictions?filter=at-risk",
      description: "Members showing signs of disengagement - missed meetings or delayed payments",
      members: ["Rosa Garcia", "Elena Villanueva", "Pedro Santos", "Luis Martinez"]
    },
    {
      id: "status-inactive",
      name: "Inactive",
      value: 12,
      color: "#ef4444",
      route: "/dashboard/predictions?filter=inactive",
      description: "Members who have not participated or contributed in recent months",
      members: ["Pedro Reyes", "Carmen Flores"]
    },
  ];

  // Filter status data based on pie chart filter
  const statusData = pieChartFilter === "all"
    ? statusDataAll
    : statusDataAll.filter(status => status.name.toLowerCase() === pieChartFilter.replace("-", " "));

  // Sector Engagement Data
  const sectors = ["Rice Farming", "Corn", "Fishery", "Livestock", "High-Value Crops"];
  const months = ["Nov", "Dec", "Jan", "Feb", "Mar", "Apr"];

  const heatmapData = [
    [85, 88, 90, 92, 89, 91], // Rice Farming
    [72, 75, 78, 80, 82, 85], // Corn
    [68, 70, 72, 75, 78, 80], // Fishery
    [55, 58, 60, 62, 65, 68], // Livestock
    [90, 92, 88, 85, 87, 89], // High-Value Crops
  ];

  // Sector Bar Chart Data
  const sectorBarData = [
    {
      sector: "Rice Farming",
      engagement: 89,
      members: 312,
      avgCapital: 25962,
      color: "#22c55e",
      membersList: ["Maria Santos", "Carlos Ramos", "Jose Cruz"]
    },
    {
      sector: "Corn",
      engagement: 79,
      members: 267,
      avgCapital: 25843,
      color: "#f59e0b",
      membersList: ["Juan Dela Cruz", "Elena Villanueva"]
    },
    {
      sector: "Fishery",
      engagement: 75,
      members: 145,
      avgCapital: 26207,
      color: "#3b82f6",
      membersList: ["Rosa Garcia", "Roberto Aquino"]
    },
    {
      sector: "Livestock",
      engagement: 61,
      members: 203,
      avgCapital: 26108,
      color: "#ef4444",
      membersList: ["Pedro Reyes"]
    },
    {
      sector: "High-Value Crops",
      engagement: 88,
      members: 320,
      avgCapital: 26250,
      color: "#8b5cf6",
      membersList: ["Ana Lopez"]
    },
  ];

  const getHeatmapColor = (value: number) => {
    if (value >= 85) return "bg-green-600";
    if (value >= 70) return "bg-green-500";
    if (value >= 55) return "bg-amber-500";
    return "bg-red-500";
  };

  const handlePieClick = (entry: any) => {
    setDetailModalData({
      type: "status",
      title: `${entry.name} Members`,
      description: entry.description,
      value: entry.value,
      members: entry.members,
      color: entry.color
    });
    setShowDetailModal(true);
  };

  const handleBarClick = (data: any) => {
    setDetailModalData({
      type: "sector",
      title: `${data.sector} Sector`,
      description: `Engagement score: ${data.engagement}% | Members: ${data.members} | Avg Capital: ₱${data.avgCapital.toLocaleString()}`,
      value: data.members,
      members: data.membersList,
      color: data.color,
      engagement: data.engagement
    });
    setShowDetailModal(true);
  };

  const handleLineClick = (data: any) => {
    if (data && data.activePayload && data.activePayload[0]) {
      const payload = data.activePayload[0].payload;
      setDetailModalData({
        type: "trend",
        title: `Member Growth - ${payload.month}`,
        description: `Total members in ${payload.month}`,
        value: payload.members,
        color: "#1b5e3f"
      });
      setShowDetailModal(true);
    }
  };

  return (
    <div className="p-8">
      <div className="mb-8 flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-display mb-2">Dashboard</h1>
          <p className="text-muted-foreground">Overview of your cooperative's performance</p>
        </div>
        <button
          onClick={() => setShowFilters(!showFilters)}
          className="px-4 py-2 bg-card border border-border rounded-lg hover:bg-muted transition-all flex items-center gap-2"
        >
          <Filter className="w-4 h-4" />
          Filters
          <ChevronDown className={`w-4 h-4 transition-transform ${showFilters ? "rotate-180" : ""}`} />
        </button>
      </div>

      {/* Filters Panel */}
      {showFilters && (
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm mb-8 animate-in fade-in slide-in-from-top-2 duration-200">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium mb-2">Year</label>
              <select
                value={selectedYear}
                onChange={(e) => setSelectedYear(e.target.value)}
                className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
              >
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium mb-2">Sector</label>
              <select
                value={selectedSector}
                onChange={(e) => setSelectedSector(e.target.value)}
                className="w-full px-4 py-2 rounded-lg border border-input bg-input-background focus:outline-none focus:ring-2 focus:ring-ring"
              >
                <option value="all">All Sectors</option>
                <option value="rice_farming">Rice Farming</option>
                <option value="corn">Corn</option>
                <option value="fishery">Fishery</option>
                <option value="livestock">Livestock</option>
                <option value="high_value_crops">High-Value Crops</option>
              </select>
            </div>
            <div className="flex items-end">
              <button
                onClick={() => {
                  setSelectedYear("2026");
                  setSelectedSector("all");
                }}
                className="px-4 py-2 text-sm text-muted-foreground hover:text-foreground transition-colors"
              >
                Reset Filters
              </button>
            </div>
          </div>
        </div>
      )}

      {/* KPI Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {kpis.map((kpi, index) => {
          const Icon = kpi.icon;
          return (
            <div
              key={index}
              onClick={() => navigate(kpi.route)}
              className="bg-card rounded-xl p-6 border border-border shadow-sm hover:shadow-lg transition-all hover:scale-105 cursor-pointer group"
            >
              <div className="flex items-start justify-between mb-4">
                <div className={`${kpi.color} w-12 h-12 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform`}>
                  <Icon className="w-6 h-6 text-white" />
                </div>
                <div className="flex flex-col items-end gap-1">
                  <span className="text-sm text-green-600 bg-green-50 px-2 py-1 rounded">
                    {kpi.change}
                  </span>
                  <ArrowRight className="w-4 h-4 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity" />
                </div>
              </div>
              <h3 className="text-2xl font-bold mb-1">{kpi.value}</h3>
              <p className="text-sm text-muted-foreground">{kpi.label}</p>
            </div>
          );
        })}
      </div>

      {/* Charts Row 1: Line Chart + Donut Chart */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {/* Membership Participation Trend - Line Chart */}
        <div className="lg:col-span-2 bg-card rounded-xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
          <div className="flex items-center justify-between mb-6">
            <div>
              <h2 className="text-xl font-display mb-1">Membership Participation Trend</h2>
              <p className="text-sm text-muted-foreground">
                {lineChartPeriod === "12m" ? "12-month" : "6-month"} growth trajectory - Click on chart for details
              </p>
            </div>
            <div className="flex items-center gap-4">
              {/* Period Filter */}
              <div className="flex items-center gap-2 bg-muted/30 rounded-lg p-1">
                <button
                  onClick={() => setLineChartPeriod("6m")}
                  className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                    lineChartPeriod === "6m"
                      ? "bg-primary text-primary-foreground shadow-sm"
                      : "text-muted-foreground hover:text-foreground"
                  }`}
                >
                  6M
                </button>
                <button
                  onClick={() => setLineChartPeriod("12m")}
                  className={`px-3 py-1.5 rounded-md text-sm font-medium transition-all ${
                    lineChartPeriod === "12m"
                      ? "bg-primary text-primary-foreground shadow-sm"
                      : "text-muted-foreground hover:text-foreground"
                  }`}
                >
                  12M
                </button>
              </div>
              <div className="text-right">
                <div className="text-2xl font-bold text-green-600">+27.2%</div>
                <div className="text-xs text-muted-foreground">Year-over-Year Growth</div>
              </div>
            </div>
          </div>

          {/* Legend */}
          <div className="mb-4 flex items-center gap-4 p-3 bg-muted/30 rounded-lg">
            <div className="flex items-center gap-2">
              <div className="w-8 h-1 bg-primary rounded"></div>
              <span className="text-sm font-medium">Total Members</span>
            </div>
            <span className="text-xs text-muted-foreground">• Tracks monthly member count growth across all sectors</span>
          </div>

          <div className="h-80" key={`line-chart-wrapper-${chartId}`}>
            <ResponsiveContainer width="100%" height="100%">
              <LineChart
                data={participationData}
                margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
                onClick={handleLineClick}
              >
                <defs>
                  <linearGradient id="colorMembers" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#dc2626" stopOpacity={0.1}/>
                    <stop offset="95%" stopColor="#dc2626" stopOpacity={0}/>
                  </linearGradient>
                </defs>
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
                  domain={['dataMin - 50', 'dataMax + 50']}
                  label={{ value: 'Members', angle: -90, position: 'insideLeft', style: { fontSize: 12, fill: '#666' } }}
                />
                <Tooltip
                  contentStyle={{
                    backgroundColor: "#fff",
                    border: "1px solid #e5e5e4",
                    borderRadius: "8px",
                    padding: "12px"
                  }}
                  formatter={(value: number) => [`${value} members`, "Total Members"]}
                  labelFormatter={(label) => `Period: ${label}`}
                />
                <Line
                  type="monotone"
                  dataKey="members"
                  stroke="#dc2626"
                  strokeWidth={3}
                  dot={{ fill: "#dc2626", r: 5, strokeWidth: 2, stroke: "#fff" }}
                  activeDot={{ r: 8, strokeWidth: 2, stroke: "#fff", cursor: "pointer" }}
                  isAnimationActive={false}
                  fillOpacity={1}
                  fill="url(#colorMembers)"
                />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Member Status Distribution - Donut Chart */}
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
          <div className="mb-4 flex items-start justify-between">
            <div>
              <h2 className="text-xl font-display mb-1">Member Status Distribution</h2>
              <p className="text-sm text-muted-foreground">Click segments for detailed member lists</p>
            </div>
            {/* Status Filter */}
            <select
              value={pieChartFilter}
              onChange={(e) => setPieChartFilter(e.target.value as any)}
              className="px-3 py-1.5 text-sm border border-input bg-input-background rounded-lg focus:outline-none focus:ring-2 focus:ring-ring"
            >
              <option value="all">All Status</option>
              <option value="active">Active Only</option>
              <option value="at-risk">At-Risk Only</option>
              <option value="inactive">Inactive Only</option>
            </select>
          </div>

          {/* Custom Legend with Descriptions */}
          <div className="mb-4 space-y-2">
            {statusData.map((status) => (
              <div key={status.id} className="flex items-start gap-2 p-2 rounded hover:bg-muted/30 transition-colors cursor-pointer" onClick={() => handlePieClick(status)}>
                <div className="w-4 h-4 rounded-full shrink-0 mt-0.5" style={{ backgroundColor: status.color }}></div>
                <div className="flex-1">
                  <div className="flex items-center justify-between">
                    <TooltipHint content={status.description} position="top" showIcon={true}>
                      <span className="text-sm font-medium">{status.name}</span>
                    </TooltipHint>
                    <span className="text-sm font-bold">{status.value}</span>
                  </div>
                  <p className="text-xs text-muted-foreground">{status.description}</p>
                </div>
              </div>
            ))}
          </div>

          <div className="h-64 flex items-center justify-center" key={`pie-chart-wrapper-${chartId}`}>
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={statusData}
                  cx="50%"
                  cy="50%"
                  innerRadius={50}
                  outerRadius={90}
                  paddingAngle={3}
                  dataKey="value"
                  onClick={(entry) => handlePieClick(entry)}
                  className="cursor-pointer"
                  isAnimationActive={false}
                  startAngle={90}
                  endAngle={-270}
                  label={({ value, percent }) => `${(percent * 100).toFixed(1)}%`}
                  labelLine={false}
                >
                  {statusData.map((entry) => (
                    <Cell
                      key={entry.id}
                      fill={entry.color}
                      className="hover:opacity-80 transition-opacity"
                      strokeWidth={2}
                      stroke="#fff"
                    />
                  ))}
                </Pie>
                <Tooltip
                  contentStyle={{
                    backgroundColor: "#fff",
                    border: "1px solid #e5e5e4",
                    borderRadius: "8px",
                    padding: "12px"
                  }}
                  formatter={(value: number, name: string) => [`${value} members`, name]}
                />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>

      {/* Sector Engagement Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {/* Sector Engagement Bar Chart */}
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
          <div className="mb-6 flex items-start justify-between">
            <div>
              <h2 className="text-xl font-display mb-1">Sector {barChartMetric === "engagement" ? "Engagement" : barChartMetric === "members" ? "Members" : "Capital"} Overview</h2>
              <p className="text-sm text-muted-foreground">Click bars to view sector details</p>
            </div>
            {/* Metric Filter */}
            <select
              value={barChartMetric}
              onChange={(e) => setBarChartMetric(e.target.value as any)}
              className="px-3 py-1.5 text-sm border border-input bg-input-background rounded-lg focus:outline-none focus:ring-2 focus:ring-ring"
            >
              <option value="engagement">Engagement Score</option>
              <option value="members">Member Count</option>
              <option value="capital">Avg Share Capital</option>
            </select>
          </div>

          {/* Legend */}
          <div className="mb-4 p-3 bg-muted/30 rounded-lg">
            <p className="text-xs text-muted-foreground">
              <span className="font-medium">
                {barChartMetric === "engagement" && "Engagement Score: Calculated based on meeting attendance, capital contribution, and activity levels"}
                {barChartMetric === "members" && "Member Count: Total number of registered members in each sector"}
                {barChartMetric === "capital" && "Average Share Capital: Mean share capital contribution per member in each sector"}
              </span>
            </p>
          </div>

          <div className="h-80">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={sectorBarData} margin={{ top: 20, right: 30, left: 20, bottom: 60 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#e5e5e4" vertical={false} />
                <XAxis
                  dataKey="sector"
                  stroke="#666"
                  fontSize={11}
                  tickLine={false}
                  axisLine={false}
                  angle={-45}
                  textAnchor="end"
                  height={80}
                />
                <YAxis
                  stroke="#666"
                  fontSize={12}
                  tickLine={false}
                  axisLine={false}
                  label={{
                    value: barChartMetric === "engagement" ? "Engagement %" : barChartMetric === "members" ? "Members" : "Capital (₱)",
                    angle: -90,
                    position: 'insideLeft',
                    style: { fontSize: 12, fill: '#666' }
                  }}
                />
                <Tooltip
                  contentStyle={{
                    backgroundColor: "#fff",
                    border: "1px solid #e5e5e4",
                    borderRadius: "8px",
                    padding: "12px"
                  }}
                  formatter={(value: number) => {
                    if (barChartMetric === "engagement") return [`${value}%`, "Engagement"];
                    if (barChartMetric === "members") return [`${value}`, "Members"];
                    return [`₱${value.toLocaleString()}`, "Avg Capital"];
                  }}
                  labelFormatter={(label) => `Sector: ${label}`}
                />
                <Bar
                  dataKey={barChartMetric === "engagement" ? "engagement" : barChartMetric === "members" ? "members" : "avgCapital"}
                  radius={[8, 8, 0, 0]}
                  onClick={handleBarClick}
                  cursor="pointer"
                >
                  {sectorBarData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color} className="hover:opacity-80 transition-opacity" />
                  ))}
                </Bar>
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Sector Engagement Heatmap */}
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
          <div className="mb-6 flex items-start justify-between">
            <div>
              <h2 className="text-xl font-display mb-1">6-Month Engagement Trend</h2>
              <p className="text-sm text-muted-foreground">Engagement score by sector over time</p>
            </div>
            {/* Sector Filter */}
            <select
              value={heatmapSector}
              onChange={(e) => setHeatmapSector(e.target.value)}
              className="px-3 py-1.5 text-sm border border-input bg-input-background rounded-lg focus:outline-none focus:ring-2 focus:ring-ring"
            >
              <option value="all">All Sectors</option>
              <option value="Rice Farming">Rice Farming</option>
              <option value="Corn">Corn</option>
              <option value="Fishery">Fishery</option>
              <option value="Livestock">Livestock</option>
              <option value="High-Value Crops">High-Value Crops</option>
            </select>
          </div>

          <div className="overflow-x-auto">
            <div className="min-w-[500px]">
              {/* Header Row */}
              <div className="grid grid-cols-7 gap-2 mb-2">
                <div className="text-sm font-medium text-muted-foreground"></div>
                {months.map((month, index) => (
                  <div key={index} className="text-sm font-medium text-center text-muted-foreground">
                    {month}
                  </div>
                ))}
              </div>

              {/* Heatmap Rows */}
              {sectors
                .filter((sector) => heatmapSector === "all" || sector === heatmapSector)
                .map((sector, filteredIndex) => {
                  const sectorIndex = sectors.indexOf(sector);
                  return (
                    <div key={sectorIndex} className="grid grid-cols-7 gap-2 mb-2">
                      <div className="text-sm font-medium text-muted-foreground flex items-center pr-2">
                        {sector}
                      </div>
                      {heatmapData[sectorIndex].map((value, monthIndex) => (
                        <div
                          key={monthIndex}
                          className={`${getHeatmapColor(value)} rounded-lg h-12 flex items-center justify-center text-white font-bold text-sm hover:scale-110 transition-transform cursor-pointer relative group`}
                          title={`${sector} - ${months[monthIndex]}: ${value}%`}
                        >
                          {value}%
                          <div className="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">
                            {sector}: {value}% engagement
                          </div>
                        </div>
                      ))}
                    </div>
                  );
                })}
            </div>
          </div>

          {/* Legend */}
          <div className="flex flex-wrap items-center gap-4 mt-6 pt-4 border-t border-border">
            <span className="text-sm font-medium text-muted-foreground">Engagement Level:</span>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-green-600 rounded"></div>
              <span className="text-xs">Excellent (≥85%)</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-green-500 rounded"></div>
              <span className="text-xs">Good (70-84%)</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-amber-500 rounded"></div>
              <span className="text-xs">Fair (55-69%)</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-red-500 rounded"></div>
              <span className="text-xs">Low (&lt;55%)</span>
            </div>
          </div>
        </div>
      </div>

      {/* Detail Modal */}
      {showDetailModal && detailModalData && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 animate-in fade-in duration-200" onClick={() => setShowDetailModal(false)}>
          <div
            className="bg-card rounded-xl max-w-2xl w-full shadow-2xl animate-in zoom-in-95 duration-200"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Header */}
            <div
              className="p-6 rounded-t-xl flex items-center justify-between"
              style={{ backgroundColor: detailModalData.color }}
            >
              <div className="text-white">
                <h2 className="text-2xl font-display font-bold">{detailModalData.title}</h2>
                <p className="text-white/90 text-sm">{detailModalData.description}</p>
              </div>
              <button
                onClick={() => setShowDetailModal(false)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6 text-white" />
              </button>
            </div>

            {/* Content */}
            <div className="p-6">
              <div className="mb-6">
                <div className="text-4xl font-bold mb-2">{detailModalData.value}</div>
                <div className="text-sm text-muted-foreground">
                  {detailModalData.type === "status" && "Total Members"}
                  {detailModalData.type === "sector" && `Total Members in ${detailModalData.title}`}
                  {detailModalData.type === "trend" && "Total Members in this Period"}
                </div>
              </div>

              {detailModalData.members && detailModalData.members.length > 0 && (
                <div>
                  <h3 className="font-bold mb-3">Sample Members:</h3>
                  <div className="space-y-2">
                    {detailModalData.members.map((member: string, index: number) => (
                      <div key={index} className="flex items-center gap-3 p-3 bg-muted/30 rounded-lg">
                        <div className="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style={{ backgroundColor: `${detailModalData.color}20`, color: detailModalData.color }}>
                          <span className="font-bold">{member.charAt(0)}</span>
                        </div>
                        <div>
                          <div className="font-medium">{member}</div>
                          <div className="text-xs text-muted-foreground">Member ID: M{(index + 1).toString().padStart(3, '0')}</div>
                        </div>
                      </div>
                    ))}
                  </div>

                  <button
                    onClick={() => {
                      setShowDetailModal(false);
                      navigate("/dashboard/members");
                    }}
                    className="w-full mt-4 px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition-all"
                  >
                    View All Members
                  </button>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
