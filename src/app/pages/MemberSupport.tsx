import { type FormEvent, useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router";
import {
  ArrowLeft,
  CheckCircle2,
  Eye,
  HelpCircle,
  MessageSquare,
  Search,
  X,
} from "lucide-react";

type ConcernType =
  | "Membership Concern"
  | "Share Capital Concern"
  | "Document/Record Request"
  | "Announcement Clarification"
  | "Activity/Program Inquiry"
  | "Technical Support"
  | "Other";
type InquiryStatus = "Pending" | "In Review" | "Resolved";
type InquiryPriority = "Low" | "Normal" | "High";

interface Inquiry {
  id: string;
  subject: string;
  type: ConcernType;
  dateSubmitted: string;
  status: InquiryStatus;
  priority: InquiryPriority;
  message: string;
  contactNumber?: string;
}

const heroImage =
  "https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80&w=2400";

const concernTypes: ConcernType[] = [
  "Membership Concern",
  "Share Capital Concern",
  "Document/Record Request",
  "Announcement Clarification",
  "Activity/Program Inquiry",
  "Technical Support",
  "Other",
];

const sampleInquiries: Inquiry[] = [
  {
    id: "TCK-2026-001",
    subject: "Clarification about April share capital posting",
    type: "Share Capital Concern",
    dateSubmitted: "Apr 14, 2026",
    status: "Resolved",
    priority: "Normal",
    message: "I would like to confirm if my April contribution was already posted to my share capital record.",
  },
  {
    id: "TCK-2026-002",
    subject: "Request copy of contribution receipt",
    type: "Document/Record Request",
    dateSubmitted: "Apr 16, 2026",
    status: "In Review",
    priority: "Normal",
    message: "Please provide a copy of my latest contribution receipt for personal record keeping.",
  },
  {
    id: "TCK-2026-003",
    subject: "Question about rice farming workshop",
    type: "Activity/Program Inquiry",
    dateSubmitted: "Apr 18, 2026",
    status: "Pending",
    priority: "Low",
    message: "I want to know if the rice farming workshop is open to members who recently joined the sector.",
  },
];

const statusColors: Record<InquiryStatus, string> = {
  Pending: "bg-amber-100 text-amber-700 border-amber-200",
  "In Review": "bg-blue-100 text-blue-700 border-blue-200",
  Resolved: "bg-green-100 text-green-700 border-green-200",
};

const priorityColors: Record<InquiryPriority, string> = {
  Low: "bg-stone-100 text-gray-600",
  Normal: "bg-blue-50 text-blue-700",
  High: "bg-red-50 text-red-700",
};

export default function MemberSupport() {
  const navigate = useNavigate();
  const [inquiries, setInquiries] = useState<Inquiry[]>(sampleInquiries);
  const [concernType, setConcernType] = useState<ConcernType>("Membership Concern");
  const [subject, setSubject] = useState("");
  const [message, setMessage] = useState("");
  const [priority, setPriority] = useState<InquiryPriority>("Normal");
  const [contactNumber, setContactNumber] = useState("");
  const [statusFilter, setStatusFilter] = useState<"All" | InquiryStatus>("All");
  const [typeFilter, setTypeFilter] = useState<"All" | ConcernType>("All");
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedInquiry, setSelectedInquiry] = useState<Inquiry | null>(null);
  const [successMessage, setSuccessMessage] = useState("");
  const [errors, setErrors] = useState<{ subject?: string; message?: string }>({});

  useEffect(() => {
    const role = localStorage.getItem("userRole");
    if (role !== "member") {
      if (role === "chairman") navigate("/dashboard");
      else if (role === "bookkeeper") navigate("/dashboard/bookkeeper");
      else navigate("/");
    }
  }, [navigate]);

  const filteredInquiries = useMemo(
    () =>
      inquiries.filter((inquiry) => {
        const query = searchQuery.trim().toLowerCase();
        const matchesSearch =
          !query ||
          inquiry.id.toLowerCase().includes(query) ||
          inquiry.subject.toLowerCase().includes(query) ||
          inquiry.type.toLowerCase().includes(query);
        const matchesStatus = statusFilter === "All" || inquiry.status === statusFilter;
        const matchesType = typeFilter === "All" || inquiry.type === typeFilter;

        return matchesSearch && matchesStatus && matchesType;
      }),
    [inquiries, searchQuery, statusFilter, typeFilter],
  );

  const openInquiries = inquiries.filter((inquiry) => inquiry.status !== "Resolved").length;
  const resolvedConcerns = inquiries.filter((inquiry) => inquiry.status === "Resolved").length;

  const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const nextErrors: { subject?: string; message?: string } = {};
    if (!subject.trim()) {
      nextErrors.subject = "Subject is required.";
    }
    if (!message.trim()) {
      nextErrors.message = "Message is required.";
    }

    setErrors(nextErrors);
    if (Object.keys(nextErrors).length > 0) {
      return;
    }

    const ticketNumber = String(inquiries.length + 1).padStart(3, "0");
    const newInquiry: Inquiry = {
      id: `TCK-2026-${ticketNumber}`,
      subject: subject.trim(),
      type: concernType,
      dateSubmitted: "May 11, 2026",
      status: "Pending",
      priority,
      message: message.trim(),
      contactNumber: contactNumber.trim() || undefined,
    };

    setInquiries((current) => [newInquiry, ...current]);
    setSuccessMessage("Your inquiry has been submitted successfully.");
    setConcernType("Membership Concern");
    setSubject("");
    setMessage("");
    setPriority("Normal");
    setContactNumber("");
    setErrors({});
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
                  Inquiry & Support
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Member Inquiry and Support
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Submit concerns about membership, share capital, documents, announcements, or cooperative services.
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
          <div className="rounded-xl border border-amber-200 bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-amber-700">{openInquiries}</div>
            <div className="text-sm text-muted-foreground">Open Inquiries</div>
          </div>
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-green-600">{resolvedConcerns}</div>
            <div className="text-sm text-muted-foreground">Resolved Concerns</div>
          </div>
          <div className="rounded-xl border border-blue-200 bg-card p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-blue-700">1-2 days</div>
            <div className="text-sm text-muted-foreground">Average Response Time</div>
          </div>
        </div>

        <div className="grid grid-cols-1 gap-6 xl:grid-cols-[0.85fr_1.15fr]">
          <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
            <div className="border-b border-stone-200 px-5 py-5 md:px-6">
              <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                Submit Concern
              </p>
              <h2 className="mt-1 text-xl font-display">Inquiry Form</h2>
            </div>

            <form onSubmit={handleSubmit} className="space-y-5 px-5 py-6 md:px-6">
              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">Concern Type</label>
                <select
                  value={concernType}
                  onChange={(event) => setConcernType(event.target.value as ConcernType)}
                  className="h-11 w-full rounded-lg border border-stone-200 bg-white px-3 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
                  {concernTypes.map((type) => (
                    <option key={type} value={type}>
                      {type}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">Subject</label>
                <input
                  type="text"
                  value={subject}
                  onChange={(event) => {
                    setSubject(event.target.value);
                    setErrors((current) => ({ ...current, subject: undefined }));
                  }}
                  className={`h-11 w-full rounded-lg border bg-white px-4 text-sm outline-none transition-all focus:ring-2 ${
                    errors.subject
                      ? "border-red-300 focus:ring-red-200"
                      : "border-stone-200 focus:border-primary focus:ring-primary/20"
                  }`}
                  placeholder="Enter inquiry subject"
                />
                {errors.subject && <p className="mt-2 text-sm font-medium text-red-600">{errors.subject}</p>}
              </div>

              <div>
                <label className="mb-2 block text-sm font-semibold text-gray-700">Message</label>
                <textarea
                  value={message}
                  onChange={(event) => {
                    setMessage(event.target.value);
                    setErrors((current) => ({ ...current, message: undefined }));
                  }}
                  rows={5}
                  className={`w-full rounded-lg border bg-white px-4 py-3 text-sm outline-none transition-all focus:ring-2 ${
                    errors.message
                      ? "border-red-300 focus:ring-red-200"
                      : "border-stone-200 focus:border-primary focus:ring-primary/20"
                  }`}
                  placeholder="Describe your concern or request"
                />
                {errors.message && <p className="mt-2 text-sm font-medium text-red-600">{errors.message}</p>}
              </div>

              <div className="grid gap-4 md:grid-cols-2">
                <div>
                  <label className="mb-2 block text-sm font-semibold text-gray-700">Priority</label>
                  <select
                    value={priority}
                    onChange={(event) => setPriority(event.target.value as InquiryPriority)}
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white px-3 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  >
                    <option value="Low">Low</option>
                    <option value="Normal">Normal</option>
                    <option value="High">High</option>
                  </select>
                </div>
                <div>
                  <label className="mb-2 block text-sm font-semibold text-gray-700">Optional Contact Number</label>
                  <input
                    type="tel"
                    value={contactNumber}
                    onChange={(event) => setContactNumber(event.target.value)}
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                    placeholder="+63 912 345 6789"
                  />
                </div>
              </div>

              <button
                type="submit"
                className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30] md:w-auto"
              >
                <MessageSquare className="h-4 w-4" />
                Submit Inquiry
              </button>
            </form>
          </section>

          <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
            <div className="border-b border-stone-200 px-5 py-5 md:px-6">
              <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                  <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    Support Tickets
                  </p>
                  <h2 className="mt-1 text-xl font-display">Recent Inquiries</h2>
                </div>
                <div className="text-sm font-medium text-gray-500">
                  {filteredInquiries.length} result{filteredInquiries.length === 1 ? "" : "s"}
                </div>
              </div>

              <div className="mt-5 grid gap-3 border-t border-stone-100 pt-4 lg:grid-cols-[minmax(220px,1fr)_180px_240px]">
                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(event) => setSearchQuery(event.target.value)}
                    placeholder="Search inquiries"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <select
                  value={statusFilter}
                  onChange={(event) => setStatusFilter(event.target.value as "All" | InquiryStatus)}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-3 text-sm font-medium text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
                  <option value="All">All Statuses</option>
                  <option value="Pending">Pending</option>
                  <option value="In Review">In Review</option>
                  <option value="Resolved">Resolved</option>
                </select>

                <select
                  value={typeFilter}
                  onChange={(event) => setTypeFilter(event.target.value as "All" | ConcernType)}
                  className="h-11 rounded-lg border border-stone-200 bg-white px-3 text-sm font-medium text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
                  <option value="All">All Concern Types</option>
                  {concernTypes.map((type) => (
                    <option key={type} value={type}>
                      {type}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-stone-50">
                  <tr>
                    <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Ticket ID</th>
                    <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Subject</th>
                    <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Date</th>
                    <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">Priority</th>
                    <th className="px-6 py-4 text-right text-sm font-semibold text-gray-600">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredInquiries.length === 0 ? (
                    <tr>
                      <td colSpan={6} className="px-6 py-12 text-center text-sm text-gray-500">
                        No inquiries found.
                      </td>
                    </tr>
                  ) : (
                    filteredInquiries.map((inquiry) => (
                      <tr key={inquiry.id} className="border-t border-stone-100 transition-all hover:bg-green-50/30">
                        <td className="px-6 py-4 text-sm font-semibold text-gray-950">{inquiry.id}</td>
                        <td className="min-w-[260px] px-6 py-4">
                          <p className="text-sm font-semibold text-gray-950">{inquiry.subject}</p>
                          <p className="mt-1 text-xs text-gray-500">{inquiry.type}</p>
                        </td>
                        <td className="px-6 py-4 text-sm text-gray-500">{inquiry.dateSubmitted}</td>
                        <td className="px-6 py-4">
                          <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-semibold ${statusColors[inquiry.status]}`}>
                            {inquiry.status}
                          </span>
                        </td>
                        <td className="px-6 py-4">
                          <span className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${priorityColors[inquiry.priority]}`}>
                            {inquiry.priority}
                          </span>
                        </td>
                        <td className="px-6 py-4">
                          <div className="flex justify-end">
                            <button
                              onClick={() => setSelectedInquiry(inquiry)}
                              className="inline-flex items-center gap-2 rounded-lg border border-stone-200 px-3 py-2 text-sm font-semibold text-primary transition-all hover:bg-green-50"
                            >
                              <Eye className="h-4 w-4" />
                              View
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
        </div>
      </main>

      {selectedInquiry && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4"
          onClick={() => setSelectedInquiry(null)}
        >
          <div
            className="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between gap-4 border-b border-stone-200 px-6 py-5">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">{selectedInquiry.id}</p>
                <h2 className="mt-1 text-2xl font-display text-gray-950">{selectedInquiry.subject}</h2>
              </div>
              <button
                onClick={() => setSelectedInquiry(null)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
                aria-label="Close inquiry details"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="space-y-5 px-6 py-6">
              <div className="flex flex-wrap gap-2">
                <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-semibold ${statusColors[selectedInquiry.status]}`}>
                  {selectedInquiry.status}
                </span>
                <span className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${priorityColors[selectedInquiry.priority]}`}>
                  {selectedInquiry.priority}
                </span>
              </div>

              <div className="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 px-4 py-4 text-sm md:grid-cols-2">
                <div>
                  <p className="text-gray-500">Concern Type</p>
                  <p className="font-semibold text-gray-950">{selectedInquiry.type}</p>
                </div>
                <div>
                  <p className="text-gray-500">Date Submitted</p>
                  <p className="font-semibold text-gray-950">{selectedInquiry.dateSubmitted}</p>
                </div>
                <div>
                  <p className="text-gray-500">Status</p>
                  <p className="font-semibold text-gray-950">{selectedInquiry.status}</p>
                </div>
                <div>
                  <p className="text-gray-500">Contact Number</p>
                  <p className="font-semibold text-gray-950">{selectedInquiry.contactNumber || "Not provided"}</p>
                </div>
              </div>

              <div className="rounded-lg border border-stone-200 bg-white px-4 py-4">
                <div className="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <HelpCircle className="h-4 w-4 text-primary" />
                  Message
                </div>
                <p className="text-sm leading-7 text-gray-600">{selectedInquiry.message}</p>
              </div>
            </div>

            <div className="flex justify-end border-t border-stone-200 px-6 py-5">
              <button
                onClick={() => setSelectedInquiry(null)}
                className="inline-flex h-11 items-center justify-center rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
