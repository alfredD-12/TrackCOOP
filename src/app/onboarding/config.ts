type OnboardingStep = {
  title: string;
  description: string;
  bullets?: string[];
};

type PageOnboarding = {
  pageTitle: string;
  steps: OnboardingStep[];
};

export type OnboardingRole = "chairman" | "bookkeeper" | "member";

export const onboardingConfig: Partial<Record<OnboardingRole, Record<string, PageOnboarding>>> = {
  chairman: {
    "/dashboard": {
      pageTitle: "Chairman Dashboard",
      steps: [
        {
          title: "Sidebar navigation",
          description: "Use the left sidebar to move between the chairman tools.",
          bullets: [
            "Dashboard returns to the main overview.",
            "Documents, Members, Predictions, Announcements, Alerts, Gallery, and Reports open their matching work areas.",
            "Logout signs out of the current account.",
          ],
        },
        {
          title: "Top actions",
          description: "The header buttons give you the fastest route to common dashboard actions.",
          bullets: [
            "View Reports opens the reporting workspace.",
            "Filters opens the dashboard filter panel for year and sector focus.",
          ],
        },
        {
          title: "KPI cards and charts",
          description: "The cards and charts summarize participation, member status, sector performance, and engagement trends.",
          bullets: [
            "KPI cards jump to member or prediction pages.",
            "Chart controls switch periods or metrics.",
            "Clickable charts open detail modals for the selected data.",
          ],
        },
      ],
    },
    "/dashboard/documents": {
      pageTitle: "Documents",
      steps: [
        {
          title: "Document workspace",
          description: "This page stores and organizes cooperative documents for review and download.",
        },
        {
          title: "Primary controls",
          description: "The main actions manage intake and retrieval.",
          bullets: [
            "Upload opens the document upload flow.",
            "Search and date filters narrow the repository.",
            "Category summaries help you scan document volume by type.",
          ],
        },
        {
          title: "Repository table",
          description: "Each row represents a stored document record.",
          bullets: [
            "Open the row tools to preview or download a file.",
            "Processed uploads are added back into the main table.",
          ],
        },
      ],
    },
    "/dashboard/members": {
      pageTitle: "Members",
      steps: [
        {
          title: "Member directory",
          description: "This page tracks member records, status, sectors, and contribution activity.",
        },
        {
          title: "Primary controls",
          description: "Use the main controls to manage the directory.",
          bullets: [
            "Add Member opens the new-member form.",
            "Search finds members by name, ID, or email.",
            "Sector and status filters narrow the table view.",
          ],
        },
        {
          title: "Table and details",
          description: "The table is the main operating surface for member review.",
          bullets: [
            "Column headers sort the directory.",
            "Selecting a member opens the side detail view with share capital and document history.",
            "Pagination moves through the full member list.",
          ],
        },
      ],
    },
    "/dashboard/predictions": {
      pageTitle: "Predictions",
      steps: [
        {
          title: "Prediction overview",
          description: "This page forecasts member engagement and flags at-risk behavior.",
        },
        {
          title: "Primary controls",
          description: "The key buttons update and refine prediction output.",
          bullets: [
            "Generate Predictions refreshes the AI scoring simulation.",
            "Search and filter controls narrow the member prediction list.",
            "Clear Filters resets the current table filters.",
          ],
        },
        {
          title: "Prediction table",
          description: "Each member row shows predicted status, sector, and engagement score.",
          bullets: [
            "Column headers sort the table.",
            "Selecting a row opens detailed risk factors.",
            "Alert actions are used to contact members who need follow-up.",
          ],
        },
      ],
    },
    "/dashboard/announcements": {
      pageTitle: "Announcements and Alerts",
      steps: [
        {
          title: "Communications center",
          description: "This page handles outbound announcements and follow-up alerts for at-risk members.",
        },
        {
          title: "Announcement actions",
          description: "The top controls manage message publishing.",
          bullets: [
            "Compose Announcement opens the compose modal.",
            "Tabs switch between the announcement feed and the member alert list.",
            "View shows the full announcement record.",
          ],
        },
        {
          title: "Row actions",
          description: "Each announcement row includes direct management tools.",
          bullets: [
            "Edit reopens the selected announcement in edit mode.",
            "Delete removes the selected announcement after confirmation.",
            "Pinned and read-progress indicators show visibility and reach.",
          ],
        },
      ],
    },
    "/dashboard/alerts": {
      pageTitle: "System Alerts",
      steps: [
        {
          title: "Alert monitor",
          description: "This page shows important system notices, warnings, and operational reminders.",
        },
        {
          title: "Alert filters",
          description: "The filter buttons group alerts by severity or category.",
        },
        {
          title: "Alert actions",
          description: "Each alert row includes direct response options.",
          bullets: [
            "Take Action opens the related response workflow.",
            "Dismiss clears the alert from immediate attention.",
          ],
        },
      ],
    },
    "/dashboard/gallery": {
      pageTitle: "Gallery",
      steps: [
        {
          title: "Media library",
          description: "This page manages albums and media for cooperative activities.",
        },
        {
          title: "Primary controls",
          description: "The top actions create and add content.",
          bullets: [
            "Create Album opens the album creation modal.",
            "Upload Media opens the media upload modal.",
            "Search and filters narrow albums or photos.",
          ],
        },
        {
          title: "View modes",
          description: "Switch between album-first browsing and full photo browsing.",
        },
        {
          title: "Media actions",
          description: "Album and media cards include direct controls.",
          bullets: [
            "Open an album to inspect only that collection.",
            "Edit and delete tools manage individual records.",
          ],
        },
      ],
    },
    "/dashboard/reports": {
      pageTitle: "Reports",
      steps: [
        {
          title: "Reporting workspace",
          description: "This page generates analytical reports and gives access to recent outputs.",
        },
        {
          title: "Report cards",
          description: "Each report card opens a different report type.",
          bullets: [
            "Generate Report opens the preview panel for that report.",
            "The preview panel supports PDF download and print actions.",
          ],
        },
        {
          title: "Recent reports table",
          description: "This table lists recently generated files for quick reuse.",
          bullets: [
            "Download saves the selected report output.",
          ],
        },
      ],
    },
  },
};
