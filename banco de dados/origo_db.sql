--
-- PostgreSQL database dump
--

\restrict NfdFfJmNO7rVCEtKG6IhQakYYUN9xcRGuKjAKReWjUiAnBrBE2dIMfcab0L4zkE

-- Dumped from database version 16.11 (Ubuntu 16.11-0ubuntu0.24.04.1)
-- Dumped by pg_dump version 16.11 (Ubuntu 16.11-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: campaigns; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.campaigns (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text NOT NULL,
    goal_amount bigint NOT NULL,
    pledged_amount bigint DEFAULT '0'::bigint NOT NULL,
    starts_at timestamp(0) without time zone,
    ends_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    cover_image_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    cover_image_webp_path character varying(255),
    creator_page_id bigint,
    ending_soon_notified_at timestamp(0) without time zone,
    finished_notified_at timestamp(0) without time zone,
    goal_reached_notified_at timestamp(0) without time zone,
    niche character varying(255),
    CONSTRAINT campaigns_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'active'::character varying, 'successful'::character varying, 'failed'::character varying, 'cancelled'::character varying])::text[])))
);


ALTER TABLE public.campaigns OWNER TO postgres;

--
-- Name: campaigns_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.campaigns_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.campaigns_id_seq OWNER TO postgres;

--
-- Name: campaigns_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.campaigns_id_seq OWNED BY public.campaigns.id;


--
-- Name: creator_page_followers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.creator_page_followers (
    id bigint NOT NULL,
    creator_page_id bigint NOT NULL,
    follower_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.creator_page_followers OWNER TO postgres;

--
-- Name: creator_page_followers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.creator_page_followers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.creator_page_followers_id_seq OWNER TO postgres;

--
-- Name: creator_page_followers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.creator_page_followers_id_seq OWNED BY public.creator_page_followers.id;


--
-- Name: creator_pages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.creator_pages (
    id bigint NOT NULL,
    owner_user_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    primary_category character varying(255),
    subcategory character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.creator_pages OWNER TO postgres;

--
-- Name: creator_pages_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.creator_pages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.creator_pages_id_seq OWNER TO postgres;

--
-- Name: creator_pages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.creator_pages_id_seq OWNED BY public.creator_pages.id;


--
-- Name: creator_profiles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.creator_profiles (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    primary_category character varying(255) NOT NULL,
    subcategory character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.creator_profiles OWNER TO postgres;

--
-- Name: creator_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.creator_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.creator_profiles_id_seq OWNER TO postgres;

--
-- Name: creator_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.creator_profiles_id_seq OWNED BY public.creator_profiles.id;


--
-- Name: creator_supporters; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.creator_supporters (
    id bigint NOT NULL,
    creator_id bigint NOT NULL,
    supporter_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.creator_supporters OWNER TO postgres;

--
-- Name: creator_supporters_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.creator_supporters_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.creator_supporters_id_seq OWNER TO postgres;

--
-- Name: creator_supporters_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.creator_supporters_id_seq OWNED BY public.creator_supporters.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id bigint NOT NULL,
    data text NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notifications OWNER TO postgres;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: pledges; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pledges (
    id bigint NOT NULL,
    campaign_id bigint NOT NULL,
    user_id bigint NOT NULL,
    reward_id bigint,
    amount bigint NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    provider character varying(255) DEFAULT 'mock'::character varying NOT NULL,
    provider_payment_id character varying(255),
    paid_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    payment_method character varying(255) DEFAULT 'card'::character varying NOT NULL,
    provider_payload json,
    checkout_incomplete_reminded_at timestamp(0) without time zone,
    CONSTRAINT pledges_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'paid'::character varying, 'refunded'::character varying, 'canceled'::character varying])::text[])))
);


ALTER TABLE public.pledges OWNER TO postgres;

--
-- Name: pledges_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pledges_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pledges_id_seq OWNER TO postgres;

--
-- Name: pledges_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pledges_id_seq OWNED BY public.pledges.id;


