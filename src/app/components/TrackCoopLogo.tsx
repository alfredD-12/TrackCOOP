import trackcoopLogo from "../../imports/trackcoop-logo.svg";

interface TrackCoopLogoProps {
  className?: string;
  markClassName?: string;
  titleClassName?: string;
  tone?: "light" | "dark";
}

export default function TrackCoopLogo({
  className = "",
  markClassName = "h-10 w-10",
  titleClassName = "text-xl",
  tone = "light",
}: TrackCoopLogoProps) {
  const trackColor = tone === "dark" ? "text-gray-900" : "text-white";
  const coopColor = tone === "dark" ? "text-green-600" : "text-green-300";

  return (
    <div
      className={`inline-flex items-center gap-3 ${className}`}
      aria-label="TrackCOOP"
    >
      <span
        className={`inline-flex shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white p-1.5 ${markClassName}`}
      >
        <img
          src={trackcoopLogo}
          alt=""
          aria-hidden="true"
          className="h-full w-full object-cover"
        />
      </span>
      <span className={`font-display font-bold leading-none ${titleClassName}`}>
        <span className={trackColor}>Track</span>
        <span className={coopColor}>COOP</span>
      </span>
    </div>
  );
}
