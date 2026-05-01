import { useMemo, useState } from "react";
import {
  ArrowLeft,
  ArrowRight,
  ArrowUpDown,
  Eye,
  Mail,
  MapPin,
  Phone,
  Search,
  TrendingUp,
  UserPlus,
  Users,
  Wallet,
  X,
} from "lucide-react";
import { Link } from "react-router";
import { Bar, BarChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";
import { formatCurrency } from "../../utils/formatters";
import ConfirmDialog from "../components/ConfirmDialog";
import TooltipHint from "../components/Tooltip";

type MemberStatus = "Active" | "At-Risk" | "Inactive";
type Sector =
  | "rice_farming"
  | "corn"
  | "fishery"
  | "livestock"
  | "high_value_crops";

type SortKey =
  | "name"
  | "sector"
  | "status"
  | "shareCapital"
  | "joined"
  | "id";

interface Member {
  id: string;
  name: string;
  email: string;
  phone: string;
  address: string;
  status: MemberStatus;
  sector: Sector;
  shareCapital: number;
  joined: string;
  joinedIso: string;
  photo: string;
}

const heroImage =
  "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=2400";

const sectorLabels: Record<Sector, string> = {
  rice_farming: "Rice Farming",
  corn: "Corn",
  fishery: "Fishery",
  livestock: "Livestock",
  high_value_crops: "High-Value Crops",
};

const statusColors: Record<MemberStatus, string> = {
  Active: "bg-green-50 text-green-700 ring-green-100",
  "At-Risk": "bg-amber-50 text-amber-700 ring-amber-100",
  Inactive: "bg-red-50 text-red-700 ring-red-100",
};

const statusDescriptions: Record<MemberStatus, string> = {
  Active: "Members actively participating and contributing regularly",
  "At-Risk": "Members showing signs of disengagement through missed meetings or delayed payments",
  Inactive: "Members who have not participated or contributed in recent months",
};

const membersData: Member[] = [
  {
    id: "M001",
    name: "Maria Santos",
    email: "maria.santos@email.com",
    phone: "+63 912 345 6789",
    address: "Quezon City, Metro Manila",
    status: "Active",
    sector: "rice_farming",
    shareCapital: 25000,
    joined: "Jan 15, 2024",
    joinedIso: "2024-01-15",
    photo:
      "https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M002",
    name: "Juan Dela Cruz",
    email: "juan.delacruz@email.com",
    phone: "+63 923 456 7890",
    address: "Makati City, Metro Manila",
    status: "Active",
    sector: "corn",
    shareCapital: 18000,
    joined: "Feb 3, 2024",
    joinedIso: "2024-02-03",
    photo:
      "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M003",
    name: "Rosa Garcia",
    email: "rosa.garcia@email.com",
    phone: "+63 934 567 8901",
    address: "Pasig City, Metro Manila",
    status: "At-Risk",
    sector: "fishery",
    shareCapital: 32000,
    joined: "Feb 20, 2024",
    joinedIso: "2024-02-20",
    photo:
      "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M004",
    name: "Pedro Reyes",
    email: "pedro.reyes@email.com",
    phone: "+63 945 678 9012",
    address: "Mandaluyong City, Metro Manila",
    status: "Inactive",
    sector: "livestock",
    shareCapital: 12000,
    joined: "Mar 5, 2024",
    joinedIso: "2024-03-05",
    photo:
      "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M005",
    name: "Ana Lopez",
    email: "ana.lopez@email.com",
    phone: "+63 956 789 0123",
    address: "Taguig City, Metro Manila",
    status: "Active",
    sector: "high_value_crops",
    shareCapital: 45000,
    joined: "Mar 18, 2024",
    joinedIso: "2024-03-18",
    photo:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M006",
    name: "Carlos Ramos",
    email: "carlos.ramos@email.com",
    phone: "+63 967 890 1234",
    address: "Paranaque City, Metro Manila",
    status: "Active",
    sector: "rice_farming",
    shareCapital: 28000,
    joined: "Apr 2, 2026",
    joinedIso: "2026-04-02",
    photo:
      "https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M007",
    name: "Elena Villanueva",
    email: "elena.v@email.com",
    phone: "+63 978 901 2345",
    address: "Las Pinas City, Metro Manila",
    status: "At-Risk",
    sector: "corn",
    shareCapital: 15000,
    joined: "Apr 5, 2026",
    joinedIso: "2026-04-05",
    photo:
      "https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M008",
    name: "Roberto Aquino",
    email: "roberto.a@email.com",
    phone: "+63 989 012 3456",
    address: "Muntinlupa City, Metro Manila",
    status: "Active",
    sector: "fishery",
    shareCapital: 38000,
    joined: "Apr 8, 2026",
    joinedIso: "2026-04-08",
    photo:
      "https://images.unsplash.com/photo-1507591064344-4c6ce005b128?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M009",
    name: "Lorna Bautista",
    email: "lorna.b@email.com",
    phone: "+63 917 221 8876",
    address: "San Jose del Monte, Bulacan",
    status: "Active",
    sector: "high_value_crops",
    shareCapital: 30500,
    joined: "Apr 12, 2026",
    joinedIso: "2026-04-12",
    photo:
      "https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M010",
    name: "Dennis Mercado",
    email: "dennis.m@email.com",
    phone: "+63 918 443 1921",
    address: "Cabanatuan City, Nueva Ecija",
    status: "Inactive",
    sector: "corn",
    shareCapital: 17000,
    joined: "Apr 16, 2026",
    joinedIso: "2026-04-16",
    photo:
      "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M011",
    name: "Jessa Manaloto",
    email: "jessa.m@email.com",
    phone: "+63 919 534 2452",
    address: "Calumpit, Bulacan",
    status: "Active",
    sector: "rice_farming",
    shareCapital: 22000,
    joined: "Apr 18, 2026",
    joinedIso: "2026-04-18",
    photo:
      "https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=800",
  },
  {
    id: "M012",
    name: "Paolo Navarro",
    email: "paolo.n@email.com",
    phone: "+63 920 654 2011",
    address: "San Fernando, Pampanga",
    status: "At-Risk",
    sector: "livestock",
    shareCapital: 26400,
    joined: "Apr 22, 2026",
    joinedIso: "2026-04-22",
    photo:
      "https://images.unsplash.com/photo-1504257432389-52343af06ae3?auto=format&fit=crop&q=80&w=800",
  },
];

const pageSize = 5;

const sortLabels: Record<SortKey, string> = {
  name: "Member",
  sector: "Sector",
  status: "Status",
  shareCapital: "Share Capital",
  joined: "Joined",
  id: "ID",
};

export default function Members() {
  const [members] = useState<Member[]>(membersData);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedSector, setSelectedSector] = useState<Sector | "all">("all");
  const [selectedStatus, setSelectedStatus] = useState<MemberStatus | "all">(
    "all"
  );
  const [selectedMember, setSelectedMember] = useState<Member | null>(null);
  const [showAddModal, setShowAddModal] = useState(false);
  const [page, setPage] = useState(1);
  const [sortKey, setSortKey] = useState<SortKey>("name");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("asc");
  const [confirmDialog, setConfirmDialog] = useState<{
    isOpen: boolean;
    title: string;
    message: string;
    onConfirm: () => void;
    variant?: "danger" | "warning" | "info" | "success";
  }>({
    isOpen: false,
    title: "",
    message: "",
    onConfirm: () => {},
  });

  const filteredMembers = useMemo(() => {
    return members.filter((member) => {
      const query = searchQuery.trim().toLowerCase();
      const matchesSearch =
        !query ||
        member.name.toLowerCase().includes(query) ||
        member.id.toLowerCase().includes(query) ||
        member.email.toLowerCase().includes(query);
      const matchesSector =
        selectedSector === "all" || member.sector === selectedSector;
      const matchesStatus =
        selectedStatus === "all" || member.status === selectedStatus;

      return matchesSearch && matchesSector && matchesStatus;
    });
  }, [members, searchQuery, selectedSector, selectedStatus]);

  const sortedMembers = useMemo(() => {
    const sorted = [...filteredMembers].sort((left, right) => {
      const direction = sortDirection === "asc" ? 1 : -1;

      switch (sortKey) {
        case "name":
        case "id":
          return left[sortKey].localeCompare(right[sortKey]) * direction;
        case "sector":
          return (
            sectorLabels[left.sector].localeCompare(sectorLabels[right.sector]) *
            direction
          );
        case "status":
          return left.status.localeCompare(right.status) * direction;
        case "shareCapital":
          return (left.shareCapital - right.shareCapital) * direction;
        case "joined":
          return left.joinedIso.localeCompare(right.joinedIso) * direction;
        default:
          return 0;
      }
    });

    return sorted;
  }, [filteredMembers, sortDirection, sortKey]);

  const totalPages = Math.max(1, Math.ceil(sortedMembers.length / pageSize));
  const currentPage = Math.min(page, totalPages);

  const paginatedMembers = useMemo(() => {
    const startIndex = (currentPage - 1) * pageSize;
    return sortedMembers.slice(startIndex, startIndex + pageSize);
  }, [currentPage, sortedMembers]);

  const activeFilters = [selectedSector !== "all", selectedStatus !== "all", !!searchQuery.trim()].filter(Boolean).length;

  const stats = {
    total: members.length,
    active: members.filter((member) => member.status === "Active").length,
    atRisk: members.filter((member) => member.status === "At-Risk").length,
    inactive: members.filter((member) => member.status === "Inactive").length,
  };

  const clearFilters = () => {
    setSearchQuery("");
    setSelectedSector("all");
    setSelectedStatus("all");
    setPage(1);
  };

  const handleSort = (nextKey: SortKey) => {
    if (sortKey === nextKey) {
      setSortDirection((current) => (current === "asc" ? "desc" : "asc"));
    } else {
      setSortKey(nextKey);
      setSortDirection("asc");
    }
    setPage(1);
  };

  const getShareCapitalHistory = (member: Member) => {
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
    return months.map((month, index) => ({
      id: `${member.id}-${month}-${index}`,
      month,
      amount: Math.round(member.shareCapital * (0.68 + index * 0.06)),
    }));
  };

  const getDocumentHistory = () => [
    { name: "Membership Application", date: "Jan 15, 2024", type: "PDF" },
    { name: "Share Certificate", date: "Jan 20, 2024", type: "PDF" },
    { name: "Annual Statement", date: "Mar 10, 2026", type: "PDF" },
    { name: "Payment Receipt", date: "Apr 5, 2026", type: "PDF" },
  ];

  const rangeStart = sortedMembers.length === 0 ? 0 : (currentPage - 1) * pageSize + 1;
  const rangeEnd = Math.min(currentPage * pageSize, sortedMembers.length);

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

        <div className="relative mx-auto max-w-[1600px] px-6 py-8 md:px-8 md:py-10">
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div className="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
              <div className="max-w-4xl">
                <p className="mb-4 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-semibold text-white shadow-sm backdrop-blur">
                  Members
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Cooperative Member Directory
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Review member records, status, and contribution activity.
                </p>
              </div>

              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <button
                  onClick={() => setShowAddModal(true)}
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                >
                  <UserPlus className="h-4 w-4" />
                  Add Member
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <section className="mb-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
          <article className="animate-in fade-in slide-in-from-bottom-3 rounded-lg border border-blue-100 bg-blue-50 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-700 text-white shadow-sm">
              <Users className="h-6 w-6" />
            </div>
            <p className="text-sm font-semibold text-gray-600">Total Members</p>
            <p className="mt-2 font-display text-4xl font-bold text-gray-950">
              {stats.total}
            </p>
          </article>

          <article className="animate-in fade-in slide-in-from-bottom-3 rounded-lg border border-green-100 bg-green-50 p-5 shadow-sm transition-all delay-75 duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary text-white shadow-sm">
              <TrendingUp className="h-6 w-6" />
            </div>
            <p className="text-sm font-semibold text-gray-600">Active Members</p>
            <p className="mt-2 font-display text-4xl font-bold text-gray-950">
              {stats.active}
            </p>
          </article>

          <article className="animate-in fade-in slide-in-from-bottom-3 rounded-lg border border-amber-100 bg-amber-50 p-5 shadow-sm transition-all delay-150 duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-amber-600 text-white shadow-sm">
              <Users className="h-6 w-6" />
            </div>
            <p className="text-sm font-semibold text-gray-600">At-Risk Members</p>
            <p className="mt-2 font-display text-4xl font-bold text-gray-950">
              {stats.atRisk}
            </p>
          </article>

          <article className="animate-in fade-in slide-in-from-bottom-3 rounded-lg border border-red-100 bg-red-50 p-5 shadow-sm transition-all delay-200 duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-red-600 text-white shadow-sm">
              <Wallet className="h-6 w-6" />
            </div>
            <p className="text-sm font-semibold text-gray-600">Inactive Members</p>
            <p className="mt-2 font-display text-4xl font-bold text-gray-950">
              {stats.inactive}
            </p>
          </article>
        </section>

        <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Member Directory
                </p>
                <h2 className="mt-1 font-display text-2xl font-bold text-gray-950">
                  Records and Status
                </h2>
              </div>
              <div className="text-sm font-medium text-gray-500">
                {sortedMembers.length} result{sortedMembers.length === 1 ? "" : "s"}
              </div>
            </div>

            <div className="mt-5 border-t border-stone-100 pt-4">
              <div className="grid gap-3 xl:grid-cols-[minmax(280px,1fr)_200px_220px_auto] xl:items-center">
                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(event) => {
                      setSearchQuery(event.target.value);
                      setPage(1);
                    }}
                    placeholder="Search members"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <select
                  value={selectedStatus}
                  onChange={(event) => {
                    setSelectedStatus(event.target.value as MemberStatus | "all");
                    setPage(1);
                  }}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by member status"
                >
                  <option value="all">All Status</option>
                  <option value="Active">Active</option>
                  <option value="At-Risk">At-Risk</option>
                  <option value="Inactive">Inactive</option>
                </select>

                <select
                  value={selectedSector}
                  onChange={(event) => {
                    setSelectedSector(event.target.value as Sector | "all");
                    setPage(1);
                  }}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  aria-label="Filter by sector"
                >
                  <option value="all">All Sectors</option>
                  {(Object.keys(sectorLabels) as Sector[]).map((sector) => (
                    <option key={sector} value={sector}>
                      {sectorLabels[sector]}
                    </option>
                  ))}
                </select>

                <button
                  onClick={clearFilters}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-600 transition-all hover:-translate-y-0.5 hover:bg-stone-50 hover:text-primary"
                >
                  Clear
                  {activeFilters > 0 ? ` (${activeFilters})` : ""}
                </button>
              </div>
            </div>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-stone-50">
                <tr>
                  {([
                    "name",
                    "sector",
                    "status",
                    "shareCapital",
                    "joined",
                  ] as SortKey[]).map((key) => (
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
                  ))}
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody
                key={`${searchQuery}-${selectedSector}-${selectedStatus}-${sortKey}-${sortDirection}-${currentPage}`}
                className="animate-in fade-in duration-300"
              >
                {paginatedMembers.length === 0 ? (
                  <tr>
                    <td
                      colSpan={6}
                      className="px-6 py-14 text-center text-gray-500"
                    >
                      No members found.
                    </td>
                  </tr>
                ) : (
                  paginatedMembers.map((member, index) => (
                    <tr
                      key={member.id}
                      className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-green-50/50"
                      style={{ animationDelay: `${Math.min(index * 35, 220)}ms` }}
                    >
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-3">
                          <img
                            src={member.photo}
                            alt={member.name}
                            className="h-12 w-12 rounded-full object-cover ring-2 ring-green-100"
                          />
                          <div>
                            <p className="font-semibold text-gray-950">
                              {member.name}
                            </p>
                            <p className="text-xs text-gray-500">
                              {member.id} / {member.email}
                            </p>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4 text-gray-600">
                        {sectorLabels[member.sector]}
                      </td>
                      <td className="px-6 py-4">
                        <TooltipHint
                          content={statusDescriptions[member.status]}
                          position="top"
                        >
                          <span
                            className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold ring-1 ${statusColors[member.status]}`}
                          >
                            {member.status}
                          </span>
                        </TooltipHint>
                      </td>
                      <td className="px-6 py-4 font-medium text-gray-950">
                        {formatCurrency(member.shareCapital)}
                      </td>
                      <td className="px-6 py-4 text-gray-600">{member.joined}</td>
                      <td className="px-6 py-4">
                        <button
                          onClick={() => setSelectedMember(member)}
                          className="inline-flex items-center gap-2 rounded-lg border border-green-100 bg-green-50 px-4 py-2 text-sm font-semibold text-primary transition-all hover:-translate-y-0.5 hover:border-primary/30 hover:bg-green-100"
                        >
                          <Eye className="h-4 w-4" />
                          View
                        </button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>

          <div className="flex flex-col gap-4 border-t border-stone-200 px-5 py-4 md:flex-row md:items-center md:justify-between md:px-6">
            <p className="text-sm text-gray-500">
              Showing {rangeStart}-{rangeEnd} of {sortedMembers.length}
            </p>
            <div className="flex items-center gap-2">
              <button
                onClick={() => setPage((current) => Math.max(1, current - 1))}
                disabled={currentPage === 1}
                className="inline-flex h-10 items-center gap-2 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-50"
              >
                <ArrowLeft className="h-4 w-4" />
                Prev
              </button>
              <div className="flex items-center gap-1">
                {Array.from({ length: totalPages }, (_, index) => {
                  const nextPage = index + 1;
                  return (
                    <button
                      key={nextPage}
                      onClick={() => setPage(nextPage)}
                      className={`h-10 w-10 rounded-lg text-sm font-semibold transition-all ${
                        currentPage === nextPage
                          ? "bg-primary text-white shadow-sm"
                          : "border border-stone-200 bg-white text-gray-700 hover:bg-stone-50"
                      }`}
                    >
                      {nextPage}
                    </button>
                  );
                })}
              </div>
              <button
                onClick={() =>
                  setPage((current) => Math.min(totalPages, current + 1))
                }
                disabled={currentPage === totalPages}
                className="inline-flex h-10 items-center gap-2 rounded-lg border border-stone-200 bg-white px-4 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-50"
              >
                Next
                <ArrowRight className="h-4 w-4" />
              </button>
            </div>
          </div>
        </section>
      </main>

      {selectedMember && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm animate-in fade-in duration-200"
          onClick={() => setSelectedMember(null)}
        >
          <div
            className="flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-lg bg-white shadow-2xl animate-in zoom-in-95 duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between border-b border-stone-200 bg-stone-50 p-6">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Member Profile
                </p>
                <h2 className="mt-1 font-display text-2xl font-bold text-gray-950">
                  {selectedMember.name}
                </h2>
                <p className="mt-2 text-sm text-gray-600">
                  Cooperative member record, contact details, and contribution view.
                </p>
              </div>
              <button
                onClick={() => setSelectedMember(null)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-white hover:text-gray-800"
                aria-label="Close member preview"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="overflow-y-auto p-5 md:p-6">
              <div className="grid gap-4 xl:grid-cols-[1.15fr_1.35fr]">
                <div className="rounded-lg border border-green-100 bg-green-50 p-4">
                  <div className="overflow-hidden rounded-lg">
                    <img
                      src={selectedMember.photo}
                      alt={selectedMember.name}
                      className="h-[280px] w-full object-cover sm:h-[360px]"
                    />
                  </div>
                  <div className="mt-4 flex flex-wrap items-center gap-2">
                    <span
                      className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold ring-1 ${statusColors[selectedMember.status]}`}
                    >
                      {selectedMember.status}
                    </span>
                    <span className="inline-flex items-center rounded-full bg-white px-3 py-1 text-sm font-semibold text-primary shadow-sm">
                      {sectorLabels[selectedMember.sector]}
                    </span>
                    <span className="inline-flex items-center rounded-full bg-white px-3 py-1 text-sm font-semibold text-gray-600 shadow-sm">
                      {selectedMember.id}
                    </span>
                  </div>
                </div>

                <div className="grid gap-4">
                  <div className="grid gap-3 sm:grid-cols-2">
                    <div className="rounded-lg border border-stone-200 bg-white p-4">
                      <p className="text-xs font-bold uppercase tracking-[0.14em] text-gray-500">
                        Joined
                      </p>
                      <p className="mt-1 font-semibold text-gray-950">
                        {selectedMember.joined}
                      </p>
                    </div>
                    <div className="rounded-lg border border-stone-200 bg-white p-4">
                      <p className="text-xs font-bold uppercase tracking-[0.14em] text-gray-500">
                        Share Capital
                      </p>
                      <p className="mt-1 font-semibold text-gray-950">
                        {formatCurrency(selectedMember.shareCapital)}
                      </p>
                    </div>
                  </div>

                  <div className="rounded-lg border border-stone-200 bg-white p-5">
                    <h3 className="font-semibold text-gray-950">
                      Member Details
                    </h3>
                    <div className="mt-4 space-y-4">
                      <div className="flex items-start gap-3">
                        <Mail className="mt-0.5 h-5 w-5 text-gray-400" />
                        <div>
                          <p className="text-xs font-bold uppercase tracking-[0.12em] text-gray-500">
                            Email
                          </p>
                          <p className="mt-1 text-gray-700">{selectedMember.email}</p>
                        </div>
                      </div>
                      <div className="flex items-start gap-3">
                        <Phone className="mt-0.5 h-5 w-5 text-gray-400" />
                        <div>
                          <p className="text-xs font-bold uppercase tracking-[0.12em] text-gray-500">
                            Phone
                          </p>
                          <p className="mt-1 text-gray-700">{selectedMember.phone}</p>
                        </div>
                      </div>
                      <div className="flex items-start gap-3">
                        <MapPin className="mt-0.5 h-5 w-5 text-gray-400" />
                        <div>
                          <p className="text-xs font-bold uppercase tracking-[0.12em] text-gray-500">
                            Address
                          </p>
                          <p className="mt-1 text-gray-700">{selectedMember.address}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="rounded-lg border border-stone-200 bg-white p-5">
                    <div className="mb-4 flex items-center justify-between gap-3">
                      <div>
                        <h3 className="font-semibold text-gray-950">
                          Share Capital Trend
                        </h3>
                        <p className="mt-1 text-sm text-gray-500">
                          Demo six-month contribution history.
                        </p>
                      </div>
                      <Link
                        to="/dashboard/predictions"
                        className="inline-flex items-center gap-2 rounded-lg border border-green-100 bg-green-50 px-3 py-2 text-sm font-semibold text-primary transition-all hover:bg-green-100"
                      >
                        <TrendingUp className="h-4 w-4" />
                        View Predictions
                      </Link>
                    </div>

                    <div className="h-52" key={`member-chart-${selectedMember.id}`}>
                      <ResponsiveContainer width="100%" height="100%">
                        <BarChart
                          data={getShareCapitalHistory(selectedMember)}
                          margin={{ top: 8, right: 12, left: 0, bottom: 0 }}
                        >
                          <XAxis
                            dataKey="month"
                            stroke="#78716c"
                            fontSize={12}
                            tickLine={false}
                            axisLine={false}
                          />
                          <YAxis
                            stroke="#78716c"
                            fontSize={12}
                            tickLine={false}
                            axisLine={false}
                          />
                          <Tooltip
                            contentStyle={{
                              backgroundColor: "#fff",
                              border: "1px solid #e7e5e4",
                              borderRadius: "8px",
                              padding: "12px",
                            }}
                            formatter={(value: number) => formatCurrency(value)}
                          />
                          <Bar
                            dataKey="amount"
                            fill="#1b5e3f"
                            radius={[8, 8, 0, 0]}
                            isAnimationActive={false}
                          />
                        </BarChart>
                      </ResponsiveContainer>
                    </div>
                  </div>

                  <div className="rounded-lg border border-stone-200 bg-white p-5">
                    <h3 className="font-semibold text-gray-950">
                      Recent Documents
                    </h3>
                    <div className="mt-4 space-y-3">
                      {getDocumentHistory().map((document) => (
                        <div
                          key={`${selectedMember.id}-${document.name}`}
                          className="flex items-center justify-between rounded-lg border border-stone-100 px-4 py-3"
                        >
                          <div>
                            <p className="font-medium text-gray-950">
                              {document.name}
                            </p>
                            <p className="mt-1 text-sm text-gray-500">
                              {document.type} / {document.date}
                            </p>
                          </div>
                          <button className="text-sm font-semibold text-primary transition-colors hover:text-green-800">
                            View
                          </button>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </div>

              <button
                onClick={() => setSelectedMember(null)}
                className="mt-4 w-full rounded-lg bg-primary px-5 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800 hover:shadow-md"
              >
                Done
              </button>
            </div>
          </div>
        </div>
      )}

      {showAddModal && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm animate-in fade-in duration-200"
          onClick={() => setShowAddModal(false)}
        >
          <div
            className="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-2xl animate-in zoom-in-95 duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between border-b border-stone-200 bg-stone-50 p-6">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Add Member
                </p>
                <h2 className="mt-1 font-display text-2xl font-bold text-gray-950">
                  New Cooperative Member
                </h2>
              </div>
              <button
                onClick={() => setShowAddModal(false)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-white hover:text-gray-800"
                aria-label="Close add member modal"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="p-6">
              <form
                className="space-y-4"
                onSubmit={(event) => {
                  event.preventDefault();
                  setConfirmDialog({
                    isOpen: true,
                    title: "Add New Member?",
                    message:
                      "Are you sure you want to add this new member to the cooperative? They will be granted access to member services.",
                    variant: "success",
                    onConfirm: () => {
                      setShowAddModal(false);
                    },
                  });
                }}
              >
                <div className="grid gap-4 md:grid-cols-2">
                  <div>
                    <label className="mb-2 block text-sm font-medium text-gray-900">
                      Full Name
                    </label>
                    <input
                      type="text"
                      className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      placeholder="Juan Dela Cruz"
                    />
                  </div>
                  <div>
                    <label className="mb-2 block text-sm font-medium text-gray-900">
                      Email
                    </label>
                    <input
                      type="email"
                      className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      placeholder="juan@email.com"
                    />
                  </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                  <div>
                    <label className="mb-2 block text-sm font-medium text-gray-900">
                      Phone
                    </label>
                    <input
                      type="tel"
                      className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      placeholder="+63 912 345 6789"
                    />
                  </div>
                  <div>
                    <label className="mb-2 block text-sm font-medium text-gray-900">
                      Sector
                    </label>
                    <select className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
                      <option value="">Select Sector</option>
                      {(Object.keys(sectorLabels) as Sector[]).map((sector) => (
                        <option key={sector} value={sector}>
                          {sectorLabels[sector]}
                        </option>
                      ))}
                    </select>
                  </div>
                </div>

                <div>
                  <label className="mb-2 block text-sm font-medium text-gray-900">
                    Address
                  </label>
                  <input
                    type="text"
                    className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                    placeholder="Street, City, Province"
                  />
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                  <div>
                    <label className="mb-2 block text-sm font-medium text-gray-900">
                      Initial Share Capital
                    </label>
                    <input
                      type="number"
                      className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      placeholder="25000"
                    />
                  </div>
                  <div>
                    <label className="mb-2 block text-sm font-medium text-gray-900">
                      Status
                    </label>
                    <select className="w-full rounded-lg border border-stone-200 bg-white px-4 py-3 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
                      <option value="Active">Active</option>
                      <option value="At-Risk">At-Risk</option>
                      <option value="Inactive">Inactive</option>
                    </select>
                  </div>
                </div>

                <div className="flex gap-3 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowAddModal(false)}
                    className="flex-1 rounded-lg border border-stone-200 bg-white px-6 py-3 font-semibold text-gray-700 transition-all hover:bg-stone-50"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="flex-1 rounded-lg bg-primary px-6 py-3 font-semibold text-white transition-all hover:bg-green-800"
                  >
                    Add Member
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      <ConfirmDialog
        isOpen={confirmDialog.isOpen}
        onClose={() => setConfirmDialog({ ...confirmDialog, isOpen: false })}
        onConfirm={confirmDialog.onConfirm}
        title={confirmDialog.title}
        message={confirmDialog.message}
        variant={confirmDialog.variant}
      />
    </div>
  );
}
