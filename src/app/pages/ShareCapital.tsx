import type { FormEvent } from "react";
import { useMemo, useState } from "react";
import {
  ArrowUpDown,
  Pencil,
  Plus,
  Search,
  Trash2,
  TrendingUp,
  Users,
  Wallet,
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

const shareEvidenceImages = {
  capital:
    "https://images.unsplash.com/photo-1554224154-22dec7ec8818?auto=format&fit=crop&q=80&w=1200",
  deposit:
    "https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?auto=format&fit=crop&q=80&w=1200",
};

type MemberStatus = "Active" | "Inactive";
type SortKey = "member" | "sector" | "capital" | "lastPayment" | "status";

interface ShareCapitalMember {
  id: string;
  name: string;
  sector: string;
  capital: number;
  lastPayment: string;
  lastPaymentIso: string;
  status: MemberStatus;
  evidenceImage: string;
  evidenceName: string;
}

const initialMembers: ShareCapitalMember[] = [
  {
    id: "M001",
    name: "Maria Santos",
    sector: "Rice Farming",
    capital: 25000,
    lastPayment: "Apr 14, 2026",
    lastPaymentIso: "2026-04-14",
    status: "Active",
    evidenceImage: shareEvidenceImages.capital,
    evidenceName: "maria-share-capital-proof.jpg",
  },
  {
    id: "M002",
    name: "Juan Dela Cruz",
    sector: "Corn",
    capital: 18000,
    lastPayment: "Apr 12, 2026",
    lastPaymentIso: "2026-04-12",
    status: "Active",
    evidenceImage: shareEvidenceImages.deposit,
    evidenceName: "juan-share-capital-slip.jpg",
  },
  {
    id: "M003",
    name: "Rosa Garcia",
    sector: "Fishery",
    capital: 32000,
    lastPayment: "Apr 10, 2026",
    lastPaymentIso: "2026-04-10",
    status: "Active",
    evidenceImage: shareEvidenceImages.capital,
    evidenceName: "rosa-capital-confirmation.jpg",
  },
  {
    id: "M004",
    name: "Pedro Reyes",
    sector: "Livestock",
    capital: 12000,
    lastPayment: "Mar 28, 2026",
    lastPaymentIso: "2026-03-28",
    status: "Inactive",
    evidenceImage: shareEvidenceImages.deposit,
    evidenceName: "pedro-capital-deposit-slip.jpg",
  },
  {
    id: "M005",
    name: "Ana Lopez",
    sector: "High-Value Crops",
    capital: 45000,
    lastPayment: "Apr 13, 2026",
    lastPaymentIso: "2026-04-13",
    status: "Active",
    evidenceImage: shareEvidenceImages.capital,
    evidenceName: "ana-share-capital-proof.jpg",
  },
  {
    id: "M006",
    name: "Carlos Ramos",
    sector: "Rice Farming",
    capital: 28000,
    lastPayment: "Apr 11, 2026",
    lastPaymentIso: "2026-04-11",
    status: "Active",
    evidenceImage: shareEvidenceImages.deposit,
    evidenceName: "carlos-capital-ledger-entry.jpg",
  },
];

const sortLabels: Record<SortKey, string> = {
  member: "Member",
  sector: "Sector",
  capital: "Share Capital",
  lastPayment: "Last Payment",
  status: "Status",
};

function formatDisplayDate(dateIso: string) {
  const date = new Date(`${dateIso}T12:00:00`);
  return date.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}

function createShareCapitalForm(member?: ShareCapitalMember) {
  if (member) {
    return {
      memberId: member.id,
      sector: member.sector,
      dateIso: member.lastPaymentIso,
      amount: String(member.capital),
      status: member.status,
      evidenceImage: member.evidenceImage,
      evidenceName: member.evidenceName,
    };
  }

  return {
    memberId: "M001",
    sector: "Rice Farming",
    dateIso: "2026-04-18",
    amount: "3500",
    status: "Active",
    evidenceImage: shareEvidenceImages.capital,
    evidenceName: "share-capital-acknowledgement-apr18.jpg",
  };
}

export default function ShareCapital() {
  const [members, setMembers] = useState(initialMembers);
  const [searchQuery, setSearchQuery] = useState("");
  const [sectorFilter, setSectorFilter] = useState<string>("all");
  const [statusFilter, setStatusFilter] = useState<"all" | MemberStatus>("all");
  const [capitalBandFilter, setCapitalBandFilter] = useState<
    "all" | "under_20000" | "20000_30000" | "above_30000"
  >("all");
  const [sortKey, setSortKey] = useState<SortKey>("capital");
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("desc");
  const [dialogMode, setDialogMode] = useState<"record" | "edit">("record");
  const [dialogOpen, setDialogOpen] = useState(false);
  const [selectedMemberId, setSelectedMemberId] = useState<string | null>(null);
  const [shareForm, setShareForm] = useState(createShareCapitalForm());
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
  }, [capitalBandFilter, members, searchQuery, sectorFilter, statusFilter]);

  const sortedMembers = useMemo(() => {
    return [...filteredMembers].sort((left, right) => {
      const direction = sortDirection === "asc" ? 1 : -1;

      switch (sortKey) {
        case "member": {
          const nameCompare = left.name.localeCompare(right.name) * direction;
          return nameCompare !== 0 ? nameCompare : left.id.localeCompare(right.id) * direction;
        }
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
    setSectorFilter("all");
    setStatusFilter("all");
    setCapitalBandFilter("all");
  };

  const handleSort = (nextKey: SortKey) => {
    if (sortKey === nextKey) {
      setSortDirection((current) => (current === "asc" ? "desc" : "asc"));
    } else {
      setSortKey(nextKey);
      setSortDirection(nextKey === "capital" ? "desc" : "asc");
    }
  };

  const openRecordDialog = () => {
    setDialogMode("record");
    setSelectedMemberId(null);
    setShareForm(createShareCapitalForm());
    setDialogOpen(true);
  };

  const openEditDialog = (member: ShareCapitalMember) => {
    setDialogMode("edit");
    setSelectedMemberId(member.id);
    setShareForm(createShareCapitalForm(member));
    setDialogOpen(true);
  };

  const handleShareFormChange = (name: string, value: string) => {
    if (name === "memberId") {
      const selectedMember = members.find((member) => member.id === value);

      setShareForm((current) => ({
        ...current,
        memberId: value,
        sector: selectedMember?.sector ?? current.sector,
        status: selectedMember?.status ?? current.status,
        evidenceImage: selectedMember?.evidenceImage ?? current.evidenceImage,
        evidenceName: selectedMember?.evidenceName ?? current.evidenceName,
      }));
      return;
    }

    setShareForm((current) => ({ ...current, [name]: value }));
  };

  const handleShareSubmit = (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const selectedMember = members.find((member) => member.id === shareForm.memberId);

    if (!selectedMember) {
      return;
    }

    setDialogOpen(false);

    runSimulation({
      processingTitle:
        dialogMode === "record"
          ? "Uploading share capital proof"
          : "Updating share capital record",
      processingDescription:
        dialogMode === "record"
          ? `${shareForm.evidenceName} is being attached to ${selectedMember.name}'s capital record.`
          : `${shareForm.evidenceName} is being re-attached while ${selectedMember.name}'s record is updated.`,
      completeTitle:
        dialogMode === "record"
          ? "Share capital recorded"
          : "Share capital record updated",
      completeDescription:
        dialogMode === "record"
          ? `${selectedMember.name}'s contribution has been posted to the share capital directory.`
          : `${selectedMember.name}'s share capital record has been updated.`,
      tone: "green",
      onComplete: () => {
        setMembers((current) =>
          current.map((member) => {
            if (member.id !== shareForm.memberId) {
              return member;
            }

            const nextCapital =
              dialogMode === "record"
                ? member.capital + Number(shareForm.amount)
                : Number(shareForm.amount);

            return {
              ...member,
              sector: shareForm.sector,
              capital: nextCapital,
              lastPaymentIso: shareForm.dateIso,
              lastPayment: formatDisplayDate(shareForm.dateIso),
              status: shareForm.status as MemberStatus,
              evidenceImage: shareForm.evidenceImage,
              evidenceName: shareForm.evidenceName,
            };
          }),
        );
      },
    });
  };

  const handleDeleteMemberRecord = (member: ShareCapitalMember) => {
    setConfirmDialog({
      isOpen: true,
      title: "Delete share capital record?",
      message: `This will remove ${member.name} from the share capital directory.`,
      onConfirm: () => {
        runSimulation({
          processingTitle: "Removing share capital record",
          processingDescription: `${member.name}'s record is being removed from the directory.`,
          completeTitle: "Share capital record deleted",
          completeDescription: `${member.name}'s share capital record has been removed.`,
          tone: "red",
          onComplete: () => {
            setMembers((current) => current.filter((item) => item.id !== member.id));
          },
        });
      },
    });
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
                  Share Capital
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Member Share Capital
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Member share capital contributions and tracking.
                </p>
              </div>
              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <button
                  onClick={openRecordDialog}
                  data-tour="share-capital-record"
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                >
                  <Plus className="h-4 w-4" />
                  Record Share Capital
                </button>
              </div>
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
              {formatCurrency(Math.round(totalCapital / Math.max(members.length, 1)))}
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

            <div
              className="mt-5 border-t border-stone-100 pt-4"
              data-tour="share-capital-filters"
            >
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
                        | "above_30000",
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

          <div
            className="overflow-x-auto"
            data-tour="share-capital-table"
          >
            <table className="w-full">
              <thead className="bg-stone-50">
                <tr>
                  {(["member", "sector", "capital", "lastPayment", "status"] as SortKey[]).map(
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
                    ),
                  )}
                  <th className="px-6 py-4 text-right text-sm font-semibold text-gray-600">
                    Actions
                  </th>
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
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-3">
                          <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                            <span className="font-bold text-primary">
                              {member.name.charAt(0)}
                            </span>
                          </div>
                          <div>
                            <p className="font-medium text-gray-950">{member.name}</p>
                            <p className="text-xs text-gray-500">{member.id}</p>
                          </div>
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
                      <td className="px-6 py-4">
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => openEditDialog(member)}
                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-gray-600 transition-all hover:border-primary hover:bg-green-50 hover:text-primary"
                            aria-label={`Edit ${member.name}`}
                            title="Edit record"
                          >
                            <Pencil className="h-4 w-4" />
                          </button>
                          <button
                            onClick={() => handleDeleteMemberRecord(member)}
                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-gray-600 transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                            aria-label={`Delete ${member.name}`}
                            title="Delete record"
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
        </section>
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
                    {dialogMode === "record" ? "Record Share Capital" : "Edit Share Capital Record"}
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
                id="share-capital-form"
                onSubmit={handleShareSubmit}
                className="grid min-h-0 flex-1 lg:grid-cols-[minmax(0,1fr)_320px]"
              >
                <div className="min-h-0 overflow-y-auto px-6 py-6">
                  <div className="grid gap-4 md:grid-cols-2">
                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Member
                      </span>
                      <select
                        value={shareForm.memberId}
                        onChange={(event) =>
                          handleShareFormChange("memberId", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      >
                        {members.map((member) => (
                          <option key={member.id} value={member.id}>
                            {member.name} ({member.id})
                          </option>
                        ))}
                      </select>
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Sector
                      </span>
                      <input
                        type="text"
                        value={shareForm.sector}
                        readOnly
                        className="h-11 w-full rounded-lg border border-stone-200 bg-stone-50 px-4 text-sm text-gray-500 outline-none"
                      />
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Date
                      </span>
                      <input
                        type="date"
                        value={shareForm.dateIso}
                        onChange={(event) =>
                          handleShareFormChange("dateIso", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      />
                    </label>

                    <label>
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Status
                      </span>
                      <select
                        value={shareForm.status}
                        onChange={(event) =>
                          handleShareFormChange("status", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      >
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                      </select>
                    </label>

                    <label className="md:col-span-2">
                      <span className="mb-2 block text-sm font-semibold text-gray-700">
                        Share Capital Amount
                      </span>
                      <input
                        type="number"
                        min="0"
                        step="0.01"
                        value={shareForm.amount}
                        onChange={(event) =>
                          handleShareFormChange("amount", event.target.value)
                        }
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                      />
                    </label>

                    <div className="md:col-span-2 rounded-lg border border-dashed border-stone-300 bg-stone-50 px-4 py-4">
                      <div className="flex items-center gap-3">
                        <div className="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100 text-green-700">
                          <Wallet className="h-5 w-5" />
                        </div>
                        <div>
                          <p className="text-sm font-semibold text-gray-950">
                            Share capital proof attached
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
                      src={shareForm.evidenceImage}
                      alt={shareForm.evidenceName}
                      className="h-64 w-full object-cover"
                    />
                    <div className="border-t border-stone-200 px-4 py-4">
                      <p className="text-sm font-semibold text-gray-950">
                        {shareForm.evidenceName}
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
                    form="share-capital-form"
                    className="inline-flex h-11 items-center justify-center rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
                  >
                    {dialogMode === "record" ? "Upload Share Capital Record" : "Save Share Capital Update"}
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
