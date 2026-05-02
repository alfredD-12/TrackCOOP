import { useMemo, useState, useRef, type ElementType } from "react";
import {
  Archive,
  ArrowRight,
  Brain,
  CalendarDays,
  CheckCircle2,
  Clock,
  Database,
  Download,
  Eye,
  FileCheck2,
  FileText,
  FolderOpen,
  Loader2,
  ScanText,
  Search,
  Sparkles,
  Upload,
  X,
  Check,
} from "lucide-react";

type DocumentCategory =
  | "membership_forms"
  | "financial_records"
  | "meeting_minutes"
  | "project_documents"
  | "compliance_documents"
  | "announcements";

type ProcessingStage =
  | "uploading"
  | "ocr"
  | "nlp"
  | "classifying"
  | "complete";

type ToastState = "idle" | "processing" | "complete";

interface Document {
  id: string;
  name: string;
  type: string;
  size: string;
  date: string;
  dateIso: string;
  category: DocumentCategory;
  uploadedBy: string;
}

interface DemoFile {
  name: string;
  type: string;
  size: string;
  category: DocumentCategory;
  confidence: number;
  extractedPreview: string;
}

const heroImage =
  "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=2400";

const categoryLabels: Record<DocumentCategory, string> = {
  membership_forms: "Membership Forms",
  financial_records: "Financial Records",
  meeting_minutes: "Meeting Minutes",
  project_documents: "Project Documents",
  compliance_documents: "Compliance Documents",
  announcements: "Announcements",
};

const categoryColors: Record<DocumentCategory, string> = {
  membership_forms: "bg-blue-50 text-blue-700 ring-blue-100",
  financial_records: "bg-green-50 text-green-700 ring-green-100",
  meeting_minutes: "bg-purple-50 text-purple-700 ring-purple-100",
  project_documents: "bg-orange-50 text-orange-700 ring-orange-100",
  compliance_documents: "bg-red-50 text-red-700 ring-red-100",
  announcements: "bg-cyan-50 text-cyan-700 ring-cyan-100",
};

const categoryDescriptions: Record<DocumentCategory, string> = {
  membership_forms: "Member intake and registration record",
  financial_records: "Finance and share capital record",
  meeting_minutes: "Meeting notes and board decisions",
  project_documents: "Project files and progress updates",
  compliance_documents: "Compliance and certification record",
  announcements: "Notice and cooperative bulletin",
};

const categoryIcons: Record<DocumentCategory, ElementType> = {
  membership_forms: FileCheck2,
  financial_records: Database,
  meeting_minutes: Clock,
  project_documents: Archive,
  compliance_documents: FolderOpen,
  announcements: Sparkles,
};

const demoFiles: DemoFile[] = [
  {
    name: "Member-Application-Batch-0426.pdf",
    type: "PDF",
    size: "1.8 MB",
    category: "membership_forms",
    confidence: 94,
    extractedPreview:
      "Applicant records include full name, household information, address, cooperative sector, contact number, and membership consent confirmation.",
  },
  {
    name: "Cooperative-Onboarding-Forms-April.pdf",
    type: "PDF",
    size: "1.6 MB",
    category: "membership_forms",
    confidence: 92,
    extractedPreview:
      "The document contains several member intake forms, identity details, family information, and initial share capital acknowledgement.",
  },
  {
    name: "New-Member-Verification-Packet.pdf",
    type: "PDF",
    size: "1.9 MB",
    category: "membership_forms",
    confidence: 95,
    extractedPreview:
      "Detected member application fields, barangay residence, occupation, dependent information, and signature blocks for review.",
  },
];

const processingStages: Array<{
  id: ProcessingStage;
  label: string;
  description: string;
  progress: number;
}> = [
  {
    id: "uploading",
    label: "Uploading",
    description: "Sending document to the cooperative document workspace.",
    progress: 18,
  },
  {
    id: "ocr",
    label: "OCR extraction",
    description: "Extracting text from scanned pages and form fields.",
    progress: 44,
  },
  {
    id: "nlp",
    label: "NLP analysis",
    description: "Detecting names, sections, intent, and document signals.",
    progress: 70,
  },
  {
    id: "classifying",
    label: "Auto-categorizing",
    description: "Matching the document to repository categories.",
    progress: 90,
  },
  {
    id: "complete",
    label: "Complete",
    description: "Document processed and added to the repository.",
    progress: 100,
  },
];

