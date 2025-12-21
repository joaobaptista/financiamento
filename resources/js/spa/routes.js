import HomeView from './views/HomeView.vue';
import CampaignIndexView from './views/CampaignIndexView.vue';
import CampaignShowView from './views/CampaignShowView.vue';
import LoginView from './views/LoginView.vue';
import RegisterView from './views/RegisterView.vue';
import DashboardIndexView from './views/DashboardIndexView.vue';
import DashboardShowView from './views/DashboardShowView.vue';
import CampaignCreateView from './views/CampaignCreateView.vue';
import CampaignEditView from './views/CampaignEditView.vue';
import StaticPageView from './views/StaticPageView.vue';

export const routes = [
    { path: '/', name: 'home', component: HomeView },
    { path: '/campaigns', name: 'campaigns.index', component: CampaignIndexView },
    { path: '/campaigns/:slug', name: 'campaigns.show', component: CampaignShowView, props: true },

    // Institucional / Ajuda (usado no footer)
    {
        path: '/about',
        name: 'pages.about',
        component: StaticPageView,
        meta: {
            title: 'Quem somos',
            lead: 'Conheça a Origo e nossa missão.',
            content: [
                'A Origo é uma plataforma de crowdfunding para projetos criativos.',
                'Aqui você encontra iniciativas independentes e pode apoiar criadores diretamente.',
            ],
        },
    },
    {
        path: '/how-it-works',
        name: 'pages.how',
        component: StaticPageView,
        meta: {
            title: 'Como funciona',
            lead: 'O básico do financiamento coletivo na Origo.',
            content: [
                'Explore campanhas, escolha uma recompensa e faça seu apoio.',
                'Criadores publicam atualizações e entregam recompensas conforme o cronograma.',
            ],
        },
    },
    {
        path: '/blog',
        name: 'pages.blog',
        component: StaticPageView,
        meta: {
            title: 'Blog',
            lead: 'Novidades, dicas e histórias de bastidores.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/team',
        name: 'pages.team',
        component: StaticPageView,
        meta: {
            title: 'Nosso time',
            lead: 'Pessoas por trás da plataforma.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/press',
        name: 'pages.press',
        component: StaticPageView,
        meta: {
            title: 'Imprensa',
            lead: 'Materiais e contato para imprensa.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/retrospectiva-2020',
        name: 'pages.retrospective2020',
        component: StaticPageView,
        meta: {
            title: 'Retrospectiva 2020',
            lead: 'Um resumo simbólico para manter o footer completo.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/support',
        name: 'pages.support',
        component: StaticPageView,
        meta: {
            title: 'Central de suporte',
            lead: 'Tire dúvidas e encontre ajuda rápida.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/contact',
        name: 'pages.contact',
        component: StaticPageView,
        meta: {
            title: 'Contato',
            lead: 'Fale com a gente.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/updates',
        name: 'pages.updates',
        component: StaticPageView,
        meta: {
            title: 'Atualizações',
            lead: 'Histórico de melhorias e mudanças.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/fees',
        name: 'pages.fees',
        component: StaticPageView,
        meta: {
            title: 'Nossa taxa',
            lead: 'Como funcionam as taxas da plataforma.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/security',
        name: 'pages.security',
        component: StaticPageView,
        meta: {
            title: 'Responsabilidades e segurança',
            lead: 'Boas práticas e informações importantes.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/terms',
        name: 'pages.terms',
        component: StaticPageView,
        meta: {
            title: 'Termos de uso',
            lead: 'Regras e condições de uso da plataforma.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/privacy',
        name: 'pages.privacy',
        component: StaticPageView,
        meta: {
            title: 'Política de privacidade',
            lead: 'Como tratamos seus dados.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/popular',
        name: 'pages.popular',
        component: StaticPageView,
        meta: {
            title: 'Populares',
            lead: 'Curadoria e destaques.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/no-ar',
        name: 'pages.onAir',
        component: StaticPageView,
        meta: {
            title: 'No ar',
            lead: 'Campanhas ativas no momento.',
            content: 'Explore as campanhas na página de exploração.',
        },
    },
    {
        path: '/finalizados',
        name: 'pages.finished',
        component: StaticPageView,
        meta: {
            title: 'Finalizados',
            lead: 'Projetos concluídos.',
            content: 'Conteúdo em breve.',
        },
    },
    {
        path: '/assinaturas',
        name: 'pages.subscriptions',
        component: StaticPageView,
        meta: {
            title: 'Assinaturas',
            lead: 'Apoio recorrente.',
            content: 'Conteúdo em breve.',
        },
    },

    { path: '/login', name: 'login', component: LoginView },
    { path: '/register', name: 'register', component: RegisterView },

    { path: '/dashboard', name: 'dashboard.index', component: DashboardIndexView },
    { path: '/dashboard/campaigns/:id', name: 'dashboard.show', component: DashboardShowView, props: true },

    { path: '/me/campaigns/create', name: 'me.campaigns.create', component: CampaignCreateView },
    { path: '/me/campaigns/:id/edit', name: 'me.campaigns.edit', component: CampaignEditView, props: true },
];
