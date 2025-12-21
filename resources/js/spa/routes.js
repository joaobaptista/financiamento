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

export const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
        meta: {
            title: 'Crowdfunding para projetos criativos',
            description: 'Apoie criadores, escolha recompensas e acompanhe campanhas em tempo real.',
            ogType: 'website',
        },
    },
    {
        path: '/campaigns',
        name: 'campaigns.index',
        component: CampaignIndexView,
        meta: {
            title: 'Explorar campanhas',
            description: 'Descubra campanhas ativas e apoie projetos criativos com recompensas.',
            ogType: 'website',
        },
    },
    {
        path: '/campaigns/:slug',
        name: 'campaigns.show',
        component: CampaignShowView,
        props: true,
        meta: {
            title: 'Campanha',
            description: 'Veja detalhes, recompensas e apoie esta campanha.',
            ogType: 'article',
        },
    },

    // Institucional / Ajuda (usado no footer)
    {
        path: '/about',
        name: 'pages.about',
        component: StaticPageView,
        meta: {
            title: 'Quem somos',
            lead: 'Conheça a Origo e nossa missão.',
            description: 'Conheça a Origo e nossa missão.',
            ogType: 'website',
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
            description: 'O básico do financiamento coletivo: explorar campanhas, escolher recompensas e apoiar criadores.',
            ogType: 'website',
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
            description: 'Novidades, dicas e histórias de bastidores sobre crowdfunding e criação.',
            ogType: 'website',
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
            description: 'Conheça o time por trás da plataforma.',
            ogType: 'website',
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
            description: 'Materiais e contato para imprensa.',
            ogType: 'website',
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
            description: 'Tire dúvidas e encontre ajuda rápida.',
            ogType: 'website',
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
            description: 'Fale com a gente.',
            ogType: 'website',
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
            description: 'Regras e condições de uso da plataforma.',
            ogType: 'website',
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
            description: 'Como tratamos seus dados.',
            ogType: 'website',
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

    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: {
            title: 'Entrar',
            description: 'Acesse sua conta para apoiar campanhas e acompanhar recompensas.',
            robots: 'noindex, nofollow',
        },
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterView,
        meta: {
            title: 'Criar conta',
            description: 'Crie sua conta para apoiar campanhas e criar projetos.',
            robots: 'noindex, nofollow',
        },
    },

    {
        path: '/dashboard',
        name: 'dashboard.index',
        component: DashboardIndexView,
        meta: {
            title: 'Dashboard',
            description: 'Área do criador para acompanhar suas campanhas.',
            robots: 'noindex, nofollow',
        },
    },
    {
        path: '/dashboard/campaigns/:id',
        name: 'dashboard.show',
        component: DashboardShowView,
        props: true,
        meta: {
            title: 'Dashboard da campanha',
            description: 'Acompanhe apoios e métricas da sua campanha.',
            robots: 'noindex, nofollow',
        },
    },

    {
        path: '/me/creator/setup',
        name: 'me.creator.setup',
        component: CreatorSetupCategoryView,
        meta: {
            title: 'Configurar perfil de criador',
            description: 'Configure sua categoria como criador antes de criar campanhas.',
            robots: 'noindex, nofollow',
        },
    },

    {
        path: '/me/campaigns/create',
        name: 'me.campaigns.create',
        component: CampaignCreateView,
        meta: {
            title: 'Criar campanha',
            description: 'Crie uma nova campanha de crowdfunding.',
            robots: 'noindex, nofollow',
        },
    },
    {
        path: '/me/campaigns/:id/edit',
        name: 'me.campaigns.edit',
        component: CampaignEditView,
        props: true,
        meta: {
            title: 'Editar campanha',
            description: 'Edite detalhes da sua campanha.',
            robots: 'noindex, nofollow',
        },
    },
];
