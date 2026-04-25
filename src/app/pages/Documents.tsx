import { useState, useRef } from "react";
import { FileText, Download, Eye, Upload, CheckCircle, Loader2, X, AlertCircle } from "lucide-react";
import { formatFileSize, validateFileSize, validateOCRFileType } from "../../utils/formatters";

type DocumentCategory = "membership_forms" | "financial_records" | "meeting_minutes" | "project_documents" | "compliance_documents" | "announcements";

type ProcessingStep = "uploading" | "extracting" | "classifying" | "done" | null;

interface Document {
  id: string;
  name: string;
  type: string;
  size: string;
  date: string;
  category: DocumentCategory;
  uploadedBy: string;
}

const categoryLabels: Record<DocumentCategory, string> = {
  membership_forms: "Membership Forms",
  financial_records: "Financial Records",
  meeting_minutes: "Meeting Minutes",
  project_documents: "Project Documents",
  compliance_documents: "Compliance Documents",
  announcements: "Announcements",
};

const categoryColors: Record<DocumentCategory, string> = {
  membership_forms: "bg-blue-100 text-blue-700",
  financial_records: "bg-green-100 text-green-700",
  meeting_minutes: "bg-purple-100 text-purple-700",
  project_documents: "bg-orange-100 text-orange-700",
  compliance_documents: "bg-red-100 text-red-700",
  announcements: "bg-cyan-100 text-cyan-700",
};

