import { CheckCircle2, LoaderCircle } from "lucide-react";

export interface SimulationToastState {
  title: string;
  description: string;
  tone?: "green" | "red" | "blue";
  status: "processing" | "complete";
}

interface SimulationToastProps {
  toast: SimulationToastState | null;
}

const toneClasses = {
  green: "border-green-200 bg-white text-green-700",
  red: "border-red-200 bg-white text-red-700",
  blue: "border-blue-200 bg-white text-blue-700",
};

export default function SimulationToast({ toast }: SimulationToastProps) {
  if (!toast) {
    return null;
  }

  const tone = toast.tone ?? "green";

  return (
    <div className="pointer-events-none fixed bottom-6 right-6 z-[60] w-full max-w-sm animate-in slide-in-from-bottom-4 fade-in duration-300">
      <div className={`overflow-hidden rounded-xl border shadow-xl ${toneClasses[tone]}`}>
        <div className="flex items-start gap-3 px-4 py-4">
          <div className="mt-0.5 flex h-10 w-10 items-center justify-center rounded-full bg-stone-100">
            {toast.status === "processing" ? (
              <LoaderCircle className="h-5 w-5 animate-spin" />
            ) : (
              <CheckCircle2 className="h-5 w-5" />
            )}
          </div>
          <div className="min-w-0 flex-1">
            <p className="text-sm font-semibold text-gray-950">{toast.title}</p>
            <p className="mt-1 text-sm leading-5 text-gray-500">{toast.description}</p>
          </div>
        </div>
        {toast.status === "processing" && (
          <div className="h-1 w-full overflow-hidden bg-stone-100">
            <div className="h-full w-1/2 animate-pulse bg-[#1B5E3C]" />
          </div>
        )}
      </div>
    </div>
  );
}
