@extends('layouts.app')

@section('title', 'Criar Campanha')

@section('content')
    <div class="container">
        <h1 class="mb-4">Criar Nova Campanha</h1>

        <form action="{{ route('campaigns.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Informações Básicas</h5>

                            <div class="mb-3">
                                <label class="form-label">Título da Campanha *</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição *</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="10" required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Meta de Arrecadação (R$) *</label>
                                    <input type="number" name="goal_amount"
                                        class="form-control @error('goal_amount') is-invalid @enderror"
                                        value="{{ old('goal_amount') }}" min="1" step="0.01" required>
                                    @error('goal_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data de Término *</label>
                                    <input type="date" name="ends_at"
                                        class="form-control @error('ends_at') is-invalid @enderror"
                                        value="{{ old('ends_at') }}" required>
                                    @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL da Imagem de Capa (Opcional)</label>
                                <input type="text" name="cover_image_path" class="form-control"
                                    value="{{ old('cover_image_path') }}" placeholder="https://exemplo.com/imagem.jpg">
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Recompensas (Opcional)</h5>
                            <p class="text-muted small">Ofereça recompensas para incentivar apoios maiores.</p>

                            <div id="rewards-container">
                                <div class="reward-item border p-3 mb-3 rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="rewards[0][title]" class="form-control"
                                                placeholder="Título da Recompensa">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input type="number" name="rewards[0][min_amount]" class="form-control"
                                                placeholder="Valor Mínimo (R$)" step="0.01">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input type="number" name="rewards[0][quantity]" class="form-control"
                                                placeholder="Quantidade (opcional)">
                                        </div>
                                        <div class="col-12">
                                            <textarea name="rewards[0][description]" class="form-control" rows="2"
                                                placeholder="Descrição da recompensa"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addReward()">
                                <i class="bi bi-plus"></i> Adicionar Recompensa
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Salvar como Rascunho
                        </button>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Dicas</h6>
                            <ul class="small text-muted">
                                <li>Seja claro e específico sobre seu projeto</li>
                                <li>Defina uma meta realista</li>
                                <li>Ofereça recompensas atrativas</li>
                                <li>Use uma imagem de capa chamativa</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let rewardCount = 1;
            function addReward() {
                const container = document.getElementById('rewards-container');
                const newReward = `
                <div class="reward-item border p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="rewards[${rewardCount}][title]" class="form-control" placeholder="Título da Recompensa">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="number" name="rewards[${rewardCount}][min_amount]" class="form-control" placeholder="Valor Mínimo (R$)" step="0.01">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="number" name="rewards[${rewardCount}][quantity]" class="form-control" placeholder="Quantidade (opcional)">
                        </div>
                        <div class="col-12">
                            <textarea name="rewards[${rewardCount}][description]" class="form-control" rows="2" placeholder="Descrição da recompensa"></textarea>
                        </div>
                    </div>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', newReward);
                rewardCount++;
            }
        </script>
    @endpush
@endsection