export default function Documents() {
  const [documents, setDocuments] = useState<Document[]>([
    { id: "1", name: "Annual Financial Report 2025", type: "PDF", size: "2.4 MB", date: "Apr 10, 2026", category: "financial_records", uploadedBy: "Maria Santos" },
    { id: "2", name: "Board Meeting Minutes - March", type: "PDF", size: "156 KB", date: "Apr 8, 2026", category: "meeting_minutes", uploadedBy: "Juan Dela Cruz" },
    { id: "3", name: "New Member Application - Rosa Garcia", type: "PDF", size: "890 KB", date: "Apr 5, 2026", category: "membership_forms", uploadedBy: "Pedro Reyes" },
    { id: "4", name: "Q1 Project Status Report", type: "PDF", size: "1.2 MB", date: "Apr 1, 2026", category: "project_documents", uploadedBy: "Ana Lopez" },
    { id: "5", name: "Compliance Certificate 2026", type: "PDF", size: "3.1 MB", date: "Mar 28, 2026", category: "compliance_documents", uploadedBy: "Carlos Ramos" },
    { id: "6", name: "Annual General Meeting Notice", type: "PDF", size: "450 KB", date: "Mar 25, 2026", category: "announcements", uploadedBy: "Maria Santos" },
  ]);

  const [selectedCategory, setSelectedCategory] = useState<DocumentCategory | "all">("all");
  const [isDragging, setIsDragging] = useState(false);
  const [processingStep, setProcessingStep] = useState<ProcessingStep>(null);
  const [uploadingFile, setUploadingFile] = useState<string | null>(null);
  const [uploadError, setUploadError] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const MAX_FILE_SIZE_MB = 2;

  const simulateOCRProcessing = async (file: File) => {
    setUploadingFile(file.name);

    // Step 1: Uploading
    setProcessingStep("uploading");
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Step 2: Extracting Text
    setProcessingStep("extracting");
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Step 3: Classifying
    setProcessingStep("classifying");
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Step 4: Done
    setProcessingStep("done");

    // Randomly assign a category based on file name
    const categories: DocumentCategory[] = [
      "membership_forms",
      "financial_records",
      "meeting_minutes",
      "project_documents",
      "compliance_documents",
      "announcements"
    ];

    let category: DocumentCategory = "project_documents";
    const fileName = file.name.toLowerCase();
    if (fileName.includes("member") || fileName.includes("application")) {
      category = "membership_forms";
    } else if (fileName.includes("financial") || fileName.includes("budget") || fileName.includes("revenue")) {
      category = "financial_records";
    } else if (fileName.includes("meeting") || fileName.includes("minutes")) {
      category = "meeting_minutes";
    } else if (fileName.includes("compliance") || fileName.includes("certificate")) {
      category = "compliance_documents";
    } else if (fileName.includes("announcement") || fileName.includes("notice")) {
      category = "announcements";
    }

    // Add to documents
    const newDoc: Document = {
      id: Date.now().toString(),
      name: file.name,
      type: file.type.includes("pdf") ? "PDF" : "Image",
      size: `${(file.size / 1024 / 1024).toFixed(2)} MB`,
      date: new Date().toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }),
      category: category,
      uploadedBy: "Current User"
    };

    setTimeout(() => {
      setDocuments(prev => [newDoc, ...prev]);
      setProcessingStep(null);
      setUploadingFile(null);
    }, 1000);
  };

  const handleFileSelect = (files: FileList | null) => {
    if (!files || files.length === 0) return;
    const file = files[0];

    // Reset error state
    setUploadError(null);

    // Validate file type
    const typeValidation = validateOCRFileType(file);
    if (!typeValidation.isValid) {
      setUploadError(typeValidation.error || "Invalid file type");
      return;
    }

    // Validate file size (2MB max)
    const sizeValidation = validateFileSize(file, MAX_FILE_SIZE_MB);
    if (!sizeValidation.isValid) {
      setUploadError(sizeValidation.error || "File size exceeds maximum");
      return;
    }

    // All validations passed, proceed with OCR processing
    simulateOCRProcessing(file);
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    handleFileSelect(e.dataTransfer.files);
  };

  const filteredDocuments = selectedCategory === "all"
    ? documents
    : documents.filter(doc => doc.category === selectedCategory);

  const getStepStatus = (step: ProcessingStep) => {
    const steps: ProcessingStep[] = ["uploading", "extracting", "classifying", "done"];
    const currentIndex = steps.indexOf(processingStep);
    const stepIndex = steps.indexOf(step);

    if (stepIndex < currentIndex) return "completed";
    if (stepIndex === currentIndex) return "active";
    return "pending";
  };

  return (
    <div className="p-8">
      <div className="mb-8">
        <h1 className="text-3xl font-display mb-2">Documents</h1>
        <p className="text-muted-foreground">Upload, process, and manage cooperative documents with OCR</p>
      </div>

      {/* Drag and Drop Upload Area */}
      <div className="mb-6">
        <input
          ref={fileInputRef}
          type="file"
          accept=".pdf,.png,.jpg,.jpeg,.gif"
          onChange={(e) => handleFileSelect(e.target.files)}
          className="hidden"
        />

        <div
          onDragOver={handleDragOver}
          onDragLeave={handleDragLeave}
          onDrop={handleDrop}
          onClick={() => fileInputRef.current?.click()}
          className={`border-2 border-dashed rounded-xl p-12 text-center cursor-pointer transition-all ${
            isDragging
              ? "border-primary bg-primary/5 scale-[1.02]"
              : uploadError
              ? "border-red-300 bg-red-50"
              : "border-border bg-card hover:border-primary hover:bg-primary/5"
          }`}
        >
          <div className="flex flex-col items-center gap-4">
            <div className={`w-16 h-16 rounded-full flex items-center justify-center transition-colors ${
              isDragging ? "bg-primary" : uploadError ? "bg-red-100" : "bg-primary/10"
            }`}>
              <Upload className={`w-8 h-8 ${isDragging ? "text-white" : uploadError ? "text-red-600" : "text-primary"}`} />
            </div>
            <div>
              <h3 className="font-bold mb-1">Drop files here or click to upload</h3>
              <p className="text-sm text-muted-foreground mb-2">
                Supports PDF and image files (PNG, JPG, JPEG, GIF)
              </p>
              <div className="flex items-center justify-center gap-2 text-xs text-muted-foreground">
                <span className="px-2 py-1 bg-muted rounded">Maximum file size: {MAX_FILE_SIZE_MB}MB</span>
                <span className="px-2 py-1 bg-blue-100 text-blue-700 rounded font-medium">OCR Enabled</span>
              </div>
            </div>
          </div>
        </div>

        {/* Error Message */}
        {uploadError && (
          <div className="mt-4 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
            <AlertCircle className="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
            <div className="flex-1">
              <h4 className="font-medium text-red-900 mb-1">Upload Failed</h4>
              <p className="text-sm text-red-700">{uploadError}</p>
            </div>
            <button
              onClick={() => setUploadError(null)}
              className="text-red-600 hover:text-red-800 transition-colors"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        )}
      </div>

      {/* OCR Processing Status */}
      {processingStep && (
        <div className="bg-card rounded-xl p-6 border border-border shadow-sm mb-6">
          <div className="mb-4">
            <div className="flex items-center justify-between mb-2">
              <h3 className="font-bold">Processing: {uploadingFile}</h3>
              {processingStep !== "done" && (
                <button
                  onClick={() => {
                    setProcessingStep(null);
                    setUploadingFile(null);
                  }}
                  className="text-muted-foreground hover:text-foreground"
                >
                  <X className="w-4 h-4" />
                </button>
              )}
            </div>
            <p className="text-sm text-muted-foreground">
              {processingStep === "uploading" && "Uploading file to server..."}
              {processingStep === "extracting" && "Extracting text using OCR..."}
              {processingStep === "classifying" && "Classifying document category..."}
              {processingStep === "done" && "Processing complete!"}
            </p>
          </div>

          {/* Progress Steps */}
          <div className="flex items-center gap-2">
            {/* Step 1: Uploading */}
            <div className="flex-1">
              <div className={`flex items-center gap-2 mb-2 ${
                getStepStatus("uploading") === "completed" ? "text-green-600" :
                getStepStatus("uploading") === "active" ? "text-primary" :
                "text-muted-foreground"
              }`}>
                {getStepStatus("uploading") === "completed" ? (
                  <CheckCircle className="w-5 h-5" />
                ) : getStepStatus("uploading") === "active" ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  <div className="w-5 h-5 rounded-full border-2 border-current" />
                )}
                <span className="text-sm font-medium">Uploading</span>
              </div>
              <div className={`h-2 rounded-full ${
                getStepStatus("uploading") === "completed" ? "bg-green-500" :
                getStepStatus("uploading") === "active" ? "bg-primary" :
                "bg-muted"
              }`} />
            </div>

            <div className="w-8 h-0.5 bg-muted" />

            {/* Step 2: Extracting */}
            <div className="flex-1">
              <div className={`flex items-center gap-2 mb-2 ${
                getStepStatus("extracting") === "completed" ? "text-green-600" :
                getStepStatus("extracting") === "active" ? "text-primary" :
                "text-muted-foreground"
              }`}>
                {getStepStatus("extracting") === "completed" ? (
                  <CheckCircle className="w-5 h-5" />
                ) : getStepStatus("extracting") === "active" ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  <div className="w-5 h-5 rounded-full border-2 border-current" />
                )}
                <span className="text-sm font-medium">Extracting Text</span>
              </div>
              <div className={`h-2 rounded-full ${
                getStepStatus("extracting") === "completed" ? "bg-green-500" :
                getStepStatus("extracting") === "active" ? "bg-primary" :
                "bg-muted"
              }`} />
            </div>

            <div className="w-8 h-0.5 bg-muted" />

            {/* Step 3: Classifying */}
            <div className="flex-1">
              <div className={`flex items-center gap-2 mb-2 ${
                getStepStatus("classifying") === "completed" ? "text-green-600" :
                getStepStatus("classifying") === "active" ? "text-primary" :
                "text-muted-foreground"
              }`}>
                {getStepStatus("classifying") === "completed" ? (
                  <CheckCircle className="w-5 h-5" />
                ) : getStepStatus("classifying") === "active" ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  <div className="w-5 h-5 rounded-full border-2 border-current" />
                )}
                <span className="text-sm font-medium">Classifying</span>
              </div>
              <div className={`h-2 rounded-full ${
                getStepStatus("classifying") === "completed" ? "bg-green-500" :
                getStepStatus("classifying") === "active" ? "bg-primary" :
                "bg-muted"
              }`} />
            </div>

            <div className="w-8 h-0.5 bg-muted" />

            {/* Step 4: Done */}
            <div className="flex-1">
              <div className={`flex items-center gap-2 mb-2 ${
                getStepStatus("done") === "completed" || getStepStatus("done") === "active" ? "text-green-600" : "text-muted-foreground"
              }`}>
                {getStepStatus("done") === "completed" || getStepStatus("done") === "active" ? (
                  <CheckCircle className="w-5 h-5" />
                ) : (
                  <div className="w-5 h-5 rounded-full border-2 border-current" />
                )}
                <span className="text-sm font-medium">Done</span>
              </div>
              <div className={`h-2 rounded-full ${
                getStepStatus("done") === "completed" || getStepStatus("done") === "active" ? "bg-green-500" : "bg-muted"
              }`} />
            </div>
          </div>
        </div>
      )}

      {/* Category Filter Bar */}
      <div className="bg-card rounded-xl p-4 border border-border shadow-sm mb-6">
        <div className="flex items-center gap-2 flex-wrap">
          <span className="text-sm font-medium text-muted-foreground mr-2">Filter by:</span>
          <button
            onClick={() => setSelectedCategory("all")}
            className={`px-4 py-2 rounded-lg text-sm transition-all ${
              selectedCategory === "all"
                ? "bg-primary text-primary-foreground shadow-sm"
                : "bg-muted text-muted-foreground hover:bg-secondary"
            }`}
          >
            All Documents ({documents.length})
          </button>
          {(Object.keys(categoryLabels) as DocumentCategory[]).map((category) => {
            const count = documents.filter(doc => doc.category === category).length;
            return (
              <button
                key={category}
                onClick={() => setSelectedCategory(category)}
                className={`px-4 py-2 rounded-lg text-sm transition-all ${
                  selectedCategory === category
                    ? "bg-primary text-primary-foreground shadow-sm"
                    : "bg-muted text-muted-foreground hover:bg-secondary"
                }`}
              >
                {categoryLabels[category]} ({count})
              </button>
            );
          })}
        </div>
      </div>

      {/* Documents Repository Table */}
      <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-muted/50">
              <tr>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">File Name</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Category</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Upload Date</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Uploaded By</th>
                <th className="text-left px-6 py-4 text-sm font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              {filteredDocuments.length === 0 ? (
                <tr>
                  <td colSpan={5} className="px-6 py-12 text-center text-muted-foreground">
                    No documents found in this category
                  </td>
                </tr>
              ) : (
                filteredDocuments.map((doc) => (
                  <tr key={doc.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                          <FileText className="w-5 h-5 text-primary" />
                        </div>
                        <div>
                          <div className="font-medium">{doc.name}</div>
                          <div className="text-xs text-muted-foreground">{doc.type} • {doc.size}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-3 py-1 rounded-full text-sm ${categoryColors[doc.category]}`}>
                        {categoryLabels[doc.category]}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-muted-foreground">{doc.date}</td>
                    <td className="px-6 py-4 text-muted-foreground">{doc.uploadedBy}</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <button className="p-2 hover:bg-muted rounded-lg transition-colors" title="View">
                          <Eye className="w-4 h-4 text-muted-foreground" />
                        </button>
                        <button className="p-2 hover:bg-muted rounded-lg transition-colors" title="Download">
                          <Download className="w-4 h-4 text-muted-foreground" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
