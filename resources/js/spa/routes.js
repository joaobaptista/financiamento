import HomeView from './views/HomeView.vue';
import CampaignIndexView from './views/CampaignIndexView.vue';
import CampaignShowView from './views/CampaignShowView.vue';
import LoginView from './views/LoginView.vue';
import RegisterView from './views/RegisterView.vue';
import DashboardIndexView from './views/DashboardIndexView.vue';
import DashboardShowView from './views/DashboardShowView.vue';
import CampaignCreateView from './views/CampaignCreateView.vue';
import CampaignEditView from './views/CampaignEditView.vue';

export const routes = [
    { path: '/', name: 'home', component: HomeView },
    { path: '/campaigns', name: 'campaigns.index', component: CampaignIndexView },
    { path: '/campaigns/:slug', name: 'campaigns.show', component: CampaignShowView, props: true },

    { path: '/login', name: 'login', component: LoginView },
    { path: '/register', name: 'register', component: RegisterView },

    { path: '/dashboard', name: 'dashboard.index', component: DashboardIndexView },
    { path: '/dashboard/campaigns/:id', name: 'dashboard.show', component: DashboardShowView, props: true },

    { path: '/me/campaigns/create', name: 'me.campaigns.create', component: CampaignCreateView },
    { path: '/me/campaigns/:id/edit', name: 'me.campaigns.edit', component: CampaignEditView, props: true },
];
