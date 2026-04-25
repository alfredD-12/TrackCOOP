import { useState, type FormEvent } from "react";
import { useNavigate } from "react-router";
import {
  AlertTriangle,
  ArrowRight,
  CalendarDays,
  CheckCircle2,
  ChevronLeft,
  ChevronRight,
  Clock,
  Eye,
  EyeOff,
  LogIn,
  Mail,
  MapPin,
  Megaphone,
  Phone,
  Plus,
  Send,
  Sprout,
  UserPlus,
  Waves,
  X,
} from "lucide-react";
import TrackCoopLogo from "../components/TrackCoopLogo";

type PortalRole = "chairman" | "bookkeeper" | "member";

interface ChildInfo {
  id: number;
  name: string;
  age: string;
  benefit: string;
}

interface MembershipForm {
  firstName: string;
  middleName: string;
  lastName: string;
  email: string;
  phone: string;
  status: string;
  placeOfBirth: string;
  birthday: string;
  currentHome: string;
  occupation: string;
  fatherName: string;
  motherName: string;
  wifeName: string;
  username: string;
  password: string;
  confirmPassword: string;
  agreedToTerms: boolean;
}

type MembershipAlert = {
  isOpen: boolean;
  type: "success" | "error";
  title: string;
  message: string;
};

const heroImage =
  "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=2400";

const initialMembershipForm: MembershipForm = {
  firstName: "Juan",
  middleName: "Santos",
  lastName: "Dela Cruz",
  email: "juan.delacruz@example.com",
  phone: "9123456789",
  status: "married",
  placeOfBirth: "Nasugbu, Batangas",
  birthday: "1990-05-14",
  currentHome: "wawa",
  occupation: "Rice Farmer",
  fatherName: "Pedro Dela Cruz",
  motherName: "Maria Santos",
  wifeName: "Ana Reyes Dela Cruz",
  username: "juan.delacruz",
  password: "Member123!",
  confirmPassword: "Member123!",
  agreedToTerms: true,
};

const announcements = [
  {
    category: "General Assembly",
    date: "May 15, 2026",
    title: "Annual General Assembly",
    description:
      "Members are invited to review cooperative updates, share capital reports, and plans for the next production cycle.",
  },
  {
    category: "Rice Farming",
    date: "May 22, 2026",
    title: "Sustainable Rice Production Training",
    description:
      "A field session on seed selection, soil preparation, water management, and practical pest control methods.",
  },
  {
    category: "Fishery",
    date: "June 3, 2026",
    title: "Fisherfolk Equipment Support Briefing",
    description:
      "Qualified members can attend the orientation for equipment assistance, safety reminders, and program requirements.",
  },
];

const galleryItems = [
  {
    title: "Rice Fields",
    label: "Cultivation",
    image:
      "https://images.unsplash.com/photo-1751818430558-1c2a12283155?auto=format&fit=crop&q=80&w=1200",
  },
  {
    title: "Harvest Work",
    label: "Production",
    image:
      "https://images.pexels.com/photos/11196642/pexels-photo-11196642.jpeg?auto=compress&cs=tinysrgb&w=1200",
  },
  {
    title: "Fisherfolk Livelihood",
    label: "Fishery",
    image:
      "https://images.pexels.com/photos/36724819/pexels-photo-36724819.jpeg?auto=compress&cs=tinysrgb&w=1200",
  },
  {
    title: "Shared Fields",
    label: "Community",
    image:
      "https://images.unsplash.com/photo-1564425230012-925965619730?auto=format&fit=crop&q=80&w=1200",
  },
  {
    title: "Crop Care",
    label: "High-Value Crops",
    image:
      "https://images.unsplash.com/photo-1746014929708-fcb859fd3185?auto=format&fit=crop&q=80&w=1200",
  },
  {
    title: "Fishing Boats",
    label: "Coastal Resources",
    image:
      "https://images.pexels.com/photos/33701901/pexels-photo-33701901.jpeg?auto=compress&cs=tinysrgb&w=1200",
  },
];

