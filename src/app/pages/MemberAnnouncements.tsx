import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router";
import {
  Calendar,
  CheckCircle2,
  Circle,
  Filter,
  Megaphone,
  Search,
} from "lucide-react";

type Sector = "all" | "rice_farming" | "corn" | "fishery" | "livestock" | "high_value_crops";

interface Announcement {
  id: string;
  title: string;
  content: string;
  date: string;
  sender: string;
  sector: Sector;
  unread: boolean;
}

const heroImage =
  "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=2400";

const sectorLabels: Record<Sector, string> = {
  all: "All Members",
  rice_farming: "Rice Farming",
  corn: "Corn",
  fishery: "Fishery",
  livestock: "Livestock",
  high_value_crops: "High-Value Crops",
};

const sectorColors: Record<Sector, string> = {
  all: "bg-blue-100 text-blue-700 border-blue-200",
  rice_farming: "bg-green-100 text-green-700 border-green-200",
  corn: "bg-yellow-100 text-yellow-700 border-yellow-200",
  fishery: "bg-cyan-100 text-cyan-700 border-cyan-200",
  livestock: "bg-orange-100 text-orange-700 border-orange-200",
  high_value_crops: "bg-purple-100 text-purple-700 border-purple-200",
};

const memberSector: Sector = "rice_farming";