const initialDocuments: Document[] = [
  {
    id: "1",
    name: "Annual Financial Report 2025",
    type: "PDF",
    size: "2.4 MB",
    date: "Apr 10, 2026",
    dateIso: "2026-04-10",
    category: "financial_records",
    uploadedBy: "Maria Santos",
  },
  {
    id: "2",
    name: "Board Meeting Minutes - March",
    type: "PDF",
    size: "156 KB",
    date: "Apr 8, 2026",
    dateIso: "2026-04-08",
    category: "meeting_minutes",
    uploadedBy: "Juan Dela Cruz",
  },
  {
    id: "3",
    name: "New Member Application - Rosa Garcia",
    type: "PDF",
    size: "890 KB",
    date: "Apr 5, 2026",
    dateIso: "2026-04-05",
    category: "membership_forms",
    uploadedBy: "Pedro Reyes",
  },
  {
    id: "4",
    name: "Q1 Project Status Report",
    type: "PDF",
    size: "1.2 MB",
    date: "Apr 1, 2026",
    dateIso: "2026-04-01",
    category: "project_documents",
    uploadedBy: "Ana Lopez",
  },
  {
    id: "5",
    name: "Compliance Certificate 2026",
    type: "PDF",
    size: "3.1 MB",
    date: "Mar 28, 2026",
    dateIso: "2026-03-28",
    category: "compliance_documents",
    uploadedBy: "Carlos Ramos",
  },
  {
    id: "6",
    name: "Annual General Meeting Notice",
    type: "PDF",
    size: "450 KB",
    date: "Mar 25, 2026",
    dateIso: "2026-03-25",
    category: "announcements",
    uploadedBy: "Maria Santos",
  },
];

const wait = (duration: number) =>
  new Promise((resolve) => setTimeout(resolve, duration));

