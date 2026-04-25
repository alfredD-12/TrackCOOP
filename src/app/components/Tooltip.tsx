import { useState, useRef, useEffect } from "react";
import { HelpCircle } from "lucide-react";

interface TooltipProps {
  content: string;
  children: React.ReactNode;
  position?: "top" | "bottom" | "left" | "right";
  showIcon?: boolean;
}

export default function Tooltip({
  content,
  children,
  position = "top",
  showIcon = false,
}: TooltipProps) {
  const [isVisible, setIsVisible] = useState(false);
  const [tooltipPosition, setTooltipPosition] = useState({ top: 0, left: 0 });
  const triggerRef = useRef<HTMLDivElement>(null);
  const tooltipRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (isVisible && triggerRef.current && tooltipRef.current) {
      const triggerRect = triggerRef.current.getBoundingClientRect();
      const tooltipRect = tooltipRef.current.getBoundingClientRect();

      let top = 0;
      let left = 0;

      switch (position) {
        case "top":
          top = triggerRect.top - tooltipRect.height - 8;
          left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2;
          break;
        case "bottom":
          top = triggerRect.bottom + 8;
          left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2;
          break;
        case "left":
          top = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2;
          left = triggerRect.left - tooltipRect.width - 8;
          break;
        case "right":
          top = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2;
          left = triggerRect.right + 8;
          break;
      }

      setTooltipPosition({ top, left });
    }
  }, [isVisible, position]);

  return (
    <div
      ref={triggerRef}
      className="inline-flex items-center gap-1 cursor-help"
      onMouseEnter={() => setIsVisible(true)}
      onMouseLeave={() => setIsVisible(false)}
    >
      {children}
      {showIcon && <HelpCircle className="w-4 h-4 text-muted-foreground" />}

      {isVisible && (
        <div
          ref={tooltipRef}
          className="fixed z-50 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-lg max-w-xs animate-in fade-in zoom-in-95 duration-150"
          style={{
            top: `${tooltipPosition.top}px`,
            left: `${tooltipPosition.left}px`,
          }}
        >
          <div className="relative">
            {content}
            <div
              className={`absolute w-2 h-2 bg-gray-900 transform rotate-45 ${
                position === "top"
                  ? "bottom-[-4px] left-1/2 -translate-x-1/2"
                  : position === "bottom"
                  ? "top-[-4px] left-1/2 -translate-x-1/2"
                  : position === "left"
                  ? "right-[-4px] top-1/2 -translate-y-1/2"
                  : "left-[-4px] top-1/2 -translate-y-1/2"
              }`}
            />
          </div>
        </div>
      )}
    </div>
  );
}
