@extends('layouts.app')

@section('title', 'Meu Dashboard')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Minhas Campanhas</h1>
            <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Campanha
            </a>
        </div>

        @forelse($campaigns as $campaign)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5>{{ $campaign->title }}</h5>
                            <p class="text-muted mb-2">{{ Str::limit($campaign->description, 150) }}</p>

                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-success"
                                    style="width: {{ min($campaign->calculateProgress(), 100) }}%"></div>
                            </div>

                            <div class="d-flex gap-4 text-sm">
                                <span><strong>{{ $campaign->formatted_pledged }}</strong> de
                                    {{ $campaign->formatted_goal }}</span>
                                <span>{{ $campaign->pledges_count }} apoios</span>
                                <span>{{ $campaign->daysRemaining() }} dias restantes</span>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <span
                                class="badge bg-{{ $campaign->status === 'active' ? 'success' : ($campaign->status === 'draft' ? 'secondary' : 'primary') }} mb-2">
                                {{ ucfirst($campaign->status) }}
                            </span>

                            <div class="btn-group d-block">
                                @if($campaign->status === 'draft')
                                    <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form action="{{ route('campaigns.publish', $campaign->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-rocket-takeoff"></i> Publicar
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('dashboard.show', $campaign->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-bar-chart"></i> Estatísticas
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">Você ainda não criou nenhuma campanha.</p>
                <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                    Criar Minha Primeira Campanha
                </a>
            </div>
        @endforelse
    </div>
@endsection