export default function Documents() {
  const [documents, setDocuments] = useState<Document[]>(initialDocuments);
  const [selectedCategory, setSelectedCategory] = useState<
    DocumentCategory | "all"
  >("all");
  const [nameFilter, setNameFilter] = useState("");
  const [dateFromFilter, setDateFromFilter] = useState("");
  const [dateToFilter, setDateToFilter] = useState("");
  const [showUploadModal, setShowUploadModal] = useState(false);
  const [showMetadataModal, setShowMetadataModal] = useState(false);
  const [selectedDocument, setSelectedDocument] = useState<Document | null>(
    null
  );
  // Download toast state
  type DownloadStage = "idle" | "preparing" | "downloading" | "done";
  const [downloadStage, setDownloadStage] = useState<DownloadStage>("idle");
  const [downloadProgress, setDownloadProgress] = useState(0);
  const [downloadLabel, setDownloadLabel] = useState("");
  const dlProgressTimer = useRef<ReturnType<typeof setInterval> | null>(null);
  const [toastState, setToastState] = useState<ToastState>("idle");
  const [processingStage, setProcessingStage] =
    useState<ProcessingStage>("uploading");
  const [processedDocument, setProcessedDocument] = useState<Document | null>(
    null
  );
  const [processedFile, setProcessedFile] = useState<DemoFile | null>(null);
  const [demoFile, setDemoFile] = useState<DemoFile>(demoFiles[0]);

  const stageDetails =
    processingStages.find((stage) => stage.id === processingStage) ??
    processingStages[0];

  const filteredDocuments = documents.filter((doc) => {
    const matchesCategory =
      selectedCategory === "all" || doc.category === selectedCategory;
    const matchesName = doc.name
      .toLowerCase()
      .includes(nameFilter.trim().toLowerCase());
    const matchesDateFrom =
      !dateFromFilter || doc.dateIso >= dateFromFilter;
    const matchesDateTo = !dateToFilter || doc.dateIso <= dateToFilter;

    return matchesCategory && matchesName && matchesDateFrom && matchesDateTo;
  });

  const categoryCounts = useMemo(
    () =>
      (Object.keys(categoryLabels) as DocumentCategory[]).reduce(
        (counts, category) => ({
          ...counts,
          [category]: documents.filter((doc) => doc.category === category)
            .length,
        }),
        {} as Record<DocumentCategory, number>
      ),
    [documents]
  );

  const openDemoUpload = () => {
    const nextFile = demoFiles[Math.floor(Math.random() * demoFiles.length)];
    setDemoFile(nextFile);
    setShowUploadModal(true);
  };

  const startDemoProcessing = async () => {
    const fileToProcess = demoFile;
    setShowUploadModal(false);
    setProcessedDocument(null);
    setProcessedFile(null);
    setProcessingStage("uploading");
    setToastState("processing");

    for (const stage of processingStages) {
      setProcessingStage(stage.id);
      await wait(stage.id === "complete" ? 350 : 1150);
    }

    const date = new Date().toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: "numeric",
    });
    const dateIso = new Date().toISOString().slice(0, 10);
    const newDoc: Document = {
      id: `demo-${Date.now()}`,
      name: fileToProcess.name,
      type: fileToProcess.type,
      size: fileToProcess.size,
      date,
      dateIso,
      category: fileToProcess.category,
      uploadedBy: "Current User",
    };

    setDocuments((current) => [newDoc, ...current]);
    setProcessedDocument(newDoc);
    setProcessedFile(fileToProcess);
    setToastState("complete");
  };

  const dismissCompletion = () => {
    setToastState("idle");
  };

  const openMetadata = () => {
    setShowMetadataModal(true);
    setToastState("idle");
  };

  const handleDownload = (doc: Document) => {
    if (downloadStage !== "idle") return;
    setDownloadLabel(doc.name);
    setDownloadProgress(0);
    setDownloadStage("preparing");

    setTimeout(() => {
      setDownloadStage("downloading");
      setDownloadProgress(0);
      let progress = 0;
      dlProgressTimer.current = setInterval(() => {
        progress += Math.random() * 18 + 8;
        if (progress >= 100) {
          progress = 100;
          clearInterval(dlProgressTimer.current!);
          setDownloadProgress(100);
          setDownloadStage("done");
          setTimeout(() => {
            setDownloadStage("idle");
            setDownloadProgress(0);
          }, 3000);
        } else {
          setDownloadProgress(Math.round(progress));
        }
      }, 200);
    }, 1000);
  };

  const clearFilters = () => {
    setSelectedCategory("all");
    setNameFilter("");
    setDateFromFilter("");
    setDateToFilter("");
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
                  Documents
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Cooperative Records Repository
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Review uploads and organized records in one place.
                </p>
              </div>

              <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center lg:justify-end">
                <button
                  onClick={openDemoUpload}
                  disabled={toastState === "processing"}
                  className="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0"
                >
                  <Upload className="h-4 w-4" />
                  Upload Document
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <section className="mb-6 grid gap-5 md:grid-cols-3">
          <article className="animate-in fade-in slide-in-from-bottom-3 rounded-lg border border-green-100 bg-green-50 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary text-white shadow-sm">
              <FileText className="h-6 w-6" />
            </div>
            <p className="text-sm font-semibold text-gray-600">Total Files</p>
            <p className="mt-2 font-display text-4xl font-bold text-gray-950">
              {documents.length}
            </p>
          </article>

          <article className="animate-in fade-in slide-in-from-bottom-3 rounded-lg border border-blue-100 bg-blue-50 p-5 shadow-sm transition-all delay-75 duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-700 text-white shadow-sm">
              <Brain className="h-6 w-6" />
            </div>
            <p className="text-sm font-semibold text-gray-600">
              Prototype Pipeline
            </p>
            <p className="mt-2 font-display text-4xl font-bold text-gray-950">
              4
            </p>
          </article>

          <article className="animate-in fade-in slide-in-from-bottom-3 rounded-lg border border-amber-100 bg-amber-50 p-5 shadow-sm transition-all delay-150 duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-amber-600 text-white shadow-sm">
              <FileCheck2 className="h-6 w-6" />
            </div>
            <p className="text-sm font-semibold text-gray-600">
              Latest Category
            </p>
            <p className="mt-2 font-display text-3xl font-bold text-gray-950">
              {processedDocument
                ? categoryLabels[processedDocument.category]
                : "Membership Forms"}
            </p>
          </article>
        </section>

        <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Document Repository
                </p>
                <h2 className="mt-1 font-display text-2xl font-bold text-gray-950">
                  Files and Auto Categories
                </h2>
              </div>
              <button
                onClick={openDemoUpload}
                disabled={toastState === "processing"}
                className="inline-flex items-center justify-center gap-2 rounded-lg border border-green-100 bg-green-50 px-4 py-2.5 font-semibold text-primary transition-all hover:-translate-y-0.5 hover:border-primary/30 hover:bg-green-100 disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0"
              >
                <Upload className="h-4 w-4" />
                Demo Upload
              </button>
            </div>

            <div className="mt-5 border-t border-stone-100 pt-4">
              <div className="mt-4 grid gap-3 xl:grid-cols-[minmax(220px,0.9fr)_minmax(260px,1fr)_auto_auto] xl:items-center">
                <div className="relative">
                  <FolderOpen className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <select
                    value={selectedCategory}
                    onChange={(event) =>
                      setSelectedCategory(
                        event.target.value as DocumentCategory | "all"
                      )
                    }
                    className="h-11 w-full appearance-none rounded-lg border border-stone-200 bg-white pl-10 pr-10 text-sm font-semibold text-gray-700 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                    aria-label="Document type"
                  >
                    <option value="all">All document types ({documents.length})</option>
                    {(Object.keys(categoryLabels) as DocumentCategory[]).map(
                      (category) => (
                        <option key={category} value={category}>
                          {categoryLabels[category]} ({categoryCounts[category]})
                        </option>
                      )
                    )}
                  </select>
                </div>

                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    value={nameFilter}
                    onChange={(event) => setNameFilter(event.target.value)}
                    placeholder="Search files"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <div className="grid gap-2 rounded-lg border border-stone-200 bg-white p-1.5 sm:grid-cols-[auto_144px_auto_144px] sm:items-center">
                  <span className="inline-flex items-center gap-2 px-2 text-sm font-semibold text-gray-500">
                    <CalendarDays className="h-4 w-4" />
                  </span>
                  <input
                    type="date"
                    value={dateFromFilter}
                    onChange={(event) => {
                      const value = event.target.value;
                      setDateFromFilter(value);
                      if (dateToFilter && dateToFilter < value) {
                        setDateToFilter(value);
                      }
                    }}
                    className="h-9 rounded-md border border-stone-100 bg-stone-50 px-3 text-sm outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                    aria-label="Uploaded from date"
                  />
                  <span className="hidden text-center text-sm font-semibold text-gray-400 sm:block">
                    to
                  </span>
                  <input
                    type="date"
                    value={dateToFilter}
                    min={dateFromFilter || undefined}
                    onChange={(event) => setDateToFilter(event.target.value)}
                    className="h-9 rounded-md border border-stone-100 bg-stone-50 px-3 text-sm outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                    aria-label="Uploaded to date"
                  />
                </div>

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
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    File Name
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    Category
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    Upload Date
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    Uploaded By
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody
                key={`${selectedCategory}-${nameFilter}-${dateFromFilter}-${dateToFilter}-${documents.length}`}
                className="animate-in fade-in duration-300"
              >
                {filteredDocuments.length === 0 ? (
                  <tr>
                    <td
                      colSpan={5}
                      className="px-6 py-14 text-center text-gray-500"
                    >
                      No documents found in this category.
                    </td>
                  </tr>
                ) : (
                  filteredDocuments.map((doc, index) => {
                    const Icon = categoryIcons[doc.category];

                    return (
                      <tr
                        key={doc.id}
                        className="border-t border-stone-100 transition-all duration-300 animate-in fade-in slide-in-from-bottom-2 hover:bg-green-50/50"
                        style={{ animationDelay: `${Math.min(index * 35, 240)}ms` }}
                      >
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-3">
                            <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-green-50 text-primary">
                              <FileText className="h-5 w-5" />
                            </span>
                            <div>
                              <p className="font-semibold text-gray-950">
                                {doc.name}
                              </p>
                              <p className="text-xs text-gray-500">
                                {doc.type} - {doc.size} /{" "}
                                {categoryDescriptions[doc.category]}
                              </p>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4">
                          <span
                            className={`inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold ring-1 ${categoryColors[doc.category]}`}
                          >
                            <Icon className="h-4 w-4" />
                            {categoryLabels[doc.category]}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-gray-600">{doc.date}</td>
                        <td className="px-6 py-4 text-gray-600">
                          {doc.uploadedBy}
                        </td>
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-2">
                            <button
                              onClick={() => setSelectedDocument(doc)}
                              className="rounded-lg p-2 text-gray-500 transition-all hover:-translate-y-0.5 hover:bg-stone-100 hover:text-primary"
                              title="View"
                              aria-label={`View ${doc.name}`}
                            >
                              <Eye className="h-4 w-4" />
                            </button>
                            <button
                              onClick={() => handleDownload(doc)}
                              className="rounded-lg p-2 text-gray-500 transition-all hover:-translate-y-0.5 hover:bg-stone-100 hover:text-primary"
                              title="Download"
                              aria-label={`Download ${doc.name}`}
                            >
                              <Download className="h-4 w-4" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        </section>
      </main>

      {toastState !== "idle" && (
        <div className="fixed right-5 top-5 z-[90] w-[calc(100vw-2.5rem)] max-w-md animate-in fade-in slide-in-from-top-3 duration-300">
          <div className="overflow-hidden rounded-lg border border-white/50 bg-white shadow-2xl">
            <div className="p-5">
              <div className="flex items-start gap-4">
                <span
                  className={`flex h-11 w-11 shrink-0 items-center justify-center rounded-lg ${
                    toastState === "complete"
                      ? "bg-green-100 text-green-700"
                      : "bg-primary text-white"
                  }`}
                >
                  {toastState === "complete" ? (
                    <CheckCircle2 className="h-6 w-6" />
                  ) : (
                    <Loader2 className="h-6 w-6 animate-spin" />
                  )}
                </span>
                <div className="min-w-0 flex-1">
                  <div className="flex items-start justify-between gap-3">
                    <div>
                      <p className="font-display text-xl font-bold text-gray-950">
                        {toastState === "complete"
                          ? "Upload Complete"
                          : stageDetails.label}
                      </p>
                      <p className="mt-1 text-sm leading-6 text-gray-600">
                        {toastState === "complete"
                          ? `${processedDocument?.name ?? "Document"} has been categorized and added to the repository.`
                          : stageDetails.description}
                      </p>
                    </div>
                    {toastState === "complete" && (
                      <button
                        onClick={dismissCompletion}
                        className="rounded-lg p-1.5 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800"
                        aria-label="Dismiss upload notification"
                      >
                        <X className="h-4 w-4" />
                      </button>
                    )}
                  </div>

                  <div className="mt-4 h-2 overflow-hidden rounded-full bg-stone-100">
                    <div
                      className="h-full rounded-full bg-primary transition-all duration-500"
                      style={{ width: `${stageDetails.progress}%` }}
                    />
                  </div>

                  {toastState === "complete" && (
                    <div className="mt-4 grid gap-2 sm:grid-cols-2">
                      <button
                        onClick={dismissCompletion}
                        className="rounded-lg border border-stone-200 bg-white px-4 py-2.5 font-semibold text-gray-700 transition-all hover:bg-stone-50"
                      >
                        Done
                      </button>
                      <button
                        onClick={openMetadata}
                        className="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-semibold text-white transition-all hover:bg-green-800"
                      >
                        Show Metadata
                        <ArrowRight className="h-4 w-4" />
                      </button>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Download Progress Toast */}
      {downloadStage !== "idle" && (
        <div className="fixed bottom-6 right-6 z-50 w-80 rounded-xl border border-green-200 bg-white shadow-2xl p-5 animate-in slide-in-from-bottom-5 fade-in duration-300">
          <div className="flex items-start gap-3">
            <div className={`mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full transition-colors ${
              downloadStage === "done" ? "bg-green-100" : "bg-stone-100"
            }`}>
              {downloadStage === "done" ? (
                <Check className="h-5 w-5 text-green-600" />
              ) : (
                <Loader2 className="h-5 w-5 animate-spin text-primary" />
              )}
            </div>

            <div className="flex-1 min-w-0">
              <p className="font-semibold text-sm truncate">{downloadLabel}</p>
              <p className="text-xs text-gray-500 mt-0.5">
                {downloadStage === "preparing" && "Preparing your file…"}
                {downloadStage === "downloading" && `Downloading — ${downloadProgress}%`}
                {downloadStage === "done" && "Download complete!"}
              </p>

              {downloadStage !== "done" && (
                <div className="mt-2 h-1.5 w-full rounded-full bg-stone-100 overflow-hidden">
                  <div
                    className="h-full rounded-full bg-primary transition-all duration-200"
                    style={{ width: downloadStage === "preparing" ? "12%" : `${downloadProgress}%` }}
                  />
                </div>
              )}
            </div>

            <button
              onClick={() => {
                clearInterval(dlProgressTimer.current!);
                setDownloadStage("idle");
              }}
              className="shrink-0 p-1 rounded hover:bg-stone-100 transition-colors"
            >
              <X className="h-4 w-4 text-gray-400" />
            </button>
          </div>
        </div>
      )}

      {showUploadModal && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm animate-in fade-in duration-200"
          onClick={() => setShowUploadModal(false)}
          role="dialog"
          aria-modal="true"
          aria-labelledby="demo-upload-title"
        >
          <div
            className="w-full max-w-xl overflow-hidden rounded-lg bg-white shadow-2xl animate-in zoom-in-95 duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between border-b border-stone-200 p-6">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Attached File
                </p>
                <h2
                  id="demo-upload-title"
                  className="mt-1 font-display text-2xl font-bold text-gray-950"
                >
                  Ready for Prototype Processing
                </h2>
                <p className="mt-2 text-sm leading-6 text-gray-600">
                  A sample file is ready.
                </p>
              </div>
              <button
                onClick={() => setShowUploadModal(false)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800"
                aria-label="Close upload modal"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="p-6">
              <div className="rounded-lg border border-green-100 bg-green-50 p-5">
                <div className="flex items-start gap-4">
                  <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary text-white shadow-sm">
                    <FileText className="h-6 w-6" />
                  </span>
                  <div className="min-w-0 flex-1">
                    <p className="font-semibold text-gray-950">
                      {demoFile.name}
                    </p>
                    <p className="mt-1 text-sm text-gray-600">
                      {demoFile.type} - {demoFile.size}
                    </p>
                    <p className="mt-3 inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold text-primary shadow-sm">
                      Demo file attached
                    </p>
                  </div>
                </div>
              </div>

              <div className="mt-5 grid gap-3 sm:grid-cols-2">
                <button
                  type="button"
                  onClick={() => setShowUploadModal(false)}
                  className="rounded-lg border border-stone-200 bg-white px-5 py-3 font-semibold text-gray-700 transition-all hover:bg-stone-50"
                >
                  Cancel
                </button>
                <button
                  type="button"
                  onClick={startDemoProcessing}
                  className="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800 hover:shadow-md"
                >
                  Start Processing
                  <ArrowRight className="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {showMetadataModal && processedDocument && processedFile && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm animate-in fade-in duration-200"
          onClick={() => setShowMetadataModal(false)}
          role="dialog"
          aria-modal="true"
          aria-labelledby="metadata-title"
        >
          <div
            className="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-2xl animate-in zoom-in-95 duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="border-b border-stone-200 bg-green-50 p-6">
              <div className="flex items-start justify-between gap-4">
                <div>
                  <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    Extracted Metadata
                  </p>
                  <h2
                    id="metadata-title"
                    className="mt-1 font-display text-2xl font-bold text-gray-950"
                  >
                    {processedDocument.name}
                  </h2>
                  <p className="mt-2 text-sm text-gray-600">
                    Prototype OCR and NLP results from the completed upload.
                  </p>
                </div>
                <button
                  onClick={() => setShowMetadataModal(false)}
                  className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-white hover:text-gray-800"
                  aria-label="Close metadata modal"
                >
                  <X className="h-5 w-5" />
                </button>
              </div>
            </div>

            <div className="space-y-5 p-6">
              <div className="grid gap-3 sm:grid-cols-2">
                {[
                  ["File Type", processedDocument.type],
                  ["File Size", processedDocument.size],
                  ["Upload Date", processedDocument.date],
                  ["Uploaded By", processedDocument.uploadedBy],
                ].map(([label, value]) => (
                  <div
                    key={label}
                    className="rounded-lg border border-stone-200 bg-stone-50 p-4"
                  >
                    <p className="text-xs font-bold uppercase tracking-[0.14em] text-gray-500">
                      {label}
                    </p>
                    <p className="mt-1 font-semibold text-gray-950">{value}</p>
                  </div>
                ))}
              </div>

              <div className="grid gap-3 sm:grid-cols-3">
                <div className="rounded-lg border border-green-100 bg-green-50 p-4">
                  <ScanText className="mb-3 h-5 w-5 text-primary" />
                  <p className="text-sm font-semibold text-gray-950">
                    OCR Status
                  </p>
                  <p className="mt-1 text-sm text-gray-600">
                    Text extracted successfully
                  </p>
                </div>
                <div className="rounded-lg border border-blue-100 bg-blue-50 p-4">
                  <Brain className="mb-3 h-5 w-5 text-blue-700" />
                  <p className="text-sm font-semibold text-gray-950">
                    NLP Status
                  </p>
                  <p className="mt-1 text-sm text-gray-600">
                    Entities and document intent detected
                  </p>
                </div>
                <div className="rounded-lg border border-amber-100 bg-amber-50 p-4">
                  <FileCheck2 className="mb-3 h-5 w-5 text-amber-700" />
                  <p className="text-sm font-semibold text-gray-950">
                    Auto Category
                  </p>
                  <p className="mt-1 text-sm text-gray-600">
                    {categoryLabels[processedFile.category]} -{" "}
                    {processedFile.confidence}% confidence
                  </p>
                </div>
              </div>

              <div className="rounded-lg border border-stone-200 bg-white p-5">
                <p className="mb-2 text-sm font-semibold text-gray-950">
                  Extracted Preview
                </p>
                <p className="leading-7 text-gray-600">
                  {processedFile.extractedPreview}
                </p>
              </div>

              <button
                onClick={() => setShowMetadataModal(false)}
                className="w-full rounded-lg bg-primary px-5 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800 hover:shadow-md"
              >
                Done
              </button>
            </div>
          </div>
        </div>
      )}

      {selectedDocument && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm animate-in fade-in duration-200"
          onClick={() => setSelectedDocument(null)}
          role="dialog"
          aria-modal="true"
          aria-labelledby="document-preview-title"
        >
          <div
            className="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-lg bg-white shadow-2xl animate-in zoom-in-95 duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between border-b border-stone-200 bg-stone-50 p-6">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Preview
                </p>
                <h2
                  id="document-preview-title"
                  className="mt-1 font-display text-2xl font-bold text-gray-950"
                >
                  {selectedDocument.name}
                </h2>
                <p className="mt-2 text-sm text-gray-600">
                  {categoryDescriptions[selectedDocument.category]}
                </p>
              </div>
              <button
                onClick={() => setSelectedDocument(null)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-white hover:text-gray-800"
                aria-label="Close preview"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <div className="overflow-y-auto p-5 md:p-6">
              <div className="rounded-lg border border-stone-200 bg-stone-50 p-4">
                <div className="mb-4 flex items-center justify-between">
                  <p className="text-sm font-semibold text-gray-950">
                    Document View
                  </p>
                  <span className="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-600 shadow-sm">
                    Demo
                  </span>
                </div>
                <div className="flex h-[280px] items-center justify-center rounded-lg border border-dashed border-stone-300 bg-white sm:h-[360px] lg:h-[420px]">
                  <div className="text-center">
                    <FileText className="mx-auto h-16 w-16 text-stone-300" />
                    <p className="mt-4 font-display text-4xl font-bold text-gray-900">
                      Document View
                    </p>
                    <p className="mt-2 text-sm text-gray-500">
                      Prototype preview canvas
                    </p>
                  </div>
                </div>
              </div>

              <div className="mt-4 grid gap-3 md:grid-cols-3">
                <div className="rounded-lg border border-green-100 bg-green-50 p-5">
                  <div className="flex items-start gap-4">
                    <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary text-white">
                      <FileText className="h-6 w-6" />
                    </span>
                    <div>
                      <p className="font-semibold text-gray-950">
                        {selectedDocument.type} - {selectedDocument.size}
                      </p>
                      <p className="mt-1 text-sm leading-6 text-gray-600">
                        {selectedDocument.date}
                        <br />
                        {selectedDocument.uploadedBy}
                      </p>
                    </div>
                  </div>
                </div>

                <div className="rounded-lg border border-stone-200 bg-white p-4">
                  <p className="text-xs font-bold uppercase tracking-[0.14em] text-gray-500">
                    Category
                  </p>
                  <p className="mt-1 font-semibold text-gray-950">
                    {categoryLabels[selectedDocument.category]}
                  </p>
                </div>

                <div className="rounded-lg border border-stone-200 bg-white p-4">
                  <p className="text-xs font-bold uppercase tracking-[0.14em] text-gray-500">
                    Status
                  </p>
                  <p className="mt-1 font-semibold text-gray-950">
                    Available
                  </p>
                </div>
              </div>

              <button
                onClick={() => setSelectedDocument(null)}
                className="mt-4 w-full rounded-lg bg-primary px-5 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800 hover:shadow-md"
              >
                Done
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
