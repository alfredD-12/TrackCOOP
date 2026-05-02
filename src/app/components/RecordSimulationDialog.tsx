import type { FormEvent } from "react";
import { FileImage, Upload, X } from "lucide-react";
import { ImageWithFallback } from "./figma/ImageWithFallback";

export type SimulationField = {
  name: string;
  label: string;
  type: "text" | "number" | "date" | "textarea" | "select";
  options?: Array<{ label: string; value: string }>;
  placeholder?: string;
  readOnly?: boolean;
  helperText?: string;
  step?: string;
  min?: string;
};

interface RecordSimulationDialogProps {
  isOpen: boolean;
  title: string;
  description: string;
  submitLabel: string;
  values: Record<string, string>;
  fields: SimulationField[];
  evidenceImage: string;
  evidenceName: string;
  evidenceLabel: string;
  evidenceHint?: string;
  accentClassName?: string;
  onClose: () => void;
  onSubmit: (event: FormEvent<HTMLFormElement>) => void;
  onChange: (name: string, value: string) => void;
}

export default function RecordSimulationDialog({
  isOpen,
  title,
  description,
  submitLabel,
  values,
  fields,
  evidenceImage,
  evidenceName,
  evidenceLabel,
  evidenceHint = "Evidence file attached for this record.",
  accentClassName = "bg-primary/10 text-primary",
  onClose,
  onSubmit,
  onChange,
}: RecordSimulationDialogProps) {
  if (!isOpen) {
    return null;
  }

  return (
    <div
      className="fixed inset-0 z-50 overflow-y-auto bg-black/55 p-4 sm:p-6"
      onClick={onClose}
      role="dialog"
      aria-modal="true"
    >
      <div
        className="mx-auto my-6 flex max-h-[calc(100vh-3rem)] w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl"
        onClick={(event) => event.stopPropagation()}
      >
        <div className="flex items-start justify-between gap-4 border-b border-stone-200 px-6 py-5">
          <div>
            <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
              Simulated Upload
            </p>
            <h2 className="mt-1 text-2xl font-display text-gray-950">{title}</h2>
            <p className="mt-2 max-w-2xl text-sm leading-6 text-gray-500">{description}</p>
          </div>
          <button
            onClick={onClose}
            className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
            aria-label="Close dialog"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        <form
          onSubmit={onSubmit}
          className="grid min-h-0 flex-1 gap-0 lg:max-h-[calc(100vh-11rem)] lg:grid-cols-[minmax(0,1fr)_320px]"
        >
          <div className="min-h-0 overflow-y-auto px-6 py-6">
            <div className="space-y-5 pb-2">
              <div className="grid gap-4 md:grid-cols-2">
                {fields.map((field) => (
                  <label
                    key={field.name}
                    className={field.type === "textarea" ? "md:col-span-2" : ""}
                  >
                    <span className="mb-2 block text-sm font-semibold text-gray-700">
                      {field.label}
                    </span>

                    {field.type === "textarea" ? (
                      <textarea
                        value={values[field.name] ?? ""}
                        onChange={(event) => onChange(field.name, event.target.value)}
                        placeholder={field.placeholder}
                        readOnly={field.readOnly}
                        rows={4}
                        className="min-h-[112px] w-full rounded-lg border border-stone-200 bg-white px-4 py-3 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 read-only:bg-stone-50 read-only:text-gray-500"
                      />
                    ) : field.type === "select" ? (
                      <select
                        value={values[field.name] ?? ""}
                        onChange={(event) => onChange(field.name, event.target.value)}
                        disabled={field.readOnly}
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm font-medium text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:bg-stone-50 disabled:text-gray-500"
                      >
                        {field.options?.map((option) => (
                          <option key={option.value} value={option.value}>
                            {option.label}
                          </option>
                        ))}
                      </select>
                    ) : (
                      <input
                        type={field.type}
                        value={values[field.name] ?? ""}
                        onChange={(event) => onChange(field.name, event.target.value)}
                        placeholder={field.placeholder}
                        readOnly={field.readOnly}
                        step={field.step}
                        min={field.min}
                        className="h-11 w-full rounded-lg border border-stone-200 bg-white px-4 text-sm text-gray-950 outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 read-only:bg-stone-50 read-only:text-gray-500"
                      />
                    )}

                    {field.helperText && (
                      <span className="mt-2 block text-xs text-gray-500">{field.helperText}</span>
                    )}
                  </label>
                ))}
              </div>

              <div className="rounded-lg border border-dashed border-stone-300 bg-stone-50 px-4 py-4">
                <div className="flex items-center gap-3">
                  <div className={`flex h-11 w-11 items-center justify-center rounded-lg ${accentClassName}`}>
                    <Upload className="h-5 w-5" />
                  </div>
                  <div>
                    <p className="text-sm font-semibold text-gray-950">{evidenceLabel}</p>
                    <p className="text-xs text-gray-500">{evidenceHint}</p>
                  </div>
                </div>
              </div>
            </div>

            <div className="sticky bottom-0 -mx-6 mt-6 border-t border-stone-200 bg-white px-6 pb-6 pt-5">
              <div className="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                  type="button"
                  onClick={onClose}
                  className="inline-flex h-11 items-center justify-center rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="inline-flex h-11 items-center justify-center rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
                >
                  {submitLabel}
                </button>
              </div>
            </div>
          </div>

          <aside className="min-h-0 overflow-y-auto border-t border-stone-200 bg-stone-50 px-6 py-6 lg:border-t-0 lg:border-l">
            <div className="flex items-center gap-2 text-sm font-semibold text-gray-700">
              <FileImage className="h-4 w-4 text-primary" />
              Evidence Preview
            </div>
            <div className="mt-4 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
              <ImageWithFallback
                src={evidenceImage}
                alt={evidenceName}
                className="h-64 w-full object-cover"
              />
              <div className="border-t border-stone-200 px-4 py-4">
                <p className="text-sm font-semibold text-gray-950">{evidenceName}</p>
                <p className="mt-1 text-xs leading-5 text-gray-500">
                  This preview is attached to the current record.
                </p>
              </div>
            </div>
          </aside>
        </form>
      </div>
    </div>
  );
}
