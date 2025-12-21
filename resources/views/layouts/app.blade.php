<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Catarse') }} - @yield('title', 'Crowdfunding')</title>

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
            transition: transform 0.2s;
            height: 100%;
        }

        .campaign-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .progress {
            height: 8px;
        }

        .site-footer {
            margin-top: 4rem;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light app-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/logo.svg') }}" alt="{{ config('app.name', 'Catarse') }}" height="55" class="me-2 align-text-bottom">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('campaigns.index') }}">Explorar Campanhas</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard.index') }}">
                                <i class="bi bi-speedometer2"></i> Meu Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('campaigns.create') }}">
                                <i class="bi bi-plus-circle"></i> Criar Campanha
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-light text-primary ms-2" href="{{ route('register') }}">Cadastrar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <div class="container">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <div class="container">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer bg-dark text-light pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <h6 class="fw-semibold">Bem-vindo</h6>
                    <ul class="list-unstyled mt-3 mb-0">
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/about">Quem Somos</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/how-it-works">Como funciona</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/blog">Blog</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/team">Nosso time</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/press">Imprensa</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/retrospectiva-2020">Retrospectiva 2020</a></li>
                    </ul>

                    <div class="mt-4">
                        <h6 class="fw-semibold">Redes Sociais</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2">
                                <a class="link-light text-decoration-none" href="https://facebook.com" target="_blank" rel="noopener">
                                    <i class="bi bi-facebook me-2"></i>Facebook
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="link-light text-decoration-none" href="https://twitter.com" target="_blank" rel="noopener">
                                    <i class="bi bi-twitter-x me-2"></i>Twitter
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="link-light text-decoration-none" href="https://instagram.com" target="_blank" rel="noopener">
                                    <i class="bi bi-instagram me-2"></i>Instagram
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="link-light text-decoration-none" href="https://github.com" target="_blank" rel="noopener">
                                    <i class="bi bi-github me-2"></i>Github
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <h6 class="fw-semibold">Ajuda</h6>
                    <ul class="list-unstyled mt-3 mb-0">
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/support">Central de Suporte</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/contact">Contato</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/updates">Atualizações</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/fees">Nossa Taxa</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/security">Responsabilidades e Segurança</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/terms">Termos de uso</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/privacy">Política de privacidade</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3">
                    <h6 class="fw-semibold">Faça uma campanha</h6>
                    <ul class="list-unstyled mt-3 mb-0">
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/me/campaigns/create">Comece seu projeto</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/campaigns?category=musica">Música no Origo</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/campaigns?category=publicacao">Publicações Independentes</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/campaigns?category=jornalismo">Jornalismo</a></li>
                        <li class="mb-2"><a class="link-light text-decoration-none" href="/assinaturas">Assinaturas</a></li>
                    </ul>

                    <div class="mt-4">
                        <h6 class="fw-semibold">Apoie projetos no Origo</h6>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li class="mb-2"><a class="link-light text-decoration-none" href="/campaigns">Explore projetos</a></li>
                            <li class="mb-2"><a class="link-light text-decoration-none" href="/popular">Populares</a></li>
                            <li class="mb-2"><a class="link-light text-decoration-none" href="/no-ar">No ar</a></li>
                            <li class="mb-2"><a class="link-light text-decoration-none" href="/finalizados">Finalizados</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <h6 class="fw-semibold">Assine nossa news</h6>
                    <form class="mt-3" onsubmit="return false;">
                        <div class="input-group">
                            <input
                                type="email"
                                class="form-control"
                                placeholder="Digite seu email"
                                aria-label="Digite seu email"
                                autocomplete="email"
                            />
                            <button class="btn btn-primary" type="submit" aria-label="Assinar">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                        <div class="form-text text-secondary mt-2">Você pode cancelar a qualquer momento.</div>
                    </form>
                </div>
            </div>

            <div class="border-top border-secondary mt-4 pt-4 d-flex flex-column flex-md-row justify-content-between gap-2">
                <div class="text-secondary small">Origo — plataforma de crowdfunding para projetos criativos.</div>
                <div class="text-secondary small">&copy; {{ date('Y') }} Origo. Todos os direitos reservados.</div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>