--
-- Name: rewards; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rewards (
    id bigint NOT NULL,
    campaign_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text NOT NULL,
    min_amount bigint NOT NULL,
    quantity integer,
    remaining integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.rewards OWNER TO postgres;

--
-- Name: rewards_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rewards_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rewards_id_seq OWNER TO postgres;

--
-- Name: rewards_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rewards_id_seq OWNED BY public.rewards.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    google_id character varying(255),
    postal_code character varying(20),
    address_street character varying(255),
    address_number character varying(50),
    address_complement character varying(255),
    address_neighborhood character varying(255),
    address_city character varying(255),
    address_state character varying(2),
    phone character varying(30),
    profile_photo_path character varying(255)
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: campaigns id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.campaigns ALTER COLUMN id SET DEFAULT nextval('public.campaigns_id_seq'::regclass);


--
-- Name: creator_page_followers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_page_followers ALTER COLUMN id SET DEFAULT nextval('public.creator_page_followers_id_seq'::regclass);


--
-- Name: creator_pages id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_pages ALTER COLUMN id SET DEFAULT nextval('public.creator_pages_id_seq'::regclass);


--
-- Name: creator_profiles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_profiles ALTER COLUMN id SET DEFAULT nextval('public.creator_profiles_id_seq'::regclass);


--
-- Name: creator_supporters id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_supporters ALTER COLUMN id SET DEFAULT nextval('public.creator_supporters_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: pledges id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pledges ALTER COLUMN id SET DEFAULT nextval('public.pledges_id_seq'::regclass);


--
-- Name: rewards id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rewards ALTER COLUMN id SET DEFAULT nextval('public.rewards_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: campaigns; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.campaigns (id, user_id, title, slug, description, goal_amount, pledged_amount, starts_at, ends_at, status, cover_image_path, created_at, updated_at, cover_image_webp_path, creator_page_id, ending_soon_notified_at, finished_notified_at, goal_reached_notified_at, niche) FROM stdin;
1	1	Livro de Contos Fantásticos	livro-de-contos-fantasticos	Um livro com 20 contos de fantasia originais, ilustrado por artistas brasileiros. O projeto visa publicar uma edição de luxo com capa dura e ilustrações coloridas.	5000000	3500000	2025-12-14 00:14:03	2026-01-13 00:14:03	active	https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N
2	2	Documentário sobre Música Brasileira	documentario-sobre-musica-brasileira	Um documentário que explora a riqueza da música brasileira, desde o samba até o funk carioca, com entrevistas exclusivas com grandes artistas.	10000000	2500000	2025-12-19 00:14:03	2026-01-18 00:14:03	active	https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N
4	2	Podcast sobre Tecnologia	podcast-sobre-tecnologia	Um podcast semanal discutindo as últimas tendências em tecnologia e inovação.	2000000	0	\N	2026-01-28 00:14:03	draft	\N	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N
3	1	Jogo de Tabuleiro Cooperativo	jogo-de-tabuleiro-cooperativo	Um jogo de tabuleiro cooperativo ambientado no Brasil colonial, onde os jogadores trabalham juntos para construir uma comunidade sustentável.	3000000	1202000	2025-12-24 00:14:03	2026-01-23 00:14:03	active	https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?w=800	2025-12-29 00:14:03	2025-12-29 20:43:17	\N	\N	\N	\N	\N	\N
5	6	Livro O Positivismo na Maçonaria do Rito Moderno	livro-o-positivismo-na-maconaria-do-rito-moderno	O projeto\n\nA obra explora a intersecção entre o Positivismo e a Maçonaria do Rito Moderno, destacando como este último, surgido no século XVIII durante o Iluminismo, se fundamenta em princípios racionais e humanistas. O Rito Moderno buscou integrar ciência, moralidade e razão na formação do indivíduo e da sociedade, distanciando-se de abordagens mais místicas. Com a ascensão do Positivismo de Auguste Comte no século XIX, especialmente na França e no Brasil, essa corrente filosófica encontrou um ambiente propício dentro da Maçonaria moderna, que já valorizava a razão como guia para a humanidade e defendia uma moral laica. O Positivismo propõe que o conhecimento humano evolui através de três estágios: teológico, metafísico e científico. Na fase científica, os indivíduos abandonam explicações sobrenaturais em favor da observação empírica.\n\nO lema de Comte — “Amor por princípio, Ordem por base e Progresso por fim” — ressoa com os ideais maçônicos de Liberdade, Igualdade e Fraternidade. No contexto brasileiro do século XIX, muitos maçons eram intelectuais engajados com as ideias republicanas e positivistas. A Maçonaria do Rito Moderno adotou diversos elementos positivistas em sua prática: valorização da educação pública, apoio à laicidade estatal e promoção de um progresso social baseado na solidariedade. Enquanto o Positivismo busca uma "religião da humanidade", centrada no progresso coletivo, o Rito Moderno foca no aprimoramento moral individual como objetivo central da iniciação.\n\nEssa relação fortaleceu a visão progressista da Maçonaria como uma instituição comprometida com o avanço do conhecimento e justiça social.\n\nLivro com 120 páginas em formato A5, papel couchê 90g.	80000	200	2025-12-29 23:31:02	2026-01-10 00:00:00	active	/storage/campaign-covers/5/d8532f4a-6d24-4ad6-a00f-35f73f938611.png	2025-12-29 23:30:31	2025-12-30 17:23:56	\N	1	\N	\N	\N	publicacao
7	6	Campanha de arrecadação na nova edicao colorida do Eduardo	campanha-de-arrecadacao-na-nova-edicao-colorida-do-eduardo	Campanha de arrecadação na nova edicao colorida do Eduardo	10000	200	2025-12-30 17:31:36	2026-01-31 00:00:00	active	/storage/campaign-covers/7/d567f3e3-9b3b-4974-b73d-4b8cfc34c5be.jpg	2025-12-30 17:31:32	2025-12-30 18:00:23	\N	1	\N	\N	\N	quadrinhos
\.


--
-- Data for Name: creator_page_followers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.creator_page_followers (id, creator_page_id, follower_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: creator_pages; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.creator_pages (id, owner_user_id, name, slug, primary_category, subcategory, created_at, updated_at) FROM stdin;
1	6	João Machado	joao-machado	\N	\N	2025-12-29 23:30:31	2025-12-29 23:30:31
\.


--
-- Data for Name: creator_profiles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.creator_profiles (id, user_id, primary_category, subcategory, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: creator_supporters; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.creator_supporters (id, creator_id, supporter_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_12_17_131814_create_campaigns_table	1
5	2025_12_17_131815_create_rewards_table	1
6	2025_12_17_131816_create_pledges_table	1
7	2025_12_21_000001_add_google_id_to_users_table	1
8	2025_12_21_100000_add_scaling_indexes	1
9	2025_12_21_120001_add_cover_image_webp_path_to_campaigns_table	1
10	2025_12_21_130001_create_creator_supporters_table	1
11	2025_12_21_130002_create_notifications_table	1
12	2025_12_21_180001_create_creator_profiles_table	1
13	2025_12_21_190001_create_creator_pages_table	1
14	2025_12_21_190002_create_creator_page_followers_table	1
15	2025_12_21_190003_add_creator_page_id_to_campaigns_table	1
16	2025_12_22_235900_add_payment_fields_to_pledges_table	1
17	2025_12_23_000001_add_supporter_contact_fields_to_users_table	1
18	2025_12_23_120000_add_profile_photo_path_to_users_table	1
19	2025_12_24_000001_add_checkout_incomplete_reminder_to_pledges_table	2
20	2025_12_24_000002_add_campaign_notification_timestamps	2
21	2025_12_24_000003_add_campaign_goal_reached_notified_at	2
22	2025_12_25_142827_add_niche_to_campaigns_table	2
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: pledges; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pledges (id, campaign_id, user_id, reward_id, amount, status, provider, provider_payment_id, paid_at, created_at, updated_at, payment_method, provider_payload, checkout_incomplete_reminded_at) FROM stdin;
1	1	3	2	5000	paid	mock	mock_6951c7cb38dae	2025-12-28 00:14:03	2025-12-29 00:14:03	2025-12-29 00:14:03	card	\N	\N
2	1	4	1	2000	paid	mock	mock_6951c7cb39f1c	2025-12-23 00:14:03	2025-12-29 00:14:03	2025-12-29 00:14:03	card	\N	\N
3	1	5	\N	10000	paid	mock	mock_6951c7cb3a938	2025-12-24 00:14:03	2025-12-29 00:14:03	2025-12-29 00:14:03	card	\N	\N
4	2	3	3	3000	paid	mock	mock_6951c7cb3af5f	2025-12-21 00:14:03	2025-12-29 00:14:03	2025-12-29 00:14:03	card	\N	\N
5	2	5	\N	5000	paid	mock	mock_6951c7cb3b691	2025-12-23 00:14:03	2025-12-29 00:14:03	2025-12-29 00:14:03	card	\N	\N
6	3	4	4	15000	paid	mock	mock_6951c7cb3bcba	2025-12-24 00:14:03	2025-12-29 00:14:03	2025-12-29 00:14:03	card	\N	\N
7	3	6	\N	100	pending	mock	mock_6951c896bea33	\N	2025-12-29 00:17:26	2025-12-29 00:17:26	pix	{"type":"pix","copy_paste":"00020126580014BR.GOV.BCB.PIX0136F0DA950986FF07ABBAACBD275204000053039865405000015802BR5920ORIGO MOCK PIX6009SAO PAULO622905254E2F7E304BC630A06304CEB7","expires_at":"2025-12-29T00:47:26+00:00"}	\N
8	3	6	\N	1000	pending	mock	mock_6952e50a5cf63	\N	2025-12-29 20:31:06	2025-12-29 20:31:06	pix	{"type":"pix","copy_paste":"00020126580014BR.GOV.BCB.PIX01365387825941618BE1AF322C4E5204000053039865405000105802BR5920ORIGO MOCK PIX6009SAO PAULO6229052522143060064E1C3A63048851","expires_at":"2025-12-29T21:01:06+00:00"}	\N
13	3	6	\N	1000	canceled	mercadopago	139925306704	\N	2025-12-29 20:45:14	2025-12-30 20:50:41	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.1,"refunded":0},"client_id":0,"date_created":"2025-12-29T16:45:15.000-04:00","external_charge_id":"01KDNXQ8X94HPQ2FN698ES85Q8","id":"139925306704-001","last_updated":"2025-12-29T16:45:15.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-29T16:45:15.059-04:00","execution_id":"01KDNXQ8WBJF0M292ZMVANYMDB"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":null,"date_created":"2025-12-29T16:45:15.000-04:00","date_last_updated":"2025-12-30T16:50:40.000-04:00","date_of_expiration":"2025-12-30T16:45:14.000-04:00","deduction_schema":null,"description":"Apoio campanha #3","differential_pricing_id":null,"external_reference":"pledge_13","fee_details":[],"financing_group":null,"id":139925306704,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":null,"money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":null,"entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":null,"long_name":null,"transfer_account_id":null},"is_same_bank_account_owner":null,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":null,"branch":null,"external_account_id":null,"id":null,"identification":[],"long_name":null}},"bank_transfer_id":null,"e2e_id":null,"financial_institution":null,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce3520400005303986540510.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1399253067046304BCEE","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139925306704\\/ticket?caller_id=1503133188&hash=474e949a-51f3-4ae3-95ab-f748cbf3643e","transaction_id":null},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"cancelled","status_detail":"expired","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":10,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":null,"external_resource_url":null,"financial_institution":null,"installment_amount":0,"net_received_amount":0,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":10,"transaction_id":null}}	\N
22	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 00:49:32	2025-12-30 00:49:32	pix	\N	\N
11	3	6	\N	1000	paid	mercadopago	139821183894	2025-12-29 20:43:04	2025-12-29 20:43:03	2025-12-29 20:43:04	pix	{"type":"pix","copy_paste":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce3520400005303986540510.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1398211838946304D50A","qr_code_base64":"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAK0ElEQVR42uzdQXIayRIG4FKwYMkRdBQdDY6mo+gILFkoVC+soanM7G6DxzOm58X3bwjGputr73IqK6uJiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIyL+bfZ\\/l\\/fpHb\\/3r+\\/NY\\/vz04z++9t7arveP1uY\\/\\/n7oV1rn2JfTrg9b\\/O+0tLS0tLS0tLS0tLS0tL+n\\/Sjf39tL7+cf2v79eZx9fl4XmD4v8cft+\\/v04\\/bjYX3oh\\/rz+sptfLbbvxMtLS0tLS0tLS0tLS3thrWj0tzHmjcopwUOP2re3XjFSfs6fnzVfqWHtOs\\/wfH2fZdq3lF9X2hpaWlpaWlpaWlpaWn\\/W9qp5p3K1GkTdHfdOZ12THPNW2rfl7GTerhutx6uy4TCmZaWlpaWlpaWlpaWlvb\\/QdvH5+n2uRtl68dVX\\/tvx4JfQ5ceMqlpaWlpaWlpaWlpaWlpaf+EtnQLt\\/lx0amonhZq1wq9XbUfcXP4MvZ15w\\/pqcz\\/B3qbaWlpaWlpaWlpaWlpaf+kdj65KOzzHq7latDWfd5ROE\\/l6v7XH\\/Ibc5ZoaWlpaWlpaWlpaWlp\\/5h2MWHYUGrwXXvgPtW8o\\/bt8zm4o+W4jfFHvxdaWlpaWlpaWlpaWlraP6lN3a+h77bHcjX1336mHy+Wq9PEorfV\\/tv6o\\/18dhItLS0tLS0tLS0tLS3txrT78ndDzTuGDr2k+bep3zZkvnPaR8Hc4s5pqH1b7MNND6GlpaWlpaWlpaWlpaWl\\/Se0Q7dfWmA5Y6Ew\\/mh6yPutEv8qn\\/X60T5ajVt8CC0tLS0tLS0tLS0tLe32tCFj6NBfC73fhg+9rEwu6km7+JCUB24OTS3HtLS0tLS0tLS0tLS0tBvT5qFDZXLRwrHRwzguOm5RuZSu4UvpFk6F88LOaaelpaWlpaWlpaWlpaX9b2hD1+vofv26\\/uavz+NNu0ubnrVsnW96HuKrhltVDksbsZf2aGhpaWlpaWlpaWlpaWlpf0WbuoW\\/0tnT1ODbSrH9ujQqqcVyv41Xnu566bFL+LP8e\\/V4+pWWlpaWlpaWlpaWlpZ2U9q6z3uJ3cNfqWytl36mmjcsuKLNXcKn24+mV77MH0JLS0tLS0tLS0tLS0u7SW2aYFSHDaVrWs6xUA4LvY7rWkbL8VrS9mtLD3mk5qWlpaWlpaWlpaWlpaV9lja3zpazp3WEbb4xdHyG+bfpKpbzKJxPpS\\/3dDuw+pl2TO\\/Ov6WlpaWlpaWlpaWlpaWlfVC7cF1Lv41IWpivFBp+U6txzXs5c3rs93IZlfrdqVC0tLS0tLS0tLS0tLS0z9GG8rXFmjfc9VKHDp3ioN2PuO+bGn5fxn5v2BxOLcdhWu84g3p\\/n5eWlpaWlpaWlpaWlpb2Wdp86ed8ctG5lKu1W7jeINr72vWjLW2\\/Hm+txp9Lr9poaWlpaWlpaWlpaWlpN6oNo2tTuTq\\/MXSXtjpT827972+z7ddc+7Yy\\/mjUvLS0tLS0tLS0tLS0tLRb15bW2aw9rs6\\/bT8rU1\\/Sw06lgD61ehS0pfFHtLS0tLS0tLS0tLS0tLT\\/gHa+cO0OXmj4XVgoHRedKvO32ZnTsM+7eGD1wV1pWlpaWlpaWlpaWlpa2ido80I\\/Tbr7Je3r1s3iHofm9lHrnocytSB\\/RMn9E7K0tLS0tLS0tLS0tLS0z9Lu05\\/Nb1EJjb7HuHBo9E2bnuvqUPueR7fwuJpl\\/9jkIlpaWlpaWlpaWlpaWtonapePjV6Pi4ZaN\\/Tfzu\\/k3C\\/WwOlhx9n3hZ3Tu2dOaWlpaWlpaWlpaWlpaWkf1y7WxS+jQj\\/E\\/d2q3Y0pvWHk7\\/yVzy0Mb8pl\\/rxCp6WlpaWlpaWlpaWlpd2uth4X3Y9hQ++3Wver1Lw9HVRNC6WLY9bufDmMyUX9doC1Fs60tLS0tLS0tLS0tLS029Peq3lD0rHR13Lny7zmnbS55XhqMU7aFgvn+\\/NvaWlpaWlpaWlpaWlpaZ+r\\/eltKqeyUHq1tXI13Rx6GPrvK1gWHjYShujS0tLS0tLS0tLS0tLS0v6GNlzbMuYs5eL6WI6Nnm6\\/3JUzppfxkLGvm7uFT1E5\\/b+BaZP4dbwqLS0tLS0tLS0tLS0t7Ua1dUpvX9IuKkPNmzaLw1btfPxRnVwUJhh9tIdCS0tLS0tLS0tLS0tL+yxtWOh9peF3uq6llXJ1vGrd9NzPz5qmIbr15tAeX\\/lCS0tLS0tLS0tLS0tLu1ltK\\/uV+3JMtCZc+rm47TrfOf0qZ07DBKPXuIN6Gf9+\\/YF9XlpaWlpaWlpaWlpaWtrnaOtCb7dTnG2x5j1dtanWrYV0j6c40xDdPtRhctHDpzhpaWlpaWlpaWlpaWlpaduvTC5abPSdiurjbb83NP6uLZS+n+ebxqf4yt8r7tLZ04e7hWlpaWlpaWlpaWlpaWmfqE2V5ktZIA8hOsY5ty0OHdovzb8NNe5C4ZzGH81vn6GlpaWlpaWlpaWlpaXdmraPndLr5\\/Jx0XmL8W5x7m3qFn4vhfN0i8qxTCxKLck\\/uzmUlpaWlpaWlpaWlpaW9onaVhZosf\\/2ZX4ByrRQ1a5dgPJ2U76kgnnadh2v\\/kjNS0tLS0tLS0tLS0tLS0v7S9rXuDVbp\\/bW4jpN6522ahc3i9vipvG38lBG\\/r7OXpGWlpaWlpaWlpaWlpZ2k9p800rZom2lXJ2ua\\/kcC4RydT5o97B0gLWnwrnfHrb2EFpaWlpaWlpaWlpaWtpNaev9nenG0PQZ7nzpY+d0UqeW4x7Pmi4cWO1xB7W++iO9zbS0tLS0tLS0tLS0tLTP0U6V5nu8ReU8WmfT3NsWd0wXdk5r6i0qLT6k1rZrTby0tLS0tLS0tLS0tLS0tH9bOxZKc5fqnS\\/1mGg9c7qPc5dexkN6fOXdrEbPr9zvVui0tLS0tLS0tLS0tLS0z9IG9bzm7eO46NQlfFjfoh0L7YfyfTaxqJc7X6Yf52tHaWlpaWlpaWlpaWlpabeorWdO+\\/0u4WkHddTA+ZhoeshUvh5vr5xfdXxekpqWlpaWlpaWlpaWlpZ2o9p05rQtHRP9Gi2z5zK5aO0hU99t6r8N15Ae41+eb7vS0tLS0tLS0tLS0tLSblK7qK+1bpt\\/T5ucj77yYX6hZ+3DpaWlpaWlpaWlpaWlpaX9t7TfyZd9nm7fP8fZ09e4QLgE9K0U2W9x7FG4fnScPf0sO86P9DbT0tLS0tLS0tLS0tLSPke7n88Reo8LheOiY8FQ89b5t2\\/x4phWhuiGm0On\\/d16c+h4CC0tLS0tLS0tLS0tLe0mtR\\/l+xg61MbZ08Vu4XyLSqp538qtKems6WHl4OrdnVNaWlpaWlpaWlpaWlraLWhTK23VjmOin0Od+m3XCufD\\/EKU6SHTZ\\/1dHYNES0tLS0tLS0tLS0tLS\\/svaHscjZQafcMWbbsW1WnQ7r5M651ajs9x5bVX\\/psVOi0tLS0tLS0tLS0tLe2ztK3cEBq2bA9xgV3aJB5\\/col\\/uR5cDRfI1DOmr0vjj2hpaWlpaWlpaWlpaWm3pl3vFk6Nvr0MG8oZP9qv7JzWwrmVsUdttBzT0tLS0tLS0tLS0tLSblT708lFfZSrp7jpOS0QbhCdhg5da95a44Yfn2P\\/7WdSJwEtLS0tLS0tLS0tLS0t7W9oRURERERERERERERERERERERERERERERERERERDad\\/wUAAP\\/\\/l3vvImZiDxYAAAAASUVORK5CYII=","expires_at":"2025-12-29T20:56:33.000-04:00"}	\N
12	3	6	\N	1000	paid	mercadopago	139178009429	2025-12-29 20:43:17	2025-12-29 20:43:16	2025-12-29 20:43:17	pix	{"type":"pix","copy_paste":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce352040000530398654041.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1391780094296304EED4","qr_code_base64":"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKsUlEQVR42uzdTXIiORAGUBEsWPoIHIWjmaNxFB+BpRcEmhgPVVJmiQZP\\/1Ad8b4N4RkovepdhlKpIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiK\\/N7u6yKls2uf53y\\/99\\/leL6W81XocPeSjlFo\\/SznU+vX36d\\/\\/\\/nb7LKOHbL8W+fpf+yWClpaWlpaWlpaWlpaWlvYXaD\\/S39NCtV7D59cCXwvWm67efrxLCw0ecrz9Pb3ql3rftAF1oKWlpaWlpaWlpaWlpV2zNi30X3l6+HehctPlcjX8KL\\/qlLfbN75+PL36pT209trplT9paWlpaWlpaWlpaWlp\\/zptbWXr8fb3tFC5advnrv24LXidytew\\/dreIte8tLS0tLS0tLS0tLS0tH+pdtq\\/jDVvue2cllvrbNPv7rfOHub\\/fG2vWm87qLS0tLS0tLS0tLS0tLS0v12btmo36QDouFu4zMdGu4cc5oWvbZ+3Szuwum0\\/+rneZlpaWlpaWlpaWlpaWto\\/qR1OLpq6had93mG38KWVq8vJRd97yM\\/NWaKlpaWlpaWlpaWlpaX9M9phYmPv1C3chg4N8pF2SvP82\\/Cjc1uqbb\\/+79DS0tLS0tLS0tLS0tL+Se2+9d2GB7b+2+udH23TWdPaWmjL4gqWqMwP+frR\\/lH\\/LS0tLS0tLS0tLS0tLe1rtYOzk23o0DWMsC1z\\/+0lLZRvU\\/lc\\/vi9v02l678d7pye7v3D0tLS0tLS0tLS0tLS0tJ+S\\/txp3v4lIrr0OjbTS6atMODqm3kb9zvnSYXfW+fl5aWlpaWlpaWlpaWlnYN2qnSvG3RllD7lrlbeLts9N3P+7rdzaG7ft5tzPTKodadDqzun6t5aWlpaWlpaWlpaWlpaV+o7XZOw3HR+yNst8PhQuFVSz+5KLxySQ+N6nYNKS0tLS0tLS0tLS0tLe16taHS3Ax3Tsu8CboN2625dXY5TLemVz\\/fl9DS0tLS0tLS0tLS0tLS\\/iptvLczLJAr9WN\\/5nS4zztN6w3l\\/ibptqnMjzeIPjcVipaWlpaWlpaWlpaWlvaV2rbAo4WCNkznnf8O3cKH\\/oxp6Q+wTvu6UftMzUtLS0tLS0tLS0tLS0v7Km3eOR0s1DY9L7cFu5p3oB7++Hj37pfYevzcnS+0tLS0tLS0tLS0tLS0L9N+tAVun5323N8cug2ttWGBUPN2Tby1r3HPy8lFZXSAlZaWlpaWlpaWlpaWlpb212iXx0UHl35217W0kUhxazYlvnKZN4237bO7OTTMDa60tLS0tLS0tLS0tLS0q9V+\\/Gh39a0urm95vzNgN7Qa55tDw3TeklqOH07ppaWlpaWlpaWlpaWlpV2NttbBnS\\/h5tAy7BZe7qTu2vWj+WGhcM7Xjw6G6NLS0tLS0tLS0tLS0tKuUVvad5fKTdgEbaNru+yXk4vunz29VziH21Me1760tLS0tLS0tLS0tLS0r9LmU5y5lfbc17w11bjxTs6p5g2vOtw57Qro4R7ug31eWlpaWlpaWlpaWlpaWtpntN3NoWFy0Vu78+U9Tettx0XzGdTd8uDqqS\\/z3\\/p\\/gjyl98muYVpaWlpaWlpaWlpaWtrXavOln5PysLiu5XL7uxs6lMvV9sq1PaT0Q3RLeuWpW\\/izfR5oaWlpaWlpaWlpaWlp\\/w5tud\\/oO82\\/Pc5dwjXVvnHBMPe2nTkd3KKSa11aWlpaWlpaWlpaWlralWqHo2tr2uy8Lltm9\\/0Dd6kv917NO3jIMg+6hWlpaWlpaWlpaWlpaWlpn9QOuoXLvM873Rwaz54e5ym9+dqWkqb05pbjMjrAegmvnA6u0tLS0tLS0tLS0tLS0q5Km8vVXTsmOi005X00uaj2+70\\/uH70uHjI9KrTfu+jO0xpaWlpaWlpaWlpaWlpX6+N3cGt5q19udp1CYcW40voGm4172cqnOty+\\/V9fsXL\\/e1XWlpaWlpaWlpaWlpa2pVpd6lltrYRtYfFWdPtHfUgp3TtaNh+\\/cp4iO4zFTotLS0tLS0tLS0tLS0tbX165zTVxZvwjGPash1W5nnkb02697n1uLs4ZlCJt1ZjWlpaWlpaWlpaWlpa2lVq94tu4dLU5\\/4zNv62BYfjj\\/LYo3zGdDuslZ85IUtLS0tLS0tLS0tLS0v7Km3eOQ3TZze3mnfTFnobLfHZHnaaH9opz8vt16Cedkz3tLS0tLS0tLS0tLS0tOvWlvad2perJfXfntPm5z5NLmp9t522LK9myU28Yc+WlpaWlpaWlpaWlpaWdv3aXPvmmrem052tVfbetZqfbZv11G+\\/Tsq31Ly7HxXOtLS0tLS0tLS0tLS0tLQ\\/rc3fDcX2W43Dh2pbqC24a3+f5gU3qWu4DluOw50vtf+kpaWlpaWlpaWlpaWlXaW23Hp122e+OXT63C61NV36GYYPvbXPVusOJhdN\\/15P7vPS0tLS0tLS0tLS0tLSrkUbFsoTi7ZN\\/dG3Gnc7p9NCU6tx2DHt8j4XzNtUOH8+2uSlpaWlpaWlpaWlpaWlfbF2WGkuz5xelq2zZblzGtSl337tXvm9v4qlfq\\/mpaWlpaWlpaWlpaWlpaV9flpv6hbO17Rcw8J54G6esxSK7EPNN4hewudQe7dbmJaWlpaWlpaWlpaWlva12sGM3EM\\/rbdr9H10XUuoeWva930f1bylHVxtD3mwz0tLS0tLS0tLS0tLS0u7Am2+nuXctLXfOS2jLuHcarxbPuTYD9M9pzFItX8ILS0tLS0tLS0tLS0t7aq103cOt98cFvNv4wLH9Kpt03NwU+hpPnPajT+6d\\/3oRylPTy6ipaWlpaWlpaWlpaWlpS3PnOJs39ml61lyl\\/C5jUhq03rjzaG3X+QhTZtwcPU4bxIPuoYLLS0tLS0tLS0tLS0t7bq1\\/5WrQZcrz\\/e7W7RxklG4OKamluM6jz+K3cLP3BhKS0tLS0tLS0tLS0tLuwLt8tLPTeoWzpOLphp3MML2MBfQ00Py5KLu71wo54tjaGlpaWlpaWlpaWlpadenrek3bYFNmH\\/bNj1La53thg6l7ddru360Ls+ctgwe8nBaLy0tLS0tLS0tLS0tLe1rtAN9qHlzK23pD2CW1DpbUzNv6R9Slq+cf\\/zkmVNaWlpaWlpaWlpaWlpa2v+jDbrpmOg1bdEObg79mHW1dQufR63Gl\\/DK7QKZ71TmtLS0tLS0tLS0tLS0tK\\/R7pbHR09pf7f0o2trHdwc2l3+ebgzB\\/c47\\/d2Z0+7luMwQ+nJXWlaWlpaWlpaWlpaWlraF2g\\/7u6c\\/mAObrfZuax5h7eodLVv+PElSWhpaWlpaWlpaWlpaWnXrr3TOluHZ06DcpB0FUvefv1B\\/20Yf0RLS0tLS0tLS0tLS0tL+xu0m1Zcd5d\\/LlWlnT3d1\\/Gln4e5S\\/iaKvRL+F6YF1xpaWlpaWlpaWlpaWlp\\/yJt3tctyzOny5tCY+vxqT\\/AWlsN3M6cduruVf93hU5LS0tLS0tLS0tLS0v7x7T3u4XzMdHQLXxJZepgdO1hdOa007aCuY5elZaWlpaWlpaWlpaWlnZt2vqj7771w4e2aed021pms255i8om1LxB+fCsKS0tLS0tLS0tLS0tLS3t97UiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiq84\\/AQAA\\/\\/9zaKAO1TPKvwAAAABJRU5ErkJggg==","expires_at":"2025-12-29T21:34:27.000-04:00"}	\N
23	5	6	\N	100	canceled	mercadopago	\N	\N	2025-12-30 00:56:26	2025-12-30 00:56:27	pix	\N	\N
24	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 02:23:57	2025-12-30 02:23:57	pix	\N	\N
16	5	6	\N	100	paid	mercadopago	139301856905	2025-12-29 23:32:41	2025-12-29 23:32:14	2025-12-29 23:32:41	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"bank_info":{"is_same_bank_account_owner":true},"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.01,"refunded":0},"client_id":0,"date_created":"2025-12-29T19:32:14.000-04:00","external_charge_id":"01KDP791VWV3JBTMZPC72S1XKN","id":"139301856905-001","last_updated":"2025-12-29T19:32:14.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-29T19:32:14.855-04:00","execution_id":"01KDP791T4VHQA67M4TF2Y6ZEQ"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":"2025-12-29T19:32:40.000-04:00","date_created":"2025-12-29T19:32:14.000-04:00","date_last_updated":"2025-12-29T19:32:40.000-04:00","date_of_expiration":"2025-12-30T19:32:14.000-04:00","deduction_schema":null,"description":"Apoio campanha #5","differential_pricing_id":null,"external_reference":"pledge_16","fee_details":[{"amount":0.01,"fee_payer":"collector","type":"mercadopago_fee"}],"financing_group":null,"id":139301856905,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":"2025-12-29T19:32:40.000-04:00","money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":"XXXXXXXXXXX","entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"sub_type":"INTER_PSP","transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":29055667642,"long_name":"MERCADO PAGO INSTITUI\\u00c7\\u00c3O DE PAGAMENTO LTDA.","transfer_account_id":null},"is_same_bank_account_owner":true,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":84935392,"branch":"1","external_account_id":null,"id":null,"identification":[],"long_name":"NU PAGAMENTOS S.A. - INSTITUI\\u00c7\\u00c3O DE PAGAMENTO"}},"bank_transfer_id":120909639071,"e2e_id":null,"financial_institution":1,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce352040000530398654041.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter139301856905630418BA","qr_code_base64":"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKtElEQVR42uzdTVIbyxIG0FJooKGWwFK0NFgaS2EJDBkoqBfGalVmdusH82z1jTjfhIsvqE97lq6szCYiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIifze7Psvrrz\\/f\\/\\/qvz9PXkPfT7z313tq297fW+nI+Tz+5+fql5ws\\/NX3Y4p\\/T0tLS0tLS0tLS0tLS0v5M+1a+f\\/31gMUHHr++e5496CN95Nf3h\\/OrBW1Qv5xfOXydcqClpaWlpaWlpaWlpaVds3ZUmrtS807aUPN+qb+Ux6EN6pP28\\/Tgz\\/S85\\/NbbNMrj+r7g5aWlpaWlpaWlpaWlva\\/pf1dph7OuvDAfVQua1PhvHgMm45faWlpaWlpaWlpaWlpaf\\/72j7UU837EsvW6ZcW+2+nmjcVzr\\/VL+WVaWlpaWlpaWlpaWlpaWn\\/rrZ0C9eEbuH9aPTtp+7hST01+p4q9c2oxOs575S3Uqn\\/WW8zLS0tLS0tLS0tLS0t7b\\/UzicXTee8nydd1U4Ti46jXH0bd03LYfF9H\\/KDOUu0tLS0tLS0tLS0tLS0\\/0y7mHrH9DOVqy\\/XCuepXD3M5uBO2c4nF\\/0stLS0tLS0tLS0tLS0tP9Sm7pfe5x\\/G66HTmVqmIPb2pUFKNOF1cP5FUMTb6h5xwSjXZmdREtLS0tLS0tLS0tLS7s+be6\\/Ha2z6RZnuHhZDz2TemFyUZ9tUzmm2red1d\\/vFqalpaWlpaWlpaWlpaWlvWcX57SupSrruW+4LjqujdZz3t3SlN6Fab0tjj\\/6GP9WcKNCp6WlpaWlpaWlpaWlpX2UNvTs1kbfUbZO3cLH8cBUruY7p2ly0aG85XRYnGrdcHH1es1LS0tLS0tLS0tLS0tLuwZtyWdS10bfUetux+KTUK4OdVo\\/2oeyx+PXXrqFaWlpaWlpaWlpaWlpaderXei\\/PZy1n2l9Zrpz+hTV6ZfDg\\/bxELQ289Z8xA7gRktLS0tLS0tLS0tLS0v7U21Qp7zHLuGgTofD21FUP83WtdRD4vzqi6\\/aYsVOS0tLS0tLS0tLS0tLuz7tbjT81p85lGuiaelnjw\\/cpaPaMv6onbS9TOudXvGYxh7dV6HT0tLS0tLS0tLS0tLSPlI7Gnz70tChel00XFQN10XLyenyndNpaO50\\/Jo+pM8KZ1paWlpaWlpaWlpaWtpVacOh5+tsi0qbn5i2eNh5TA8q10Y\\/Ux9ubeJtZRVLot6Yf0tLS0tLS0tLS0tLS0tLe492cedLKw\\/czM95U6NvLxV63flSz3nD94t3T7+3OZSWlpaWlpaWlpaWlpb2n2nbhUG7PXYJLw\\/aTdN6p1d\\/PX\\/dpGm9I+FD0oXVO2\\/I0tLS0tLS0tLS0tLS0j5Qu1vavFLvmoay9T0etx7L0s+gTeOP0tzb3C2cLq6+3XvnlJaWlpaWlpaWlpaWlvZh2rCvM\\/Xf1tG140F9vOLi5KIwNDdtUenlAuvC\\/NvroaWlpaWlpaWlpaWlpV2HdhfvUE6HnkHbrvXffqTj13nN+6XbjGPYPubfjtucd97ipKWlpaWlpaWlpaWlpaX95iygOrnofex8eTkf1W7HwN2n0tD7VK6LTq96KHdO++mV68Si0bfc7juVpqWlpaWlpaWlpaWlpX2ANj\\/o+6nl6hg+VFuOF7722C0cJvHe3PlCS0tLS0tLS0tLS0tL+xjtLv2\\/+QbRFg8\\/W+oWTq\\/abnQN99nF1bBF5TuTi2hpaWlpaWlpaWlpaWkfq10sW9+HOimnbSptPLDo+uUhuun7hR2cb7S0tLS0tLS0tLS0tLS0f0ubKvJp+edLa+XINmh7nNbbYrdwn7ccB+34N4FjkXxntjAtLS0tLS0tLS0tLS3tY7TTddGw9LOdhw2Fab3huui4Jpo\\/JHULv8bRv+FDXtrC4ph+R7cwLS0tLS0tLS0tLS0t7WO185\\/J829bPDm9NLo21bxh\\/WhqOQ7jj5I2\\/L3dnLNES0tLS0tLS0tLS0tL+1jtYgttOuTcpMPO1q4sQEl3Tt\\/HCeq4uLqd9+MuhJaWlpaWlpaWlpaWlpb2\\/6Cdz1kK61r28w96KeqxOTR8SIstxn20HL\\/ExTFt6VVDyzEtLS0tLS0tLS0tLS3tyrRJ2Utj74Jy3\\/Pm0MVBu7txvjvGH23mO1\\/CtN77znlpaWlpaWlpaWlpaWlp16Ht5QGhW\\/i5dA2Pube15TjcOU3Hrfs4RDePParacfuVlpaWlpaWlpaWlpaWdq3acV6ZytVJuRkfPNXCb7EPN4+uHa++HwX0S9GPjaHHsn70ztDS0tLS0tLS0tLS0tI+QBvK1cUT1FrrTltUplucvejTh77HJt6pgA5bVMJV0Fo409LS0tLS0tLS0tLS0tL+VBse8FrOe8ewoVbWtVy6NvpRvn8v3cL9wi8vjvylpaWlpaWlpaWlpaWlXZ92odI8xHUt+35x6edoNf5Iyz9Ly3FPd03nXcLtG93CtLS0tLS0tLS0tLS0tI\\/VTsOHplr3q\\/bNW1OeL2wQHQ8Ox651\\/u28a3ib1IurWF6vFOe0tLS0tLS0tLS0tLS0K9CmoUPTgzbzBSjv5RV72aJymNW8+c7p2MF5TILv1by0tLS0tLS0tLS0tLS0tPef8\\/bRLdzL5tD50s\\/a4JvnLI1fDi3H9bC4R\\/VT0dHS0tLS0tLS0tLS0tKuV\\/tWro0ezvq67PP6upb5g\\/blAmv9sIVu4dRyTEtLS0tLS0tLS0tLS7s+bbpzuovlaR461C+o0\\/Hr2PlSd7z0cnI6Fc5Zfb1bmJaWlpaWlpaWlpaWlvax2lauiebzy\\/TAl3LndH5yWh8Uat7n2fij7dAudALT0tLS0tLS0tLS0tLS0v5Uu0tLP0dxnc55w4PSOW8fXcL14up41U2\\/mauvTEtLS0tLS0tLS0tLS7sqbbpz+lHWtYTz3Vr7jkbfVna+7OLF1YWdL+83Don7vTdkaWlpaWlpaWlpaWlpaf+59i2ua1nI\\/MT0eHrA8cLF1fCK0wSjWjhPLcfjwz4un+HS0tLS0tLS0tLS0tLSrkcbklpn+\\/lBvQwdqtdFFycXbebaVu6gLow\\/oqWlpaWlpaWlpaWlpV2z9ro+tdCmXZwtXsBceOVDHH+Umnhr4dwv\\/H3R0tLS0tLS0tLS0tLS0v5\\/ta0tLPvMG0Tnyz5zhX5qOa4jf\\/P60fnkonxYTEtLS0tLS0tLS0tLS7tG7W5edr7GB7+Xn093Trdl6FBYHPMaz3n3sYDephbk2+tHaWlpaWlpaWlpaWlpaVelfSvfj\\/m3k25TuoWnB2zHpKLb3cL5AutX6sXVPu6e0tLS0tLS0tLS0tLS0q5XO84rg3Z+YtrToef0wMXCuRbQrbXb\\/bdv986\\/paWlpaWlpaWlpaWlpaX9c204op0q9FaK63Zu+O0XpvXWMr+XQ+J01\\/QnFTotLS0tLS0tLS0tLS3tA7W\\/H\\/A6u3t6TEe2Lda+LZ7z7sYvVW3qGq417tOtCUa0tLS0tLS0tLS0tLS0a9Be7hYOO1\\/aaf7t87nBdzv\\/4MPsleuF1XrntCfBuLhKS0tLS0tLS0tLS0tLu0rtpcPO1\\/NhZzg53S\\/134YHj8PPWuOGE9Pwyv389VLTLi0tLS0tLS0tLS0tLS3tn2lFREREREREREREREREREREREREREREREREREREVp3\\/BQAA\\/\\/\\/sJIeB3WrjPwAAAABJRU5ErkJggg==","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139301856905\\/ticket?caller_id=1503133188&hash=91890da1-38bf-4bea-a8f7-8fa22bc8a151","transaction_id":"PIXE18236120202512292332s098f3b9e48"},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"approved","status_detail":"accredited","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":1,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":120909639071,"external_resource_url":null,"financial_institution":"1","installment_amount":0,"net_received_amount":0.99,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":1,"transaction_id":"PIXE18236120202512292332s098f3b9e48"}}	\N
17	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 00:13:51	2025-12-30 00:13:51	pix	\N	\N
18	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 00:14:13	2025-12-30 00:14:13	pix	\N	\N
19	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 00:14:16	2025-12-30 00:14:16	pix	\N	\N
20	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 00:34:05	2025-12-30 00:34:06	pix	\N	\N
21	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 00:46:47	2025-12-30 00:46:48	pix	\N	\N
26	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 03:46:42	2025-12-30 03:46:42	pix	\N	\N
27	5	6	\N	1000	canceled	mercadopago	\N	\N	2025-12-30 04:25:10	2025-12-30 04:25:11	pix	\N	\N
30	7	6	\N	100	paid	mercadopago	139401385959	2025-12-30 17:33:05	2025-12-30 17:32:14	2025-12-30 17:33:05	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"bank_info":{"is_same_bank_account_owner":false},"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.01,"refunded":0},"client_id":0,"date_created":"2025-12-30T13:32:14.000-04:00","external_charge_id":"01KDR52K7JM2R76E87AXK2GMAD","id":"139401385959-001","last_updated":"2025-12-30T13:32:14.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-30T13:32:14.972-04:00","execution_id":"01KDR52K6JKYJT722ECA9QQYMN"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":"2025-12-30T13:33:04.000-04:00","date_created":"2025-12-30T13:32:14.000-04:00","date_last_updated":"2025-12-30T13:33:04.000-04:00","date_of_expiration":"2025-12-31T13:32:14.000-04:00","deduction_schema":null,"description":"Apoio campanha #7","differential_pricing_id":null,"external_reference":"pledge_30","fee_details":[{"amount":0.01,"fee_payer":"collector","type":"mercadopago_fee"}],"financing_group":null,"id":139401385959,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":"2025-12-30T13:33:04.000-04:00","money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":"XXXXXXXXXXX","entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"sub_type":"INTER_PSP","transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":29055667642,"long_name":"MERCADO PAGO INSTITUI\\u00c7\\u00c3O DE PAGAMENTO LTDA.","transfer_account_id":null},"is_same_bank_account_owner":false,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":980182705,"branch":"1","external_account_id":null,"id":null,"identification":[],"long_name":"NU PAGAMENTOS S.A. - INSTITUI\\u00c7\\u00c3O DE PAGAMENTO"}},"bank_transfer_id":120927687595,"e2e_id":null,"financial_institution":1,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce352040000530398654041.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1394013859596304775A","qr_code_base64":"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKzElEQVR42uzdQXIixxIG4FKwYMkROApHE0fTUTgCSy0I+oU1dFdmVSGQxx76Ob5\\/o9BYor\\/2LpVZWUVERERERERERERERERERERERERERERERERERERE\\/t1spy4ff\\/37rn6tuaTvS9lM06mUaZzr7Qlv03Qu5f3OT339yP7Ov9PS0tLS0tLS0tLS0tLS\\/p721Hz\\/cXtAuWmPywMvX\\/\\/09XUfH\\/SZPvLr+8Nf\\/35utEF9vP1yiV\\/nHGhpaWlpaWlpaWlpaWnXrK2V5rapeeeydc65aqfl62d64E17vT34mp73vrzFJtW8tfr+pKWlpaWlpaWlpaWlpf3\\/0r7dOqhzuRoeuIvKS6p5a+2bPmT+pWttu84F9Z6WlpaWlpaWlpaWlpb2v6ENTc+55j3Gedz5l4bzt3PN2+p28UMutLS0tLS0tLS0tLS0tLR\\/QNtMC7cJ08K7Oug73aaHZ\\/U86Hur1MPIcdvnneor\\/wOzzbS0tLS0tLS0tLS0tLR\\/UttvLvr1oLlsPXbaeWPRpZarp3rWNBXOz37Ib+xZoqWlpaWlpaWlpaWlpf1j2mHaM6bXVK4evyuc53L10O3BnbPpNxf9XmhpaWlpaWlpaWlpaWn\\/pDZNv071uGg\\/OrtJe3BL+eYClHn\\/7WF5xTzEO9e8dYPRttmdREtLS0tLS0tLS0tLS7s+bTg7WZcP5c1Fs3YaNT3vfWj\\/ym\\/Nq27ShZ4\\/m7+lpaWlpaWlpaWlpaWlpX2sLc3C3VbZ9n2nfnNRLa5LnBZut\\/QONhmVuP7os\\/6t4MHfE2hpaWlpaWlpaWlpaWlfpQ37bz\\/qsdGm3ztPC1\\/qA1O5ms+cpsK5zdwsTrVuOLj6sEKnpaWlpaWlpaWlpaWlfa22zzUdG22XDrWra9tytX\\/l8+3A6jmeOd3097ekwpmWlpaWlpaWlpaWlpZ2rdr0M4dFe22uz5yqet+vqu2v0dzFzUXtMG+bNAFcaGlpaWlpaWlpaWlpaWl\\/V5uOiYaK\\/Vy6rb1tem36vm0SD179FD\\/sc\\/ihtLS0tLS0tLS0tLS0tGvSbvtjo2WZ2b2msjVd+jndNhmlwnnba+fjo+nCmF298yXdHHp6okKnpaWlpaWlpaWlpaWlfbm22Vz0xNKh1DnNZ07r\\/ttzvEF0Sq84t1\\/bD3um5qWlpaWlpaWlpaWlpaV9lTb8bN06+5bUx+4ClFCulr5gvqmvaQ433RgaNhe1H9JIaGlpaWlpaWlpaWlpaWn\\/tnbbdFfDjSuHOOg76POmKeFTLO+DsjaJQ583fD88e3p47u8JtLS0tLS0tLS0tLS0tH9aW+4v2k193aDstYNXrSPH7YHVwYe0d5dOtLS0tLS0tLS0tLS0tOvUbkc3r1ybfbehbD03ndN06ee2ab+ey2DvbZ4WrhuMtj84c0pLS0tLS0tLS0tLS0v7Mm24ReX2s+PVtfVBoVOa1h6la0h\\/\\/VI71DstHdRNv\\/821L60tLS0tLS0tLS0tLS0a9amM5Tz0qFv5m8HD\\/r2KpZ6FDTM4X790ia9+sNTnLS0tLS0tLS0tLS0tLS0z2vDz9QK\\/VzvfDkurdpw18u+GejdN8dF51c9NGdOp9srz+V+Ox38dFealpaWlpaWlpaWlpaW9gXa\\/KCfJ4waN8uH2pHjwdcpTgu3F8nQ0tLS0tLS0tLS0tLSrk87vABlavqXb7XZGaaFSyn9mdN7U8NTd3A13KKSNhc9OdtMS0tLS0tLS0tLS0tL+xrtsGw9V3VSpjs5N0nZ1Lz5w96773PntBbQtLS0tLS0tLS0tLS0tLT\\/hjZV5F8V+u6m3fUPqtPDoWI\\/xKVN7cjxFM+izn8TuAznlmlpaWlpaWlpaWlpaWnXrJ2Pi27r5qKyLBsK23p3tQZu+7y11g3Twh9x9W\\/7Ie3FMdMT08K0tLS0tLS0tLS0tLS0r9X2P\\/NWO6ehPL23urZOC5f6ysM7X8L6o6QtzYc8mG2mpaWlpaWlpaWlpaWlfaF2OEKbtOMHpQtQ0itv73zIfGNoO487CC0tLS0tLS0tLS0tLS3tP6Dt9ywNFu1OsUW7aRbsfqY7YOqx0V19+nEZPQ4Xx5TRq4aRY1paWlpaWlpaWlpaWtqVaZNySoO9H\\/GY6FQfVGvecE1LOri6rf3duv7orb\\/zJWzrfa7PS0tLS0tLS0tLS0tLS7sO7dQ\\/4GMpW6+3\\/7JJ17WU5uxpOnNa260lHVTt1x5dhgdXaWlpaWlpaWlpaWlpaderTcdFh+Xq1HRS285p6Jj2r7qrNW\\/tqF7S5qLvb0+hpaWlpaWlpaWlpaWlXYd2PnD52XdQP5aDlyXVvO9LmTq4RSV96DkO815vNW9Yh9SuP5pq4UxLS0tLS0tLS0tLS0tL+7va3Oet08KpQm+Pj947NvrZfH\\/uy\\/z+ly9p5Lj+rYCWlpaWlpaWlpaWlpZ2fdp7leaub9XW61lKv6koXP5ZXzXsv621bjslvKkHVR9OC9PS0tLS0tLS0tLS0tK+VhvUH03TsyzHRK\\/pR9+XDUWbvu3a7r\\/tp4Y3ST28iuXjialhWlpaWlpaWlpaWlpa2tdqa+2b99+mC1DO8axp6JjumwtQDs38bdp\\/O40K6B\\/WvLS0tLS0tLS0tLS0tLS05cenOOuepWtdtJsu\\/Qx3vMzHRU+xMp9\\/+Stv6SKZ+frRKar3jY6WlpaWlpaWlpaWlpZ25dp0XUupNW+67PP761r6B+1iy3bwYeH60fohYeSYlpaWlpaWlpaWlpaWdpXa+jPbqUt\\/50tW105quvOlveNlajqnc+Gc1T+ZFqalpaWlpaWlpaWlpaV9gXY\\/ujE0LB0qS+f0V9oH1s7p4EGH5faU3Dk9LmdN8xzuw\\/lbWlpaWlpaWlpaWlpaWtrntf3U8Fsqsts9S22ft5kSntL1o0\\/l0SvT0tLS0tLS0tLS0tLSrkS7TTty64POowdeUu07fNXaLL4Op4RLt6130CSeHt1QQ0tLS0tLS0tLS0tLS\\/sy7Sle13Ivg9W1temZj4umXxp2TksdOW4L51RA09LS0tLS0tLS0tLS0q5PG1L\\/fVfL1veonb+2+2+n5haVudZN2vThU2q\\/Dpfo0tLS0tLS0tLS0tLS0q5M+70+1br37uIcvvJhqXGnOsQ7ULYpPwotLS0tLS0tLS0tLS0t7Q+TL\\/s8dot328s+850vt5HjrGuvH+03F4Uzpz9T09LS0tLS0tLS0tLS0v4x7eCOl3YPbj02eu\\/MaWjRHpZluqHPG2rfsmhLmha+f\\/0oLS0tLS0tLS0tLS0t7aq0p+b7uv+21Jo3dVJD0\\/PU17y1c5puUZmSsrZfL83\\/t8cnZGlpaWlpaWlpaWlpaWlfq02jtB9N53TqmqCh6bnv529vr\\/qWCujadv1m\\/vb07P5bWlpaWlpaWlpaWlpaWtq\\/rw0t2uEdL\\/vmAadu1e+vX27L\\/Pphm+as6faJmWZaWlpaWlpaWlpaWlraFWrL7UFt7XtJLdtyq31TDvHm0N39ZnFZCudQ4+4fbTCipaWlpaWlpaWlpaWlXYP2\\/rRwuPOl3Pbfvi8Dvpv+gw\\/dK+fO6bE7czolQT24SktLS0tLS0tLS0tLS7tK7XBzUah52\\/23x1jrtntw67HRtsYNHdPwytPyNbRfP77pnNLS0tLS0tLS0tLS0tLSPqkVERERERERERERERERERERERERERERERERERERWXX+FwAA\\/\\/+14E0miRn8LQAAAABJRU5ErkJggg==","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139401385959\\/ticket?caller_id=1503133188&hash=24bdffe7-f5be-4a3a-a1a3-8db32abdf704","transaction_id":"PIXE18236120202512301732s147cf58777"},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"approved","status_detail":"accredited","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":1,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":120927687595,"external_resource_url":null,"financial_institution":"1","installment_amount":0,"net_received_amount":0.99,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":1,"transaction_id":"PIXE18236120202512301732s147cf58777"}}	\N
14	3	6	\N	1000	canceled	mercadopago	139924649542	\N	2025-12-29 20:56:09	2025-12-30 21:00:28	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.1,"refunded":0},"client_id":0,"date_created":"2025-12-29T16:56:10.000-04:00","external_charge_id":"01KDNYB8J4PM9273FYKZ8BFY08","id":"139924649542-001","last_updated":"2025-12-29T16:56:10.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-29T16:56:10.063-04:00","execution_id":"01KDNYB8H6HM7PF1YSB0PG3QQ9"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":null,"date_created":"2025-12-29T16:56:10.000-04:00","date_last_updated":"2025-12-30T17:00:27.000-04:00","date_of_expiration":"2025-12-30T16:56:09.000-04:00","deduction_schema":null,"description":"Apoio campanha #3","differential_pricing_id":null,"external_reference":"pledge_14","fee_details":[],"financing_group":null,"id":139924649542,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":null,"money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":null,"entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":null,"long_name":null,"transfer_account_id":null},"is_same_bank_account_owner":null,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":null,"branch":null,"external_account_id":null,"id":null,"identification":[],"long_name":null}},"bank_transfer_id":null,"e2e_id":null,"financial_institution":null,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce3520400005303986540510.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1399246495426304483D","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139924649542\\/ticket?caller_id=1503133188&hash=d0e2cfa4-64af-438d-8399-53525019c280","transaction_id":null},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"cancelled","status_detail":"expired","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":10,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":null,"external_resource_url":null,"financial_institution":null,"installment_amount":0,"net_received_amount":0,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":10,"transaction_id":null}}	\N
28	5	6	\N	100	paid	mercadopago	140052300118	2025-12-30 17:23:56	2025-12-30 17:23:28	2025-12-30 17:24:02	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"bank_info":{"is_same_bank_account_owner":true},"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.01,"refunded":0},"client_id":0,"date_created":"2025-12-30T13:23:29.000-04:00","external_charge_id":"01KDR4JHMKDMEXV8ARTBB3FZ26","id":"140052300118-001","last_updated":"2025-12-30T13:23:29.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-30T13:23:29.053-04:00","execution_id":"01KDR4JHKNPRHTVMEQB7SRZNXH"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":"2025-12-30T13:23:55.000-04:00","date_created":"2025-12-30T13:23:29.000-04:00","date_last_updated":"2025-12-30T13:24:00.000-04:00","date_of_expiration":"2025-12-31T13:23:28.000-04:00","deduction_schema":null,"description":"Apoio campanha #5","differential_pricing_id":null,"external_reference":"pledge_28","fee_details":[{"amount":0.01,"fee_payer":"collector","type":"mercadopago_fee"}],"financing_group":null,"id":140052300118,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":"2025-12-30T13:23:55.000-04:00","money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":"XXXXXXXXXXX","entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"sub_type":"INTER_PSP","transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":29055667642,"long_name":"MERCADO PAGO INSTITUI\\u00c7\\u00c3O DE PAGAMENTO LTDA.","transfer_account_id":null},"is_same_bank_account_owner":true,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":84935392,"branch":"1","external_account_id":null,"id":null,"identification":[],"long_name":"NU PAGAMENTOS S.A. - INSTITUI\\u00c7\\u00c3O DE PAGAMENTO"}},"bank_transfer_id":120927427923,"e2e_id":null,"financial_institution":1,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce352040000530398654041.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1400523001186304B22D","qr_code_base64":"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKqUlEQVR42uzdQXJiSQ4G4CS8YOkjcBQfzRzNR+EIXnpBkBNdwyMlvQTjru6Cjvj+jcM95r2P2mlSKTURERERERERERERERERERERERERERERERERERER+Xez7at8\\/PXfX88\\/W2vv8X\\/8\\/Ov3Y2u73lt76f3QWp\\/ndP745vyheX79ye7Kf6elpaWlpaWlpaWlpaWl\\/T3tofz+cX7B2\\/mD+\\/jCpF1e9JUe+ev3t\\/Mft9WHl4e29JD0hDdaWlpaWlpaWlpaWlraZ9aOSnM7at4eX9jj7y\\/nX45DG9Rn7en84lN633t5yNAu1fcXLS0tLS0tLS0tLS0t7X9LuzmfoC7lan3hojyulalw7vGrn8o\\/Qa15aWlpaWlpaWlpaWlpaf\\/T2lrz7mMtvHxo2n+71LxBuXx4PORIS0tLS0tLS0tLS0tLS\\/sHtKVbuGYzuoRfR6Pvrxe2oV4afc+V+mZU4vWct4+v\\/A\\/0NtPS0tLS0tLS0tLS0tL+Se16ctFy53QpV6t2mVh0HOXqYdw1LRdX73vIb8xZoqWlpaWlpaWlpaWlpf1j2mk2Zd7tKZWr+1uF81KuLpOL3mYfev1bKlpaWlpaWlpaWlpaWtpn0Kbu1z7rv22jTF3K1vDhabn6FleyhH+C9UNeRudvmp1ES0tLS0tLS0tLS0tL+6za5cGjdTacoI4ydXLoGbKeXJS+at3JmRd63td\\/S0tLS0tLS0tLS0tLS0v7I2140VpZz337enLReujQdl2Zj2m9x\\/GzxfFHX+P\\/K\\/i4fdRLS0tLS0tLS0tLS0tL+0DtbhzNliPa8KI2rouO89yXoZ1OLqqHxuGcN9W64eLqPRU6LS0tLS0tLS0tLS0t7aO0YYvKx2Uhyimp95cXHtMWlXY5Qc3l6lL7lourfSh7PH7tpVuYlpaWlpaWlpaWlpaW9tm1qVx9u2hPZX1mH9dFd1F9rXX2dXz1tmrmrfkqTby0tLS0tLS0tLS0tLS0tL+nbfGaaCvzlerU3mP5UL4uWgbwhp0v7Tzyt371w5V+5W+n+NLS0tLS0tLS0tLS0tI+RrsdDb8fpeJMNW9bL\\/1cD9pN2aRpvWlhzPLQXZxgFNaPfluh09LS0tLS0tLS0tLS0j5SWyYXhUPP11nDb6hxD+Uhb6uat+Y4vvpy2zX8vKfmpaWlpaWlpaWlpaWlpX2Udvo3myvq4\\/TDteZNTbwt1riv68lF46Fpd2mjpaWlpaWlpaWlpaWlpf1dbS2uw8aV0ejb05ylfRm0m9a2pIe8xlG\\/4Zw3PTT8O+3i4hhaWlpaWlpaWlpaWlra59O2+wftvse1LeHIdjT4blP38DjnTWtIX9JDkvKeG7K0tLS0tLS0tLS0tLS0D9RuZ5tXcpfw0vibuoUn10XPhXJYPxo+3C41cO4WnrYcf3vnlJaWlpaWlpaWlpaWlvZh2h5f8NXC3NvJyWk99DyUWne0zm5KK+0mnZTuV8Nz79z5QktLS0tLS0tLS0tLS\\/sE2nSHcrIAZTr\\/NrxoV25xtqLcx8K5j\\/m34zZnv+8WJy0tLS0tLS0tLS0tLS3t\\/drDmNI7KvTPuPNlM4YN9Xi+m1uOr0zrPaXf63nv9M5po6WlpaWlpaWlpaWlpX1ObX7Rz1PL1b46LO5Jucy\\/XR8W\\/2TnCy0tLS0tLS0tLS0tLe1jtNMFKH3a6JtG107m3667hsMKlv3lIZMtKnVy0ccdvc20tLS0tLS0tLS0tLS0j9FOy9bPoU7Kz3MfbhsvTM27N2veyUOmBTQtLS0tLS0tLS0tLS0t7T+uTRX5x+VotqXpvUnbv5nW+1Yq9Krvl4fVHaa0tLS0tLS0tLS0tLS0z6td\\/mY7Jhe1clTbL5OLcqtxfcj55\\/8\\/9BFH\\/9aH1MUx\\/Y5uYVpaWlpaWlpaWlpaWtrHatd\\/UycXhUPP9MdhdG2qecP60dRyHMYfJW34d7sdWlpaWlpaWlpaWlpa2odrpy20fZSr+9no2rAAZf2Vt6WJ9zNeXJ1MLpqElpaWlpaWlpaWlpaWlvYf0K7nLIXi+nV9F7WVc97rm0PDh\\/ctTOvtfbXzJSnfaGlpaWlpaWlpaWlpaZ9Tm5R9NPa29Q6YfbyDuhvLPtcXV7fjfHeMP9qMWvdzjPxN3cLfnvPS0tLS0tLS0tLS0tLSPoe2r1+Q1ra0cnJaP9x7T3dOWzluHS3HeexR1d6eXERLS0tLS0tLS0tLS0v7DNp0XXRRjvm3dXTtS9ocml6UvnrdHDpp4q2bQ+8OLS0tLS0tLS0tLS0t7QO021GuphPUyS7O+qJRtvZy6JlucZ5S4fwem3h38cPbVDjT0tLS0tLS0tLS0tLS0v6uNnQLjyPb0\\/S66FjXcu3a6Ff5\\/bN0C\\/fy4V3pR67rR2lpaWlpaWlpaWlpaWmfTHut0kwbQ0\\/rLuE6qSgs\\/xwtx0vNm2rd+lXzzpe7K3RaWlpaWlpaWlpaWlrax2gPcWTtdj3n9j3eOQ0bRMeLv0rhHObfflwmFp1GwRzUaf1oj7ddaWlpaWlpaWlpaWlpaZ9Sm9ZnfsXJRZv1ApTaOtvLFpW3WPOmi6qLOhy\\/HuIF1vsrdFpaWlpaWlpaWlpaWlra+09Ol\\/lKZefLaQzaTV3CfRTVh1mZ32Z3TUMLcvrKx3Ro\\/G23MC0tLS0tLS0tLS0tLe0DtS01+l45ou2jXG3t6rqW6YvWm0PzxdUkOMQqnJaWlpaWlpaWlpaWlvYptfUE9SPueOnrnS9VPcrXtPNl01epHz6c75zu4kNudQvT0tLS0tLS0tLS0tLSPlablKFM\\/Rw\\/2+XkNN85XZ+c1hdNT05z\\/20dh3R3tzAtLS0tLS0tLS0tLS0tbb\\/vFmdbF9d1RFJfnfP20SW8i3dNJxdXb+bmV6alpaWlpaWlpaWlpaV9Nu3yN+NFn2X4UJpgdMcx7Li42lPLcVtN650cEvfvdr7Q0tLS0tLS0tLS0tLSPlab\\/7YkDB3qPS\\/9TBdWr324rQvn9\\/KQ9cIYWlpaWlpaWlpaWlpa2qfUhkxbZ0f\\/7csoXxd1GDo0CuZfL9ycC+egTQ+\\/8RBaWlpaWlpaWlpaWlraJ9Xe1o\\/+2xYPP9vowz3MPlQL5c+yRSUoa9qPQktLS0tLS0tLS0tLS0v7w2ym57v7S3EdjmjrxdW32C38Oms1fjn\\/XicXhb7ln6lpaWlpaWlpaWlpaWlp\\/5h2Uq7WObhLuVrvnK5fdO2cN9S+7aJt65bja+tHaWlpaWlpaWlpaWlpaZ9Heyi\\/j\\/m3tbE3L\\/8c5enkRW+XyUXh2LWXsUe73tNX7uPuKS0tLS0tLS0tLS0tLe3zatOLP8rJ6b6o95e+23Z9ctHyVde7OW\\/03x5+MK2XlpaWlpaWlpaWlpaWlvZvasMR7XTHy272gu0V7a+HbEqF\\/lLumm7v6GmmpaWlpaWlpaWlpaWlfULt8qJ69\\/SYjmxbrH3b6BZOd06rNnUN1xp3990EI1paWlpaWlpaWlpaWtpn0F7vFk5zb19St\\/BStlbt9ALr+5Wu4aVwHtqv37hzSktLS0tLS0tLS0tLS\\/uva6+3zi6HnZuxPeV11n\\/b1ndQ11tUrl1cnVxg\\/bhxckpLS0tLS0tLS0tLS0tLe6dWRERERERERERERERERERERERERERERERERERE5KnzvwAAAP\\/\\/PQ+DoUy6\\/HMAAAAASUVORK5CYII=","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/140052300118\\/ticket?caller_id=1503133188&hash=d81c8858-575c-409d-8618-7ef780c2c397","transaction_id":"PIXE18236120202512301723s094d97ce4e"},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"approved","status_detail":"accredited","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":1,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":120927427923,"external_resource_url":null,"financial_institution":"1","installment_amount":0,"net_received_amount":0.99,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":1,"transaction_id":"PIXE18236120202512301723s094d97ce4e"}}	\N
29	5	6	\N	100	pending	mercadopago	139403690497	\N	2025-12-30 17:24:14	2025-12-30 17:24:16	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.01,"refunded":0},"client_id":0,"date_created":"2025-12-30T13:24:15.000-04:00","external_charge_id":"01KDR4KYTMDS0T69S3S49PB3HW","id":"139403690497-001","last_updated":"2025-12-30T13:24:15.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-30T13:24:15.325-04:00","execution_id":"01KDR4KYSKEG6W18W7KCR669F6"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":null,"date_created":"2025-12-30T13:24:15.000-04:00","date_last_updated":"2025-12-30T13:24:15.000-04:00","date_of_expiration":"2025-12-31T13:24:15.000-04:00","deduction_schema":null,"description":"Apoio campanha #5","differential_pricing_id":null,"external_reference":"pledge_29","fee_details":[],"financing_group":null,"id":139403690497,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":null,"money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":null,"entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":null,"long_name":null,"transfer_account_id":null},"is_same_bank_account_owner":null,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":null,"branch":null,"external_account_id":null,"id":null,"identification":[],"long_name":null}},"bank_transfer_id":null,"e2e_id":null,"financial_institution":null,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce352040000530398654041.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter13940369049763049845","qr_code_base64":"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKuklEQVR42uzdQXLqSBIGYDlYsOQIPgpHexyNo3AEll4Q1sR4EJWZKhl4njbqiO\\/feDxtpI\\/eZWdW1iAiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIi\\/2y24yzH+M935R+eh+HP9X8Ow2YcT8Mw9vN5fcJb+lDN15+8L\\/z\\/tLS0tLS0tLS0tLS0tLQ\\/057K78f\\/vmBS78bxcHvh5etPvn6+xxd9pEd+\\/b6\\/frWkDerD9cND\\/DllT0tLS0tLS0tLS0tLS7tmbas0t63m3d8e\\/JZq3q9a9yuXpE0\\/99ead3+rfYfrV80Padqp+v6gpaWlpaWlpaWlpaWl\\/Xdpp5p3KlenF1blpVvzzh8ydVJ38SvXmpeWlpaWlpaWlpaWlpb236s9l+bnV+27ac3P6UPd+dup5t015eHWhp2Gei+0tLS0tLS0tLS0tLS0tL+gLcV1TZgW3rVB3\\/E6PTypW3P4o32oJp09PZVK\\/e9mm2lpaWlpaWlpaWlpaWl\\/UzvfXDTVvJ\\/zM6e7a+37VeteWrl6amdNS+H82EN+sGeJlpaWlpaWlpaWlpaW9te03byVfbefqVw9fFc4T+XqfrYHd0gjx4derft3oaWlpaWlpaWlpaWlpf1NbZp+TcdEw\\/HQXZy\\/vaQPd8vVaXi3rj86xIedhrDBaDvfnURLS0tLS0tLS0tLS0u7Pm1IG50NHdRJO\\/aankvt2PlXfitfdVJuygTwd\\/O3tLS0tLS0tLS0tLS0tLRPad9bXTxX1r5vOC7ajo3WPu+2XTs6lg1Gqewf4vqjjyShpaWlpaWlpaWlpaWlXaN2215YbwxN17UM7bho6+duknY6c1o2F+WvOjWLU60bDq5+X\\/PS0tLS0tLS0tLS0tLSvlYbKs+xHBNdGvRNq2uHebna1On60bEpx9h+Hcu0MC0tLS0tLS0tLS0tLe16tdve9Otn+7krymmE9n3hIpTlfy11mLfmI04AD7S0tLS0tLS0tLS0tLS0P9UG9TFW6qlFG9RpxHgzv3a0XdcyHVwdrk3izlevZf4QK3ZaWlpaWlpaWlpaWlra9Wk708Kp9t3FQd+waHeaFq6LduvK36atD5lW\\/XauH71bodPS0tLS0tLS0tLS0tK+UlsGfDtLh4Y48Fsv\\/dy2wd+6\\/mg\\/65Berg+7tIfUW2fu1Ly0tLS0tLS0tLS0tLS0r9KGyz+PcVS2qjsfSpuL5sdGw4d2pWAOm4vG3jWkd7b10tLS0tLS0tLS0tLS0tI+ol2q0NML31pxHW4ObYO+Y6rQ53e+1D5v+D1V5O\\/Xs6f7Z\\/97Ai0tLS0tLS0tLS0tLe3vaIflRbvp7Ok5vnDTPS6aHtJGjsdyDWnY1luVj5yQpaWlpaWlpaWlpaWlpX2hdtu7eeWz7Lv9Zlo4XfrZ7ZzWvbd5WrhtMHrmzCktLS0tLS0tLS0tLS3ty7Thvs7r34a9t7VzOqlPbYQ2zd8eY63bGeod7xbOAy0tLS0tLS0tLS0tLe3qtXV17bmVra3mXZq\\/\\/ejdxfmWrmI5zNqv+SjofIiXlpaWlpaWlpaWlpaWlvbH2rRo96O9aH+77POtLRsa4zUteeQ4HRedvuq+nDmdDq5O5X6dDn64K01LS0tLS0tLS0tLS0v7Am1+0fMJo8YtH+Xa0XMbNU4\\/xzgtnO9+oaWlpaWlpaWlpaWlpV2jdjuvOJcGfdPq2vqC00Ibdvn60XCLyjObi2hpaWlpaWlpaWlpaWlfq+2WreemTsrzdQ53iC\\/s1rz5YX9mv+fOaSugH7nzhZaWlpaWlpaWlpaWlpb2WW2qyKfLPw\\/DUFq2QTvGCn2I08LjfOQ4aNt\\/E7h055ZpaWlpaWlpaWlpaWlp16ydKs1t21yUtvZOx0Tr5qL3ebmapoWPiw+ZPnxJU8KP9XlpaWlpaWlpaWlpaWlpX6ad\\/03efzvEzukYX5BX17aaN1w\\/mkaOw\\/qjpA3\\/3r4PLS0tLS0tLS0tLS0t7cu1p7n62r8Mte\\/U7NzFr9afv20PO7cOaju4upnP43ZCS0tLS0tLS0tLS0tLS\\/t\\/0M73LIXrWnbxEaFFO5Q9S91rW8KHD7fR43BxzND7qsP3Z05paWlpaWlpaWlpaWlpX6hNyrEM9ua0F4UHp2nhUPMeW9\\/3+uG3+Z0vYVvvY31eWlpaWlpaWlpaWlpa2nVox\\/KCMC38J04N1zOnaeQ4nDmt7da2RDevPara7\\/ff0tLS0tLS0tLS0tLS0q5Bm46LHm9Nz892XHRsI7VD2VyUXpS++q4V0IfZEO8lbS5q7dYHQ0tLS0tLS0tLS0tLS\\/sCbShXUwe1s7lomHVOw+\\/dGvgch3inAjrcohLWH9XCmZaWlpaWlpaWlpaWlpb2p9pOv7dW5odYVNcWbTo2+lF+P5dp4fHOmdN6\\/SgtLS0tLS0tLS0tLS3tyrTb7t6g0pL9LNqpXB1aoVw3GO3L\\/ts\\/sUmcpoSHJ6aFaWlpaWlpaWlpaWlpaV+uPcUB32287HO4Njv7N4i2F4e2a91\\/O58a3iR19yqW4zfTwrS0tLS0tLS0tLS0tLTr0Lbp18\\/ri3LntL0o7L2dPvReLkDZl\\/nbtP92nLdhn655aWlpaWlpaWlpaWlpaWkf0k6Vedna+9kW7e7KnS\\/TVzzFrxxWJe1nS5pCs7heR\\/pedLS0tLS0tLS0tLS0tLQr1w7tupZU86bLPr+\\/rmX+ol1s2XYe1pkWTiPHtLS0tLS0tLS0tLS0tOvTpq2z6c6XvHRoXFC3Tmq686Xe8TKWzulUOGf13WlhWlpaWlpaWlpaWlpa2hdqh3JMNJSndYPRoZw5nXdOOy\\/a325PyZ3Tw+2saV6H9MgtKrS0tLS0tLS0tLS0tLS0D2rT4t2xnTVtfd7phUt3vnykF7Wfj+beV6alpaWlpaWlpaWlpaVdiXabduS2F53LCzu177zPO7YlROn60Xrny\\/lOk3h84IYaWlpaWlpaWlpaWlpa2tdoT\\/G6lk66q2vf4wrb7UIte17onIYCuj3so9fDpaWlpaWlpaWlpaWlpV2bdozl6hCPiYYXDXHp0FhGZ8OyoTY6+zbXDuUMamf9ES0tLS0tLS0tLS0tLe2atd\\/rU627dBdn9yvvbzXu2IZ4O8qa4anQ0tLS0tLS0tLS0tLS0j6ZfNln21h0aa3ar6J607RLF8e0ZnG+fnS+uSicOX1OTUtLS0tLS0tLS0tLS\\/tr2s6g7zG++Bz1l6Z\\/jw8MLdp2cUzo8+7iwdVN0w\\/xzpel60dpaWlpaWlpaWlpaWlpV6U9ld\\/b\\/ts8NTwtHWrHRfPq2vSiaUp4H0eNx7j2aFg4uHr\\/hCwtLS0tLS0tLS0tLS3ta7VplPY465y+lU1Gm\\/lx0Xn+9+F0Icq9+dvTo\\/tvaWlpaWlpaWlpaWlpaWn\\/Xttt0ebiergN\\/PbPnqYyf\\/rK9cKY+uG7J2JpaWlpaWlpaWlpaWlp16YdruVqrn3bdS3TH29SkzhNC6eatxbO09RwrXHf720woqWlpaWlpaWlpaWlpV2DdnlaONz1UqeF2+aicAZ1P\\/vKuXN6mJ05HRe+Mi0tLS0tLS0tLS0tLe0qtUvNzuOt2fnWOqe7tro2KefHRmuNOywcXM0HWNO\\/J1paWlpaWlpaWlpaWlraH2hFREREREREREREREREREREREREREREREREREREVp3\\/BAAA\\/\\/9XlqCwGpaYJAAAAABJRU5ErkJggg==","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139403690497\\/ticket?caller_id=1503133188&hash=bc2cf5e9-ca3f-4f71-988b-e89edb8e5ce3","transaction_id":null},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"pending","status_detail":"pending_waiting_transfer","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":1,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":null,"external_resource_url":null,"financial_institution":null,"installment_amount":0,"net_received_amount":0,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":1,"transaction_id":null}}	\N
31	7	6	\N	100	paid	mercadopago	139406541171	2025-12-30 18:00:23	2025-12-30 17:59:57	2025-12-30 18:00:23	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"bank_info":{"is_same_bank_account_owner":true},"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.01,"refunded":0},"client_id":0,"date_created":"2025-12-30T13:59:57.000-04:00","external_charge_id":"01KDR6NAYN0S1GF7B3SP2RC1NA","id":"139406541171-001","last_updated":"2025-12-30T13:59:57.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-30T13:59:57.662-04:00","execution_id":"01KDR6NAXPP7SZJEY64HA04HMM"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":"2025-12-30T14:00:22.000-04:00","date_created":"2025-12-30T13:59:57.000-04:00","date_last_updated":"2025-12-30T14:00:22.000-04:00","date_of_expiration":"2025-12-31T13:59:57.000-04:00","deduction_schema":null,"description":"Apoio campanha #7","differential_pricing_id":null,"external_reference":"pledge_31","fee_details":[{"amount":0.01,"fee_payer":"collector","type":"mercadopago_fee"}],"financing_group":null,"id":139406541171,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":"2025-12-30T14:00:22.000-04:00","money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":"XXXXXXXXXXX","entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"sub_type":"INTER_PSP","transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":29055667642,"long_name":"MERCADO PAGO INSTITUI\\u00c7\\u00c3O DE PAGAMENTO LTDA.","transfer_account_id":null},"is_same_bank_account_owner":true,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":84935392,"branch":"1","external_account_id":null,"id":null,"identification":[],"long_name":"NU PAGAMENTOS S.A. - INSTITUI\\u00c7\\u00c3O DE PAGAMENTO"}},"bank_transfer_id":120975970156,"e2e_id":null,"financial_institution":1,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce352040000530398654041.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter13940654117163048DE5","qr_code_base64":"iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQMAAABpQ4TyAAAABlBMVEX\\/\\/\\/8AAABVwtN+AAAKqUlEQVR42uzdQXIiO9IHcBEsWPoIPgpHg6NxFI7AkgXh+uJ7Q5WUKZXB3f0aZuL333R47Cr96u1ylEoVEREREREREREREREREREREREREREREREREREREfl3s5u6nO6\\/2k9fpXykX17uv\\/ycplK203QuZRrn6\\/6Xm38eOqz81fyy4f9OS0tLS0tLS0tLS0tLS\\/t72nP6+fT\\/C8zqj2k6Lgve7vrbXTsvdA2v\\/Ofn\\/fJpjbZRH5dPbv4ty38nWlpaWlpaWlpaWlpa2jfW1kpzV2vemk2qebf3H27fab9q4dzkkF5StXP1faWlpaWlpaWlpaWlpaX979JG3WFZMCvH2mHhPLXbsGH7lZaWlpaWlpaWlpaWlvZ\\/SjvXvMdFO9WHhv23175597hsw5bh9istLS0tLS0tLS0tLS0t7b+jTcX1YJ+36RY+Ll3Ct\\/u+7lRfsl8q9k2txPM+b\\/PJf6C3mZaWlpaWlpaWlpaWlvZvavvJRf9ZaC5bj512nlh0q+XquZ41rYXzD17yG3OWaGlpaWlpaWlpaWlpaf+adphxt3AYOrRWOM\\/l6jy5aD966OOXVLS0tLS0tLS0tLS0tLTvoA3dr9Oo\\/7bUMnUuW5uHh+XqPl3JEpp4w0u2tfN3fXYSLS0tLS0tLS0tLS0t7Vtpy72V9lpffGpbaOeDl3nTs4xaaHf9w8fuTs5buNDzuf5bWlpaWlpaWlpaWlpaWtrntU2j73x89NQdF930Zfi5PWua93l3fWVep\\/U214+WdvzRtf5\\/BasVOi0tLS0tLS0tLS0tLe3LtWFr9tpu0TYLlXpctO7nbqs2v6QsZ05j5n3eUOs2B1cf1ry0tLS0tLS0tLS0tLS0L9QOG36\\/QsPvcVE32nNpbhINBXMJn1wPrk5VObXbr1PqFqalpaWlpaWlpaWlpaV9a23uet0v2q96a0qpCx7akbX5IpT1hXIzb8617QAutLS0tLS0tLS0tLS0tLR\\/SrsLdfL9xblLOM9XuqUDq2EAb2w5PrSf\\/pE+taR+5YdTfGlpaWlpaWlpaWlpaWlfo93Vht9TVwPPx0Rjt3A\\/uWg3Gri7CdN6w4Uxs7r55OdqXlpaWlpaWlpaWlpaWtp30OaG36r8GDX8NjXuOb1kn2refbdDequfPhfMaxfH0NLS0tLS0tLS0tLS0r6ZNv5tXahRhzK1tP22g0s\\/p9TEW7qXxMlF0+ga0gc3h9LS0tLS0tLS0tLS0tLSPqONFXrY760L5ktAt3XO0jld2xJeEjaJm33e0t4gOjh7+uTNobS0tLS0tLS0tLS0tLR\\/W1vCfZ3DQbula\\/iNg3ZDg+++vfvlUv+taab1Tu0+75MnZGlpaWlpaWlpaWlpaWlfqN2Nbl6JXcJBe1n51HN66WkplPPc29gtPGw5Lo92TmlpaWlpaWlpaWlpaWlfpp3aMvVamrm3sYW2LjR\\/2rYuFMrUweSiPEy3br+We617DbUvLS0tLS0tLS0tLS0t7Ttrd\\/VFp2Xo0Nw6O55\\/mxfKLw1XsRzvLw19uJ\\/1k8OnnGhpaWlpaWlpaWlpaWlp\\/4j2XKf01gr9Uu98OS5btds6cPczNfR+dsdF4yev7feG7uBnKnRaWlpaWlpaWlpaWlraF2rjQj9PLlenbrN46sceXdLkonMreebOF1paWlpaWlpaWlpaWtrXaGOjb1DnRt8wuvacPjVvw+brR4\\/LSwa3qPxkchEtLS0tLS0tLS0tLS3ta7XDsvVS1UF5uffhllKGrbPf1ryDlwwL6Gf6b2lpaWlpaWlpaWlpaWlpf6QNFflp2ZotYXpv0Oat2v7Ol1ihZ\\/20vOxXZgvT0tLS0tLS0tLS0tLSvkY7HxcN17XMZepXPSb6UWvg0OB7bjeJr1V5akf\\/5pd8tgdYd6H8paWlpaWlpaWlpaWlpX1Tbf83eehQs+k5tQtsw6WfteZtrh8NLcfN+KOgLekl39W8tLS0tLS0tLS0tLS0tK\\/Vnnt1qHnDQs2Ca\\/234RaV\\/ZRvUxlMLhqElpaWlpaWlpaWlpaWlvYPaPs5S01x\\/bHytm8G7NaG3+bhY2mm9U5Td+dLeMmelpaWlpaWlpaWlpaW9j21QTnVxt7m2OghLRhePDf6hoOru7q\\/W8cfberLLnXkb+gWfrjPS0tLS0tLS0tLS0tLS\\/se2qlfoB4TnbuGt2Gh8Knh+tFru91awkHVfuzRLX3y9Ymal5aWlpaWlpaWlpaWlvaV2nBctL8xNI6urQsOJheFT883h17aHdVbmFz0\\/dxbWlpaWlpaWlpaWlpa2vfQzgcur2kHNVyAUkKZelx2TAe3qISXNi+ZC+dDe0T0s324kewfzb+lpaWlpaWlpaWlpaWlpX2sbRY4rez35i7h9WOj1\\/TzJXUL54dDeT+4fpSWlpaWlpaWlpaWlpb2zbS7lXlBH6lb+GNqrmeZa97YLZxfFubf1lp3\\/KnPdQvT0tLS0tLS0tLS0tLSvlx7rjXv\\/d9NuEXlsHKDaF242XbN82\\/7ruHmKpZzq29y+qYyp6WlpaWlpaWlpaWlpX2hNp45TQtt+gtQcuvsrP1MhfO+\\/f18F+d8YHV+2bn\\/7\\/aTyUW0tLS0tLS0tLS0tLS0tI93TlOj73znS3PmNFz6mRt840vCw\\/Ws6VdtOQ6ffAubxg+7hWlpaWlpaWlpaWlpaWlfqI1dw\\/W6lqnu9x66Kb3j61qGC\\/U3h97Cp9fW412YYERLS0tLS0tLS0tLS0v7jtpdPfE5tXe+hElFa3e+xAXTnS+bvgs4P\\/xPodzUvJ9P9DbT0tLS0tLS0tLS0tLSvlA7+Js8dKgsO6fxzGm\\/c5oXGu6cxv7bqbuG9EG3MC0tLS0tLS0tLS0tLS3tk9pdOPkZiuu6z\\/uVHspdw\\/na0Ws6c\\/oo334yLS0tLS0tLS0tLS0t7Vtpmxm5daHLaMFbqH1ro29pD6zOm8bzJ276O18uDzaJH3QL09LS0tLS0tLS0tLS0r5Q208uijl2te586edt2HIcuoP3o53TUruG527h\\/sIYWlpaWlpaWlpaWlpa2rfUNllpnZ3SBKMpHRdthg3t2\\/FHWRtePg3HH9HS0tLS0tLS0tLS0tK+s\\/Z7fe2\\/XbuL8zx6aLp\\/6kf\\/kqzMKT8KLS0tLS0tLS0tLS0tLe0Ps+kXbLqF80L9nS+7\\/uF8\\/Wg\\/uag5c\\/ozNS0tLS0tLS0tLS0tLe1f0w7K1TwHd86hO3O6DdowuSjs8za1b1m0JXQLf3v9KC0tLS0tLS0tLS0tLe37aM\\/p5zr\\/trk9JWyCxss\\/v11o37YcT\\/3Yo3xw9ZkTsrS0tLS0tLS0tLS0tLSv1YZN0NPqzunUb3p+jiYWDQroUsrj\\/tvzs\\/NvaWlpaWlpaWlpaWlpaWl\\/Xdts0R5XyuW8wPDMaS3zN6lC36azpr9TodPS0tLS0tLS0tLS0tK+UFvuC+Xa9xa2bOs+b3P5Zz5zmrWhazjXuOEgKy0tLS0tLS0tLS0tLe27ate7hZs7X8p9\\/m3oFm4WqgdVBzunh5Wu4blwzi+hpaWlpaWlpaWlpaWlfVPtsGW2qXmP953UUpoRtnmBcINonn97qcp8cHVa\\/l1r2qWlpaWlpaWlpaWlpaWl\\/TWtiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIyFvn\\/wIAAP\\/\\/ZAkAh7\\/DkpwAAAAASUVORK5CYII=","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139406541171\\/ticket?caller_id=1503133188&hash=4ff13ea6-a91b-47f4-998d-f6b76c5cdac4","transaction_id":"PIXE18236120202512301800s09e78d6921"},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"approved","status_detail":"accredited","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":1,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":120975970156,"external_resource_url":null,"financial_institution":"1","installment_amount":0,"net_received_amount":0.99,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":1,"transaction_id":"PIXE18236120202512301800s09e78d6921"}}	\N
15	5	6	\N	1000	canceled	mercadopago	139949846752	\N	2025-12-29 23:31:59	2025-12-30 23:35:43	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.1,"refunded":0},"client_id":0,"date_created":"2025-12-29T19:32:00.000-04:00","external_charge_id":"01KDP78KG04MHWG9EP70FR10GY","id":"139949846752-001","last_updated":"2025-12-29T19:32:00.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-29T19:32:00.138-04:00","execution_id":"01KDP78KF5KN5H55CXQX5CMQXH"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":null,"date_created":"2025-12-29T19:32:00.000-04:00","date_last_updated":"2025-12-30T19:35:42.000-04:00","date_of_expiration":"2025-12-30T19:31:59.000-04:00","deduction_schema":null,"description":"Apoio campanha #5","differential_pricing_id":null,"external_reference":"pledge_15","fee_details":[],"financing_group":null,"id":139949846752,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":null,"money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":null,"entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":null,"long_name":null,"transfer_account_id":null},"is_same_bank_account_owner":null,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":null,"branch":null,"external_account_id":null,"id":null,"identification":[],"long_name":null}},"bank_transfer_id":null,"e2e_id":null,"financial_institution":null,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce3520400005303986540510.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1399498467526304ADF6","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139949846752\\/ticket?caller_id=1503133188&hash=0f41770b-f90e-4097-a57f-319387bfd51c","transaction_id":null},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"cancelled","status_detail":"expired","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":10,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":null,"external_resource_url":null,"financial_institution":null,"installment_amount":0,"net_received_amount":0,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":10,"transaction_id":null}}	\N
9	3	6	\N	100	canceled	mercadopago	139921369758	\N	2025-12-29 20:34:52	2025-12-30 20:37:24	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.01,"refunded":0},"client_id":0,"date_created":"2025-12-29T16:34:53.000-04:00","external_charge_id":"01KDNX49E7Z4FFB3601T731K9D","id":"139921369758-001","last_updated":"2025-12-29T16:34:53.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-29T16:34:53.009-04:00","execution_id":"01KDNX49DB02B80A71FAVJPAB4"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":null,"date_created":"2025-12-29T16:34:53.000-04:00","date_last_updated":"2025-12-30T16:37:22.000-04:00","date_of_expiration":"2025-12-30T16:34:52.000-04:00","deduction_schema":null,"description":"Apoio campanha #3","differential_pricing_id":null,"external_reference":"pledge_9","fee_details":[],"financing_group":null,"id":139921369758,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":null,"money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":null,"entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":null,"long_name":null,"transfer_account_id":null},"is_same_bank_account_owner":null,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":null,"branch":null,"external_account_id":null,"id":null,"identification":[],"long_name":null}},"bank_transfer_id":null,"e2e_id":null,"financial_institution":null,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce352040000530398654041.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter1399213697586304C03D","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139921369758\\/ticket?caller_id=1503133188&hash=190c080c-9b93-4585-90e6-ba43c0e271fa","transaction_id":null},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"cancelled","status_detail":"expired","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":1,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":null,"external_resource_url":null,"financial_institution":null,"installment_amount":0,"net_received_amount":0,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":1,"transaction_id":null}}	\N
10	3	6	\N	1000	canceled	mercadopago	139276618865	\N	2025-12-29 20:37:49	2025-12-30 20:40:43	pix	{"accounts_info":null,"acquirer_reconciliation":[],"additional_info":{"tracking_id":"platform:v1-whitelabel,so:ALL,type:N\\/A,security:none"},"authorization_code":null,"binary_mode":false,"brand_id":null,"build_version":"3.136.0-rc-1","call_for_authorize_id":null,"callback_url":null,"captured":true,"card":[],"charges_details":[{"accounts":{"from":"collector","to":"mp"},"amounts":{"original":0.1,"refunded":0},"client_id":0,"date_created":"2025-12-29T16:37:50.000-04:00","external_charge_id":"01KDNX9PNQZ8QAN56PWXRYRW0Y","id":"139276618865-001","last_updated":"2025-12-29T16:37:50.000-04:00","metadata":{"reason":"","source":"proc-svc-charges","source_detail":"processing_fee_charge"},"name":"mercadopago_fee","refund_charges":[],"reserve_id":null,"type":"fee","update_charges":[]}],"charges_execution_info":{"internal_execution":{"date":"2025-12-29T16:37:50.403-04:00","execution_id":"01KDNX9PMTZRFNC5K9MTMQ1H3S"}},"collector_id":123306416,"corporation_id":null,"counter_currency":null,"coupon_amount":0,"currency_id":"BRL","date_approved":null,"date_created":"2025-12-29T16:37:50.000-04:00","date_last_updated":"2025-12-30T16:40:42.000-04:00","date_of_expiration":"2025-12-30T16:37:50.000-04:00","deduction_schema":null,"description":"Apoio campanha #3","differential_pricing_id":null,"external_reference":"pledge_10","fee_details":[],"financing_group":null,"id":139276618865,"installments":1,"integrator_id":null,"issuer_id":"12501","live_mode":true,"marketplace_owner":null,"merchant_account_id":null,"merchant_number":null,"metadata":[],"money_release_date":null,"money_release_schema":null,"money_release_status":"released","notification_url":"http:\\/\\/origocrowd.com.br\\/api\\/webhooks\\/mercadopago","operation_type":"regular_payment","order":[],"payer":{"email":null,"entity_type":null,"first_name":null,"id":"1503133188","identification":{"number":null,"type":null},"last_name":null,"operator_id":null,"phone":{"number":null,"extension":null,"area_code":null},"type":null},"payment_method":{"id":"pix","issuer_id":"12501","type":"bank_transfer"},"payment_method_id":"pix","payment_type_id":"bank_transfer","platform_id":null,"point_of_interaction":{"application_data":{"name":null,"operating_system":null,"version":null},"business_info":{"branch":"Merchant Services","sub_unit":"default","unit":"online_payments"},"location":{"source":null,"state_id":null},"transaction_data":{"bank_info":{"collector":{"account_alias":null,"account_holder_name":"Joao Baptista Reus Fagundes Machado","account_id":null,"long_name":null,"transfer_account_id":null},"is_same_bank_account_owner":null,"origin_bank_id":null,"origin_wallet_id":null,"payer":{"account_id":null,"branch":null,"external_account_id":null,"id":null,"identification":[],"long_name":null}},"bank_transfer_id":null,"e2e_id":null,"financial_institution":null,"infringement_notification":{"status":null,"type":null},"is_end_consumer":null,"merchant_category_code":null,"qr_code":"00020126580014br.gov.bcb.pix0136ae3dd7f9-ff09-406c-b415-909728152ce3520400005303986540510.005802BR5908EVOLUOIT6012Porto Alegre62250521mpqrinter13927661886563041451","ticket_url":"https:\\/\\/www.mercadopago.com.br\\/payments\\/139276618865\\/ticket?caller_id=1503133188&hash=a0ace66d-5afd-481a-b1f4-bc66932305f1","transaction_id":null},"type":"OPENPLATFORM"},"pos_id":null,"processing_mode":"aggregator","refunds":[],"release_info":null,"shipping_amount":0,"sponsor_id":null,"statement_descriptor":null,"status":"cancelled","status_detail":"expired","store_id":null,"tags":null,"taxes_amount":0,"transaction_amount":10,"transaction_amount_refunded":0,"transaction_details":{"acquirer_reference":null,"bank_transfer_id":null,"external_resource_url":null,"financial_institution":null,"installment_amount":0,"net_received_amount":0,"overpaid_amount":0,"payable_deferral_period":null,"payment_method_reference_id":null,"total_paid_amount":10,"transaction_id":null}}	\N
\.


