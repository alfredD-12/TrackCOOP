import { useEffect, useMemo, useRef, useState } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import type { TourPlacement, TourStep } from "../onboarding/chairmanTour";

interface SpotlightOnboardingProps {
  step: TourStep | null;
  stepIndex: number;
  stepCount: number;
  onClose: () => void;
  onNext: () => void;
  onPrevious: () => void;
}

type RectState = {
  top: number;
  left: number;
  width: number;
  height: number;
};

const tooltipWidth = 360;
const tooltipHeight = 220;
const gap = 18;
const padding = 10;

function clamp(value: number, min: number, max: number) {
  return Math.min(Math.max(value, min), max);
}

function getTooltipPosition(
  rect: RectState,
  placement: TourPlacement,
): { top: number; left: number; arrowPlacement: TourPlacement } {
  const viewportWidth = window.innerWidth;
  const viewportHeight = window.innerHeight;

  let arrowPlacement = placement;
  let top = rect.top + rect.height + gap;
  let left = rect.left + rect.width / 2 - tooltipWidth / 2;

  if (placement === "top") {
    top = rect.top - tooltipHeight - gap;
  } else if (placement === "left") {
    top = rect.top + rect.height / 2 - tooltipHeight / 2;
    left = rect.left - tooltipWidth - gap;
  } else if (placement === "right") {
    top = rect.top + rect.height / 2 - tooltipHeight / 2;
    left = rect.left + rect.width + gap;
  }

  if (left + tooltipWidth > viewportWidth - 16) {
    left = viewportWidth - tooltipWidth - 16;
  }

  if (left < 16) {
    left = 16;
  }

  if (top + tooltipHeight > viewportHeight - 16) {
    top = rect.top - tooltipHeight - gap;
    arrowPlacement = "top";
  }

  if (top < 16) {
    top = clamp(rect.top + rect.height + gap, 16, viewportHeight - tooltipHeight - 16);
    arrowPlacement = "bottom";
  }

  if (placement === "left" || placement === "right") {
    top = clamp(top, 16, viewportHeight - tooltipHeight - 16);
    if (placement === "left" && left < 16) {
      left = clamp(rect.left + rect.width + gap, 16, viewportWidth - tooltipWidth - 16);
      arrowPlacement = "right";
    }
    if (placement === "right" && left + tooltipWidth > viewportWidth - 16) {
      left = clamp(rect.left - tooltipWidth - gap, 16, viewportWidth - tooltipWidth - 16);
      arrowPlacement = "left";
    }
  }

  return { top, left, arrowPlacement };
}

