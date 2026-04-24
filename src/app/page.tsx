"use client";

import Image from "next/image";
import { useState } from "react";

type Language = "en" | "fil";

const photos = {
  hero:
    "https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=1800",
  cardOne:
    "https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
  cardTwo:
    "https://images.unsplash.com/photo-1500651230702-0e2d8a49d4ad?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
  cardThree:
    "https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
  cardFour:
    "https://images.unsplash.com/photo-1523741543316-beb7fc7023d8?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
  impact:
    "https://images.unsplash.com/photo-1574943320219-553eb213f72d?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=1200",
  mission:
    "https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=1800",
  feedOne:
    "https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
  feedTwo:
    "https://images.unsplash.com/photo-1492496913980-501348b61469?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
  feedThree:
    "https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
  feedFour:
    "https://images.unsplash.com/photo-1500651230702-0e2d8a49d4ad?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=900",
} as const;

const content = {
  en: {
    topbar: "Serving farmers and fisherfolk in Nasugbu, Batangas",
    nav: ["Home", "About", "Gallery", "Updates", "Contact"],
    contactButton: "Contact Us",
    language: "Language",
    heroEyebrow: "Empowering Our Cooperative Community",
    heroTitle: "The Journey Of Farmers And Fisherfolk Together",
    aboutKicker: "About Us",
    aboutTitle: "Agricultural Empowerment Initiative",
    impactKicker: "Our Impact",
    impactTitle: "Transformation Through Community Building",
    impactText:
      "A welcoming online home that reflects the strength, dignity, and daily work of the cooperative community.",
    missionKicker: "Our Mission",
    missionTitle:
      "Join Us In Our Mission To Strengthen Agriculture And Empower Our Cooperative Community",
    missionText:
      "A clear and beautiful homepage helps members stay connected, informed, and welcomed.",
    missionButton: "Learn More",
    feedTitle: "Instagram Feed",
    contactKicker: "Contact Us",
    addressLabel: "Office Address",
    address: "Sitio Camp Avejar, Brgy. Lumbangan, Nasugbu, Batangas",
    audienceLabel: "Our Community",
    audience: "Farmers, fisherfolk, officers, and cooperative members",
    messageLabel: "Send A Message",
    placeholders: {
      name: "Your Name",
      email: "Email Address",
      message: "Write your message",
    },
    submit: "Submit",
    footer: ["Home", "About", "Gallery", "Updates", "Contact"],
  },
  fil: {
    topbar: "Naglilingkod para sa mga magsasaka at mangingisda sa Nasugbu, Batangas",
    nav: ["Bahay", "Tungkol", "Gallery", "Updates", "Kontak"],
    contactButton: "Makipag-ugnayan",
    language: "Wika",
    heroEyebrow: "Pinalalakas Ang Komunidad Ng Kooperatiba",
    heroTitle: "Ang Paglalakbay Ng Mga Magsasaka At Mangingisda Nang Sama-Sama",
    aboutKicker: "Tungkol",
    aboutTitle: "Inisyatiba Para Sa Pag-unlad Ng Agrikultura",
    impactKicker: "Aming Epekto",
    impactTitle: "Pagbabago Sa Pamamagitan Ng Pagbuo Ng Komunidad",
    impactText:
      "Isang magiliw na online home na sumasalamin sa lakas, dangal, at araw-araw na gawain ng komunidad ng kooperatiba.",
    missionKicker: "Aming Misyon",
    missionTitle:
      "Samahan Kami Sa Aming Misyon Na Palakasin Ang Agrikultura At Komunidad Ng Kooperatiba",
    missionText:
      "Sa malinaw at magandang homepage, mas nananatiling konektado, may alam, at komportable ang mga miyembro.",
    missionButton: "Alamin Pa",
    feedTitle: "Instagram Feed",
    contactKicker: "Kontak",
    addressLabel: "Lokasyon",
    address: "Sitio Camp Avejar, Brgy. Lumbangan, Nasugbu, Batangas",
    audienceLabel: "Aming Komunidad",
    audience: "Magsasaka, mangingisda, opisyal, at mga miyembro",
    messageLabel: "Magpadala Ng Mensahe",
    placeholders: {
      name: "Iyong Pangalan",
      email: "Email Address",
      message: "Ilagay ang mensahe",
    },
    submit: "Isumite",
    footer: ["Bahay", "Tungkol", "Gallery", "Updates", "Kontak"],
  },
} as const;

