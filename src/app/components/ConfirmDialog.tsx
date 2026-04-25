import { X, AlertTriangle, CheckCircle, Info, Trash2 } from "lucide-react";

interface ConfirmDialogProps {
  isOpen: boolean;
  onClose: () => void;
  onConfirm: () => void;
  title: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  variant?: "danger" | "warning" | "info" | "success";
}

export default function ConfirmDialog({
  isOpen,
  onClose,
  onConfirm,
  title,
  message,
  confirmText = "Confirm",
  cancelText = "Cancel",
  variant = "danger",
}: ConfirmDialogProps) {
  if (!isOpen) return null;

  const variantStyles = {
    danger: {
      icon: Trash2,
      iconBg: "bg-red-100",
      iconColor: "text-red-600",
      confirmBg: "bg-red-600 hover:bg-red-700",
      confirmText: "text-white",
    },
    warning: {
      icon: AlertTriangle,
      iconBg: "bg-amber-100",
      iconColor: "text-amber-600",
      confirmBg: "bg-amber-600 hover:bg-amber-700",
      confirmText: "text-white",
    },
    info: {
      icon: Info,
      iconBg: "bg-blue-100",
      iconColor: "text-blue-600",
      confirmBg: "bg-blue-600 hover:bg-blue-700",
      confirmText: "text-white",
    },
    success: {
      icon: CheckCircle,
      iconBg: "bg-green-100",
      iconColor: "text-green-600",
      confirmBg: "bg-green-600 hover:bg-green-700",
      confirmText: "text-white",
    },
  };

  const style = variantStyles[variant];
  const Icon = style.icon;

  const handleConfirm = () => {
    onConfirm();
    onClose();
  };

  return (
    <div
      className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
      onClick={onClose}
    >
      <div
        className="bg-background rounded-xl max-w-md w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="p-6 border-b border-border">
          <div className="flex items-start gap-4">
            <div className={`w-12 h-12 ${style.iconBg} rounded-lg flex items-center justify-center shrink-0`}>
              <Icon className={`w-6 h-6 ${style.iconColor}`} />
            </div>
            <div className="flex-1 min-w-0">
              <h2 className="text-xl font-display mb-2">{title}</h2>
              <p className="text-sm text-muted-foreground leading-relaxed">{message}</p>
            </div>
            <button
              onClick={onClose}
              className="p-2 hover:bg-muted rounded-lg transition-colors shrink-0"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Actions */}
        <div className="p-6 flex gap-3">
          <button
            onClick={onClose}
            className="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all font-medium"
          >
            {cancelText}
          </button>
          <button
            onClick={handleConfirm}
            className="flex-1 px-6 py-3 bg-[#1B5E3C] hover:bg-[#164d30] text-white rounded-lg transition-all font-medium"
          >
            {confirmText}
          </button>
        </div>
      </div>
    </div>
  );
}