export default function LandingPage() {
  const navigate = useNavigate();
  const [showLoginModal, setShowLoginModal] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [loginEmail, setLoginEmail] = useState("chairman@trackcoop.com");
  const [loginPassword, setLoginPassword] = useState("chairman123");
  const [loginRole, setLoginRole] = useState<PortalRole>("chairman");
  const [showMemberModal, setShowMemberModal] = useState(false);
  const [memberStep, setMemberStep] = useState<1 | 2>(1);
  const [membershipForm, setMembershipForm] = useState<MembershipForm>(
    initialMembershipForm
  );
  const [childrenInfo, setChildrenInfo] = useState<ChildInfo[]>([
    { id: 1, name: "Maria Dela Cruz", age: "8", benefit: "Dependent" },
  ]);
  const [showMemberPassword, setShowMemberPassword] = useState(false);
  const [showMemberConfirmPassword, setShowMemberConfirmPassword] =
    useState(false);
  const [membershipAlert, setMembershipAlert] = useState<MembershipAlert>({
    isOpen: false,
    type: "success",
    title: "",
    message: "",
  });
  const [contactSent, setContactSent] = useState(false);
  const [contactForm, setContactForm] = useState({
    name: "",
    email: "",
    sector: "",
    message: "",
  });

  const scrollToSection = (id: string) => {
    document.getElementById(id)?.scrollIntoView({ behavior: "smooth" });
  };

  const handleRoleChange = (newRole: PortalRole) => {
    setLoginRole(newRole);

    if (newRole === "chairman") {
      setLoginEmail("chairman@trackcoop.com");
      setLoginPassword("chairman123");
      return;
    }

    if (newRole === "bookkeeper") {
      setLoginEmail("bookkeeper@trackcoop.com");
      setLoginPassword("bookkeeper123");
      return;
    }

    setLoginEmail("member@trackcoop.com");
    setLoginPassword("member123");
  };

  const handleLogin = (event: FormEvent) => {
    event.preventDefault();
    localStorage.setItem("userRole", loginRole);

    if (loginRole === "chairman") {
      navigate("/dashboard");
    } else if (loginRole === "bookkeeper") {
      navigate("/dashboard/bookkeeper");
    } else {
      navigate("/dashboard/member");
    }
  };

  const updateMembershipForm = (
    field: keyof MembershipForm,
    value: string | boolean
  ) => {
    setMembershipForm((current) => ({ ...current, [field]: value }));
  };

  const closeMemberModal = () => {
    setShowMemberModal(false);
    setMemberStep(1);
  };

  const resetMembershipForm = () => {
    setMembershipForm(initialMembershipForm);
    setChildrenInfo([
      { id: 1, name: "Maria Dela Cruz", age: "8", benefit: "Dependent" },
    ]);
    setShowMemberPassword(false);
    setShowMemberConfirmPassword(false);
  };

  const cancelMemberRegistration = () => {
    resetMembershipForm();
    closeMemberModal();
  };

  const closeMembershipAlert = () => {
    setMembershipAlert((current) => ({ ...current, isOpen: false }));
  };

  const handleMemberNext = (event: FormEvent) => {
    event.preventDefault();
    setMemberStep(2);
  };

  const updateChildInfo = (
    id: number,
    field: keyof Omit<ChildInfo, "id">,
    value: string
  ) => {
    setChildrenInfo((current) =>
      current.map((child) =>
        child.id === id ? { ...child, [field]: value } : child
      )
    );
  };

  const addChildInfo = () => {
    setChildrenInfo((current) => [
      ...current,
      { id: Date.now(), name: "", age: "", benefit: "None" },
    ]);
  };

  const handleMemberRegister = (event: FormEvent) => {
    event.preventDefault();

    if (membershipForm.password !== membershipForm.confirmPassword) {
      setMembershipAlert({
        isOpen: true,
        type: "error",
        title: "Password Mismatch",
        message:
          "Password and confirm password must match before registration can be submitted.",
      });
      return;
    }

    resetMembershipForm();
    closeMemberModal();
    setMembershipAlert({
      isOpen: true,
      type: "success",
      title: "Registration Submitted",
      message:
        "Please wait for confirmation from the Chairman. Your membership request has been received for review.",
    });
  };

  const handleContactSubmit = (event: FormEvent) => {
    event.preventDefault();
    setContactSent(true);
    setContactForm({ name: "", email: "", sector: "", message: "" });
  };

  return (
    <div className="min-h-screen bg-stone-50 text-gray-950">
      <header className="fixed inset-x-0 top-0 z-50 border-b border-white/20 bg-white/90 shadow-sm backdrop-blur-xl">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 md:px-8">
          <TrackCoopLogo
            markClassName="h-10 w-10 shadow-sm"
            titleClassName="text-xl"
            tone="dark"
          />

          <nav className="hidden items-center gap-8 md:flex">
            <button
              onClick={() => scrollToSection("announcements")}
              className="text-sm font-medium text-gray-700 transition-colors hover:text-primary"
            >
              Announcements
            </button>
            <button
              onClick={() => scrollToSection("about")}
              className="text-sm font-medium text-gray-700 transition-colors hover:text-primary"
            >
              About Us
            </button>
            <button
              onClick={() => scrollToSection("gallery")}
              className="text-sm font-medium text-gray-700 transition-colors hover:text-primary"
            >
              Gallery
            </button>
            <button
              onClick={() => scrollToSection("contact")}
              className="text-sm font-medium text-gray-700 transition-colors hover:text-primary"
            >
              Contact
            </button>
          </nav>

          <button
            onClick={() => setShowLoginModal(true)}
            className="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800 hover:shadow-md"
          >
            <LogIn className="h-4 w-4" />
            Portal
          </button>
        </div>
      </header>

      <main>
        <section className="relative flex min-h-[88vh] items-end overflow-hidden">
          <img
            src={heroImage}
            alt="Green rice field in the Philippines"
            className="absolute inset-0 h-full w-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-black/75 via-black/45 to-black/10" />
          <div className="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-stone-50 to-transparent" />

          <div className="relative z-10 mx-auto w-full max-w-7xl px-5 pb-16 pt-32 md:px-8 md:pb-24">
            <div className="max-w-3xl animate-in fade-in slide-in-from-bottom-6 duration-700">
              <p className="mb-5 inline-flex rounded-full border border-white/30 bg-white/15 px-4 py-2 text-sm font-medium text-white shadow-sm backdrop-blur">
                Nasugbu, Batangas
              </p>
              <h1 className="font-display text-4xl font-bold leading-tight text-white md:text-6xl">
                Nasugbu Farmers and Fisherfolks Agriculture Cooperative
              </h1>
              <p className="mt-6 max-w-2xl text-lg leading-8 text-white/90 md:text-xl">
                A community cooperative supporting farmers and fisherfolks through
                shared resources, member participation, livelihood programs, and
                transparent cooperative service.
              </p>
              <div className="mt-8 flex flex-wrap gap-3">
                <button
                  onClick={() => setShowMemberModal(true)}
                  className="inline-flex items-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                >
                  Become A Member
                  <UserPlus className="h-4 w-4" />
                </button>
                <button
                  onClick={() => scrollToSection("about")}
                  className="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 font-semibold text-primary shadow-lg transition-all hover:-translate-y-1 hover:bg-green-50 hover:shadow-xl"
                >
                  About the Cooperative
                  <ArrowRight className="h-4 w-4" />
                </button>
                <button
                  onClick={() => setShowLoginModal(true)}
                  className="inline-flex items-center gap-2 rounded-lg border border-white/35 bg-white/10 px-5 py-3 font-semibold text-white backdrop-blur transition-all hover:-translate-y-1 hover:bg-white/20"
                >
                  Member Portal
                </button>
              </div>
            </div>
          </div>
        </section>

        <section id="announcements" className="bg-stone-50 px-5 py-20 md:px-8">
          <div className="mx-auto max-w-7xl">
            <div className="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
              <div>
                <p className="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-primary">
                  Cooperative Updates
                </p>
                <h2 className="font-display text-3xl font-bold text-gray-950 md:text-4xl">
                  Announcements
                </h2>
              </div>
              <p className="max-w-2xl text-gray-600">
                Important schedules, member reminders, and sector activities for
                farmers and fisherfolks.
              </p>
            </div>

            <div className="grid gap-5 md:grid-cols-3">
              {announcements.map((announcement, index) => (
                <article
                  key={announcement.title}
                  className="group rounded-lg border border-stone-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-xl"
                  style={{ animationDelay: `${index * 90}ms` }}
                >
                  <div className="mb-5 flex items-center justify-between gap-3">
                    <span className="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-primary">
                      {announcement.category}
                    </span>
                    <Megaphone className="h-5 w-5 text-primary transition-transform group-hover:rotate-[-8deg] group-hover:scale-110" />
                  </div>
                  <div className="mb-4 flex items-center gap-2 text-sm text-gray-500">
                    <CalendarDays className="h-4 w-4" />
                    {announcement.date}
                  </div>
                  <h3 className="mb-3 font-display text-xl font-bold text-gray-950">
                    {announcement.title}
                  </h3>
                  <p className="leading-7 text-gray-600">
                    {announcement.description}
                  </p>
                </article>
              ))}
            </div>
          </div>
        </section>

        <section id="about" className="bg-white px-5 py-20 md:px-8">
          <div className="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
            <div className="relative min-h-[520px] overflow-hidden rounded-lg">
              <img
                src="https://images.pexels.com/photos/11196642/pexels-photo-11196642.jpeg?auto=compress&cs=tinysrgb&w=1400"
                alt="Farmers harvesting rice in a field"
                className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 hover:scale-105"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent" />
              <div className="absolute bottom-6 left-6 right-6 text-white">
                <p className="mb-2 text-sm font-semibold uppercase tracking-[0.16em] text-green-100">
                  Community First
                </p>
                <p className="max-w-md text-lg font-semibold">
                  Working together to strengthen local agriculture, fishery,
                  and member livelihood.
                </p>
              </div>
            </div>

            <div>
              <p className="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-primary">
                About Us
              </p>
              <h2 className="font-display text-3xl font-bold leading-tight text-gray-950 md:text-4xl">
                A cooperative built around shared effort and shared progress.
              </h2>
              <p className="mt-6 leading-8 text-gray-700">
                The Nasugbu Farmers and Fisherfolks Agriculture Cooperative
                brings together local producers who rely on land, sea, and
                community support. The cooperative helps members coordinate
                programs, maintain records, communicate announcements, and keep
                track of opportunities that support daily livelihood.
              </p>
              <p className="mt-5 leading-8 text-gray-700">
                Its work centers on member participation, responsible share
                capital management, training, agricultural support, fishery
                assistance, and transparent decision-making for every sector.
              </p>

              <div className="mt-8 grid gap-4 sm:grid-cols-3">
                <div className="rounded-lg border border-green-100 bg-green-50 p-5 transition-transform hover:-translate-y-1">
                  <Sprout className="mb-4 h-6 w-6 text-primary" />
                  <p className="font-semibold text-gray-950">Farm Support</p>
                  <p className="mt-2 text-sm leading-6 text-gray-600">
                    Rice, corn, livestock, and high-value crop members.
                  </p>
                </div>
                <div className="rounded-lg border border-blue-100 bg-blue-50 p-5 transition-transform hover:-translate-y-1">
                  <Waves className="mb-4 h-6 w-6 text-blue-700" />
                  <p className="font-semibold text-gray-950">Fishery Programs</p>
                  <p className="mt-2 text-sm leading-6 text-gray-600">
                    Coastal livelihood support and sector coordination.
                  </p>
                </div>
                <div className="rounded-lg border border-amber-100 bg-amber-50 p-5 transition-transform hover:-translate-y-1">
                  <MapPin className="mb-4 h-6 w-6 text-amber-700" />
                  <p className="font-semibold text-gray-950">Local Service</p>
                  <p className="mt-2 text-sm leading-6 text-gray-600">
                    Grounded in Nasugbu communities and member needs.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section id="gallery" className="bg-stone-100 px-5 py-20 md:px-8">
          <div className="mx-auto max-w-7xl">
            <div className="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
              <div>
                <p className="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-primary">
                  Cooperative Life
                </p>
                <h2 className="font-display text-3xl font-bold text-gray-950 md:text-4xl">
                  Gallery
                </h2>
              </div>
              <p className="max-w-2xl text-gray-600">
                A look at the fields, waters, and working communities connected
                to cooperative service.
              </p>
            </div>

            <div className="grid auto-rows-[260px] gap-4 md:grid-cols-3">
              {galleryItems.map((item, index) => (
                <figure
                  key={item.title}
                  className={`group relative overflow-hidden rounded-lg bg-gray-200 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl ${
                    index === 0 || index === 2 ? "md:row-span-2" : ""
                  }`}
                >
                  <img
                    src={item.image}
                    alt={item.title}
                    className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-80 transition-opacity group-hover:opacity-95" />
                  <figcaption className="absolute bottom-0 left-0 right-0 p-5 text-white">
                    <span className="mb-2 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur">
                      {item.label}
                    </span>
                    <p className="font-display text-2xl font-bold">
                      {item.title}
                    </p>
                  </figcaption>
                </figure>
              ))}
            </div>
          </div>
        </section>

        <section id="contact" className="bg-white px-5 py-20 md:px-8">
          <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
            <div>
              <p className="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-primary">
                Contact Us
              </p>
              <h2 className="font-display text-3xl font-bold leading-tight text-gray-950 md:text-4xl">
                Reach the cooperative office.
              </h2>
              <p className="mt-5 max-w-2xl leading-8 text-gray-700">
                Send questions about announcements, membership, sector
                activities, share capital records, or cooperative support
                programs. The office will coordinate with the right team.
              </p>

              <div className="mt-8 space-y-4">
                <div className="group flex gap-4 rounded-lg border border-stone-200 bg-stone-50 p-5 transition-all hover:-translate-y-1 hover:border-primary/25 hover:bg-green-50">
                  <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary text-white shadow-sm transition-transform group-hover:scale-105">
                    <MapPin className="h-5 w-5" />
                  </span>
                  <div>
                    <p className="font-semibold text-gray-950">Office Address</p>
                    <p className="mt-1 text-sm leading-6 text-gray-600">
                      Camp Avejar, Nasugbu, Batangas, Philippines
                    </p>
                  </div>
                </div>

                <div className="group flex gap-4 rounded-lg border border-stone-200 bg-stone-50 p-5 transition-all hover:-translate-y-1 hover:border-primary/25 hover:bg-green-50">
                  <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-700 text-white shadow-sm transition-transform group-hover:scale-105">
                    <Phone className="h-5 w-5" />
                  </span>
                  <div>
                    <p className="font-semibold text-gray-950">Phone</p>
                    <p className="mt-1 text-sm leading-6 text-gray-600">
                      +63 912 345 6789 / +63 917 876 5432
                    </p>
                  </div>
                </div>

                <div className="group flex gap-4 rounded-lg border border-stone-200 bg-stone-50 p-5 transition-all hover:-translate-y-1 hover:border-primary/25 hover:bg-green-50">
                  <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-600 text-white shadow-sm transition-transform group-hover:scale-105">
                    <Clock className="h-5 w-5" />
                  </span>
                  <div>
                    <p className="font-semibold text-gray-950">Office Hours</p>
                    <p className="mt-1 text-sm leading-6 text-gray-600">
                      Monday to Friday, 8:00 AM - 5:00 PM
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div className="relative overflow-hidden rounded-lg bg-[#12372a] p-1 shadow-xl">
              <img
                src="https://images.pexels.com/photos/33701901/pexels-photo-33701901.jpeg?auto=compress&cs=tinysrgb&w=1400"
                alt="Traditional fishing boat in the Philippines"
                className="absolute inset-0 h-full w-full object-cover opacity-35"
              />
              <div className="absolute inset-0 bg-gradient-to-br from-[#08251c]/95 via-[#12372a]/88 to-[#164e63]/78" />
              <form
                onSubmit={handleContactSubmit}
                className="relative space-y-5 rounded-lg border border-white/15 bg-white/10 p-6 text-white shadow-2xl backdrop-blur-md md:p-8"
              >
                <div>
                  <h3 className="font-display text-2xl font-bold">Send a Message</h3>
                  <p className="mt-2 text-sm leading-6 text-white/75">
                    Leave your details and the cooperative office will follow up.
                  </p>
                </div>

                {contactSent && (
                  <div className="flex items-center gap-3 rounded-lg border border-green-200/40 bg-green-300/15 px-4 py-3 text-sm text-green-50">
                    <CheckCircle2 className="h-5 w-5 shrink-0" />
                    Message noted. Please wait for office confirmation.
                  </div>
                )}

                <div className="grid gap-4 md:grid-cols-2">
                  <div>
                    <label htmlFor="contact-name" className="mb-2 block text-sm">
                      Full Name
                    </label>
                    <input
                      id="contact-name"
                      type="text"
                      value={contactForm.name}
                      onChange={(event) =>
                        setContactForm({ ...contactForm, name: event.target.value })
                      }
                      className="w-full rounded-lg border border-white/20 bg-white/90 px-4 py-3 text-gray-950 shadow-sm transition-all placeholder:text-gray-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-300"
                      placeholder="Juan dela Cruz"
                      required
                    />
                  </div>

                  <div>
                    <label htmlFor="contact-email" className="mb-2 block text-sm">
                      Email Address
                    </label>
                    <input
                      id="contact-email"
                      type="email"
                      value={contactForm.email}
                      onChange={(event) =>
                        setContactForm({ ...contactForm, email: event.target.value })
                      }
                      className="w-full rounded-lg border border-white/20 bg-white/90 px-4 py-3 text-gray-950 shadow-sm transition-all placeholder:text-gray-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-300"
                      placeholder="juan@example.com"
                      required
                    />
                  </div>
                </div>

                <div>
                  <label htmlFor="contact-sector" className="mb-2 block text-sm">
                    Sector
                  </label>
                  <select
                    id="contact-sector"
                    value={contactForm.sector}
                    onChange={(event) =>
                      setContactForm({ ...contactForm, sector: event.target.value })
                    }
                    className="w-full rounded-lg border border-white/20 bg-white/90 px-4 py-3 text-gray-950 shadow-sm transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-300"
                    required
                  >
                    <option value="">Select your sector</option>
                    <option value="rice_farming">Rice Farming</option>
                    <option value="corn">Corn</option>
                    <option value="fishery">Fishery</option>
                    <option value="livestock">Livestock</option>
                    <option value="high_value_crops">High-Value Crops</option>
                  </select>
                </div>

                <div>
                  <label htmlFor="contact-message" className="mb-2 block text-sm">
                    Message
                  </label>
                  <textarea
                    id="contact-message"
                    value={contactForm.message}
                    onChange={(event) =>
                      setContactForm({ ...contactForm, message: event.target.value })
                    }
                    className="min-h-[140px] w-full rounded-lg border border-white/20 bg-white/90 px-4 py-3 text-gray-950 shadow-sm transition-all placeholder:text-gray-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-green-300"
                    placeholder="How can the cooperative office help?"
                    required
                  />
                </div>

                <button
                  type="submit"
                  className="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-300 px-5 py-3.5 font-semibold text-green-950 shadow-lg transition-all hover:-translate-y-1 hover:bg-green-200 hover:shadow-xl"
                >
                  Send Message
                  <Send className="h-4 w-4" />
                </button>
              </form>
            </div>
          </div>
        </section>
      </main>

      <footer className="relative overflow-hidden bg-[#08251c] px-5 py-14 text-white md:px-8">
        <img
          src={heroImage}
          alt=""
          aria-hidden="true"
          className="absolute inset-0 h-full w-full object-cover opacity-20"
        />
        <div className="absolute inset-0 bg-gradient-to-br from-[#041712]/95 via-[#0f3a2d]/92 to-[#164e63]/85" />

        <div className="relative mx-auto max-w-7xl">
          <div className="grid gap-6 lg:grid-cols-[1.25fr_0.75fr_1fr_0.85fr]">
            <div className="rounded-lg border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-md">
              <TrackCoopLogo
                className="mb-4"
                markClassName="h-11 w-11 shadow-sm"
                titleClassName="text-2xl"
              />
              <p className="max-w-md text-sm leading-7 text-white/80">
                Serving cooperative members through organized information,
                transparent updates, and sector-focused support for farmers and
                fisherfolks in Nasugbu.
              </p>
              <button
                onClick={() => setShowLoginModal(true)}
                className="mt-6 inline-flex items-center gap-2 rounded-lg bg-green-300 px-5 py-3 font-semibold text-green-950 shadow-sm transition-all hover:-translate-y-1 hover:bg-green-200"
              >
                Open Portal
                <ArrowRight className="h-4 w-4" />
              </button>
            </div>

            <div className="rounded-lg border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-md">
              <h4 className="mb-4 font-display text-xl font-bold">Quick Links</h4>
              <ul className="space-y-3 text-sm text-white/80">
                <li>
                  <button
                    onClick={() => scrollToSection("announcements")}
                    className="transition-colors hover:text-green-200"
                  >
                    Announcements
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => scrollToSection("about")}
                    className="transition-colors hover:text-green-200"
                  >
                    About Us
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => scrollToSection("gallery")}
                    className="transition-colors hover:text-green-200"
                  >
                    Gallery
                  </button>
                </li>
                <li>
                  <button
                    onClick={() => scrollToSection("contact")}
                    className="transition-colors hover:text-green-200"
                  >
                    Contact Us
                  </button>
                </li>
              </ul>
            </div>

            <div className="rounded-lg border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-md">
              <h4 className="mb-4 font-display text-xl font-bold">Contact</h4>
              <ul className="space-y-4 text-sm text-white/80">
                <li className="flex gap-3">
                  <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-green-200" />
                  <span>Camp Avejar, Nasugbu, Batangas, Philippines</span>
                </li>
                <li className="flex gap-3">
                  <Mail className="mt-0.5 h-4 w-4 shrink-0 text-green-200" />
                  <span>info@trackcoop.org</span>
                </li>
                <li className="flex gap-3">
                  <Phone className="mt-0.5 h-4 w-4 shrink-0 text-green-200" />
                  <span>+63 912 345 6789</span>
                </li>
              </ul>
            </div>

            <div className="rounded-lg border border-white/15 bg-white/10 p-6 shadow-2xl backdrop-blur-md">
              <h4 className="mb-4 font-display text-xl font-bold">Office Hours</h4>
              <ul className="space-y-3 text-sm text-white/80">
                <li>Monday - Friday</li>
                <li className="text-lg font-semibold text-white">8:00 AM - 5:00 PM</li>
                <li>Saturday</li>
                <li className="font-semibold text-white">9:00 AM - 1:00 PM</li>
                <li className="pt-2 text-white/60">Sunday: Closed</li>
              </ul>
            </div>
          </div>

          <div className="mt-8 border-t border-white/15 pt-6 text-center text-sm text-white/65">
            Copyright 2026 TrackCOOP. Nasugbu Farmers and Fisherfolks Agriculture Cooperative.
          </div>
        </div>
      </footer>

      {showMemberModal && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
          onClick={closeMemberModal}
        >
          <div
            className="max-h-[calc(100vh-2rem)] w-full max-w-5xl overflow-y-auto rounded-lg border border-white/40 bg-white/95 shadow-2xl animate-in fade-in zoom-in-95 duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="sticky top-0 z-10 border-b border-green-100 bg-white/90 px-6 py-4 backdrop-blur">
              <div className="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                <div>
                  <h2 className="font-display text-2xl font-bold text-gray-950">
                    Member Registration
                  </h2>
                  <p className="mt-1 text-sm text-gray-500">
                    {memberStep === 1
                      ? "Personal details"
                      : "Family information and account security"}
                  </p>
                </div>
                <span className="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-primary">
                  Page {memberStep} of 2
                </span>
                <button
                  onClick={closeMemberModal}
                  className="ml-auto rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800"
                  aria-label="Close registration modal"
                >
                  <X className="h-5 w-5" />
                </button>
              </div>
            </div>

            {memberStep === 1 ? (
              <form onSubmit={handleMemberNext} className="space-y-6 p-6">
                <div>
                  <p className="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    Personal Details
                  </p>
                  <div className="grid gap-4 md:grid-cols-2">
                    <div>
                      <label htmlFor="member-first-name" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        First Name
                      </label>
                      <input
                        id="member-first-name"
                        value={membershipForm.firstName}
                        onChange={(event) =>
                          updateMembershipForm("firstName", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="First name"
                        required
                      />
                    </div>

                    <div>
                      <label htmlFor="member-middle-name" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Middle Name
                      </label>
                      <input
                        id="member-middle-name"
                        value={membershipForm.middleName}
                        onChange={(event) =>
                          updateMembershipForm("middleName", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Middle name"
                      />
                    </div>

                    <div>
                      <label htmlFor="member-last-name" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Last Name
                      </label>
                      <input
                        id="member-last-name"
                        value={membershipForm.lastName}
                        onChange={(event) =>
                          updateMembershipForm("lastName", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Last name"
                        required
                      />
                    </div>

                    <div>
                      <label htmlFor="member-email" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Email
                      </label>
                      <input
                        id="member-email"
                        type="email"
                        value={membershipForm.email}
                        onChange={(event) =>
                          updateMembershipForm("email", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Email address"
                        required
                      />
                    </div>

                    <div>
                      <label htmlFor="member-phone" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Phone
                      </label>
                      <div className="relative">
                        <span className="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-teal-700">
                          +63
                        </span>
                        <input
                          id="member-phone"
                          type="tel"
                          value={membershipForm.phone}
                          onChange={(event) =>
                            updateMembershipForm("phone", event.target.value)
                          }
                          className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 pl-16 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                          placeholder="9123456789"
                          required
                        />
                      </div>
                    </div>

                    <div>
                      <label htmlFor="member-status" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Status
                      </label>
                      <select
                        id="member-status"
                        value={membershipForm.status}
                        onChange={(event) =>
                          updateMembershipForm("status", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        required
                      >
                        <option value="">Select Status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="separated">Separated</option>
                      </select>
                    </div>

                    <div>
                      <label htmlFor="member-birth-place" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Place of Birth
                      </label>
                      <input
                        id="member-birth-place"
                        value={membershipForm.placeOfBirth}
                        onChange={(event) =>
                          updateMembershipForm("placeOfBirth", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Place of birth"
                        required
                      />
                    </div>

                    <div>
                      <label htmlFor="member-birthday" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Birthday
                      </label>
                      <input
                        id="member-birthday"
                        type="date"
                        value={membershipForm.birthday}
                        onChange={(event) =>
                          updateMembershipForm("birthday", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        required
                      />
                    </div>

                    <div>
                      <label htmlFor="member-current-home" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Current Home
                      </label>
                      <select
                        id="member-current-home"
                        value={membershipForm.currentHome}
                        onChange={(event) =>
                          updateMembershipForm("currentHome", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        required
                      >
                        <option value="">Select Barangay</option>
                        <option value="calayo">Calayo</option>
                        <option value="wawa">Wawa</option>
                        <option value="banilad">Banilad</option>
                        <option value="bilaran">Bilaran</option>
                        <option value="tumalim">Tumalim</option>
                        <option value="kayrilaw">Kayrilaw</option>
                      </select>
                    </div>

                    <div>
                      <label htmlFor="member-occupation" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Work/Occupation
                      </label>
                      <input
                        id="member-occupation"
                        value={membershipForm.occupation}
                        onChange={(event) =>
                          updateMembershipForm("occupation", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Occupation"
                        required
                      />
                    </div>
                  </div>
                </div>

                <div className="grid gap-3 pt-2 sm:grid-cols-2">
                  <button
                    type="button"
                    onClick={cancelMemberRegistration}
                    className="inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-6 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-red-700"
                  >
                    <X className="h-4 w-4" />
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800"
                  >
                    Next
                    <ChevronRight className="h-4 w-4" />
                  </button>
                </div>
              </form>
            ) : (
              <form onSubmit={handleMemberRegister} className="space-y-6 p-6">
                <div>
                  <p className="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    Family Information
                  </p>
                  <div className="grid gap-4 md:grid-cols-3">
                    <div>
                      <label htmlFor="member-father" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Father's Name
                      </label>
                      <input
                        id="member-father"
                        value={membershipForm.fatherName}
                        onChange={(event) =>
                          updateMembershipForm("fatherName", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Father's name"
                        required
                      />
                    </div>
                    <div>
                      <label htmlFor="member-mother" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Mother's Name
                      </label>
                      <input
                        id="member-mother"
                        value={membershipForm.motherName}
                        onChange={(event) =>
                          updateMembershipForm("motherName", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Mother's name"
                        required
                      />
                    </div>
                    <div>
                      <label htmlFor="member-wife" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Wife's Name
                      </label>
                      <input
                        id="member-wife"
                        value={membershipForm.wifeName}
                        onChange={(event) =>
                          updateMembershipForm("wifeName", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Wife's name, if married"
                      />
                    </div>
                  </div>
                </div>

                <div>
                  <div className="mb-4 flex items-center justify-between gap-4">
                    <p className="text-xs font-bold uppercase tracking-[0.18em] text-primary">
                      Children Information
                    </p>
                    <button
                      type="button"
                      onClick={addChildInfo}
                      className="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800"
                      aria-label="Add child"
                    >
                      <Plus className="h-4 w-4" />
                    </button>
                  </div>

                  <div className="space-y-3">
                    {childrenInfo.map((child) => (
                      <div key={child.id} className="grid gap-4 md:grid-cols-[1fr_0.45fr_0.7fr]">
                        <div>
                          <label htmlFor={`child-name-${child.id}`} className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                            Child's Name
                          </label>
                          <input
                            id={`child-name-${child.id}`}
                            value={child.name}
                            onChange={(event) =>
                              updateChildInfo(child.id, "name", event.target.value)
                            }
                            className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                            placeholder="Child's name"
                          />
                        </div>
                        <div>
                          <label htmlFor={`child-age-${child.id}`} className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                            Age
                          </label>
                          <input
                            id={`child-age-${child.id}`}
                            type="number"
                            min="0"
                            value={child.age}
                            onChange={(event) =>
                              updateChildInfo(child.id, "age", event.target.value)
                            }
                            className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                            placeholder="Age"
                          />
                        </div>
                        <div>
                          <label htmlFor={`child-benefit-${child.id}`} className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                            Benefit
                          </label>
                          <select
                            id={`child-benefit-${child.id}`}
                            value={child.benefit}
                            onChange={(event) =>
                              updateChildInfo(child.id, "benefit", event.target.value)
                            }
                            className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                          >
                            <option value="None">None</option>
                            <option value="Dependent">Dependent</option>
                            <option value="Scholarship">Scholarship</option>
                            <option value="Medical">Medical</option>
                          </select>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                <div>
                  <p className="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-primary">
                    Account Security
                  </p>
                  <div className="grid gap-4 md:grid-cols-3">
                    <div>
                      <label htmlFor="member-username" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Username
                      </label>
                      <input
                        id="member-username"
                        value={membershipForm.username}
                        onChange={(event) =>
                          updateMembershipForm("username", event.target.value)
                        }
                        className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                        placeholder="Create username"
                        required
                      />
                    </div>
                    <div>
                      <label htmlFor="member-password" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Password
                      </label>
                      <div className="relative">
                        <input
                          id="member-password"
                          type={showMemberPassword ? "text" : "password"}
                          value={membershipForm.password}
                          onChange={(event) =>
                            updateMembershipForm("password", event.target.value)
                          }
                          className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 pr-11 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                          placeholder="Create password"
                          required
                        />
                        <button
                          type="button"
                          onClick={() => setShowMemberPassword(!showMemberPassword)}
                          className="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-gray-500 hover:text-gray-800"
                          aria-label={showMemberPassword ? "Hide password" : "Show password"}
                        >
                          {showMemberPassword ? (
                            <EyeOff className="h-4 w-4" />
                          ) : (
                            <Eye className="h-4 w-4" />
                          )}
                        </button>
                      </div>
                    </div>
                    <div>
                      <label htmlFor="member-confirm-password" className="mb-2 block text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Confirm Password
                      </label>
                      <div className="relative">
                        <input
                          id="member-confirm-password"
                          type={showMemberConfirmPassword ? "text" : "password"}
                          value={membershipForm.confirmPassword}
                          onChange={(event) =>
                            updateMembershipForm("confirmPassword", event.target.value)
                          }
                          className="w-full rounded-lg border border-green-100 bg-green-50/40 px-4 py-3 pr-11 text-gray-950 outline-none transition-all focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                          placeholder="Confirm password"
                          required
                        />
                        <button
                          type="button"
                          onClick={() =>
                            setShowMemberConfirmPassword(!showMemberConfirmPassword)
                          }
                          className="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-gray-500 hover:text-gray-800"
                          aria-label={
                            showMemberConfirmPassword
                              ? "Hide confirm password"
                              : "Show confirm password"
                          }
                        >
                          {showMemberConfirmPassword ? (
                            <EyeOff className="h-4 w-4" />
                          ) : (
                            <Eye className="h-4 w-4" />
                          )}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="flex flex-col gap-3 text-sm text-gray-600 md:flex-row md:items-center md:justify-between">
                  <label className="inline-flex items-start gap-3">
                    <input
                      type="checkbox"
                      checked={membershipForm.agreedToTerms}
                      onChange={(event) =>
                        updateMembershipForm("agreedToTerms", event.target.checked)
                      }
                      className="mt-1 h-4 w-4 rounded border-green-200 text-primary focus:ring-primary"
                      required
                    />
                    <span>
                      I agree to the{" "}
                      <span className="font-semibold text-primary">
                        Terms and Conditions
                      </span>
                    </span>
                  </label>
                  <button
                    type="button"
                    onClick={() => {
                      closeMemberModal();
                      setShowLoginModal(true);
                    }}
                    className="text-left font-semibold text-primary hover:underline md:text-right"
                  >
                    Already have an account? Login here
                  </button>
                </div>

                <div className="grid gap-3 pt-2 sm:grid-cols-3">
                  <button
                    type="button"
                    onClick={cancelMemberRegistration}
                    className="inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-6 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-red-700"
                  >
                    <X className="h-4 w-4" />
                    Cancel
                  </button>
                  <button
                    type="button"
                    onClick={() => setMemberStep(1)}
                    className="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800"
                  >
                    <ChevronLeft className="h-4 w-4" />
                    Back
                  </button>
                  <button
                    type="submit"
                    className="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800"
                  >
                    Register
                    <UserPlus className="h-4 w-4" />
                  </button>
                </div>
              </form>
            )}
          </div>
        </div>
      )}

      {membershipAlert.isOpen && (
        <div
          className="fixed inset-0 z-[120] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
          onClick={closeMembershipAlert}
          role="dialog"
          aria-modal="true"
          aria-labelledby="membership-alert-title"
        >
          <div
            className="w-full max-w-md animate-in fade-in zoom-in-95 overflow-hidden rounded-lg border border-white/50 bg-white shadow-2xl duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="p-6">
              <div className="flex items-start gap-4">
                <div
                  className={`flex h-12 w-12 shrink-0 items-center justify-center rounded-lg ${
                    membershipAlert.type === "success"
                      ? "bg-green-100 text-green-700"
                      : "bg-red-100 text-red-700"
                  }`}
                >
                  {membershipAlert.type === "success" ? (
                    <CheckCircle2 className="h-6 w-6" />
                  ) : (
                    <AlertTriangle className="h-6 w-6" />
                  )}
                </div>
                <div className="min-w-0 flex-1">
                  <h2
                    id="membership-alert-title"
                    className="font-display text-2xl font-bold text-gray-950"
                  >
                    {membershipAlert.title}
                  </h2>
                  <p className="mt-2 leading-7 text-gray-600">
                    {membershipAlert.message}
                  </p>
                </div>
                <button
                  type="button"
                  onClick={closeMembershipAlert}
                  className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800"
                  aria-label="Close message"
                >
                  <X className="h-5 w-5" />
                </button>
              </div>
            </div>
            <div className="border-t border-gray-100 bg-gray-50 px-6 py-4">
              <button
                type="button"
                onClick={closeMembershipAlert}
                className={`w-full rounded-lg px-5 py-3 font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md ${
                  membershipAlert.type === "success"
                    ? "bg-primary hover:bg-green-800"
                    : "bg-red-600 hover:bg-red-700"
                }`}
              >
                OK
              </button>
            </div>
          </div>
        </div>
      )}

      {showLoginModal && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/55 p-4 backdrop-blur-sm"
          onClick={() => setShowLoginModal(false)}
        >
          <div
            className="w-full max-w-md animate-in fade-in zoom-in-95 rounded-lg bg-white shadow-2xl duration-200"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start justify-between border-b border-gray-200 p-6">
              <div>
                <h2 className="font-display text-2xl font-bold text-gray-950">
                  Welcome Back
                </h2>
                <p className="mt-1 text-sm text-gray-600">
                  Sign in to access your TrackCOOP portal.
                </p>
              </div>
              <button
                onClick={() => setShowLoginModal(false)}
                className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800"
                aria-label="Close login modal"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleLogin} className="space-y-5 p-6">
              <div>
                <label
                  htmlFor="login-email"
                  className="mb-2 block text-sm font-medium text-gray-900"
                >
                  Email Address
                </label>
                <input
                  id="login-email"
                  type="email"
                  value={loginEmail}
                  onChange={(event) => setLoginEmail(event.target.value)}
                  className="w-full rounded-lg border border-gray-300 px-4 py-3 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary"
                  placeholder="you@example.com"
                  required
                />
              </div>

              <div>
                <label
                  htmlFor="login-password"
                  className="mb-2 block text-sm font-medium text-gray-900"
                >
                  Password
                </label>
                <div className="relative">
                  <input
                    id="login-password"
                    type={showPassword ? "text" : "password"}
                    value={loginPassword}
                    onChange={(event) => setLoginPassword(event.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="Password"
                    required
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1 text-gray-500 transition-colors hover:text-gray-800"
                    aria-label={showPassword ? "Hide password" : "Show password"}
                  >
                    {showPassword ? (
                      <EyeOff className="h-5 w-5" />
                    ) : (
                      <Eye className="h-5 w-5" />
                    )}
                  </button>
                </div>
              </div>

              <div>
                <label
                  htmlFor="login-role"
                  className="mb-2 block text-sm font-medium text-gray-900"
                >
                  Select Role
                </label>
                <select
                  id="login-role"
                  value={loginRole}
                  onChange={(event) =>
                    handleRoleChange(event.target.value as PortalRole)
                  }
                  className="w-full cursor-pointer rounded-lg border border-gray-300 px-4 py-3 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary"
                >
                  <option value="chairman">Chairman</option>
                  <option value="bookkeeper">Bookkeeper</option>
                  <option value="member">Member</option>
                </select>
                <p className="mt-2 text-xs text-gray-500">
                  Demo credentials auto-fill based on the selected role.
                </p>
              </div>

              <button
                type="submit"
                className="w-full rounded-lg bg-primary py-3.5 text-lg font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-green-800 hover:shadow-md"
              >
                Sign In
              </button>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