function FeedCard({ src, alt }: { src: string; alt: string }) {
  return (
    <div className="feed-card media-frame relative h-44 overflow-hidden rounded-[1.25rem]">
      <Image src={src} alt={alt} fill className="object-cover" sizes="25vw" />
      <div className="absolute inset-0 bg-[rgba(15,54,39,0.16)]" />
      <div className="absolute inset-0 flex items-center justify-center">
        <div className="feed-badge rounded-full border border-white/70 bg-white/15 px-3 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
          IG
        </div>
      </div>
    </div>
  );
}

export default function Home() {
  const [language, setLanguage] = useState<Language>("en");
  const t = content[language];

  return (
    <main className="relative flex-1 overflow-hidden">
      <div className="w-full py-0">
        <section id="home" className="mt-0 overflow-hidden rounded-none bg-white">
          <div className="field-glow relative min-h-[520px] sm:min-h-[620px]">
            <Image
              src={photos.hero}
              alt="Hero field"
              fill
              className="object-cover"
              sizes="100vw"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-[rgba(16,51,37,0.66)] via-[rgba(16,51,37,0.18)] to-[rgba(16,51,37,0.06)]" />
            <div className="absolute inset-x-0 top-0 z-20 px-4 py-4 sm:px-6 lg:px-8">
              <header className="hero-header-glass rounded-[1.4rem] px-4 py-3 sm:px-6">
                <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                  <div className="flex items-center gap-3">
                    <div className="rounded-2xl border border-white/18 bg-white/10 p-2.5 backdrop-blur-sm">
                      <Image
                        src="/trackcoop-logo.svg"
                        alt="TrackCOOP logo"
                        width={42}
                        height={42}
                        priority
                      />
                    </div>
                    <div>
                      <p className="title-serif text-xl font-bold text-white drop-shadow-[0_2px_10px_rgba(0,0,0,0.22)]">
                        TrackCOOP
                      </p>
                      <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/78">
                        NFFAC
                      </p>
                    </div>
                  </div>

                  <div className="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <nav className="flex flex-wrap justify-center gap-1">
                      {t.nav.map((item, index) => {
                        const hrefs = [
                          "#home",
                          "#about",
                          "#gallery",
                          "#updates",
                          "#contact",
                        ];
                        return (
                          <a
                            key={item}
                            href={hrefs[index]}
                            className="nav-pill rounded-full px-3 py-2 text-sm font-bold text-white transition-colors hover:bg-white/14"
                          >
                            {item}
                          </a>
                        );
                      })}
                    </nav>

                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                      <div className="inline-flex items-center gap-1 rounded-full border border-white/18 bg-white/8 p-1 backdrop-blur-sm">
                        <span className="px-2 text-[11px] font-bold uppercase tracking-[0.16em] text-white/78">
                          {t.language}
                        </span>
                        <button
                          type="button"
                          onClick={() => setLanguage("en")}
                          className={`language-chip rounded-full px-3 py-2 text-xs font-bold ${
                            language === "en"
                              ? "bg-white text-[var(--accent-strong)]"
                              : "text-white"
                          }`}
                        >
                          EN
                        </button>
                        <button
                          type="button"
                          onClick={() => setLanguage("fil")}
                          className={`language-chip rounded-full px-3 py-2 text-xs font-bold ${
                            language === "fil"
                              ? "bg-white text-[var(--accent-strong)]"
                              : "text-white"
                          }`}
                        >
                          FIL
                        </button>
                      </div>

                      <a
                        href="#contact"
                        className="action-pill inline-flex items-center justify-center rounded-full border border-white/18 bg-white/12 px-5 py-3 text-sm font-extrabold text-white backdrop-blur-sm"
                      >
                        {t.contactButton}
                      </a>
                    </div>
                  </div>
                </div>
              </header>
            </div>
            <div className="hero-copy absolute inset-x-0 bottom-32 px-6 text-center text-white sm:bottom-36 sm:px-10">
              <p className="mb-3 text-sm font-medium">{t.heroEyebrow}</p>
              <h1 className="title-serif mx-auto max-w-4xl text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                {t.heroTitle}
              </h1>
            </div>
            <a
              href="#about"
              className="scroll-cue absolute bottom-5 left-1/2 z-20 flex -translate-x-1/2 flex-col items-center gap-2 text-white sm:bottom-6"
              aria-label="Scroll down"
            >
              <span className="scroll-mouse flex h-14 w-9 items-start justify-center rounded-full border border-white/50 bg-white/10 pt-2 shadow-[0_10px_22px_rgba(0,0,0,0.18)] backdrop-blur-md">
                <span className="scroll-wheel h-2.5 w-1.5 rounded-full bg-white" />
              </span>
              <span className="text-[10px] font-bold uppercase tracking-[0.24em] text-white/78">
                Scroll
              </span>
            </a>
            <div
              className="landing-torn-edge absolute inset-x-0 bottom-[-1px] z-10 h-28 sm:h-32"
              aria-hidden="true"
            >
              <svg
                viewBox="0 0 1440 180"
                preserveAspectRatio="none"
                className="block h-full w-full"
              >
                <path
                  fill="#fbf8f1"
                  d="M0 12
                  C34 44 68 2 110 28
                  C150 54 190 8 238 34
                  C286 60 328 6 376 30
                  C424 56 468 4 518 36
                  C568 68 610 10 662 38
                  C714 66 756 8 810 34
                  C862 58 906 4 958 30
                  C1010 60 1054 6 1108 34
                  C1160 64 1206 8 1260 32
                  C1312 56 1360 10 1440 26
                  L1440 180 L0 180 Z"
                />
              </svg>
            </div>
          </div>
        </section>

        <section id="about" className="relative bg-[#fbf8f1] px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
          <div className="landing-leaf left-0 top-10 hidden lg:block" />
          <div className="mx-auto max-w-3xl text-center">
            <p className="mb-3 text-sm font-semibold text-[var(--harvest)]">
              {t.aboutKicker}
            </p>
            <h2 className="title-serif text-3xl font-bold text-[var(--accent-strong)] sm:text-4xl">
              {t.aboutTitle}
            </h2>
          </div>

          <div className="mt-10 grid items-end gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <article className="landing-photo-card surface-card rotate-[-4deg]">
              <div className="media-frame relative h-72 overflow-hidden rounded-[1.6rem]">
                <Image src={photos.cardOne} alt="Card one" fill className="object-cover" sizes="25vw" />
              </div>
            </article>
            <article className="landing-photo-card surface-card rotate-[5deg] lg:translate-y-6">
              <div className="media-frame relative h-72 overflow-hidden rounded-[1.6rem]">
                <Image src={photos.cardTwo} alt="Card two" fill className="object-cover" sizes="25vw" />
              </div>
            </article>
            <article className="landing-photo-card surface-card rotate-[-5deg]">
              <div className="media-frame relative h-72 overflow-hidden rounded-[1.6rem]">
                <Image src={photos.cardThree} alt="Card three" fill className="object-cover" sizes="25vw" />
              </div>
            </article>
            <article className="landing-photo-card surface-card rotate-[4deg] lg:translate-y-5">
              <div className="media-frame relative h-72 overflow-hidden rounded-[1.6rem]">
                <Image src={photos.cardFour} alt="Card four" fill className="object-cover" sizes="25vw" />
              </div>
            </article>
          </div>
        </section>

        <section className="relative bg-[#fbf8f1] px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
          <div className="landing-section-surface highlight-surface surface-card grid gap-6 rounded-[1.8rem] px-6 py-8 sm:px-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
            <div>
              <p className="mb-3 text-sm font-semibold text-[var(--harvest)]">
                {t.impactKicker}
              </p>
              <h2 className="title-serif text-3xl font-bold text-[var(--accent-strong)] sm:text-4xl">
                {t.impactTitle}
              </h2>
              <p className="mt-4 max-w-xl text-base leading-8 text-[var(--muted)]">
                {t.impactText}
              </p>
            </div>

            <div className="media-frame relative mx-auto h-[360px] w-full max-w-[430px] overflow-hidden rounded-[1.8rem]">
              <Image
                src={photos.impact}
                alt="Impact section"
                fill
                className="object-cover"
                sizes="40vw"
              />
            </div>
          </div>
        </section>

        <section className="relative overflow-hidden bg-[#fbf8f1] px-0 pb-10 sm:pb-14">
          <div className="relative overflow-hidden rounded-none">
            <Image
              src={photos.mission}
              alt="Mission banner"
              fill
              className="object-cover"
              sizes="100vw"
            />
            <div className="absolute inset-0 bg-gradient-to-r from-[rgba(20,58,43,0.78)] via-[rgba(20,58,43,0.48)] to-[rgba(20,58,43,0.12)]" />
            <div className="relative min-h-[360px] px-6 py-12 text-white sm:px-10 lg:px-14 lg:py-16">
              <p className="mb-3 text-sm font-semibold text-[#dcc889]">
                {t.missionKicker}
              </p>
              <h2 className="title-serif max-w-4xl text-3xl font-bold leading-tight sm:text-4xl lg:text-5xl">
                {t.missionTitle}
              </h2>
              <p className="mt-4 max-w-2xl text-base leading-8 text-white/86">
                {t.missionText}
              </p>
              <a
                href="#contact"
                className="action-pill mt-7 inline-flex items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-extrabold text-[var(--accent-strong)]"
              >
                {t.missionButton}
              </a>
            </div>
            <div
              className="landing-torn-edge absolute inset-x-0 bottom-[-1px] z-10 h-28 sm:h-32"
              aria-hidden="true"
            >
              <svg
                viewBox="0 0 1440 180"
                preserveAspectRatio="none"
                className="block h-full w-full"
              >
                <path
                  fill="#fbf8f1"
                  d="M0 12
                  C34 44 68 2 110 28
                  C150 54 190 8 238 34
                  C286 60 328 6 376 30
                  C424 56 468 4 518 36
                  C568 68 610 10 662 38
                  C714 66 756 8 810 34
                  C862 58 906 4 958 30
                  C1010 60 1054 6 1108 34
                  C1160 64 1206 8 1260 32
                  C1312 56 1360 10 1440 26
                  L1440 180 L0 180 Z"
                />
              </svg>
            </div>
          </div>
        </section>

        <section id="gallery" className="bg-[#fbf8f1] px-4 py-10 text-center sm:px-6 sm:py-14 lg:px-8">
          <p className="mb-6 text-3xl font-bold text-[var(--accent-strong)]">
            {t.feedTitle}
          </p>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <FeedCard src={photos.feedOne} alt="Feed one" />
            <FeedCard src={photos.feedTwo} alt="Feed two" />
            <FeedCard src={photos.feedThree} alt="Feed three" />
            <FeedCard src={photos.feedFour} alt="Feed four" />
          </div>
        </section>

        <section id="contact" className="relative bg-[#fbf8f1] px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
          <div className="landing-leaf right-0 top-0 hidden lg:block" />
          <div className="landing-section-surface rounded-[1.8rem] px-6 py-8 sm:px-8">
            <div className="grid gap-8 lg:grid-cols-[0.72fr_1.28fr]">
              <div>
                <p className="mb-3 text-sm font-semibold text-[var(--harvest)]">
                  {t.contactKicker}
                </p>

                <div className="mt-6 space-y-5 text-sm leading-7 text-[var(--muted)]">
                  <div>
                    <p className="font-extrabold uppercase tracking-[0.16em] text-[var(--accent-strong)]">
                      {t.addressLabel}
                    </p>
                    <p>{t.address}</p>
                  </div>
                  <div>
                    <p className="font-extrabold uppercase tracking-[0.16em] text-[var(--accent-strong)]">
                      {t.audienceLabel}
                    </p>
                    <p>{t.audience}</p>
                  </div>
                </div>
              </div>

              <div>
                <p className="mb-4 text-sm font-extrabold uppercase tracking-[0.16em] text-[var(--accent-strong)]">
                  {t.messageLabel}
                </p>
                <form className="grid gap-4 md:grid-cols-2">
                  <input
                    type="text"
                    placeholder={t.placeholders.name}
                    className="interactive-input rounded-[1rem] border border-[rgba(16,91,63,0.12)] bg-white px-4 py-3 text-sm outline-none transition-colors placeholder:text-[var(--muted)] focus:border-[var(--accent)]"
                  />
                  <input
                    type="email"
                    placeholder={t.placeholders.email}
                    className="interactive-input rounded-[1rem] border border-[rgba(16,91,63,0.12)] bg-white px-4 py-3 text-sm outline-none transition-colors placeholder:text-[var(--muted)] focus:border-[var(--accent)]"
                  />
                  <textarea
                    placeholder={t.placeholders.message}
                    rows={6}
                    className="interactive-input rounded-[1rem] border border-[rgba(16,91,63,0.12)] bg-white px-4 py-3 text-sm outline-none transition-colors placeholder:text-[var(--muted)] focus:border-[var(--accent)] md:col-span-2"
                  />
                  <div className="md:col-span-2">
                    <button
                      type="submit"
                      className="action-pill inline-flex items-center justify-center rounded-full bg-[var(--accent)] px-6 py-3.5 text-sm font-extrabold text-white"
                    >
                      {t.submit}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </section>

        <footer className="rounded-none bg-[var(--accent-strong)] px-6 py-6 text-center text-white">
          <nav className="mb-3 flex flex-wrap justify-center gap-5 text-sm font-semibold">
            {t.footer.map((item) => (
              <span key={item}>{item}</span>
            ))}
          </nav>
          <p className="text-xs uppercase tracking-[0.16em] text-white/70">
            TrackCOOP
          </p>
        </footer>
      </div>
    </main>
  );
}
