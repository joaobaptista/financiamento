@extends('layouts.app')

@section('title', 'Início')

@section('content')
    <div class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold">Transforme suas ideias em realidade</h1>
                    <p class="lead">Financie projetos criativos e inovadores com o apoio da comunidade.</p>
                    <div class="mt-4">
                        <a href="{{ route('campaigns.index') }}" class="btn btn-light btn-lg me-2">
                            <i class="bi bi-search"></i> Explorar Campanhas
                        </a>
                        @auth
                            <a href="{{ route('campaigns.create') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-plus-circle"></i> Criar Campanha
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-rocket-takeoff"></i> Começar Agora
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="bi bi-lightbulb display-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <h2 class="text-center mb-4">Campanhas em Destaque</h2>
        <div class="row">
            @php
                $featuredCampaigns = \App\Domain\Campaign\Campaign::active()
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(6)
                    ->get();
            @endphp

            @forelse($featuredCampaigns as $campaign)
                <div class="col-md-4 mb-4">
                    <div class="card campaign-card">
                        @if($campaign->cover_image_path)
                            <img src="{{ $campaign->cover_image_path }}" class="card-img-top" alt="{{ $campaign->title }}">
                        @else
                            <div class="bg-secondary text-white p-5 text-center">
                                <i class="bi bi-image display-4"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $campaign->title }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($campaign->description, 100) }}</p>

                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" style="width: {{ $campaign->calculateProgress() }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between text-sm">
                                <span
                                    class="text-success fw-bold">{{ number_format($campaign->calculateProgress(), 0) }}%</span>
                                <span class="text-muted">{{ $campaign->daysRemaining() }} dias</span>
                            </div>

                            <div class="mt-2">
                                <strong>{{ $campaign->formatted_pledged }}</strong> de {{ $campaign->formatted_goal }}
                            </div>

                            <a href="{{ route('campaigns.show', $campaign->slug) }}" class="btn btn-primary w-100 mt-3">
                                Ver Campanha
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Nenhuma campanha ativa no momento.</p>
                    @auth
                        <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                            Seja o primeiro a criar uma campanha!
                        </a>
                    @endauth
                </div>
            @endforelse
        </div>

        @if($featuredCampaigns->count() > 0)
            <div class="text-center mt-4">
                <a href="{{ route('campaigns.index') }}" class="btn btn-outline-primary">
                    Ver Todas as Campanhas <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>

    <div class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Como Funciona</h2>
            <div class="row">
                <div class="col-md-4 text-center">
                    <i class="bi bi-lightbulb-fill text-primary display-4"></i>
                    <h4 class="mt-3">1. Crie sua Campanha</h4>
                    <p class="text-muted">Compartilhe sua ideia e defina sua meta de financiamento.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-people-fill text-primary display-4"></i>
                    <h4 class="mt-3">2. Receba Apoio</h4>
                    <p class="text-muted">A comunidade apoia projetos que fazem sentido.</p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="bi bi-rocket-takeoff-fill text-primary display-4"></i>
                    <h4 class="mt-3">3. Realize seu Projeto</h4>
                    <p class="text-muted">Com o financiamento, transforme sua ideia em realidade.</p>
                </div>
            </div>
        </div>
    </div>
@endsection