export default function MemberAnnouncements() {
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState("");
  const [announcements, setAnnouncements] = useState<Announcement[]>([
    {
      id: "ann-1",
      title: "Annual General Meeting - April 25, 2026",
      content:
        "All members are invited to attend the Annual General Meeting on April 25, 2026, at 2:00 PM in the Community Hall. We will discuss financial reports, elect new board members, and plan for the upcoming year.",
      date: "Apr 12, 2026",
      sender: "Board of Directors",
      sector: "all",
      unread: true,
    },
    {
      id: "ann-2",
      title: "Rice Farming Best Practices Workshop",
      content:
        "Special workshop for rice farming members on modern cultivation techniques and pest management. Join us on April 20, 2026, at 9:00 AM for a comprehensive session led by agricultural experts.",
      date: "Apr 10, 2026",
      sender: "Agricultural Team",
      sector: "rice_farming",
      unread: true,
    },
    {
      id: "ann-3",
      title: "Dividend Distribution Schedule",
      content:
        "Member dividends for Q1 2026 will be distributed on April 30, 2026. Please ensure your bank account details are up to date in your profile.",
      date: "Apr 8, 2026",
      sender: "Finance Team",
      sector: "all",
      unread: false,
    },
    {
      id: "ann-4",
      title: "Rice Farming Equipment Loan Program",
      content:
        "Low-interest equipment loans are now available for rice farming members. Applications are being accepted until May 31, 2026.",
      date: "Apr 5, 2026",
      sender: "Loan Committee",
      sector: "rice_farming",
      unread: false,
    },
    {
      id: "ann-5",
      title: "Office Hours Update",
      content:
        "Starting May 1, 2026, our office hours will be Monday to Friday, 8:00 AM - 5:00 PM. Saturday hours remain 9:00 AM - 1:00 PM.",
      date: "Apr 2, 2026",
      sender: "Administration",
      sector: "all",
      unread: false,
    },
    {
      id: "ann-6",
      title: "Irrigation System Maintenance - Rice Farming Sector",
      content:
        "The cooperative's irrigation system will undergo scheduled maintenance from April 18-20, 2026. This may affect water supply to rice farming areas.",
      date: "Mar 30, 2026",
      sender: "Technical Services",
      sector: "rice_farming",
      unread: false,
    },
    {
      id: "ann-7",
      title: "Share Capital Contribution Reminder",
      content:
        "Monthly share capital contributions are due by the 15th of each month. Regular contributions help strengthen our cooperative and increase your ownership stake.",
      date: "Mar 28, 2026",
      sender: "Finance Team",
      sector: "all",
      unread: false,
    },
  ]);

  useEffect(() => {
    const role = localStorage.getItem("userRole");
    if (role !== "member") {
      if (role === "chairman") {
        navigate("/dashboard");
      } else if (role === "bookkeeper") {
        navigate("/dashboard/bookkeeper");
      }
    }
  }, [navigate]);

  const relevantAnnouncements = useMemo(
    () => announcements.filter((announcement) => announcement.sector === "all" || announcement.sector === memberSector),
    [announcements],
  );

  const filteredAnnouncements = useMemo(
    () =>
      relevantAnnouncements.filter((announcement) => {
        const query = searchQuery.trim().toLowerCase();

        return (
          !query ||
          announcement.title.toLowerCase().includes(query) ||
          announcement.content.toLowerCase().includes(query) ||
          announcement.sender.toLowerCase().includes(query)
        );
      }),
    [relevantAnnouncements, searchQuery],
  );

  const unreadCount = filteredAnnouncements.filter((announcement) => announcement.unread).length;

  const handleMarkAllAsRead = () => {
    setAnnouncements((current) => current.map((announcement) => ({ ...announcement, unread: false })));
  };

  const handleToggleRead = (id: string) => {
    setAnnouncements((current) =>
      current.map((announcement) =>
        announcement.id === id ? { ...announcement, unread: !announcement.unread } : announcement,
      ),
    );
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
                  Member Announcements
                </p>
                <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-5xl">
                  Cooperative Updates
                </h1>
                <p className="mt-3 max-w-2xl text-lg text-white/85">
                  Read general notices and sector-specific updates relevant to your membership.
                </p>
              </div>
              <button
                onClick={handleMarkAllAsRead}
                disabled={unreadCount === 0}
                className={`inline-flex min-h-[52px] items-center justify-center gap-2 rounded-lg px-5 py-3 font-semibold shadow-lg transition-all ${
                  unreadCount > 0
                    ? "bg-green-300 text-green-950 hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                    : "cursor-not-allowed bg-white/20 text-white/70 shadow-none"
                }`}
              >
                <CheckCircle2 className="h-4 w-4" />
                Mark All as Read
              </button>
            </div>
          </div>
        </div>
      </section>

      <main className="mx-auto max-w-[1600px] px-6 py-8 md:px-8">
        <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
          <div className="rounded-xl border border-green-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-green-600">{filteredAnnouncements.length}</div>
            <div className="text-sm text-muted-foreground">Visible Announcements</div>
          </div>
          <div className="rounded-xl border border-amber-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-75 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-3xl font-bold text-amber-600">{unreadCount}</div>
            <div className="text-sm text-muted-foreground">Unread Updates</div>
          </div>
          <div className="rounded-xl border border-blue-200 bg-card p-6 shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-150 duration-300 transition-all hover:-translate-y-1 hover:shadow-lg">
            <div className="mb-1 text-2xl font-bold">{sectorLabels[memberSector]}</div>
            <div className="text-sm text-muted-foreground">Current Sector Feed</div>
          </div>
        </div>

        <section className="overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm animate-in fade-in slide-in-from-bottom-3 delay-200 duration-500">
          <div className="border-b border-stone-200 px-5 py-5 md:px-6">
            <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                  Announcement Feed
                </p>
                <h2 className="mt-1 text-xl font-display">Inbox</h2>
              </div>
              <div className="text-sm font-medium text-gray-500">
                {filteredAnnouncements.length} result{filteredAnnouncements.length === 1 ? "" : "s"}
              </div>
            </div>

            <div className="mt-5 border-t border-stone-100 pt-4">
              <div className="grid gap-3 xl:grid-cols-[minmax(280px,1fr)_220px] xl:items-center">
                <div className="relative">
                  <Search className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(event) => setSearchQuery(event.target.value)}
                    placeholder="Search announcements"
                    className="h-11 w-full rounded-lg border border-stone-200 bg-white pl-10 pr-4 text-sm outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <div className="inline-flex h-11 items-center gap-2 rounded-lg border border-stone-200 bg-stone-50 px-4 text-sm font-semibold text-gray-600">
                  <Filter className="h-4 w-4" />
                  {sectorLabels[memberSector]} + General
                </div>
              </div>
            </div>
          </div>

          <div className="divide-y divide-stone-100">
            {filteredAnnouncements.length === 0 ? (
              <div className="px-6 py-14 text-center text-gray-500">No announcements found.</div>
            ) : (
              filteredAnnouncements.map((announcement, index) => (
                <button
                  key={announcement.id}
                  onClick={() => handleToggleRead(announcement.id)}
                  className={`block w-full px-5 py-5 text-left transition-all hover:bg-green-50/30 md:px-6 ${
                    announcement.unread ? "bg-green-50/25" : "bg-white"
                  }`}
                  style={{ animationDelay: `${Math.min(index * 35, 180)}ms` }}
                >
                  <div className="flex items-start gap-4">
                    <div className="pt-1">
                      {announcement.unread ? (
                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                          <Megaphone className="h-5 w-5 text-primary" />
                        </div>
                      ) : (
                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-stone-100">
                          <Circle className="h-4 w-4 text-gray-400" />
                        </div>
                      )}
                    </div>

                    <div className="min-w-0 flex-1">
                      <div className="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                          <div className="flex flex-wrap items-center gap-2">
                            <h3 className={`text-lg ${announcement.unread ? "font-bold" : "font-semibold"}`}>
                              {announcement.title}
                            </h3>
                            {announcement.unread && (
                              <span className="rounded-full bg-primary px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-white">
                                New
                              </span>
                            )}
                          </div>
                          <div className="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                            <span className="inline-flex items-center gap-1.5">
                              <Calendar className="h-4 w-4" />
                              {announcement.date}
                            </span>
                            <span>{announcement.sender}</span>
                            <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-semibold ${sectorColors[announcement.sector]}`}>
                              {sectorLabels[announcement.sector]}
                            </span>
                          </div>
                        </div>
                      </div>

                      <p className="mt-4 text-sm leading-6 text-gray-600">{announcement.content}</p>
                    </div>
                  </div>
                </button>
              ))
            )}
          </div>
        </section>
      </main>
    </div>
  );
}
