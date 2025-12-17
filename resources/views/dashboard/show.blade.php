@extends('layouts.app')

@section('title', 'Estatísticas - ' . $campaign->title)

@section('content')
    <div class="container">
        <div class="mb-4">
            <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <h1 class="mb-4">{{ $campaign->title }}</h1>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Arrecadado</h6>
                        <h3 class="text-success">{{ $campaign->formatted_pledged }}</h3>
                        <small class="text-muted">de {{ $campaign->formatted_goal }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Apoiadores</h6>
                        <h3>{{ $stats['total_backers'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Progresso</h6>
                        <h3>{{ number_format($stats['progress'], 0) }}%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Dias Restantes</h6>
                        <h3>{{ $stats['days_remaining'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lista de Apoiadores</h5>
            </div>
            <div class="card-body">
                @if($campaign->pledges->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Apoiador</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Recompensa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaign->pledges as $pledge)
                                    <tr>
                                        <td>{{ $pledge->user->name }}</td>
                                        <td><strong class="text-success">{{ $pledge->formatted_amount }}</strong></td>
                                        <td>{{ $pledge->paid_at?->format('d/m/Y H:i') }}</td>
                                        <td>{{ $pledge->reward?->title ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">Nenhum apoio recebido ainda.</p>
                @endif
            </div>
        </div>
    </div>
@endsection