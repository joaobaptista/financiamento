function ensureMeta(selector, createAttrs) {
    let el = document.head.querySelector(selector);
    if (el) return el;

    el = document.createElement('meta');
    Object.entries(createAttrs).forEach(([k, v]) => {
        el.setAttribute(k, v);
    });
    document.head.appendChild(el);
    return el;
}

function setMetaByName(name, content) {
    const el = ensureMeta(`meta[name="${name}"]`, { name });
    el.setAttribute('content', String(content ?? ''));
}

function setMetaByProperty(property, content) {
    const el = ensureMeta(`meta[property="${property}"]`, { property });
    el.setAttribute('content', String(content ?? ''));
}

function setCanonical(url) {
    let link = document.head.querySelector('link[rel="canonical"]');
    if (!link) {
        link = document.createElement('link');
        link.setAttribute('rel', 'canonical');
        document.head.appendChild(link);
    }
    link.setAttribute('href', String(url));
}

function buildTitle(pageTitle, siteName) {
    const cleanSite = String(siteName ?? '').trim();
    const cleanPage = String(pageTitle ?? '').trim();

    if (!cleanPage) return cleanSite || document.title;
    if (!cleanSite) return cleanPage;

    // Evita duplicar "Site | Site".
    if (cleanPage.toLowerCase() === cleanSite.toLowerCase()) return cleanSite;

    return `${cleanPage} | ${cleanSite}`;
}

function truncate(text, maxLen) {
    const t = String(text ?? '').replace(/\s+/g, ' ').trim();
    if (t.length <= maxLen) return t;
    return `${t.slice(0, Math.max(0, maxLen - 1)).trim()}…`;
}

function setJsonLd(id, obj) {
    const selector = `script[type="application/ld+json"][data-seo="${id}"]`;
    let el = document.head.querySelector(selector);
    if (!el) {
        el = document.createElement('script');
        el.type = 'application/ld+json';
        el.setAttribute('data-seo', id);
        document.head.appendChild(el);
    }
    el.textContent = JSON.stringify(obj);
}

function getDefaults() {
    return window.__SEO__ || {};
}

export function absoluteUrl(pathOrUrl) {
    const value = String(pathOrUrl ?? '').trim();
    if (!value) return '';

    try {
        // Se já é URL absoluta, mantém.
        // eslint-disable-next-line no-new
        new URL(value);
        return value;
    } catch {
        // URL relativa
    }

    const base = window.__APP_URL__ || window.location.origin;
    return new URL(value.replace(/^\/+/, '/'), base).toString();
}

export function applyRouteSeo(route) {
    const defaults = getDefaults();

    const siteName = defaults.site_name || 'Catarse';
    const pageTitle = route?.meta?.title || '';

    const descriptionFromMeta = route?.meta?.description || route?.meta?.lead || '';
    const description = truncate(descriptionFromMeta || defaults.description || '', 160);

    const canonical = window.location.origin + window.location.pathname;
    const robots = route?.meta?.robots || 'index, follow';

    const ogType = route?.meta?.ogType || 'website';
    const ogImage = route?.meta?.ogImage || defaults.og_image || '';

    const fullTitle = buildTitle(pageTitle, siteName);

    document.title = fullTitle;

    setMetaByName('description', description);
    setMetaByName('keywords', defaults.keywords || '');
    setMetaByName('author', defaults.author || siteName);
    setMetaByName('robots', robots);

    setCanonical(canonical);

    setMetaByProperty('og:locale', 'pt_BR');
    setMetaByProperty('og:site_name', siteName);
    setMetaByProperty('og:type', ogType);
    setMetaByProperty('og:title', fullTitle);
    setMetaByProperty('og:description', description);
    setMetaByProperty('og:url', canonical);
    if (ogImage) setMetaByProperty('og:image', absoluteUrl(ogImage));

    const twitterCard = ogImage ? 'summary_large_image' : 'summary';
    setMetaByName('twitter:card', twitterCard);
    if (defaults.twitter_site) setMetaByName('twitter:site', defaults.twitter_site);
    setMetaByName('twitter:title', fullTitle);
    setMetaByName('twitter:description', description);
    if (ogImage) setMetaByName('twitter:image', absoluteUrl(ogImage));

    setJsonLd('route', {
        '@context': 'https://schema.org',
        '@type': 'WebPage',
        name: fullTitle,
        url: canonical,
        description,
    });
}

export function applyCampaignSeo(campaign) {
    const defaults = getDefaults();
    const siteName = defaults.site_name || 'Catarse';

    const title = campaign?.title || 'Campanha';
    const description = truncate(campaign?.description || defaults.description || '', 160);

    const canonical = absoluteUrl(`/campaigns/${campaign?.slug || ''}`) || (window.location.origin + window.location.pathname);

    const ogImage = campaign?.cover_image_path || defaults.og_image || '';

    const fullTitle = buildTitle(title, siteName);

    document.title = fullTitle;

    setMetaByName('description', description);
    setMetaByName('robots', 'index, follow');
    setCanonical(canonical);

    setMetaByProperty('og:type', 'article');
    setMetaByProperty('og:title', fullTitle);
    setMetaByProperty('og:description', description);
    setMetaByProperty('og:url', canonical);
    if (ogImage) setMetaByProperty('og:image', absoluteUrl(ogImage));

    const twitterCard = ogImage ? 'summary_large_image' : 'summary';
    setMetaByName('twitter:card', twitterCard);
    if (defaults.twitter_site) setMetaByName('twitter:site', defaults.twitter_site);
    setMetaByName('twitter:title', fullTitle);
    setMetaByName('twitter:description', description);
    if (ogImage) setMetaByName('twitter:image', absoluteUrl(ogImage));

    setJsonLd('route', {
        '@context': 'https://schema.org',
        '@type': 'CreativeWork',
        name: fullTitle,
        url: canonical,
        description,
        image: ogImage ? [absoluteUrl(ogImage)] : undefined,
    });
}