--
-- Data for Name: rewards; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rewards (id, campaign_id, title, description, min_amount, quantity, remaining, created_at, updated_at) FROM stdin;
1	1	Livro Digital	Versão digital do livro em PDF	2000	100	85	2025-12-29 00:14:03	2025-12-29 00:14:03
2	1	Livro Físico	Livro impresso com capa dura	5000	50	30	2025-12-29 00:14:03	2025-12-29 00:14:03
3	2	Acesso Antecipado	Assista ao documentário antes do lançamento oficial	3000	\N	\N	2025-12-29 00:14:03	2025-12-29 00:14:03
4	3	Jogo Completo	Uma cópia do jogo completo	15000	100	92	2025-12-29 00:14:03	2025-12-29 00:14:03
8	7	teste	teste	100	\N	\N	2025-12-30 17:36:02	2025-12-30 17:36:02
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, google_id, postal_code, address_street, address_number, address_complement, address_neighborhood, address_city, address_state, phone, profile_photo_path) FROM stdin;
1	João Silva	joao@example.com	2025-12-29 00:14:02	$2y$12$k2OEUzS3XyqJgZ5IYBzOYO1rii/olckXOMfZI8qbAFdxJcHRam82q	ngoINmvgWk	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
2	Maria Santos	maria@example.com	2025-12-29 00:14:03	$2y$12$k2OEUzS3XyqJgZ5IYBzOYO1rii/olckXOMfZI8qbAFdxJcHRam82q	u4lzRY49yo	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
3	Pedro Oliveira	pedro@example.com	2025-12-29 00:14:03	$2y$12$k2OEUzS3XyqJgZ5IYBzOYO1rii/olckXOMfZI8qbAFdxJcHRam82q	EMOvYfIZ1G	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
4	Ana Costa	ana@example.com	2025-12-29 00:14:03	$2y$12$k2OEUzS3XyqJgZ5IYBzOYO1rii/olckXOMfZI8qbAFdxJcHRam82q	Nk3CkphDrP	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
5	Carlos Souza	carlos@example.com	2025-12-29 00:14:03	$2y$12$k2OEUzS3XyqJgZ5IYBzOYO1rii/olckXOMfZI8qbAFdxJcHRam82q	xfSjmBwTes	2025-12-29 00:14:03	2025-12-29 00:14:03	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
6	João Machado	joaob042@gmail.com	\N	$2y$12$sS2mNNd6o0kd/Abv4.tNyu/PcqIReXkgf6ZwALvOG/art7RQ26PD2	\N	2025-12-29 00:16:49	2025-12-30 03:47:06	\N	90010-273	Rua Riachuelo	1280	Apto 91	Centro Histórico	Porto Alegre	RS	51984230938	avatars/6/if9taXAxsw1sv9qDFVm8E0XFHyKZuVCWx1hOvAXE.jpg
\.


