import { useState, useMemo } from "react";
import { TrendingUp, TrendingDown, AlertTriangle, X, Sparkles, ArrowUpDown } from "lucide-react";
import { useNavigate } from "react-router";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, Cell } from "recharts";

type PredictedStatus = "Active" | "At-Risk" | "Inactive";
type Sector = "rice_farming" | "corn" | "fishery" | "livestock" | "high_value_crops";
type PredictionStage = "gathering" | "analyzing" | "scoring" | "complete";
type SortKey = "name" | "sector" | "engagementScore" | "predictedStatus" | "lastActive";

interface MemberPrediction {
  id: string;
  name: string;
  sector: Sector;
  engagementScore: number;
  predictedStatus: PredictedStatus;
  lastActive: string;
  riskFactors?: {
    factor: string;
    impact: number;
  }[];
}

const sectorLabels: Record<Sector, string> = {
  rice_farming: "Rice Farming",
  corn: "Corn",
  fishery: "Fishery",
  livestock: "Livestock",
  high_value_crops: "High-Value Crops",
};

const statusColors: Record<PredictedStatus, string> = {
  Active: "bg-green-100 text-green-700",
  "At-Risk": "bg-amber-100 text-amber-700",
  Inactive: "bg-red-100 text-red-700",
};

const heroImage =
  "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=2400";

const predictionStages: Array<{
  id: PredictionStage;
  label: string;
  description: string;
  progress: number;
}> = [
  { id: "gathering", label: "Gathering Data", description: "Collecting recent member activities and financial records...", progress: 25 },
  { id: "analyzing", label: "Analyzing Patterns", description: "Running AI models on engagement patterns...", progress: 60 },
  { id: "scoring", label: "Calculating Scores", description: "Updating risk factors and engagement scores...", progress: 90 },
  { id: "complete", label: "Predictions Updated", description: "All member predictions have been successfully generated.", progress: 100 },
];

const sortLabels: Record<SortKey, string> = {
  name: "Member Name",
  sector: "Sector",
  engagementScore: "Engagement Score",
  predictedStatus: "Predicted Status",
  lastActive: "Last Active",
};

