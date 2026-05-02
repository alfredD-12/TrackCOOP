export type TourPlacement = "top" | "bottom" | "left" | "right";

export interface TourStep {
  path: string;
  selector: string;
  title: string;
  description: string;
  placement?: TourPlacement;
}

export const chairmanTourSteps: TourStep[] = [
  {
    path: "/dashboard",
    selector: '[data-tour="dashboard-sidebar"]',
    title: "Chairman Navigation",
    description:
      "This sidebar is your main route through the chairman tools. It links the dashboard, documents, members, predictions, announcements, alerts, gallery, and reports in one workflow.",
    placement: "right",
  },
  {
    path: "/dashboard",
    selector: '[data-tour="dashboard-view-reports"]',
    title: "View Reports",
    description:
      "Use View Reports to open the reporting workspace quickly when you need summaries, printable outputs, or recent generated files.",
    placement: "left",
  },
  {
    path: "/dashboard",
    selector: '[data-tour="dashboard-filters"]',
    title: "Dashboard Filters",
    description:
      "Filters narrow the overview by year and sector so the cards and charts reflect the slice of the cooperative you want to review.",
    placement: "left",
  },
  {
    path: "/dashboard",
    selector: '[data-tour="dashboard-kpis"]',
    title: "Dashboard Overview",
    description:
      "These KPI cards and charts summarize member activity, sector performance, engagement trends, and risk signals for the chairman.",
    placement: "bottom",
  },
  {
    path: "/dashboard/documents",
    selector: '[data-tour="documents-upload"]',
    title: "Document Upload",
    description:
      "This view stores cooperative files. Use Upload Document to trigger the prototype intake flow for OCR, NLP, and auto-categorization.",
    placement: "left",
  },
  {
    path: "/dashboard/documents",
    selector: '[data-tour="documents-filters"]',
    title: "Document Filters",
    description:
      "Use these controls to search, narrow by type, and filter by upload dates when you need to find a specific document quickly.",
    placement: "bottom",
  },
  {
    path: "/dashboard/documents",
    selector: '[data-tour="documents-table"]',
    title: "Document Repository",
    description:
      "The repository table lists stored files. From here you can review categories and use the row actions to preview or download a document.",
    placement: "top",
  },
  {
    path: "/dashboard/members",
    selector: '[data-tour="members-add"]',
    title: "Add Member",
    description:
      "This view manages the member directory. Use Add Member to open the demo intake form for a new cooperative member record.",
    placement: "left",
  },
  {
    path: "/dashboard/members",
    selector: '[data-tour="members-filters"]',
    title: "Member Filters",
    description:
      "Search, sector, and status filters narrow the directory so you can focus on the exact group of members you need to review.",
    placement: "bottom",
  },
  {
    path: "/dashboard/members",
    selector: '[data-tour="members-table"]',
    title: "Member Directory",
    description:
      "The member table supports sorting, pagination, and row-level viewing so you can inspect profile details, documents, and share capital activity.",
    placement: "top",
  },
  {
    path: "/dashboard/predictions",
    selector: '[data-tour="predictions-generate"]',
    title: "Generate Predictions",
    description:
      "This view forecasts engagement risk. Use Generate Predictions to run the demo scoring flow and refresh the AI prediction summary.",
    placement: "left",
  },
  {
    path: "/dashboard/predictions",
    selector: '[data-tour="predictions-filters"]',
    title: "Prediction Filters",
    description:
      "These controls help you focus the prediction list by name, sector, or status before you review at-risk members.",
    placement: "bottom",
  },
  {
    path: "/dashboard/predictions",
    selector: '[data-tour="predictions-table"]',
    title: "Prediction Table",
    description:
      "Use the table to compare scores, predicted status, and last activity, then open a row to inspect the risk factors behind the result.",
    placement: "top",
  },
  {
    path: "/dashboard/announcements",
    selector: '[data-tour="announcements-compose"]',
    title: "Compose Announcement",
    description:
      "This view handles member communications. Compose Announcement opens the draft flow for messages you want to send to selected sectors or all members.",
    placement: "left",
  },
  {
    path: "/dashboard/announcements",
    selector: '[data-tour="announcements-tabs"]',
    title: "Announcement Tabs",
    description:
      "Use these tabs to switch between the public announcement feed and the at-risk member alert workflow.",
    placement: "bottom",
  },
  {
    path: "/dashboard/announcements",
    selector: '[data-tour="announcements-table"]',
    title: "Announcement Management",
    description:
      "This section lets you review sent announcements, inspect readership, and use the row actions to view, edit, or remove a record.",
    placement: "top",
  },
  {
    path: "/dashboard/alerts",
    selector: '[data-tour="alerts-filters"]',
    title: "Alert Filters",
    description:
      "This view groups operational notices and risks. Use the alert filters to focus on critical, warning, success, or information items.",
    placement: "bottom",
  },
  {
    path: "/dashboard/alerts",
    selector: '[data-tour="alerts-list"]',
    title: "Alert Actions",
    description:
      "Each alert card shows the issue, timing, and direct actions like Take Action or Dismiss so the chairman can respond quickly.",
    placement: "top",
  },
  {
    path: "/dashboard/gallery",
    selector: '[data-tour="gallery-create-album"]',
    title: "Create Album",
    description:
      "This media view organizes event photos and activity records. Create Album starts a new collection for a sector or activity type.",
    placement: "left",
  },
  {
    path: "/dashboard/gallery",
    selector: '[data-tour="gallery-upload-media"]',
    title: "Upload Media",
    description:
      "Upload Media adds new photos into the gallery and lets you attach them to an album for cleaner organization.",
    placement: "left",
  },
  {
    path: "/dashboard/gallery",
    selector: '[data-tour="gallery-filters"]',
    title: "Gallery Filters",
    description:
      "Use search, sector, and activity filters to narrow the gallery whether you are browsing albums or all uploaded photos.",
    placement: "bottom",
  },
  {
    path: "/dashboard/reports",
    selector: '[data-tour="reports-cards"]',
    title: "Report Cards",
    description:
      "This reporting view generates the chairman summaries. Each report card opens a different report type for membership, share capital, engagement, or documents.",
    placement: "bottom",
  },
  {
    path: "/dashboard/reports",
    selector: '[data-tour="reports-table"]',
    title: "Recent Reports",
    description:
      "The recent reports table stores generated outputs so you can reopen, download, or reuse them without starting from scratch.",
    placement: "top",
  },
];