export default function SpotlightOnboarding({
  step,
  stepIndex,
  stepCount,
  onClose,
  onNext,
  onPrevious,
}: SpotlightOnboardingProps) {
  const [targetRect, setTargetRect] = useState<RectState | null>(null);
  const targetRef = useRef<HTMLElement | null>(null);

  useEffect(() => {
    if (!step) {
      setTargetRect(null);
      return;
    }

    let cancelled = false;
    let attempts = 0;
    let timer: ReturnType<typeof setTimeout> | null = null;

    const updateRect = () => {
      if (!targetRef.current) {
        return;
      }

      const rect = targetRef.current.getBoundingClientRect();
      setTargetRect({
        top: rect.top - padding,
        left: rect.left - padding,
        width: rect.width + padding * 2,
        height: rect.height + padding * 2,
      });
    };

    const findTarget = () => {
      if (cancelled) {
        return;
      }

      const element = document.querySelector(step.selector) as HTMLElement | null;
      if (element) {
        targetRef.current = element;
        element.scrollIntoView({
          block: "center",
          inline: "center",
          behavior: "smooth",
        });
        window.setTimeout(updateRect, 120);
        window.addEventListener("resize", updateRect);
        window.addEventListener("scroll", updateRect, true);
        return;
      }

      attempts += 1;
      if (attempts < 40) {
        timer = window.setTimeout(findTarget, 120);
      } else {
        setTargetRect(null);
      }
    };

    findTarget();

    return () => {
      cancelled = true;
      if (timer) {
        clearTimeout(timer);
      }
      window.removeEventListener("resize", updateRect);
      window.removeEventListener("scroll", updateRect, true);
    };
  }, [step]);

  const tooltipPosition = useMemo(() => {
    if (!targetRect || !step) {
      return null;
    }

    return getTooltipPosition(targetRect, step.placement ?? "bottom");
  }, [step, targetRect]);

  if (!step) {
    return null;
  }

  return (
    <div className="fixed inset-0 z-[220]">
      {targetRect && (
        <div
          className="pointer-events-none absolute rounded-[22px] border-[3px] border-white shadow-[0_0_0_9999px_rgba(15,23,42,0.68)] transition-all duration-300"
          style={{
            top: targetRect.top,
            left: targetRect.left,
            width: targetRect.width,
            height: targetRect.height,
          }}
        />
      )}

      <div
        className="pointer-events-auto fixed w-[min(360px,calc(100vw-24px))] rounded-[28px] bg-white p-6 shadow-2xl"
        style={{
          top: tooltipPosition?.top ?? "50%",
          left: tooltipPosition?.left ?? "50%",
          transform: tooltipPosition ? undefined : "translate(-50%, -50%)",
        }}
        role="dialog"
        aria-modal="true"
      >
        {tooltipPosition && (
          <div
            className="absolute h-5 w-5 rotate-45 bg-white"
            style={
              tooltipPosition.arrowPlacement === "bottom"
                ? {
                    top: -10,
                    left: clamp(
                      targetRect!.left + targetRect!.width / 2 - tooltipPosition.left - 10,
                      20,
                      320,
                    ),
                  }
                : tooltipPosition.arrowPlacement === "top"
                  ? {
                      bottom: -10,
                      left: clamp(
                        targetRect!.left + targetRect!.width / 2 - tooltipPosition.left - 10,
                        20,
                        320,
                      ),
                    }
                  : tooltipPosition.arrowPlacement === "left"
                    ? {
                        right: -10,
                        top: clamp(
                          targetRect!.top + targetRect!.height / 2 - tooltipPosition.top - 10,
                          20,
                          180,
                        ),
                      }
                    : {
                        left: -10,
                        top: clamp(
                          targetRect!.top + targetRect!.height / 2 - tooltipPosition.top - 10,
                          20,
                          180,
                        ),
                      }
            }
          />
        )}

        <div className="inline-flex rounded-full bg-green-50 px-3 py-1 text-sm font-semibold text-primary">
          Step {stepIndex + 1} of {stepCount}
        </div>
        <h2 className="mt-4 text-3xl font-display font-bold text-gray-950">
          {step.title}
        </h2>
        <p className="mt-3 text-base leading-7 text-gray-600">
          {step.description}
        </p>

        <div className="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-between">
          <button
            onClick={onPrevious}
            disabled={stepIndex === 0}
            className={`inline-flex h-12 items-center justify-center gap-2 rounded-xl px-5 text-sm font-semibold transition-all ${
              stepIndex === 0
                ? "cursor-not-allowed border border-stone-200 bg-stone-100 text-gray-400"
                : "border border-stone-200 bg-white text-gray-700 hover:bg-stone-50"
            }`}
          >
            <ChevronLeft className="h-4 w-4" />
            Back
          </button>

          <div className="flex gap-3">
            <button
              onClick={onClose}
              className="inline-flex h-12 items-center justify-center rounded-xl border border-stone-200 bg-white px-6 text-sm font-semibold text-gray-700 transition-all hover:bg-stone-50"
            >
              Skip
            </button>
            <button
              onClick={onNext}
              className="inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-[#35553b] px-6 text-sm font-semibold text-white transition-all hover:bg-[#29452e]"
            >
              {stepIndex === stepCount - 1 ? "Finish" : "Next"}
              {stepIndex !== stepCount - 1 && <ChevronRight className="h-4 w-4" />}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
