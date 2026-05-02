import { useMemo, useState } from "react";
import { ArrowUpDown, Search, TrendingUp, Users, Wallet } from "lucide-react";
import { formatCurrency } from "../../utils/formatters";

const heroImage =
  "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=2400";

type MemberStatus = "Active" | "Inactive";
type SortKey = "id" | "name" | "sector" | "capital" | "lastPayment" | "status";

interface ShareCapitalMember {
  id: string;
  name: string;
  sector: string;
  capital: number;
  lastPayment: string;
  lastPaymentIso: string;
  status: MemberStatus;
}

const members: ShareCapitalMember[] = [
  {
    id: "M001",
    name: "Maria Santos",
    sector: "Rice Farming",
    capital: 25000,
    lastPayment: "Apr 14, 2026",
    lastPaymentIso: "2026-04-14",
    status: "Active",
  },
  {
    id: "M002",
    name: "Juan Dela Cruz",
    sector: "Corn",
    capital: 18000,
    lastPayment: "Apr 12, 2026",
    lastPaymentIso: "2026-04-12",
    status: "Active",
  },
  {
    id: "M003",
    name: "Rosa Garcia",
    sector: "Fishery",
    capital: 32000,
    lastPayment: "Apr 10, 2026",
    lastPaymentIso: "2026-04-10",
    status: "Active",
  },
  {
    id: "M004",
    name: "Pedro Reyes",
    sector: "Livestock",
    capital: 12000,
    lastPayment: "Mar 28, 2026",
    lastPaymentIso: "2026-03-28",
    status: "Inactive",
  },
  {
    id: "M005",
    name: "Ana Lopez",
    sector: "High-Value Crops",
    capital: 45000,
    lastPayment: "Apr 13, 2026",
    lastPaymentIso: "2026-04-13",
    status: "Active",
  },
  {
    id: "M006",
    name: "Carlos Ramos",
    sector: "Rice Farming",
    capital: 28000,
    lastPayment: "Apr 11, 2026",
    lastPaymentIso: "2026-04-11",
    status: "Active",
  },
];

const sortLabels: Record<SortKey, string> = {
  id: "Member ID",
  name: "Name",
  sector: "Sector",
  capital: "Share Capital",
  lastPayment: "Last Payment",
  status: "Status",
};

