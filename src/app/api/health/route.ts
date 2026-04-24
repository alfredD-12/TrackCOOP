import { NextResponse } from "next/server";

export const runtime = "nodejs";

export async function GET() {
  return NextResponse.json({
    application: "TrackCOOP",
    status: "ok",
    timestamp: new Date().toISOString(),
    stack: {
      frontend: "Next.js App Router",
      backend: "Node.js route handlers",
      language: "TypeScript",
    },
    nextSteps: [
      "Add authentication and authorization.",
      "Connect a database.",
      "Create cooperative management modules.",
    ],
  });
}
