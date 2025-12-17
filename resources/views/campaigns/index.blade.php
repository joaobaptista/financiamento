@extends('layouts.app')

@section('title', 'Explorar Campanhas')

@section('content')
    <div class="container">
        <h1 class="mb-4">Explorar Campanhas</h1>

        <div class="row">
            @forelse($campaigns as $campaign)
                <div class="col-md-4 mb-4">
                    <div class="card campaign-card">
                        @if($campaign->cover_image_path)
                            <img src="{{ $campaign->cover_image_path }}" class="card-img-top" alt="{{ $campaign->title }}"
                                style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white p-5 text-center" style="height: 200px;">
                                <i class="bi bi-image display-4"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $campaign->title }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($campaign->description, 100) }}</p>

                            <div class="progress mb-2">
                                <div class="progress-bar bg-success"
                                    style="width: {{ min($campaign->calculateProgress(), 100) }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between text-sm mb-2">
                                <span
                                    class="text-success fw-bold">{{ number_format($campaign->calculateProgress(), 0) }}%</span>
                                <span class="text-muted">{{ $campaign->daysRemaining() }} dias</span>
                            </div>

                            <div class="mb-3">
                                <strong>{{ $campaign->formatted_pledged }}</strong> de {{ $campaign->formatted_goal }}
                            </div>

                            <a href="{{ route('campaigns.show', $campaign->slug) }}" class="btn btn-primary w-100">
                                Ver Campanha
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">Nenhuma campanha encontrada.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $campaigns->links() }}
        </div>
    </div>
@endsection