import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router";
import {
  Activity,
  AlertTriangle,
  ArrowRight,
  BarChart3,
  ChevronDown,
  Filter,
  PieChart as PieChartIcon,
  TrendingUp,
  Users,
  Wallet,
  X,
} from "lucide-react";
import {
  Bar,
  BarChart,
  CartesianGrid,
  Cell,
  Line,
  LineChart,
  Pie,
  PieChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";
import TooltipHint from "../components/Tooltip";

type StatusFilter = "all" | "active" | "at-risk" | "inactive";
type BarMetric = "engagement" | "members" | "capital";

const heroImage =
  "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=2400";

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

const statusDataAll = [
  {
    id: "status-active",
    name: "Active",
    value: 1189,
    color: "#16a34a",
    description: "Members actively participating and contributing regularly",
    members: ["Maria Santos", "Juan Dela Cruz", "Ana Lopez", "Carlos Ramos", "Roberto Aquino"],
  },
  {
    id: "status-at-risk",
    name: "At-Risk",
    value: 46,
    color: "#d97706",
    description: "Members showing signs of disengagement, missed meetings, or delayed payments",
    members: ["Rosa Garcia", "Elena Villanueva", "Pedro Santos", "Luis Martinez"],
  },
  {
    id: "status-inactive",
    name: "Inactive",
    value: 12,
    color: "#dc2626",
    description: "Members who have not participated or contributed in recent months",
    members: ["Pedro Reyes", "Carmen Flores"],
  },
];

const sectors = ["Rice Farming", "Corn", "Fishery", "Livestock", "High-Value Crops"];
const months = ["Nov", "Dec", "Jan", "Feb", "Mar", "Apr"];

const heatmapData = [
  [85, 88, 90, 92, 89, 91],
  [72, 75, 78, 80, 82, 85],
  [68, 70, 72, 75, 78, 80],
  [55, 58, 60, 62, 65, 68],
  [90, 92, 88, 85, 87, 89],
];

const sectorBarData = [
  {
    sector: "Rice Farming",
    engagement: 89,
    members: 312,
    avgCapital: 25962,
    color: "#16a34a",
    membersList: ["Maria Santos", "Carlos Ramos", "Jose Cruz"],
  },
  {
    sector: "Corn",
    engagement: 79,
    members: 267,
    avgCapital: 25843,
    color: "#d97706",
    membersList: ["Juan Dela Cruz", "Elena Villanueva"],
  },
  {
    sector: "Fishery",
    engagement: 75,
    members: 145,
    avgCapital: 26207,
    color: "#2563eb",
    membersList: ["Rosa Garcia", "Roberto Aquino"],
  },
  {
    sector: "Livestock",
    engagement: 61,
    members: 203,
    avgCapital: 26108,
    color: "#dc2626",
    membersList: ["Pedro Reyes"],
  },
  {
    sector: "High-Value Crops",
    engagement: 88,
    members: 320,
    avgCapital: 26250,
    color: "#7c3aed",
    membersList: ["Ana Lopez"],
  },
];

export default function Dashboard() {
  const navigate = useNavigate();
  const chartId = useMemo(() => `dashboard-${Date.now()}`, []);

  const [selectedYear, setSelectedYear] = useState("2026");
  const [selectedSector, setSelectedSector] = useState("all");
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [detailModalData, setDetailModalData] = useState<any>(null);
  const [showFilters, setShowFilters] = useState(false);
  const [lineChartPeriod, setLineChartPeriod] = useState<"6m" | "12m">("12m");
  const [pieChartFilter, setPieChartFilter] = useState<StatusFilter>("all");
  const [barChartMetric, setBarChartMetric] = useState<BarMetric>("engagement");
  const [heatmapSector, setHeatmapSector] = useState("all");

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

  const kpis = [
    {
      label: "Total Members",
      value: "1,247",
      change: "+12.5%",
      icon: Users,
      color: "bg-blue-600",
      tint: "bg-blue-50 border-blue-100",
      route: "/dashboard/members",
    },
    {
      label: "Active Members",
      value: "1,189",
      change: "+5.2%",
      icon: TrendingUp,
      color: "bg-primary",
      tint: "bg-green-50 border-green-100",
      route: "/dashboard/predictions",
    },
    {
      label: "At-Risk Members",
      value: "58",
      change: "+2.1%",
      icon: AlertTriangle,
      color: "bg-amber-600",
      tint: "bg-amber-50 border-amber-100",
      route: "/dashboard/predictions",
    },
    {
      label: "Total Share Capital",
      value: "₱32.5M",
      change: "+8.2%",
      icon: Wallet,
      color: "bg-teal-700",
      tint: "bg-teal-50 border-teal-100",
      route: "/dashboard/members",
    },
  ];

  const participationData =
    lineChartPeriod === "6m" ? participationDataAll.slice(-6) : participationDataAll;

  const statusData =
    pieChartFilter === "all"
      ? statusDataAll
      : statusDataAll.filter(
          (status) => status.name.toLowerCase() === pieChartFilter.replace("-", " ")
        );

  const selectedSectorLabel =
    selectedSector === "all"
      ? "All Sectors"
      : sectors.find(
          (sector) => sector.toLowerCase().replaceAll(" ", "_").replace("-", "_") === selectedSector
        ) ?? "All Sectors";

  const getHeatmapColor = (value: number) => {
    if (value >= 85) return "bg-primary text-white";
    if (value >= 70) return "bg-green-500 text-white";
    if (value >= 55) return "bg-amber-500 text-white";
    return "bg-red-500 text-white";
  };

  const handlePieClick = (entry: any) => {
    setDetailModalData({
      type: "status",
      title: `${entry.name} Members`,
      description: entry.description,
      value: entry.value,
      members: entry.members,
      color: entry.color,
    });
    setShowDetailModal(true);
  };

  const handleBarClick = (data: any) => {
    setDetailModalData({
      type: "sector",
      title: `${data.sector} Sector`,
      description: `${data.engagement}% engagement | ${data.members} members | ₱${data.avgCapital.toLocaleString()} avg capital`,
      value: data.members,
      members: data.membersList,
      color: data.color,
      engagement: data.engagement,
    });
    setShowDetailModal(true);
  };

  const handleLineClick = (data: any) => {
    if (data?.activePayload?.[0]) {
      const payload = data.activePayload[0].payload;
      setDetailModalData({
        type: "trend",
        title: `Member Growth - ${payload.month}`,
        description: `Total members in ${payload.month}`,
        value: payload.members,
        color: "#1b5e3f",
      });
      setShowDetailModal(true);
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
        <div className="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-stone-50 to-transparent" />

        <div className="relative mx-auto flex min-h-[280px] max-w-[1600px] flex-col justify-start px-6 py-8 md:min-h-[320px] md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
              <div className="max-w-4xl">
                <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                  Dashboard
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Cooperative Performance Dashboard
                </h1>
                <p className="mt-3 max-w-2xl text-lg leading-8 text-white/85">
                  {selectedYear} snapshot for {selectedSectorLabel}, focused on participation, capital, and member health.
                </p>
              </div>

              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <button
                  onClick={() => navigate("/dashboard/reports")}
                  data-tour="dashboard-view-reports"
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                >
                  View Reports
                  <ArrowRight className="h-4 w-4" />
                </button>
                <button
                  onClick={() => setShowFilters(!showFilters)}
                  data-tour="dashboard-filters"
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg border border-white/35 bg-white/10 px-5 py-3 font-semibold text-white shadow-lg backdrop-blur transition-all hover:-translate-y-1 hover:bg-white/20"
                >
                  <Filter className="h-4 w-4" />
                  Filters
                  <ChevronDown
                    className={`h-4 w-4 transition-transform ${showFilters ? "rotate-180" : ""}`}
                  />
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        {showFilters && (
          <section className="mb-8 animate-in fade-in slide-in-from-top-3 duration-300 rounded-lg border border-green-100 bg-white p-5 shadow-sm">
            <div className="mb-4 flex items-start justify-between gap-4">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Dashboard Filters
                </p>
              </div>
              <button
                onClick={() => setShowFilters(false)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800"
                aria-label="Close filters"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
              <div>
                <label htmlFor="dashboard-year" className="mb-2 block text-sm font-semibold text-gray-900">
                  Year
                </label>
                <select
                  id="dashboard-year"
                  value={selectedYear}
                  onChange={(event) => setSelectedYear(event.target.value)}
                  className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                >
                  <option value="2024">2024</option>
                  <option value="2025">2025</option>
                  <option value="2026">2026</option>
                </select>
              </div>

              <div>
                <label htmlFor="dashboard-sector" className="mb-2 block text-sm font-semibold text-gray-900">
                  Sector
                </label>
                <select
                  id="dashboard-sector"
                  value={selectedSector}
                  onChange={(event) => setSelectedSector(event.target.value)}
                  className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                >
                  <option value="all">All Sectors</option>
                  <option value="rice_farming">Rice Farming</option>
                  <option value="corn">Corn</option>
                  <option value="fishery">Fishery</option>
                  <option value="livestock">Livestock</option>
                  <option value="high_value_crops">High-Value Crops</option>
                </select>
              </div>

              <button
                onClick={() => {
                  setSelectedYear("2026");
                  setSelectedSector("all");
                }}
                className="rounded-lg border border-stone-200 bg-white px-5 py-3 font-semibold text-gray-700 transition-all hover:-translate-y-0.5 hover:border-primary/30 hover:bg-green-50 hover:text-primary"
              >
                Reset
              </button>
            </div>
          </section>
        )}

        <section
          className="mb-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4"
          data-tour="dashboard-kpis"
        >
          {kpis.map((kpi, index) => {
            const Icon = kpi.icon;

            return (
              <article
                key={kpi.label}
                onClick={() => navigate(kpi.route)}
                className={`group animate-in fade-in slide-in-from-bottom-3 cursor-pointer rounded-lg border p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl ${kpi.tint}`}
                style={{ animationDelay: `${index * 90}ms` }}
              >
                <div className="mb-5 flex items-start justify-between gap-4">
                  <span className={`flex h-12 w-12 items-center justify-center rounded-lg text-white shadow-sm transition-transform group-hover:scale-110 ${kpi.color}`}>
                    <Icon className="h-6 w-6" />
                  </span>
                  <span className="rounded-full bg-white px-3 py-1 text-sm font-bold text-primary shadow-sm">
                    {kpi.change}
                  </span>
                </div>
                <p className="text-sm font-semibold text-gray-600">{kpi.label}</p>
                <div className="mt-2 flex items-end justify-between gap-3">
                  <h2 className="font-display text-4xl font-bold text-gray-950">
                    {kpi.value}
                  </h2>
                  <ArrowRight className="mb-2 h-5 w-5 text-primary opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100" />
                </div>
              </article>
            );
          })}
        </section>

        <section className="mb-8 grid gap-6 xl:grid-cols-[1.45fr_0.95fr]">
          <article className="rounded-lg border border-stone-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-lg">
            <div className="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
              <div>
                <div className="mb-3 flex items-center gap-3">
                  <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-primary">
                    <TrendingUp className="h-5 w-5" />
                  </span>
                  <div>
                    <h2 className="font-display text-2xl font-bold text-gray-950">
                      Membership Participation Trend
                    </h2>
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <div className="flex rounded-lg bg-stone-100 p-1">
                  {(["6m", "12m"] as const).map((period) => (
                    <button
                      key={period}
                      onClick={() => setLineChartPeriod(period)}
                      className={`rounded-md px-3 py-1.5 text-sm font-semibold transition-all ${
                        lineChartPeriod === period
                          ? "bg-primary text-white shadow-sm"
                          : "text-gray-600 hover:text-gray-950"
                      }`}
                    >
                      {period.toUpperCase()}
                    </button>
                  ))}
                </div>
                <div className="text-right">
                  <p className="text-2xl font-bold text-primary">+27.2%</p>
                  <p className="text-xs text-gray-500">Year-over-year</p>
                </div>
              </div>
            </div>

            <div className="mb-4 flex flex-wrap items-center gap-3 rounded-lg border border-green-100 bg-green-50 px-4 py-3">
              <span className="h-1 w-10 rounded-full bg-primary" />
              <span className="text-sm font-semibold text-gray-800">Total Members</span>
            </div>

            <div
              className="h-80 animate-in fade-in zoom-in-95 duration-300"
              key={`line-chart-wrapper-${chartId}-${lineChartPeriod}`}
            >
              <ResponsiveContainer width="100%" height="100%">
                <LineChart
                  data={participationData}
                  margin={{ top: 8, right: 24, left: 6, bottom: 8 }}
                  onClick={handleLineClick}
                >
                  <CartesianGrid strokeDasharray="3 3" stroke="#e7e5e4" vertical={false} />
                  <XAxis dataKey="month" stroke="#78716c" fontSize={12} tickLine={false} axisLine={false} />
                  <YAxis
                    stroke="#78716c"
                    fontSize={12}
                    tickLine={false}
                    axisLine={false}
                    domain={["dataMin - 50", "dataMax + 50"]}
                  />
                  <Tooltip
                    contentStyle={{
                      backgroundColor: "#fff",
                      border: "1px solid #e7e5e4",
                      borderRadius: "8px",
                      padding: "12px",
                    }}
                    formatter={(value: number) => [`${value} members`, "Total Members"]}
                    labelFormatter={(label) => `Period: ${label}`}
                  />
                  <Line
                    type="monotone"
                    dataKey="members"
                    stroke="#1b5e3f"
                    strokeWidth={4}
                    dot={{ fill: "#1b5e3f", r: 5, strokeWidth: 2, stroke: "#fff" }}
                    activeDot={{ r: 8, strokeWidth: 2, stroke: "#fff", cursor: "pointer" }}
                    isAnimationActive={false}
                  />
                </LineChart>
              </ResponsiveContainer>
            </div>
          </article>

          <article className="rounded-lg border border-stone-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-lg">
            <div className="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
              <div className="flex items-start gap-3">
                <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                  <PieChartIcon className="h-5 w-5" />
                </span>
                <div>
                  <h2 className="font-display text-2xl font-bold text-gray-950">
                    Member Status
                  </h2>
                </div>
              </div>

              <select
                value={pieChartFilter}
                onChange={(event) => setPieChartFilter(event.target.value as StatusFilter)}
                className="rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
              >
                <option value="all">All Status</option>
                <option value="active">Active Only</option>
                <option value="at-risk">At-Risk Only</option>
                <option value="inactive">Inactive Only</option>
              </select>
            </div>

            <div className="space-y-2">
              {statusData.map((status) => (
                <button
                  key={status.id}
                  onClick={() => handlePieClick(status)}
                  className="flex w-full items-start gap-3 rounded-lg border border-transparent p-3 text-left transition-all hover:border-stone-200 hover:bg-stone-50"
                >
                  <span
                    className="mt-1 h-4 w-4 shrink-0 rounded-full"
                    style={{ backgroundColor: status.color }}
                  />
                  <span className="min-w-0 flex-1">
                    <span className="flex items-center justify-between gap-3">
                      <TooltipHint content={status.description} position="top" showIcon>
                        <span className="font-semibold text-gray-950">{status.name}</span>
                      </TooltipHint>
                      <span className="font-bold text-gray-950">{status.value}</span>
                    </span>
                  </span>
                </button>
              ))}
            </div>

            <div
              className="mt-4 h-64 animate-in fade-in zoom-in-95 duration-300"
              key={`pie-chart-wrapper-${chartId}-${pieChartFilter}`}
            >
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={statusData}
                    cx="50%"
                    cy="50%"
                    innerRadius={52}
                    outerRadius={92}
                    paddingAngle={3}
                    dataKey="value"
                    onClick={(entry) => handlePieClick(entry)}
                    className="cursor-pointer"
                    isAnimationActive={false}
                    startAngle={90}
                    endAngle={-270}
                    label={({ percent }) => `${(percent * 100).toFixed(1)}%`}
                    labelLine={false}
                  >
                    {statusData.map((entry) => (
                      <Cell key={entry.id} fill={entry.color} stroke="#fff" strokeWidth={2} />
                    ))}
                  </Pie>
                  <Tooltip
                    contentStyle={{
                      backgroundColor: "#fff",
                      border: "1px solid #e7e5e4",
                      borderRadius: "8px",
                      padding: "12px",
                    }}
                    formatter={(value: number, name: string) => [`${value} members`, name]}
                  />
                </PieChart>
              </ResponsiveContainer>
            </div>
          </article>
        </section>

        <section className="grid gap-6 xl:grid-cols-2">
          <article className="rounded-lg border border-stone-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-lg">
            <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
              <div className="flex items-start gap-3">
                <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                  <BarChart3 className="h-5 w-5" />
                </span>
                <div>
                  <h2 className="font-display text-2xl font-bold text-gray-950">
                    Sector {barChartMetric === "engagement" ? "Engagement" : barChartMetric === "members" ? "Members" : "Capital"}
                  </h2>
                </div>
              </div>

              <select
                value={barChartMetric}
                onChange={(event) => setBarChartMetric(event.target.value as BarMetric)}
                className="rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
              >
                <option value="engagement">Engagement Score</option>
                <option value="members">Member Count</option>
                <option value="capital">Avg Share Capital</option>
              </select>
            </div>

            <div
              className="h-80 animate-in fade-in zoom-in-95 duration-300"
              key={`bar-chart-${barChartMetric}`}
            >
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={sectorBarData} margin={{ top: 20, right: 18, left: 6, bottom: 64 }}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e7e5e4" vertical={false} />
                  <XAxis
                    dataKey="sector"
                    stroke="#78716c"
                    fontSize={11}
                    tickLine={false}
                    axisLine={false}
                    angle={-35}
                    textAnchor="end"
                    height={82}
                  />
                  <YAxis stroke="#78716c" fontSize={12} tickLine={false} axisLine={false} />
                  <Tooltip
                    contentStyle={{
                      backgroundColor: "#fff",
                      border: "1px solid #e7e5e4",
                      borderRadius: "8px",
                      padding: "12px",
                    }}
                    formatter={(value: number) => {
                      if (barChartMetric === "engagement") return [`${value}%`, "Engagement"];
                      if (barChartMetric === "members") return [`${value}`, "Members"];
                      return [`₱${value.toLocaleString()}`, "Avg Capital"];
                    }}
                    labelFormatter={(label) => `Sector: ${label}`}
                  />
                  <Bar
                    dataKey={
                      barChartMetric === "engagement"
                        ? "engagement"
                        : barChartMetric === "members"
                        ? "members"
                        : "avgCapital"
                    }
                    radius={[8, 8, 0, 0]}
                    onClick={handleBarClick}
                    cursor="pointer"
                    isAnimationActive={false}
                  >
                    {sectorBarData.map((entry) => (
                      <Cell key={entry.sector} fill={entry.color} />
                    ))}
                  </Bar>
                </BarChart>
              </ResponsiveContainer>
            </div>
          </article>

          <article className="rounded-lg border border-stone-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-lg">
            <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
              <div className="flex items-start gap-3">
                <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-primary">
                  <Activity className="h-5 w-5" />
                </span>
                <div>
                  <h2 className="font-display text-2xl font-bold text-gray-950">
                    6-Month Engagement
                  </h2>
                </div>
              </div>

              <select
                value={heatmapSector}
                onChange={(event) => setHeatmapSector(event.target.value)}
                className="rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
              >
                <option value="all">All Sectors</option>
                {sectors.map((sector) => (
                  <option key={sector} value={sector}>
                    {sector}
                  </option>
                ))}
              </select>
            </div>

            <div
              className="overflow-x-auto animate-in fade-in zoom-in-95 duration-300"
              key={`heatmap-${heatmapSector}`}
            >
              <div className="min-w-[520px]">
                <div className="mb-2 grid grid-cols-7 gap-2">
                  <div />
                  {months.map((month) => (
                    <div key={month} className="text-center text-sm font-semibold text-gray-500">
                      {month}
                    </div>
                  ))}
                </div>

                {sectors
                  .filter((sector) => heatmapSector === "all" || sector === heatmapSector)
                  .map((sector) => {
                    const sectorIndex = sectors.indexOf(sector);

                    return (
                      <div key={sector} className="mb-2 grid grid-cols-7 gap-2">
                        <div className="flex items-center pr-2 text-sm font-semibold text-gray-600">
                          {sector}
                        </div>
                        {heatmapData[sectorIndex].map((value, monthIndex) => (
                          <div
                            key={`${sector}-${months[monthIndex]}`}
                            className={`group relative flex h-12 items-center justify-center rounded-lg text-sm font-bold shadow-sm transition-transform hover:scale-105 ${getHeatmapColor(value)}`}
                          >
                            {value}%
                            <div className="pointer-events-none absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 rounded-lg bg-gray-950 px-3 py-2 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
                              {sector}: {value}% engagement
                            </div>
                          </div>
                        ))}
                      </div>
                    );
                  })}
              </div>
            </div>

            <div className="mt-6 flex flex-wrap items-center gap-4 border-t border-stone-200 pt-4">
              <span className="text-sm font-semibold text-gray-600">Engagement Level:</span>
              <span className="inline-flex items-center gap-2 text-xs text-gray-600">
                <span className="h-3 w-3 rounded bg-primary" />
                Excellent
              </span>
              <span className="inline-flex items-center gap-2 text-xs text-gray-600">
                <span className="h-3 w-3 rounded bg-green-500" />
                Good
              </span>
              <span className="inline-flex items-center gap-2 text-xs text-gray-600">
                <span className="h-3 w-3 rounded bg-amber-500" />
                Fair
              </span>
              <span className="inline-flex items-center gap-2 text-xs text-gray-600">
                <span className="h-3 w-3 rounded bg-red-500" />
                Low
              </span>
            </div>
          </article>
        </section>
      </main>

      {showDetailModal && detailModalData && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm animate-in fade-in duration-200"
          onClick={() => setShowDetailModal(false)}
          role="dialog"
          aria-modal="true"
        >
          <div
            className="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-2xl animate-in zoom-in-95 duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div
              className="flex items-start justify-between gap-4 p-6 text-white"
              style={{ backgroundColor: detailModalData.color }}
            >
              <div>
                <h2 className="font-display text-2xl font-bold">{detailModalData.title}</h2>
                <p className="mt-1 text-sm leading-6 text-white/90">{detailModalData.description}</p>
              </div>
              <button
                onClick={() => setShowDetailModal(false)}
                className="rounded-lg p-2 transition-colors hover:bg-white/20"
                aria-label="Close details"
              >
                <X className="h-6 w-6" />
              </button>
            </div>

            <div className="p-6">
              <div className="mb-6 rounded-lg border border-stone-200 bg-stone-50 p-5">
                <p className="font-display text-4xl font-bold text-gray-950">
                  {detailModalData.value}
                </p>
                <p className="mt-1 text-sm text-gray-600">
                  {detailModalData.type === "status" && "Total Members"}
                  {detailModalData.type === "sector" && `Total Members in ${detailModalData.title}`}
                  {detailModalData.type === "trend" && "Total Members in this Period"}
                </p>
              </div>

              {detailModalData.members?.length > 0 && (
                <div>
                  <h3 className="mb-3 font-display text-xl font-bold text-gray-950">
                    Sample Members
                  </h3>
                  <div className="space-y-2">
                    {detailModalData.members.map((member: string, index: number) => (
                      <div
                        key={member}
                        className="flex items-center gap-3 rounded-lg border border-stone-200 bg-white p-3"
                      >
                        <span
                          className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg font-bold"
                          style={{
                            backgroundColor: `${detailModalData.color}20`,
                            color: detailModalData.color,
                          }}
                        >
                          {member.charAt(0)}
                        </span>
                        <span>
                          <span className="block font-semibold text-gray-950">{member}</span>
                          <span className="text-xs text-gray-500">
                            Member ID: M{(index + 1).toString().padStart(3, "0")}
                          </span>
                        </span>
                      </div>
                    ))}
                  </div>

                  <button
                    onClick={() => {
                      setShowDetailModal(false);
                      navigate("/dashboard/members");
                    }}
                    className="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-6 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800 hover:shadow-md"
                  >
                    View All Members
                    <ArrowRight className="h-4 w-4" />
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
