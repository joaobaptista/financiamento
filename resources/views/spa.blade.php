<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seoSiteName = (string) config('seo.site_name', config('app.name', 'Catarse'));
        $seoDescription = (string) config('seo.description', 'Plataforma de crowdfunding.');
        $seoKeywords = (string) config('seo.keywords', 'crowdfunding');
        $seoAuthor = (string) config('seo.author', $seoSiteName);
        $seoOgImage = (string) (config('seo.og_image') ?? '');
        $canonicalUrl = url()->current();

        $pageSeo = $pageSeo ?? [];
        $effectiveTitle = trim((string) ($pageSeo['title'] ?? ''));
        $effectiveDescription = trim((string) ($pageSeo['description'] ?? $seoDescription));
        $effectiveRobots = trim((string) ($pageSeo['robots'] ?? 'index, follow'));
        $effectiveCanonical = (string) ($pageSeo['canonical'] ?? $canonicalUrl);
        $effectiveOgType = trim((string) ($pageSeo['og_type'] ?? 'website'));
        $effectiveOgImage = trim((string) ($pageSeo['og_image'] ?? $seoOgImage));

        $documentTitle = $effectiveTitle
            ? ($effectiveTitle . ' | ' . $seoSiteName)
            : ($seoSiteName . ' - Crowdfunding');
    @endphp

    <title>{{ $documentTitle }}</title>

    <meta name="description" content="{{ $effectiveDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="author" content="{{ $seoAuthor }}">
    <meta name="robots" content="{{ $effectiveRobots }}">

    <link rel="canonical" href="{{ $effectiveCanonical }}">

    <!-- Open Graph (Facebook/WhatsApp/LinkedIn) -->
    <meta property="og:locale" content="{{ app()->getLocale() }}">
    <meta property="og:site_name" content="{{ $seoSiteName }}">
    <meta property="og:type" content="{{ $effectiveOgType }}">
    <meta property="og:title" content="{{ $documentTitle }}">
    <meta property="og:description" content="{{ $effectiveDescription }}">
    <meta property="og:url" content="{{ $effectiveCanonical }}">
    @if($effectiveOgImage)
        <meta property="og:image" content="{{ $effectiveOgImage }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="{{ $effectiveOgImage ? 'summary_large_image' : 'summary' }}">
    @if(config('seo.twitter_site'))
        <meta name="twitter:site" content="{{ config('seo.twitter_site') }}">
    @endif
    <meta name="twitter:title" content="{{ $documentTitle }}">
    <meta name="twitter:description" content="{{ $effectiveDescription }}">
    @if($effectiveOgImage)
        <meta name="twitter:image" content="{{ $effectiveOgImage }}">
    @endif

    <!-- Site Verification (opcional) -->
    @if(config('seo.google_site_verification'))
        <meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">
    @endif
    @if(config('seo.bing_site_verification'))
        <meta name="msvalidate.01" content="{{ config('seo.bing_site_verification') }}">
    @endif

    <!-- JSON-LD (base) -->
    <script type="application/ld+json" data-seo="base">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $seoSiteName,
        'url' => config('app.url'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .app-navbar {
            background-color: #ffffff !important;
        }

        .app-navbar .navbar-brand,
        .app-navbar .nav-link,
        .app-navbar .dropdown-toggle,
        .app-navbar .navbar-toggler {
            color: #000000 !important;
        }

        .campaign-card {
            transition: box-shadow 0.2s;
            height: 100%;
        }

        .campaign-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .app-categories .nav-link {
            color: #000000;
            padding: 0.5rem 0.75rem;
        }

        .app-categories .nav-link:hover {
            text-decoration: underline;
        }

        .app-categories .router-link-active {
            font-weight: 600;
        }

        .progress {
            height: 8px;
        }

    </style>

    @vite(['resources/js/spa.js'])

    <script>
        window.__APP_URL__ = {!! json_encode((string) config('app.url'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
        window.__SEO__ = {!! json_encode([
            'site_name' => $seoSiteName,
            'description' => $seoDescription,
            'keywords' => $seoKeywords,
            'author' => $seoAuthor,
            'og_image' => $seoOgImage ?: null,
            'twitter_site' => config('seo.twitter_site') ?: null,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
    </script>
</head>
<body>
    <div id="app"></div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