export default function Predictions() {
  const navigate = useNavigate();
  const [predictions] = useState<MemberPrediction[]>([
    {
      id: "M001",
      name: "Maria Santos",
      sector: "rice_farming",
      engagementScore: 92,
      predictedStatus: "Active",
      lastActive: "Apr 12, 2026",
      riskFactors: [
        { factor: "Meeting Attendance", impact: 95 },
        { factor: "Share Capital Growth", impact: 88 },
        { factor: "Document Submissions", impact: 90 },
      ],
    },
    {
      id: "M002",
      name: "Juan Dela Cruz",
      sector: "corn",
      engagementScore: 85,
      predictedStatus: "Active",
      lastActive: "Apr 13, 2026",
      riskFactors: [
        { factor: "Meeting Attendance", impact: 82 },
        { factor: "Share Capital Growth", impact: 91 },
        { factor: "Document Submissions", impact: 83 },
      ],
    },
    {
      id: "M003",
      name: "Rosa Garcia",
      sector: "fishery",
      engagementScore: 58,
      predictedStatus: "At-Risk",
      lastActive: "Mar 28, 2026",
      riskFactors: [
        { factor: "Missed Meetings", impact: 75 },
        { factor: "Low Share Capital", impact: 68 },
        { factor: "Delayed Payments", impact: 52 },
        { factor: "Infrequent Login", impact: 45 },
      ],
    },
    {
      id: "M004",
      name: "Pedro Reyes",
      sector: "livestock",
      engagementScore: 32,
      predictedStatus: "Inactive",
      lastActive: "Feb 15, 2026",
      riskFactors: [
        { factor: "Missed Meetings", impact: 95 },
        { factor: "Low Share Capital", impact: 88 },
        { factor: "No Recent Activity", impact: 92 },
        { factor: "Overdue Payments", impact: 85 },
      ],
    },
    {
      id: "M005",
      name: "Ana Lopez",
      sector: "high_value_crops",
      engagementScore: 88,
      predictedStatus: "Active",
      lastActive: "Apr 14, 2026",
      riskFactors: [
        { factor: "Meeting Attendance", impact: 90 },
        { factor: "Share Capital Growth", impact: 86 },
        { factor: "Document Submissions", impact: 88 },
      ],
    },
    {
      id: "M007",
      name: "Elena Villanueva",
      sector: "corn",
      engagementScore: 62,
      predictedStatus: "At-Risk",
      lastActive: "Apr 1, 2026",
      riskFactors: [
        { factor: "Missed Meetings", impact: 68 },
        { factor: "Low Share Capital", impact: 55 },
        { factor: "Declining Engagement", impact: 62 },
      ],
    },
    {
      id: "M008",
      name: "Roberto Aquino",
      sector: "fishery",
      engagementScore: 78,
      predictedStatus: "Active",
      lastActive: "Apr 13, 2026",
      riskFactors: [
        { factor: "Meeting Attendance", impact: 76 },
        { factor: "Share Capital Growth", impact: 80 },
        { factor: "Document Submissions", impact: 78 },
      ],
    },
  ]);

  const [selectedMember, setSelectedMember] = useState<MemberPrediction | null>(null);
  const [isGenerating, setIsGenerating] = useState(false);
  const [toastState, setToastState] = useState<"idle" | "processing" | "complete">("idle");
  const [currentStageIndex, setCurrentStageIndex] = useState(0);

  const [searchQuery, setSearchQuery] = useState("");
  const [selectedSectorFilter, setSelectedSectorFilter] = useState<Sector | "all">("all");
  const [selectedStatusFilter, setSelectedStatusFilter] = useState<PredictedStatus | "all">("all");
  
  const [sortKey, setSortKey] = useState<SortKey>("name");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("asc");

  const stats = {
    active: predictions.filter(p => p.predictedStatus === "Active").length,
    atRisk: predictions.filter(p => p.predictedStatus === "At-Risk").length,
    inactive: predictions.filter(p => p.predictedStatus === "Inactive").length,
  };

  const handleGeneratePredictions = () => {
    if (isGenerating) return;
    setIsGenerating(true);
    setToastState("processing");
    setCurrentStageIndex(0);

    const runStage = (index: number) => {
      if (index >= predictionStages.length) {
        setToastState("complete");
        setIsGenerating(false);
        setTimeout(() => setToastState("idle"), 4000);
        return;
      }
      setCurrentStageIndex(index);
      setTimeout(() => runStage(index + 1), 1500); // 1.5s per stage
    };

    runStage(0);
  };

  const filteredPredictions = useMemo(() => {
    return predictions.filter((p) => {
      const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase());
      const matchesSector = selectedSectorFilter === "all" || p.sector === selectedSectorFilter;
      const matchesStatus = selectedStatusFilter === "all" || p.predictedStatus === selectedStatusFilter;
      return matchesSearch && matchesSector && matchesStatus;
    });
  }, [predictions, searchQuery, selectedSectorFilter, selectedStatusFilter]);

  const sortedPredictions = useMemo(() => {
    const sorted = [...filteredPredictions].sort((left, right) => {
      const direction = sortDirection === "asc" ? 1 : -1;

      switch (sortKey) {
        case "name":
          return left.name.localeCompare(right.name) * direction;
        case "sector":
          return sectorLabels[left.sector].localeCompare(sectorLabels[right.sector]) * direction;
        case "predictedStatus":
          return left.predictedStatus.localeCompare(right.predictedStatus) * direction;
        case "engagementScore":
          return (left.engagementScore - right.engagementScore) * direction;
        case "lastActive":
          return left.lastActive.localeCompare(right.lastActive) * direction;
        default:
          return 0;
      }
    });

    return sorted;
  }, [filteredPredictions, sortDirection, sortKey]);

  const handleSort = (nextKey: SortKey) => {
    if (sortKey === nextKey) {
      setSortDirection((current) => (current === "asc" ? "desc" : "asc"));
    } else {
      setSortKey(nextKey);
      setSortDirection("asc");
    }
  };

  const handleSendAlert = (member: MemberPrediction) => {
    navigate("/dashboard/announcements");
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
            <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
              <div className="max-w-4xl">
                <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                  Predictions
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Member Predictions
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  AI-powered member engagement forecasts and risk analysis
                </p>
              </div>

              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <div className="flex items-center gap-2 px-4 py-2 bg-white/15 backdrop-blur border border-white/30 text-white rounded-lg shadow-sm">
                  <Sparkles className="w-4 h-4" />
                  <span className="text-sm font-medium">Model Accuracy: 87.3%</span>
                </div>
                <button
                  onClick={handleGeneratePredictions}
                  disabled={isGenerating}
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0"
                >
                  {isGenerating ? (
                    <>
                      <div className="w-4 h-4 border-2 border-green-900 border-t-transparent rounded-full animate-spin" />
                      Generating...
                    </>
                  ) : (
                    <>
                      <Sparkles className="w-4 h-4" />
                      Generate Predictions
                    </>
                  )}
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">

      {/* Summary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-card rounded-xl p-6 border border-green-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 hover:-translate-y-1 hover:shadow-lg">
          <div className="flex items-center justify-between mb-4">
            <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <TrendingUp className="w-6 h-6 text-green-600" />
            </div>
            <div className="flex items-center gap-1 text-green-600 text-sm">
              <TrendingUp className="w-4 h-4" />
              <span>+5.2%</span>
            </div>
          </div>
          <div className="text-3xl font-bold mb-1">{stats.active}</div>
          <div className="text-sm text-muted-foreground">Total Active Members</div>
        </div>

        <div className="bg-card rounded-xl p-6 border border-amber-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 hover:-translate-y-1 hover:shadow-lg">
          <div className="flex items-center justify-between mb-4">
            <div className="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
              <AlertTriangle className="w-6 h-6 text-amber-600" />
            </div>
            <div className="flex items-center gap-1 text-amber-600 text-sm">
              <TrendingUp className="w-4 h-4" />
              <span>+2.1%</span>
            </div>
          </div>
          <div className="text-3xl font-bold mb-1 text-amber-600">{stats.atRisk}</div>
          <div className="text-sm text-muted-foreground">At-Risk Members</div>
        </div>

        <div className="bg-card rounded-xl p-6 border border-red-200 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 hover:-translate-y-1 hover:shadow-lg">
          <div className="flex items-center justify-between mb-4">
            <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
              <TrendingDown className="w-6 h-6 text-red-600" />
            </div>
            <div className="flex items-center gap-1 text-red-600 text-sm">
              <TrendingDown className="w-4 h-4" />
              <span>-1.3%</span>
            </div>
          </div>
          <div className="text-3xl font-bold mb-1 text-red-600">{stats.inactive}</div>
          <div className="text-sm text-muted-foreground">Inactive Members</div>
        </div>
      </div>

      {/* Predictions Table */}
      <div className="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
          <input
            type="text"
            placeholder="Search member..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="h-10 rounded-lg border border-stone-200 px-3 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          />
          <select
            value={selectedSectorFilter}
            onChange={(e) => setSelectedSectorFilter(e.target.value as Sector | "all")}
            className="h-10 rounded-lg border border-stone-200 px-3 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          >
            <option value="all">All Sectors</option>
            <option value="rice_farming">Rice Farming</option>
            <option value="corn">Corn</option>
            <option value="fishery">Fishery</option>
            <option value="livestock">Livestock</option>
            <option value="high_value_crops">High-Value Crops</option>
          </select>
          <select
            value={selectedStatusFilter}
            onChange={(e) => setSelectedStatusFilter(e.target.value as PredictedStatus | "all")}
            className="h-10 rounded-lg border border-stone-200 px-3 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          >
            <option value="all">All Status</option>
            <option value="Active">Active</option>
            <option value="At-Risk">At-Risk</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
        <button
          onClick={() => {
            setSearchQuery("");
            setSelectedSectorFilter("all");
            setSelectedStatusFilter("all");
          }}
          className="h-10 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-600 hover:bg-stone-50 hover:text-gray-900"
        >
          Clear Filters
        </button>
      </div>

      <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-muted/50">
              <tr>
                {([
                  "name",
                  "sector",
                  "engagementScore",
                  "predictedStatus",
                  "lastActive",
                ] as SortKey[]).map((key) => (
                  <th
                    key={key}
                    className="text-left px-6 py-4 text-sm font-medium text-muted-foreground"
                  >
                    <button
                      onClick={() => handleSort(key)}
                      className="inline-flex items-center gap-2 transition-colors hover:text-gray-950"
                    >
                      {sortLabels[key]}
                      <ArrowUpDown className="h-4 w-4" />
                      {sortKey === key && (
                        <span className="text-xs uppercase tracking-[0.12em] text-primary">
                          {sortDirection}
                        </span>
                      )}
                    </button>
                  </th>
                ))}
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Action</th>
              </tr>
            </thead>
            <tbody>
              {sortedPredictions.length === 0 ? (
                <tr>
                  <td colSpan={6} className="px-6 py-14 text-center text-gray-500">
                    No predictions found matching your filters.
                  </td>
                </tr>
              ) : (
                sortedPredictions.map((prediction, index) => (
                <tr
                  key={prediction.id}
                  className="border-t border-border hover:bg-muted/30 transition-colors cursor-pointer animate-in fade-in slide-in-from-bottom-2"
                  style={{ animationDelay: `${Math.min(index * 35, 220)}ms` }}
                  onClick={() => setSelectedMember(prediction)}
                >
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                        <span className="font-bold text-primary">{prediction.name.charAt(0)}</span>
                      </div>
                      <div>
                        <div className="font-medium">{prediction.name}</div>
                        <div className="text-xs text-muted-foreground">{prediction.id}</div>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className="text-sm">{sectorLabels[prediction.sector]}</span>
                  </td>
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-3">
                      <div className="flex-1 max-w-xs">
                        <div className="flex items-center justify-between mb-1">
                          <span className="text-xs text-muted-foreground">Score</span>
                          <span className="text-sm font-medium">{prediction.engagementScore}/100</span>
                        </div>
                        <div className="w-full bg-muted rounded-full h-2 overflow-hidden">
                          <div
                            className={`h-full rounded-full transition-all ${
                              prediction.engagementScore >= 70
                                ? "bg-green-500"
                                : prediction.engagementScore >= 50
                                ? "bg-amber-500"
                                : "bg-red-500"
                            }`}
                            style={{ width: `${prediction.engagementScore}%` }}
                          />
                        </div>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`px-3 py-1 rounded-full text-sm ${statusColors[prediction.predictedStatus]}`}>
                      {prediction.predictedStatus}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-muted-foreground text-sm">
                    {prediction.lastActive}
                  </td>
                  <td className="px-6 py-4">
                    {prediction.predictedStatus === "At-Risk" && (
                      <button
                        onClick={(e) => {
                          e.stopPropagation();
                          handleSendAlert(prediction);
                        }}
                        className="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all text-sm"
                      >
                        Send Alert
                      </button>
                    )}
                  </td>
                </tr>
              ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Member Detail Right Panel */}
      {selectedMember && (
        <div className="fixed inset-0 bg-black/50 z-50 flex justify-end" onClick={() => setSelectedMember(null)}>
          <div
            className="w-full max-w-2xl bg-background h-full overflow-y-auto shadow-2xl animate-in slide-in-from-right duration-300"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Header */}
            <div className={`sticky top-0 p-6 flex items-center justify-between ${
              selectedMember.predictedStatus === "Active"
                ? "bg-green-600 text-white"
                : selectedMember.predictedStatus === "At-Risk"
                ? "bg-amber-600 text-white"
                : "bg-red-600 text-white"
            }`}>
              <div>
                <h2 className="text-2xl font-display">{selectedMember.name}</h2>
                <p className="text-white/80">Risk Factor Analysis</p>
              </div>
              <button
                onClick={() => setSelectedMember(null)}
                className="p-2 hover:bg-white/20 rounded-lg transition-colors"
              >
                <X className="w-6 h-6" />
              </button>
            </div>

            {/* Content */}
            <div className="p-6 space-y-6">
              {/* Overview */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h3 className="font-bold mb-4">Member Overview</h3>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <div className="text-xs text-muted-foreground mb-1">Member ID</div>
                    <div className="font-medium">{selectedMember.id}</div>
                  </div>
                  <div>
                    <div className="text-xs text-muted-foreground mb-1">Sector</div>
                    <div className="font-medium">{sectorLabels[selectedMember.sector]}</div>
                  </div>
                  <div>
                    <div className="text-xs text-muted-foreground mb-1">Engagement Score</div>
                    <div className="text-2xl font-bold">
                      {selectedMember.engagementScore}
                      <span className="text-sm text-muted-foreground">/100</span>
                    </div>
                  </div>
                  <div>
                    <div className="text-xs text-muted-foreground mb-1">Predicted Status</div>
                    <span className={`inline-block px-3 py-1 rounded-full text-sm ${statusColors[selectedMember.predictedStatus]}`}>
                      {selectedMember.predictedStatus}
                    </span>
                  </div>
                </div>
              </div>

              {/* Risk Factors Chart */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h3 className="font-bold mb-4">Top Risk Factors</h3>
                <p className="text-sm text-muted-foreground mb-6">
                  Factors contributing to member status prediction (higher values indicate higher risk)
                </p>

                <div className="space-y-4">
                  {selectedMember.riskFactors?.map((factor, index) => (
                    <div key={index}>
                      <div className="flex items-center justify-between mb-2">
                        <span className="text-sm font-medium">{factor.factor}</span>
                        <span className="text-sm font-bold">{factor.impact}%</span>
                      </div>
                      <div className="w-full bg-muted rounded-full h-3 overflow-hidden">
                        <div
                          className={`h-full rounded-full transition-all ${
                            factor.impact >= 80
                              ? "bg-red-500"
                              : factor.impact >= 60
                              ? "bg-amber-500"
                              : "bg-green-500"
                          }`}
                          style={{ width: `${factor.impact}%` }}
                        />
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Recommendations */}
              <div className="bg-card rounded-xl p-6 border border-border shadow-sm">
                <h3 className="font-bold mb-4">Recommended Actions</h3>
                <div className="space-y-3">
                  {selectedMember.predictedStatus === "At-Risk" && (
                    <>
                      <div className="flex items-start gap-3 p-3 bg-amber-50 rounded-lg">
                        <AlertTriangle className="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                        <div>
                          <div className="font-medium text-sm mb-1">Send Engagement Reminder</div>
                          <div className="text-xs text-muted-foreground">
                            Contact member about recent inactivity and upcoming opportunities
                          </div>
                        </div>
                      </div>
                      <div className="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                        <TrendingUp className="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                        <div>
                          <div className="font-medium text-sm mb-1">Schedule Personal Meeting</div>
                          <div className="text-xs text-muted-foreground">
                            One-on-one discussion to understand concerns and barriers
                          </div>
                        </div>
                      </div>
                    </>
                  )}
                  {selectedMember.predictedStatus === "Inactive" && (
                    <>
                      <div className="flex items-start gap-3 p-3 bg-red-50 rounded-lg">
                        <AlertTriangle className="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                        <div>
                          <div className="font-medium text-sm mb-1">Urgent Intervention Required</div>
                          <div className="text-xs text-muted-foreground">
                            Immediate contact needed to prevent membership lapse
                          </div>
                        </div>
                      </div>
                      <div className="flex items-start gap-3 p-3 bg-purple-50 rounded-lg">
                        <TrendingUp className="w-5 h-5 text-purple-600 shrink-0 mt-0.5" />
                        <div>
                          <div className="font-medium text-sm mb-1">Re-engagement Program</div>
                          <div className="text-xs text-muted-foreground">
                            Enroll in special program designed for inactive members
                          </div>
                        </div>
                      </div>
                    </>
                  )}
                  {selectedMember.predictedStatus === "Active" && (
                    <div className="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
                      <TrendingUp className="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
                      <div>
                        <div className="font-medium text-sm mb-1">Maintain Engagement</div>
                        <div className="text-xs text-muted-foreground">
                          Continue regular communication and involvement opportunities
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>

              {/* Action Buttons */}
              <div className="flex gap-3">
                {selectedMember.predictedStatus === "At-Risk" && (
                  <button
                    onClick={() => handleSendAlert(selectedMember)}
                    className="flex-1 px-6 py-3 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all"
                  >
                    Send Alert
                  </button>
                )}
                <button className="flex-1 px-6 py-3 border border-border rounded-lg hover:bg-muted transition-all">
                  View Full Profile
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
      </main>
    </div>
  );
}