export const bookkeeperTourSteps: TourStep[] = [
  {
    path: "/dashboard/bookkeeper",
    selector: '[data-tour="dashboard-sidebar"]',
    title: "Bookkeeper Navigation",
    description:
      "This sidebar links the bookkeeper workflow from the dashboard into share capital, revenue, and expenditure records.",
    placement: "right",
  },
  {
    path: "/dashboard/bookkeeper",
    selector: '[data-tour="bookkeeper-cashflow"]',
    title: "Cash Flow View",
    description:
      "Use this chart to compare income and expenses over time before you drill into detailed ledger activity.",
    placement: "bottom",
  },
  {
    path: "/dashboard/bookkeeper",
    selector: '[data-tour="bookkeeper-transactions-filters"]',
    title: "Transaction Filters",
    description:
      "Search, type, and status filters narrow the recent transactions table so you can inspect the entries that matter.",
    placement: "bottom",
  },
  {
    path: "/dashboard/bookkeeper",
    selector: '[data-tour="bookkeeper-transactions-table"]',
    title: "Recent Transactions",
    description:
      "This table shows incoming contributions and outgoing expenses, with sortable headers for quick review.",
    placement: "top",
  },
  {
    path: "/dashboard/share-capital",
    selector: '[data-tour="share-capital-record"]',
    title: "Record Share Capital",
    description:
      "Use this action to open the upload prototype for a new share capital payment or contribution update.",
    placement: "left",
  },
  {
    path: "/dashboard/share-capital",
    selector: '[data-tour="share-capital-filters"]',
    title: "Share Capital Filters",
    description:
      "These controls help you find members by name, sector, status, or contribution amount band.",
    placement: "bottom",
  },
  {
    path: "/dashboard/share-capital",
    selector: '[data-tour="share-capital-table"]',
    title: "Contribution Directory",
    description:
      "Review member balances here, sort the columns, and use row actions to edit or remove prototype records.",
    placement: "top",
  },
  {
    path: "/dashboard/financial",
    selector: '[data-tour="financial-record"]',
    title: "Record Income",
    description:
      "This button opens the revenue intake flow for a new income or revenue entry with attached proof.",
    placement: "left",
  },
  {
    path: "/dashboard/financial",
    selector: '[data-tour="financial-filters"]',
    title: "Revenue Filters",
    description:
      "Use these filters to search the ledger, narrow by source, and focus on a revenue amount range.",
    placement: "bottom",
  },
  {
    path: "/dashboard/financial",
    selector: '[data-tour="financial-table"]',
    title: "Revenue Ledger",
    description:
      "The revenue table stores recorded income entries and supports sorting plus edit and delete actions.",
    placement: "top",
  },
  {
    path: "/dashboard/expenditures",
    selector: '[data-tour="expenditures-record"]',
    title: "Record Expense",
    description:
      "Use Record Expense to log a new disbursement and attach the supporting receipt in the prototype flow.",
    placement: "left",
  },
  {
    path: "/dashboard/expenditures",
    selector: '[data-tour="expenditures-filters"]',
    title: "Expenditure Filters",
    description:
      "Search, category, status, and amount filters help you isolate the expense records you need to review.",
    placement: "bottom",
  },
  {
    path: "/dashboard/expenditures",
    selector: '[data-tour="expenditures-table"]',
    title: "Expenditure Ledger",
    description:
      "This table lists recorded expenses with sortable columns and row actions for updating or deleting entries.",
    placement: "top",
  },
];