--
-- Name: campaigns_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.campaigns_id_seq', 7, true);


--
-- Name: creator_page_followers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.creator_page_followers_id_seq', 1, false);


--
-- Name: creator_pages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.creator_pages_id_seq', 1, true);


--
-- Name: creator_profiles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.creator_profiles_id_seq', 1, false);


--
-- Name: creator_supporters_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.creator_supporters_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 22, true);


--
-- Name: pledges_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pledges_id_seq', 31, true);


--
-- Name: rewards_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rewards_id_seq', 8, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 6, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: campaigns campaigns_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.campaigns
    ADD CONSTRAINT campaigns_pkey PRIMARY KEY (id);


--
-- Name: campaigns campaigns_slug_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.campaigns
    ADD CONSTRAINT campaigns_slug_unique UNIQUE (slug);


--
-- Name: creator_page_followers creator_page_followers_creator_page_id_follower_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_page_followers
    ADD CONSTRAINT creator_page_followers_creator_page_id_follower_id_unique UNIQUE (creator_page_id, follower_id);


--
-- Name: creator_page_followers creator_page_followers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_page_followers
    ADD CONSTRAINT creator_page_followers_pkey PRIMARY KEY (id);


--
-- Name: creator_pages creator_pages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_pages
    ADD CONSTRAINT creator_pages_pkey PRIMARY KEY (id);