export default function ShareCapital() {
  const [searchQuery, setSearchQuery] = useState("");
  const [sectorFilter, setSectorFilter] = useState<string>("all");
  const [statusFilter, setStatusFilter] = useState<"all" | MemberStatus>("all");
  const [capitalBandFilter, setCapitalBandFilter] = useState<
    "all" | "under_20000" | "20000_30000" | "above_30000"
  >("all");
  const [sortKey, setSortKey] = useState<SortKey>("capital");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("desc");

  const totalCapital = members.reduce((sum, member) => sum + member.capital, 0);
  const sectors = Array.from(new Set(members.map((member) => member.sector)));

  const filteredMembers = useMemo(() => {
    return members.filter((member) => {
      const query = searchQuery.trim().toLowerCase();
      const matchesSearch =
        !query ||
        member.name.toLowerCase().includes(query) ||
        member.id.toLowerCase().includes(query);
      const matchesSector = sectorFilter === "all" || member.sector === sectorFilter;
      const matchesStatus = statusFilter === "all" || member.status === statusFilter;
      const matchesCapitalBand =
        capitalBandFilter === "all" ||
        (capitalBandFilter === "under_20000" && member.capital < 20000) ||
        (capitalBandFilter === "20000_30000" &&
          member.capital >= 20000 &&
          member.capital <= 30000) ||
        (capitalBandFilter === "above_30000" && member.capital > 30000);

      return matchesSearch && matchesSector && matchesStatus && matchesCapitalBand;
    });
  }, [capitalBandFilter, searchQuery, sectorFilter, statusFilter]);

  const sortedMembers = useMemo(() => {
    return [...filteredMembers].sort((left, right) => {
      const direction = sortDirection === "asc" ? 1 : -1;

      switch (sortKey) {
        case "id":
        case "name":
        case "sector":
        case "status":
          return left[sortKey].localeCompare(right[sortKey]) * direction;
        case "capital":
          return (left.capital - right.capital) * direction;
        case "lastPayment":
          return left.lastPaymentIso.localeCompare(right.lastPaymentIso) * direction;
        default:
          return 0;
      }
    });
  }, [filteredMembers, sortDirection, sortKey]);

  const clearFilters = () => {
    setSearchQuery("");
    setSectorFilter("all");
    setStatusFilter("all");
    setCapitalBandFilter("all");
  };

  const handleSort = (nextKey: SortKey) => {
    if (sortKey === nextKey) {
      setSortDirection((current) => (current === "asc" ? "desc" : "asc"));
    } else {
      setSortKey(nextKey);
      setSortDirection("asc");
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
        <div className="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-stone-50 to-transparent" />
        <div className="relative mx-auto flex min-h-[280px] max-w-[1600px] flex-col justify-start px-6 py-8 md:min-h-[320px] md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="max-w-4xl">
              <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                Share Capital
              </p>
              <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                Member Share Capital
              </h1>
              <p className="mt-3 max-w-2xl text-lg text-white/85">
                Member share capital contributions and tracking.
              </p>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <TrendingUp className="h-6 w-6 text-green-600" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold text-green-600">
              {formatCurrency(totalCapital)}
            </div>
            <div className="text-sm text-muted-foreground">Total Share Capital</div>
          </div>

          <div className="rounded-xl border border-border bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                <Users className="h-6 w-6 text-blue-700" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold">{members.length}</div>
            <div className="text-sm text-muted-foreground">Contributing Members</div>
          </div>

          <div className="rounded-xl border border-border bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-3 flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <Wallet className="h-6 w-6 text-primary" />
              </div>
            </div>
            <div className="mb-1 text-3xl font-bold">
              {formatCurrency(Math.round(totalCapital / members.length))}
            </div>
            <div className="text-sm text-muted-foreground">Average per Member</div>
          </div>
        </div>

        <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Share Capital Directory
                </p>
                <h2 className="mt-1 font-display text-2xl font-bold text-gray-950">
                  Member Contributions
                </h2>
              </div>
              <div className="text-sm font-medium text-gray-500">
                {sortedMembers.length} result{sortedMembers.length === 1 ? "" : "s"}
              </div>
            </div>

            <div className="mt-5 border-t border-stone-100 pt-4">
              <div className="grid gap-3 xl:grid-cols-[minmax(280px,1fr)_200px_180px_200px_auto] xl:items-center">
                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(event) => setSearchQuery(event.target.value)}
                    placeholder="Search members by name or ID"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <select
                  value={sectorFilter}
                  onChange={(event) => setSectorFilter(event.target.value)}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by sector"
                >
                  <option value="all">All Sectors</option>
                  {sectors.map((sector) => (
                    <option key={sector} value={sector}>
                      {sector}
                    </option>
                  ))}
                </select>

                <select
                  value={statusFilter}
                  onChange={(event) =>
                    setStatusFilter(event.target.value as "all" | MemberStatus)
                  }
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by status"
                >
                  <option value="all">All Status</option>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>

                <select
                  value={capitalBandFilter}
                  onChange={(event) =>
                    setCapitalBandFilter(
                      event.target.value as
                        | "all"
                        | "under_20000"
                        | "20000_30000"
                        | "above_30000"
                    )
                  }
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by capital amount"
                >
                  <option value="all">All Amounts</option>
                  <option value="under_20000">Under 20,000</option>
                  <option value="20000_30000">20,000 to 30,000</option>
                  <option value="above_30000">Above 30,000</option>
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
                  {(["id", "name", "sector", "capital", "lastPayment", "status"] as SortKey[]).map(
                    (key) => (
                      <th
                        key={key}
                        className="px-6 py-4 text-left text-sm font-semibold text-gray-600"
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
                    )
                  )}
                </tr>
              </thead>
              <tbody
                key={`${searchQuery}-${sectorFilter}-${statusFilter}-${capitalBandFilter}-${sortKey}-${sortDirection}`}
                className="animate-in fade-in duration-300"
              >
                {sortedMembers.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="px-6 py-14 text-center text-gray-500">
                      No member contributions found.
                    </td>
                  </tr>
                ) : (
                  sortedMembers.map((member, index) => (
                    <tr
                      key={member.id}
                      className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-green-50/50"
                      style={{ animationDelay: `${Math.min(index * 40, 220)}ms` }}
                    >
                      <td className="px-6 py-4 font-medium">{member.id}</td>
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-3">
                          <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                            <span className="font-bold text-primary">
                              {member.name.charAt(0)}
                            </span>
                          </div>
                          <span className="font-medium">{member.name}</span>
                        </div>
                      </td>
                      <td className="px-6 py-4 text-sm">{member.sector}</td>
                      <td className="px-6 py-4">
                        <span className="font-bold text-green-600">
                          {formatCurrency(member.capital)}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {member.lastPayment}
                      </td>
                      <td className="px-6 py-4">
                        <span
                          className={`inline-flex rounded-full px-3 py-1 text-sm font-semibold ${
                            member.status === "Active"
                              ? "bg-green-50 text-green-700"
                              : "bg-red-50 text-red-700"
                          }`}
                        >
                          {member.status}
                        </span>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </section>
      </main>
    </div>
  );
}
