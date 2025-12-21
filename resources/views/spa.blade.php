<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Origo') }} - Crowdfunding</title>

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
</head>
<body>
    <div id="app"></div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
