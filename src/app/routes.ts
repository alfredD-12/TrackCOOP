import { createBrowserRouter } from "react-router";
import LandingPage from "./pages/LandingPage";
import Login from "./pages/Login";
import DashboardLayout from "./components/DashboardLayout";
import Dashboard from "./pages/Dashboard";
import Documents from "./pages/Documents";
import Members from "./pages/Members";
import Predictions from "./pages/Predictions";
import Announcements from "./pages/Announcements";
import Alerts from "./pages/Alerts";
import MyProfile from "./pages/MyProfile";
import Gallery from "./pages/Gallery";
import Reports from "./pages/Reports";
import BookkeeperDashboard from "./pages/BookkeeperDashboard";
import ShareCapital from "./pages/ShareCapital";
import Financial from "./pages/Financial";
import FinancialExpenditures from "./pages/FinancialExpenditures";
import MemberDashboard from "./pages/MemberDashboard";
import MemberAnnouncements from "./pages/MemberAnnouncements";

export const router = createBrowserRouter([
  {
    path: "/",
    Component: LandingPage,
  },
  {
    path: "/login",
    Component: Login,
  },
  {
    path: "/dashboard",
    Component: DashboardLayout,
    children: [
      { index: true, Component: Dashboard },
      { path: "bookkeeper", Component: BookkeeperDashboard },
      { path: "member", Component: MemberDashboard },
      { path: "share-capital", Component: ShareCapital },
      { path: "financial", Component: Financial },
      { path: "expenditures", Component: FinancialExpenditures },
      { path: "documents", Component: Documents },
      { path: "members", Component: Members },
      { path: "predictions", Component: Predictions },
      { path: "announcements", Component: Announcements },
      { path: "alerts", Component: Alerts },
      { path: "profile", Component: MyProfile },
      { path: "member-announcements", Component: MemberAnnouncements },
      { path: "gallery", Component: Gallery },
      { path: "reports", Component: Reports },
    ],
  },
]);
