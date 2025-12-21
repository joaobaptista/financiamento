<template>
    <div class="container">
        <div class="py-3">
            <div class="text-uppercase text-muted small">Criador</div>
            <h1 class="h3 fw-normal mb-0">Criar nova campanha</h1>
        </div>

        <form @submit.prevent="submit">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Informações Básicas</h5>

                            <div class="mb-3">
                                <label class="form-label">Título da Campanha *</label>
                                <input v-model="form.title" type="text" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição *</label>
                                <textarea v-model="form.description" class="form-control" rows="10" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Meta de Arrecadação (R$) *</label>
                                    <input v-model="form.goal_amount" type="number" class="form-control" min="1" step="0.01" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data de Término *</label>
                                    <input v-model="form.ends_at" type="date" class="form-control" required />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL da Imagem de Capa (Opcional)</label>
                                <input
                                    v-model="form.cover_image_path"
                                    type="text"
                                    class="form-control"
                                    placeholder="https://exemplo.com/imagem.jpg"
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload da Imagem de Capa (Opcional)</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept="image/*"
                                    @change="onCoverFileChange"
                                />
                                <div class="form-text">
                                    Enviaremos a imagem e geraremos versões otimizadas (WebP + JPG).
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Recompensas (Opcional)</h5>
                            <p class="text-muted small">Ofereça recompensas para incentivar apoios maiores.</p>

                            <div>
                                <div
                                    v-for="(r, idx) in form.rewards"
                                    :key="idx"
                                    class="reward-item border p-3 mb-3 rounded"
                                >
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input v-model="r.title" type="text" class="form-control" placeholder="Título da Recompensa" />
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input v-model="r.min_amount" type="number" class="form-control" placeholder="Valor Mínimo (R$)" step="0.01" />
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input v-model="r.quantity" type="number" class="form-control" placeholder="Quantidade (opcional)" />
                                        </div>
                                        <div class="col-12">
                                            <textarea v-model="r.description" class="form-control" rows="2" placeholder="Descrição da recompensa"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary" @click="addReward">
                                <i class="bi bi-plus"></i> Adicionar Recompensa
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="submitting">
                            <i class="bi bi-save"></i> {{ submitting ? 'Salvando…' : 'Salvar como Rascunho' }}
                        </button>
                        <RouterLink to="/dashboard" class="btn btn-outline-secondary">Cancelar</RouterLink>
                    </div>

                    <div v-if="error" class="alert alert-danger mt-3" role="alert">{{ error }}</div>
                </div>

                <div class="col-lg-4 mt-3 mt-lg-0">
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
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { apiPost } from '../api';

const router = useRouter();

const submitting = ref(false);
const error = ref('');

const coverFile = ref(null);

const form = ref({
    title: '',
    description: '',
    goal_amount: '100.00',
    ends_at: '',
    cover_image_path: '',
    rewards: [],
});

function onCoverFileChange(e) {
    const file = e?.target?.files?.[0] ?? null;
    coverFile.value = file;
}

function addReward() {
    form.value.rewards.push({ title: '', description: '', min_amount: '0.00', quantity: '' });
}

async function submit() {
    error.value = '';
    submitting.value = true;

    try {
        let created;

        if (coverFile.value) {
            const fd = new FormData();
            fd.append('title', form.value.title);
            fd.append('description', form.value.description);
            fd.append('goal_amount', String(form.value.goal_amount || ''));
            fd.append('ends_at', String(form.value.ends_at || ''));
            if (form.value.cover_image_path) fd.append('cover_image_path', form.value.cover_image_path);
            fd.append('cover_image', coverFile.value);

            (form.value.rewards || []).forEach((r, idx) => {
                fd.append(`rewards[${idx}][title]`, r.title || '');
                fd.append(`rewards[${idx}][description]`, r.description || '');
                fd.append(`rewards[${idx}][min_amount]`, String(r.min_amount || '0'));
                if (r.quantity !== '' && r.quantity !== null && r.quantity !== undefined) {
                    fd.append(`rewards[${idx}][quantity]`, String(r.quantity));
                }
            });

            created = await apiPost('/api/me/campaigns', fd);
        } else {
            const payload = {
                ...form.value,
                rewards: form.value.rewards.map(r => ({
                    title: r.title,
                    description: r.description,
                    min_amount: r.min_amount,
                    quantity: r.quantity === '' ? null : Number(r.quantity),
                })),
            };

            created = await apiPost('/api/me/campaigns', payload);
        }

        router.push(`/dashboard/campaigns/${created.id}`);
    } catch (e) {
        error.value = e?.response?.data?.message ?? 'Erro ao criar campanha.';
    } finally {
        submitting.value = false;
    }
}
</script>
