@extends('layouts.app')

@section('title', $campaign->title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                @if($campaign->cover_image_path)
                    <img src="{{ $campaign->cover_image_path }}" class="img-fluid rounded mb-4" alt="{{ $campaign->title }}">
                @else
                    <div class="bg-secondary text-white p-5 text-center rounded mb-4">
                        <i class="bi bi-image display-1"></i>
                    </div>
                @endif

                <h1>{{ $campaign->title }}</h1>
                <p class="text-muted">Por {{ $campaign->user->name }}</p>

                <div class="my-4">
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: {{ min($campaign->calculateProgress(), 100) }}%">
                            {{ number_format($campaign->calculateProgress(), 0) }}%
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-success">{{ $campaign->formatted_pledged }}</h4>
                            <small class="text-muted">arrecadado de {{ $campaign->formatted_goal }}</small>
                        </div>
                        <div class="col-4">
                            <h4>{{ $campaign->pledges()->where('status', 'paid')->count() }}</h4>
                            <small class="text-muted">apoiadores</small>
                        </div>
                        <div class="col-4">
                            <h4>{{ $campaign->daysRemaining() }}</h4>
                            <small class="text-muted">dias restantes</small>
                        </div>
                    </div>
                </div>

                <hr>

                <h3>Sobre o Projeto</h3>
                <div class="mb-4">
                    {!! nl2br(e($campaign->description)) !!}
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title">Apoiar este Projeto</h5>

                        @auth
                            <form action="{{ route('pledges.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">

                                <div class="mb-3">
                                    <label class="form-label">Valor do Apoio (R$)</label>
                                    <input type="number" name="amount" class="form-control" min="1" step="0.01" required>
                                </div>

                                @if($campaign->rewards->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Recompensa (Opcional)</label>
                                        <select name="reward_id" class="form-select">
                                            <option value="">Sem recompensa</option>
                                            @foreach($campaign->rewards as $reward)
                                                <option value="{{ $reward->id }}" {{ !$reward->isAvailable() ? 'disabled' : '' }}>
                                                    {{ $reward->title }} - {{ $reward->formatted_min_amount }}
                                                    @if(!$reward->isAvailable()) (Esgotado) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-heart-fill"></i> Apoiar Agora
                                </button>
                            </form>
                        @else
                            <p class="text-muted">Faça login para apoiar este projeto.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">Entrar</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mt-2">Cadastrar</a>
                        @endauth
                    </div>
                </div>

                @if($campaign->rewards->count() > 0)
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Recompensas</h5>
                            @foreach($campaign->rewards as $reward)
                                <div class="border-bottom pb-3 mb-3">
                                    <h6>{{ $reward->title }}</h6>
                                    <p class="text-muted small mb-1">{{ $reward->description }}</p>
                                    <strong class="text-success">{{ $reward->formatted_min_amount }}</strong>
                                    @if($reward->quantity)
                                        <small class="text-muted d-block">{{ $reward->remaining }}/{{ $reward->quantity }}
                                            disponíveis</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection