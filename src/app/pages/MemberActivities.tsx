import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router";
import {
  ArrowLeft,
  Calendar,
  CheckCircle2,
  Clock3,
  MapPin,
  Search,
  Tag,
  Users,
  X,
} from "lucide-react";

type ActivityCategory =
  | "Training"
  | "Meeting"
  | "Financial Program"
  | "Agricultural Program"
  | "Community Event";
type ActivitySector =
  | "All Members"
  | "Rice Farming"
  | "Corn"
  | "Fishery"
  | "Livestock"
  | "High-Value Crops";
type ActivityStatus = "Upcoming" | "Ongoing" | "Completed";

interface CooperativeActivity {
  id: string;
  title: string;
  category: ActivityCategory;
  sector: ActivitySector;
  date: string;
  time: string;
  location: string;
  status: ActivityStatus;
  description: string;
}

const heroImage =
  "https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&q=80&w=2400";

const activities: CooperativeActivity[] = [
  {
    id: "act-1",
    title: "Rice Farming Best Practices Workshop",
    category: "Training",
    sector: "Rice Farming",
    date: "May 5, 2026",
    time: "9:00 AM",
    location: "Cooperative Training Hall",
    status: "Upcoming",
    description: "Training on pest management, irrigation scheduling, and crop planning.",
  },
  {
    id: "act-2",
    title: "Financial Literacy Training",
    category: "Training",
    sector: "All Members",
    date: "May 10, 2026",
    time: "1:00 PM",
    location: "Community Hall",
    status: "Upcoming",
    description: "Orientation on savings, share capital, and cooperative financial participation.",
  },
  {
    id: "act-3",
    title: "Share Capital Orientation",
    category: "Financial Program",
    sector: "All Members",
    date: "May 15, 2026",
    time: "10:00 AM",
    location: "NFFAC Office",
    status: "Upcoming",
    description: "Discussion about member contributions, dividend eligibility, and share capital records.",
  },
  {
    id: "act-4",
    title: "Fishery Equipment Support Program",
    category: "Agricultural Program",
    sector: "Fishery",
    date: "May 20, 2026",
    time: "8:00 AM",
    location: "Fishery Sector Office",
    status: "Upcoming",
    description: "Program briefing for fishery members about equipment support and application requirements.",
  },
  {
    id: "act-5",
    title: "Annual General Meeting",
    category: "Meeting",
    sector: "All Members",
    date: "April 25, 2026",
    time: "2:00 PM",
    location: "Community Hall",
    status: "Completed",
    description: "Cooperative-wide meeting for reports, updates, and annual planning.",
  },
];

const categoryOptions = [
  "All",
  "Training",
  "Meeting",
  "Financial Program",
  "Agricultural Program",
  "Community Event",
] as const;
const sectorOptions = ["All", "Rice Farming", "Corn", "Fishery", "Livestock", "High-Value Crops"] as const;
const statusOptions = ["Upcoming", "Ongoing", "Completed"] as const;

const statusColors: Record<ActivityStatus, string> = {
  Upcoming: "bg-green-100 text-green-700 border-green-200",
  Ongoing: "bg-blue-100 text-blue-700 border-blue-200",
  Completed: "bg-stone-100 text-gray-600 border-stone-200",
};

export default function MemberActivities() {
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState("");
  const [categoryFilter, setCategoryFilter] = useState<(typeof categoryOptions)[number]>("All");
  const [sectorFilter, setSectorFilter] = useState<(typeof sectorOptions)[number]>("All");
  const [statusFilter, setStatusFilter] = useState<ActivityStatus>("Upcoming");
  const [selectedActivity, setSelectedActivity] = useState<CooperativeActivity | null>(null);
  const [successMessage, setSuccessMessage] = useState("");

  useEffect(() => {
    const role = localStorage.getItem("userRole");
    if (role !== "member") {
      if (role === "chairman") navigate("/dashboard");
      else if (role === "bookkeeper") navigate("/dashboard/bookkeeper");
      else navigate("/");
    }
  }, [navigate]);

  const filteredActivities = useMemo(
    () =>
      activities.filter((activity) => {
        const query = searchQuery.trim().toLowerCase();
        const matchesSearch =
          !query ||
          activity.title.toLowerCase().includes(query) ||
          activity.description.toLowerCase().includes(query) ||
          activity.location.toLowerCase().includes(query) ||
          activity.category.toLowerCase().includes(query) ||
          activity.sector.toLowerCase().includes(query);
        const matchesCategory = categoryFilter === "All" || activity.category === categoryFilter;
        const matchesSector =
          sectorFilter === "All" || activity.sector === sectorFilter || activity.sector === "All Members";
        const matchesStatus = activity.status === statusFilter;

        return matchesSearch && matchesCategory && matchesSector && matchesStatus;
      }),
    [categoryFilter, searchQuery, sectorFilter, statusFilter],
  );

  const upcomingThisMonth = activities.filter(
    (activity) => activity.status === "Upcoming" && activity.date.includes("May"),
  ).length;
  const sectorSpecificPrograms = activities.filter((activity) => activity.sector !== "All Members").length;

  const handleMarkInterested = () => {
    setSuccessMessage("Activity saved to your interested list.");
    setSelectedActivity(null);
    setTimeout(() => setSuccessMessage(""), 3000);
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
                  Activities & Programs
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Cooperative Activities and Programs
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  View available cooperative programs, training sessions, meetings, and sector-based activities.
                </p>
              </div>
              <button
                onClick={() => navigate("/dashboard/member")}
                className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
              >
                <ArrowLeft className="h-4 w-4" />
                Back to Dashboard
              </button>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        {successMessage && (
          <div className="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800 shadow-sm">
            <CheckCircle2 className="h-5 w-5" />
            {successMessage}
          </div>
        )}

        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-green-600">{activities.length}</div>
            <div className="text-sm text-muted-foreground">Available Activities</div>
          </div>
          <div className="rounded-xl border border-blue-200 bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-blue-700">{upcomingThisMonth}</div>
            <div className="text-sm text-muted-foreground">Upcoming This Month</div>
          </div>
          <div className="rounded-xl border border-amber-200 bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-amber-700">{sectorSpecificPrograms}</div>
            <div className="text-sm text-muted-foreground">Sector-Specific Programs</div>
          </div>
        </div>

        <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Browse Programs
                </p>
                <h2 className="mt-1 text-xl font-display">Activity List</h2>
              </div>
              <div className="text-sm font-medium text-gray-500">
                {filteredActivities.length} result{filteredActivities.length === 1 ? "" : "s"}
              </div>
            </div>

            <div className="mt-5 grid gap-3 border-t border-stone-100 pt-4 lg:grid-cols-[minmax(260px,1fr)_210px_210px_180px]">
              <div className="relative">
                <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(event) => setSearchQuery(event.target.value)}
                  placeholder="Search activities"
                  className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                />
              </div>

              <select
                value={categoryFilter}
                onChange={(event) => setCategoryFilter(event.target.value as (typeof categoryOptions)[number])}
                className="h-11 rounded-lg border border-stone-200 bg-white px-3 text-sm font-medium text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
              >
                {categoryOptions.map((category) => (
                  <option key={category} value={category}>
                    {category === "All" ? "All Categories" : category}
                  </option>
                ))}
              </select>

              <select
                value={sectorFilter}
                onChange={(event) => setSectorFilter(event.target.value as (typeof sectorOptions)[number])}
                className="h-11 rounded-lg border border-stone-200 bg-white px-3 text-sm font-medium text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
              >
                {sectorOptions.map((sector) => (
                  <option key={sector} value={sector}>
                    {sector === "All" ? "All Sectors" : sector}
                  </option>
                ))}
              </select>

              <select
                value={statusFilter}
                onChange={(event) => setStatusFilter(event.target.value as ActivityStatus)}
                className="h-11 rounded-lg border border-stone-200 bg-white px-3 text-sm font-medium text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
              >
                {statusOptions.map((status) => (
                  <option key={status} value={status}>
                    {status}
                  </option>
                ))}
              </select>
            </div>
          </div>

          <div className="grid grid-cols-1 gap-4 p-5 md:p-6 xl:grid-cols-2">
            {filteredActivities.length === 0 ? (
              <div className="rounded-lg border border-dashed border-stone-200 px-6 py-12 text-center text-gray-500 xl:col-span-2">
                No activities match the selected filters.
              </div>
            ) : (
              filteredActivities.map((activity) => (
                <button
                  key={activity.id}
                  onClick={() => setSelectedActivity(activity)}
                  className="rounded-lg border border-stone-200 bg-stone-50/70 p-5 text-left shadow-sm transition-all hover:-translate-y-1 hover:border-green-200 hover:bg-green-50/40 hover:shadow-lg"
                >
                  <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                    <div>
                      <div className="flex flex-wrap gap-2">
                        <span className="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-stone-200">
                          {activity.category}
                        </span>
                        <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-semibold ${statusColors[activity.status]}`}>
                          {activity.status}
                        </span>
                      </div>
                      <h3 className="mt-4 text-lg font-semibold text-gray-950">{activity.title}</h3>
                    </div>
                  </div>

                  <p className="mt-3 text-sm leading-6 text-gray-600">{activity.description}</p>
                  <div className="mt-5 grid gap-3 text-sm text-gray-500 sm:grid-cols-2">
                    <span className="inline-flex items-center gap-2">
                      <Calendar className="h-4 w-4 text-primary" />
                      {activity.date}
                    </span>
                    <span className="inline-flex items-center gap-2">
                      <Clock3 className="h-4 w-4 text-primary" />
                      {activity.time}
                    </span>
                    <span className="inline-flex items-center gap-2">
                      <MapPin className="h-4 w-4 text-primary" />
                      {activity.location}
                    </span>
                    <span className="inline-flex items-center gap-2">
                      <Users className="h-4 w-4 text-primary" />
                      {activity.sector}
                    </span>
                  </div>
                </button>
              ))
            )}
          </div>
        </section>
      </main>

      {selectedActivity && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4"
          onClick={() => setSelectedActivity(null)}
        >
          <div
            className="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between gap-4 border-b border-stone-200 px-6 py-5">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">Activity Details</p>
                <h2 className="mt-1 text-2xl font-display text-gray-950">{selectedActivity.title}</h2>
              </div>
              <button
                onClick={() => setSelectedActivity(null)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
                aria-label="Close activity details"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="space-y-5 px-6 py-6">
              <div className="flex flex-wrap gap-2">
                <span className="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-stone-200">
                  {selectedActivity.category}
                </span>
                <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-semibold ${statusColors[selectedActivity.status]}`}>
                  {selectedActivity.status}
                </span>
              </div>

              <div className="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 px-4 py-4 text-sm md:grid-cols-2">
                <div className="flex gap-3">
                  <Tag className="mt-0.5 h-4 w-4 text-primary" />
                  <div>
                    <p className="text-gray-500">Category</p>
                    <p className="font-semibold text-gray-950">{selectedActivity.category}</p>
                  </div>
                </div>
                <div className="flex gap-3">
                  <Users className="mt-0.5 h-4 w-4 text-primary" />
                  <div>
                    <p className="text-gray-500">Sector</p>
                    <p className="font-semibold text-gray-950">{selectedActivity.sector}</p>
                  </div>
                </div>
                <div className="flex gap-3">
                  <Calendar className="mt-0.5 h-4 w-4 text-primary" />
                  <div>
                    <p className="text-gray-500">Date</p>
                    <p className="font-semibold text-gray-950">{selectedActivity.date}</p>
                  </div>
                </div>
                <div className="flex gap-3">
                  <Clock3 className="mt-0.5 h-4 w-4 text-primary" />
                  <div>
                    <p className="text-gray-500">Time</p>
                    <p className="font-semibold text-gray-950">{selectedActivity.time}</p>
                  </div>
                </div>
                <div className="flex gap-3 md:col-span-2">
                  <MapPin className="mt-0.5 h-4 w-4 text-primary" />
                  <div>
                    <p className="text-gray-500">Location</p>
                    <p className="font-semibold text-gray-950">{selectedActivity.location}</p>
                  </div>
                </div>
              </div>

              <p className="text-sm leading-7 text-gray-600">{selectedActivity.description}</p>
            </div>

            <div className="flex flex-col justify-end gap-3 border-t border-stone-200 px-6 py-5 sm:flex-row">
              <button
                onClick={() => setSelectedActivity(null)}
                className="inline-flex h-11 items-center justify-center rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50"
              >
                Close
              </button>
              <button
                onClick={handleMarkInterested}
                className="inline-flex h-11 items-center justify-center rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
              >
                Mark as Interested
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