--
-- Name: creator_pages creator_pages_slug_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_pages
    ADD CONSTRAINT creator_pages_slug_unique UNIQUE (slug);


--
-- Name: creator_profiles creator_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_profiles
    ADD CONSTRAINT creator_profiles_pkey PRIMARY KEY (id);


--
-- Name: creator_profiles creator_profiles_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_profiles
    ADD CONSTRAINT creator_profiles_user_id_unique UNIQUE (user_id);


--
-- Name: creator_supporters creator_supporters_creator_id_supporter_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_supporters
    ADD CONSTRAINT creator_supporters_creator_id_supporter_id_unique UNIQUE (creator_id, supporter_id);


--
-- Name: creator_supporters creator_supporters_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_supporters
    ADD CONSTRAINT creator_supporters_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: pledges pledges_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pledges
    ADD CONSTRAINT pledges_pkey PRIMARY KEY (id);


--
-- Name: pledges pledges_provider_payment_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pledges
    ADD CONSTRAINT pledges_provider_payment_id_unique UNIQUE (provider_payment_id);


--
-- Name: rewards rewards_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rewards
    ADD CONSTRAINT rewards_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_google_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_google_id_unique UNIQUE (google_id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: campaigns_ending_soon_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_ending_soon_idx ON public.campaigns USING btree (status, ends_at, ending_soon_notified_at);


--
-- Name: campaigns_finished_notify_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_finished_notify_idx ON public.campaigns USING btree (status, ends_at, finished_notified_at);


--
-- Name: campaigns_goal_reached_notify_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_goal_reached_notify_idx ON public.campaigns USING btree (status, goal_reached_notified_at);


--
-- Name: campaigns_niche_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_niche_index ON public.campaigns USING btree (niche);


--
-- Name: campaigns_slug_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_slug_index ON public.campaigns USING btree (slug);


--
-- Name: campaigns_status_created_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_status_created_at_index ON public.campaigns USING btree (status, created_at);


--
-- Name: campaigns_status_ends_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_status_ends_at_index ON public.campaigns USING btree (status, ends_at);


--
-- Name: campaigns_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX campaigns_user_id_index ON public.campaigns USING btree (user_id);


--
-- Name: creator_page_followers_follower_id_creator_page_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX creator_page_followers_follower_id_creator_page_id_index ON public.creator_page_followers USING btree (follower_id, creator_page_id);


--
-- Name: creator_pages_owner_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX creator_pages_owner_user_id_index ON public.creator_pages USING btree (owner_user_id);


--
-- Name: creator_supporters_supporter_id_creator_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX creator_supporters_supporter_id_creator_id_index ON public.creator_supporters USING btree (supporter_id, creator_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: pledges_campaign_id_status_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX pledges_campaign_id_status_index ON public.pledges USING btree (campaign_id, status);


--
-- Name: pledges_campaign_id_status_paid_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX pledges_campaign_id_status_paid_at_index ON public.pledges USING btree (campaign_id, status, paid_at);


--
-- Name: pledges_checkout_incomplete_reminder_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX pledges_checkout_incomplete_reminder_idx ON public.pledges USING btree (status, payment_method, checkout_incomplete_reminded_at);


--
-- Name: pledges_payment_method_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX pledges_payment_method_index ON public.pledges USING btree (payment_method);


--
-- Name: pledges_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX pledges_user_id_index ON public.pledges USING btree (user_id);


--
-- Name: rewards_campaign_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX rewards_campaign_id_index ON public.rewards USING btree (campaign_id);


--
-- Name: rewards_campaign_id_min_amount_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX rewards_campaign_id_min_amount_index ON public.rewards USING btree (campaign_id, min_amount);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: campaigns campaigns_creator_page_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.campaigns
    ADD CONSTRAINT campaigns_creator_page_id_foreign FOREIGN KEY (creator_page_id) REFERENCES public.creator_pages(id) ON DELETE SET NULL;


--
-- Name: campaigns campaigns_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.campaigns
    ADD CONSTRAINT campaigns_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: creator_page_followers creator_page_followers_creator_page_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_page_followers
    ADD CONSTRAINT creator_page_followers_creator_page_id_foreign FOREIGN KEY (creator_page_id) REFERENCES public.creator_pages(id) ON DELETE CASCADE;


--
-- Name: creator_page_followers creator_page_followers_follower_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_page_followers
    ADD CONSTRAINT creator_page_followers_follower_id_foreign FOREIGN KEY (follower_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: creator_pages creator_pages_owner_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_pages
    ADD CONSTRAINT creator_pages_owner_user_id_foreign FOREIGN KEY (owner_user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: creator_profiles creator_profiles_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_profiles
    ADD CONSTRAINT creator_profiles_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: creator_supporters creator_supporters_creator_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_supporters
    ADD CONSTRAINT creator_supporters_creator_id_foreign FOREIGN KEY (creator_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: creator_supporters creator_supporters_supporter_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.creator_supporters
    ADD CONSTRAINT creator_supporters_supporter_id_foreign FOREIGN KEY (supporter_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: pledges pledges_campaign_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pledges
    ADD CONSTRAINT pledges_campaign_id_foreign FOREIGN KEY (campaign_id) REFERENCES public.campaigns(id) ON DELETE CASCADE;


--
-- Name: pledges pledges_reward_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pledges
    ADD CONSTRAINT pledges_reward_id_foreign FOREIGN KEY (reward_id) REFERENCES public.rewards(id) ON DELETE SET NULL;


--
-- Name: pledges pledges_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pledges
    ADD CONSTRAINT pledges_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: rewards rewards_campaign_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rewards
    ADD CONSTRAINT rewards_campaign_id_foreign FOREIGN KEY (campaign_id) REFERENCES public.campaigns(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict NfdFfJmNO7rVCEtKG6IhQakYYUN9xcRGuKjAKReWjUiAnBrBE2dIMfcab0L4zkE

