import HomeView from './views/HomeView.vue';
import CampaignIndexView from './views/CampaignIndexView.vue';
import CampaignShowView from './views/CampaignShowView.vue';
import LoginView from './views/LoginView.vue';
import RegisterView from './views/RegisterView.vue';
import DashboardIndexView from './views/DashboardIndexView.vue';
import DashboardShowView from './views/DashboardShowView.vue';
import CampaignCreateView from './views/CampaignCreateView.vue';
import CampaignEditView from './views/CampaignEditView.vue';
import CreatorSetupCategoryView from './views/CreatorSetupCategoryView.vue';
import StaticPageView from './views/StaticPageView.vue';
import ProfileView from './views/ProfileView.vue';

export const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
        meta: {
            titleKey: 'routes.home.title',
            descriptionKey: 'routes.home.description',
            ogType: 'website',
        },
    },
    {
        path: '/campaigns',
        name: 'campaigns.index',
        component: CampaignIndexView,
        meta: {
            titleKey: 'routes.campaignsIndex.title',
            descriptionKey: 'routes.campaignsIndex.description',
            ogType: 'website',
        },
    },
    {
        path: '/campaigns/:slug',
        name: 'campaigns.show',
        component: CampaignShowView,
        props: true,
        meta: {
            titleKey: 'routes.campaignsShow.title',
            descriptionKey: 'routes.campaignsShow.description',
            ogType: 'article',
        },
    },

    // Institucional / Ajuda (usado no footer)
    {
        path: '/about',
        name: 'pages.about',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.about.title',
            leadKey: 'routes.pages.about.lead',
            descriptionKey: 'routes.pages.about.description',
            ogType: 'website',
            contentKeys: ['routes.pages.about.content.1', 'routes.pages.about.content.2'],
        },
    },
    {
        path: '/how-it-works',
        name: 'pages.how',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.how.title',
            leadKey: 'routes.pages.how.lead',
            descriptionKey: 'routes.pages.how.description',
            ogType: 'website',
            contentKeys: ['routes.pages.how.content.1', 'routes.pages.how.content.2'],
        },
    },
    {
        path: '/blog',
        name: 'pages.blog',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.blog.title',
            leadKey: 'routes.pages.blog.lead',
            descriptionKey: 'routes.pages.blog.description',
            ogType: 'website',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/team',
        name: 'pages.team',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.team.title',
            leadKey: 'routes.pages.team.lead',
            descriptionKey: 'routes.pages.team.description',
            ogType: 'website',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/press',
        name: 'pages.press',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.press.title',
            leadKey: 'routes.pages.press.lead',
            descriptionKey: 'routes.pages.press.description',
            ogType: 'website',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/retrospectiva-2020',
        name: 'pages.retrospective2020',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.retrospective2020.title',
            leadKey: 'routes.pages.retrospective2020.lead',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/support',
        name: 'pages.support',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.support.title',
            leadKey: 'routes.pages.support.lead',
            descriptionKey: 'routes.pages.support.description',
            ogType: 'website',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/contact',
        name: 'pages.contact',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.contact.title',
            leadKey: 'routes.pages.contact.lead',
            descriptionKey: 'routes.pages.contact.description',
            ogType: 'website',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/updates',
        name: 'pages.updates',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.updates.title',
            leadKey: 'routes.pages.updates.lead',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/fees',
        name: 'pages.fees',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.fees.title',
            leadKey: 'routes.pages.fees.lead',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/security',
        name: 'pages.security',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.security.title',
            leadKey: 'routes.pages.security.lead',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/terms',
        name: 'pages.terms',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.terms.title',
            leadKey: 'routes.pages.terms.lead',
            descriptionKey: 'routes.pages.terms.description',
            ogType: 'website',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/privacy',
        name: 'pages.privacy',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.privacy.title',
            leadKey: 'routes.pages.privacy.lead',
            descriptionKey: 'routes.pages.privacy.description',
            ogType: 'website',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/popular',
        name: 'pages.popular',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.popular.title',
            leadKey: 'routes.pages.popular.lead',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/no-ar',
        name: 'pages.onAir',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.onAir.title',
            leadKey: 'routes.pages.onAir.lead',
            contentKey: 'routes.pages.onAir.content',
        },
    },
    {
        path: '/finalizados',
        name: 'pages.finished',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.finished.title',
            leadKey: 'routes.pages.finished.lead',
            contentKey: 'pages.contentSoon',
        },
    },
    {
        path: '/assinaturas',
        name: 'pages.subscriptions',
        component: StaticPageView,
        meta: {
            titleKey: 'routes.pages.subscriptions.title',
            leadKey: 'routes.pages.subscriptions.lead',
            contentKey: 'pages.contentSoon',
        },
    },

    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: {
            titleKey: 'routes.login.title',
            descriptionKey: 'routes.login.description',
            robots: 'noindex, nofollow',
        },
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterView,
        meta: {
            titleKey: 'routes.register.title',
            descriptionKey: 'routes.register.description',
            robots: 'noindex, nofollow',
        },
    },

    {
        path: '/profile',
        name: 'profile',
        component: ProfileView,
        meta: {
            titleKey: 'routes.profile.title',
            descriptionKey: 'routes.profile.description',
            robots: 'noindex, nofollow',
        },
    },

    {
        path: '/dashboard',
        name: 'dashboard.index',
        component: DashboardIndexView,
        meta: {
            titleKey: 'routes.dashboard.title',
            descriptionKey: 'routes.dashboard.description',
            robots: 'noindex, nofollow',
        },
    },
    {
        path: '/dashboard/campaigns/:id',
        name: 'dashboard.show',
        component: DashboardShowView,
        props: true,
        meta: {
            titleKey: 'routes.dashboardCampaign.title',
            descriptionKey: 'routes.dashboardCampaign.description',
            robots: 'noindex, nofollow',
        },
    },

    {
        path: '/me/creator/setup',
        name: 'me.creator.setup',
        component: CreatorSetupCategoryView,
        meta: {
            titleKey: 'routes.creatorSetup.title',
            descriptionKey: 'routes.creatorSetup.description',
            robots: 'noindex, nofollow',
        },
    },

    {
        path: '/me/campaigns/create',
        name: 'me.campaigns.create',
        component: CampaignCreateView,
        meta: {
            titleKey: 'routes.campaignCreate.title',
            descriptionKey: 'routes.campaignCreate.description',
            robots: 'noindex, nofollow',
        },
    },
    {
        path: '/me/campaigns/:id/edit',
        name: 'me.campaigns.edit',
        component: CampaignEditView,
        props: true,
        meta: {
            titleKey: 'routes.campaignEdit.title',
            descriptionKey: 'routes.campaignEdit.description',
            robots: 'noindex, nofollow',
        },
    },
];
