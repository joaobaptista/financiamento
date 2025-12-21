<?php

return [
    'site_name' => env('SEO_SITE_NAME', env('APP_NAME', 'Catarse')),

    // Descrição usada no homepage e como fallback para rotas sem meta.
    'description' => env(
        'SEO_DESCRIPTION',
        'Plataforma de crowdfunding para projetos criativos. Apoie criadores, escolha recompensas e acompanhe campanhas em tempo real.'
    ),

    // Meta keywords é opcional (muitos buscadores ignoram), mas pode ajudar em integrações.
    'keywords' => env(
        'SEO_KEYWORDS',
        'crowdfunding, financiamento coletivo, apoiar projetos, campanhas, recompensas, criadores, catarse, origo'
    ),

    'author' => env('SEO_AUTHOR', env('APP_NAME', 'Catarse')),

    // Opcional: URL absoluta para uma imagem padrão (logo/banner) para OG/Twitter.
    'og_image' => env('SEO_OG_IMAGE'),

    // Opcional: @usuario do Twitter, ex.: @catarse
    'twitter_site' => env('SEO_TWITTER_SITE'),

    // Verificações (opcionais) para Search Console / Bing Webmaster Tools.
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'bing_site_verification' => env('BING_SITE_VERIFICATION'),
];
