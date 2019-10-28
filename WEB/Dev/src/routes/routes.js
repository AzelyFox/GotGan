import LoginPage from "@/pages/LoginPage.vue";

import DashboardLayout from "@/pages/Layout/DashboardLayout.vue";

import StockDashboardTab from "@/pages/StockDashboardTab.vue";
import StockDetailTab from "@/pages/StockDetailTab.vue";
import RentDashboardTab from "@/pages/RentDashboardTab.vue";
import UserManagementTab from "@/pages/UserManagementTab.vue";
import SettingTab from "@/pages/SettingTab.vue";

const routes = [
  {
    path: "/login",
    component: LoginPage
  },
  {
    path: "/",
    component: DashboardLayout,
    redirect: "/stockdashboard",
    children: [
      {
        path: "stockDashboard",
        name: "재고 대시보드",
        component: StockDashboardTab
      },
      {
        path: "stockDetail",
        name: "재고 상세",
        component: StockDetailTab
      },
      {
        path: "rentDashboard",
        name: "반출입 대시보드",
        component: RentDashboardTab
      },
      {
        path: "userManagement",
        name: "유저 관리",
        component: UserManagementTab
      },
      {
        path: "setting",
        name: "설정",
        component: SettingTab
      }
    ]

  },
  {
    path: "/user",
    component: DashboardLayout,
    redirect: "/user/test",
    children: [
      {
        path: "test",
        name: "사용자 페이지",
        component: StockDashboardTab
      }
    ]
  }
];

export default routes;