export const memberTourSteps: TourStep[] = [
  {
    path: "/dashboard/member",
    selector: '[data-tour="dashboard-sidebar"]',
    title: "Member Navigation",
    description:
      "This sidebar keeps your member tools together so you can move between the dashboard, profile, and announcements.",
    placement: "right",
  },
  {
    path: "/dashboard/member",
    selector: '[data-tour="member-dashboard-announcements"]',
    title: "Recent Announcements",
    description:
      "This feed surfaces the latest cooperative updates, and each card opens the full announcements page.",
    placement: "bottom",
  },
  {
    path: "/dashboard/member",
    selector: '[data-tour="member-dashboard-activity"]',
    title: "Recent Activity",
    description:
      "Review your latest contribution, attendance, and record activity here without leaving the dashboard.",
    placement: "top",
  },
  {
    path: "/dashboard/profile",
    selector: '[data-tour="member-profile-edit"]',
    title: "Edit Profile",
    description:
      "Use this action when your contact details or personal information need to be updated.",
    placement: "left",
  },
  {
    path: "/dashboard/profile",
    selector: '[data-tour="member-profile-summary"]',
    title: "Profile Summary",
    description:
      "This section groups your member details, status, and contact information in one place.",
    placement: "bottom",
  },
  {
    path: "/dashboard/profile",
    selector: '[data-tour="member-profile-tabs"]',
    title: "Profile Records",
    description:
      "Switch between your share capital history and document list from these tabs.",
    placement: "bottom",
  },
  {
    path: "/dashboard/member-announcements",
    selector: '[data-tour="member-announcements-read"]',
    title: "Mark All as Read",
    description:
      "Use this button to clear unread announcement badges once you have reviewed the latest updates.",
    placement: "left",
  },
  {
    path: "/dashboard/member-announcements",
    selector: '[data-tour="member-announcements-filters"]',
    title: "Announcement Search",
    description:
      "Search the inbox here while keeping the feed focused on your sector plus general cooperative notices.",
    placement: "bottom",
  },
  {
    path: "/dashboard/member-announcements",
    selector: '[data-tour="member-announcements-list"]',
    title: "Announcement Inbox",
    description:
      "This inbox holds the full announcement stream. Opening an item lets you review its details and mark it read.",
    placement: "top",
  },
];
