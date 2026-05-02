import { ChevronLeft, ChevronRight, X } from "lucide-react";

type OnboardingStep = {
  title: string;
  description: string;
  bullets?: string[];
};

interface OnboardingModalProps {
  pageTitle: string;
  steps: OnboardingStep[];
  stepIndex: number;
  onClose: () => void;
  onNext: () => void;
  onPrevious: () => void;
}

export default function OnboardingModal({
  pageTitle,
  steps,
  stepIndex,
  onClose,
  onNext,
  onPrevious,
}: OnboardingModalProps) {
  const step = steps[stepIndex];
  const isFirst = stepIndex === 0;
  const isLast = stepIndex === steps.length - 1;

  return (
    <div
      className="fixed inset-0 z-[70] flex items-center justify-center bg-black/55 p-4"
      onClick={onClose}
      role="dialog"
      aria-modal="true"
    >
      <div
        className="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl"
        onClick={(event) => event.stopPropagation()}
      >
        <div className="flex items-start justify-between gap-4 border-b border-stone-200 px-6 py-5">
          <div>
            <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
              Onboarding
            </p>
            <h2 className="mt-1 text-2xl font-display text-gray-950">{pageTitle}</h2>
            <p className="mt-2 text-sm text-gray-500">
              Step {stepIndex + 1} of {steps.length}
            </p>
          </div>
          <button
            onClick={onClose}
            className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-stone-100 hover:text-gray-950"
            aria-label="Close onboarding"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        <div className="px-6 py-6">
          <div className="rounded-lg border border-stone-200 bg-stone-50 px-5 py-5">
            <h3 className="text-xl font-display text-gray-950">{step.title}</h3>
            <p className="mt-3 text-sm leading-6 text-gray-600">{step.description}</p>
            {step.bullets && step.bullets.length > 0 && (
              <ul className="mt-4 space-y-2 text-sm text-gray-600">
                {step.bullets.map((bullet) => (
                  <li key={bullet} className="flex gap-3">
                    <span className="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-primary" />
                    <span>{bullet}</span>
                  </li>
                ))}
              </ul>
            )}
          </div>

          <div className="mt-6 h-2 overflow-hidden rounded-full bg-stone-100">
            <div
              className="h-full bg-primary transition-all"
              style={{ width: `${((stepIndex + 1) / steps.length) * 100}%` }}
            />
          </div>
        </div>

        <div className="flex flex-col gap-3 border-t border-stone-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
          <button
            onClick={onClose}
            className="inline-flex h-11 items-center justify-center rounded-lg border border-stone-200 bg-white px-5 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50"
          >
            Skip Guide
          </button>

          <div className="flex flex-col gap-3 sm:flex-row">
            <button
              onClick={onPrevious}
              disabled={isFirst}
              className={`inline-flex h-11 items-center justify-center gap-2 rounded-lg border px-5 text-sm font-semibold transition-all ${
                isFirst
                  ? "cursor-not-allowed border-stone-200 bg-stone-100 text-gray-400"
                  : "border-stone-200 bg-white text-gray-700 hover:bg-stone-50"
              }`}
            >
              <ChevronLeft className="h-4 w-4" />
              Previous
            </button>
            <button
              onClick={isLast ? onClose : onNext}
              className="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-[#1B5E3C] px-5 text-sm font-semibold text-white transition-all hover:bg-[#164d30]"
            >
              {isLast ? "Finish" : "Next"}
              {!isLast && <ChevronRight className="h-4 w-4" />}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
