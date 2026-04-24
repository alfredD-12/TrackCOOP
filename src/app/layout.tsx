import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "TrackCOOP | NFFAC Digital Cooperative Platform",
  description:
    "Landing page for TrackCOOP, the digital document management and analytics platform for the Nasugbu Farmers and Fisherfolks Agriculture Cooperative.",
  icons: {
    icon: "/favicon.ico",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="h-full" suppressHydrationWarning>
      <body className="min-h-full flex flex-col" suppressHydrationWarning>
        {children}
      </body>
    </html>
  );
}
