<template>
    <div class="container">
        <div class="py-3">
            <div class="text-uppercase text-muted small">{{ t('campaignForm.sectionCreator') }}</div>
            <h1 class="h3 fw-normal mb-0">{{ t('campaignEdit.title') }}</h1>
        </div>

        <div v-if="loading" class="text-muted">{{ t('common.loading') }}</div>

        <form v-else @submit.prevent="submit">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ t('campaignForm.basicInfo') }}</h5>

                            <div class="mb-3">
                                <label class="form-label">{{ t('campaignForm.titleLabel') }} *</label>
                                <input v-model="form.title" type="text" class="form-control" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('campaignForm.descriptionLabel') }} *</label>
                                <textarea v-model="form.description" class="form-control" rows="10" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('campaignForm.nicheLabel') }} *</label>
                                <select v-model="form.niche" class="form-select" required>
                                    <option value="" disabled>{{ t('campaignForm.nichePlaceholder') }}</option>
                                    <option v-for="c in categories" :key="c.key" :value="c.key">{{ t(c.labelKey) }}</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ t('campaignForm.goalLabel') }} *</label>
                                    <input v-model="form.goal_amount" type="number" class="form-control" min="1" step="0.01" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ t('campaignForm.endsAtLabel') }} *</label>
                                    <input v-model="form.ends_at" type="date" class="form-control" required />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('campaignForm.coverUrlLabel') }}</label>
                                <input
                                    v-model="form.cover_image_path"
                                    type="text"
                                    class="form-control"
                                    :placeholder="t('campaignForm.coverUrlPlaceholder')"
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ t('campaignForm.coverReplaceUploadLabel') }}</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept="image/*"
                                    @change="onCoverFileChange"
                                />
                                <div class="form-text">
                                    {{ t('campaignForm.coverUploadHelpEdit') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ t('campaignForm.rewardsTitle') }}</h5>
                            <p class="text-muted small">{{ t('campaignForm.rewardsHelp') }}</p>

                            <div>
                                <div
                                    v-for="(r, idx) in form.rewards"
                                    :key="idx"
                                    class="reward-item border p-3 mb-3 rounded"
                                >
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input v-model="r.title" type="text" class="form-control" :placeholder="t('campaignForm.rewardTitlePlaceholder')" />
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input v-model="r.min_amount" type="text" class="form-control" :placeholder="t('campaignForm.rewardMinPlaceholder')" @input="formatMoneyInput($event, 'min_amount', r)" />
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input v-model="r.quantity" type="number" class="form-control" :placeholder="t('campaignForm.rewardQtyPlaceholder')" />
                                        </div>
                                        <div class="col-12 mb-2">
                                            <textarea v-model="r.description" class="form-control" rows="2" :placeholder="t('campaignForm.rewardDescPlaceholder')"></textarea>
                                        </div>
                                        
                                        <!-- Seção de Frete -->
                                        <div class="col-12 mt-3">
                                            <div class="form-check form-switch mb-2 d-flex align-items-center">
                                                <input 
                                                    v-model="r.has_shipping" 
                                                    type="checkbox" 
                                                    class="form-check-input" 
                                                    role="switch"
                                                    :id="`has_shipping_${idx}`"
                                                />
                                                <label class="form-check-label ms-2" :for="`has_shipping_${idx}`">
                                                    <strong>Possui frete</strong>
                                                </label>
                                                <i 
                                                    class="bi bi-question-circle-fill text-primary ms-2" 
                                                    style="cursor: help; font-size: 1.1rem;"
                                                    :id="`shippingTooltip_${idx}`"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    :title="'O frete é opcional para cada recompensa. Se marcado, você deve preencher o valor para todas as 5 regiões do Brasil (Norte, Nordeste, Centro-Oeste, Sudeste e Sul).'"
                                                ></i>
                                            </div>

                                            <div v-if="r.has_shipping" class="border rounded p-3 bg-light mt-2">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 small fw-semibold">Valores de frete por região *</h6>
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-sm btn-outline-secondary"
                                                        @click="openShippingModal(r)"
                                                    >
                                                        Aplicar mesmo valor para todas
                                                    </button>
                                                </div>
                                                
                                                <div class="row g-2">
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label small">Norte *</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">R$</span>
                                                            <input 
                                                                v-model="r.shipping_costs.norte" 
                                                                type="text" 
                                                                class="form-control" 
                                                                :class="{ 'is-invalid': r.has_shipping && !r.shipping_costs.norte }"
                                                                placeholder="0,00"
                                                                required
                                                                @input="formatMoneyInput($event, 'norte', r.shipping_costs)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label small">Nordeste *</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">R$</span>
                                                            <input 
                                                                v-model="r.shipping_costs.nordeste" 
                                                                type="text" 
                                                                class="form-control" 
                                                                :class="{ 'is-invalid': r.has_shipping && !r.shipping_costs.nordeste }"
                                                                placeholder="0,00"
                                                                required
                                                                @input="formatMoneyInput($event, 'nordeste', r.shipping_costs)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label small">Centro-Oeste *</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">R$</span>
                                                            <input 
                                                                v-model="r.shipping_costs['centro-oeste']" 
                                                                type="text" 
                                                                class="form-control" 
                                                                :class="{ 'is-invalid': r.has_shipping && !r.shipping_costs['centro-oeste'] }"
                                                                placeholder="0,00"
                                                                required
                                                                @input="formatMoneyInput($event, 'centro-oeste', r.shipping_costs)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label small">Sudeste *</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">R$</span>
                                                            <input 
                                                                v-model="r.shipping_costs.sudeste" 
                                                                type="text" 
                                                                class="form-control" 
                                                                :class="{ 'is-invalid': r.has_shipping && !r.shipping_costs.sudeste }"
                                                                placeholder="0,00"
                                                                required
                                                                @input="formatMoneyInput($event, 'sudeste', r.shipping_costs)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label small">Sul *</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">R$</span>
                                                            <input 
                                                                v-model="r.shipping_costs.sul" 
                                                                type="text" 
                                                                class="form-control" 
                                                                :class="{ 'is-invalid': r.has_shipping && !r.shipping_costs.sul }"
                                                                placeholder="0,00"
                                                                required
                                                                @input="formatMoneyInput($event, 'sul', r.shipping_costs)"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary" @click="addReward">
                                <i class="bi bi-plus"></i> {{ t('campaignForm.addReward') }}
                            </button>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="submitting">
                            <i class="bi bi-save"></i> {{ submitting ? t('common.saving') : t('common.save') }}
                        </button>
                        <RouterLink to="/dashboard" class="btn btn-outline-secondary">{{ t('common.cancel') }}</RouterLink>
                        <button type="button" class="btn btn-outline-danger ms-auto" :disabled="submitting" @click="destroyCampaign">
                            <i class="bi bi-trash"></i> {{ t('common.delete') }}
                        </button>
                    </div>

                    <div v-if="error" class="alert alert-danger mt-3" role="alert">{{ error }}</div>
                </div>

                <div class="col-lg-4 mt-3 mt-lg-0">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">{{ t('campaignForm.tipsTitle') }}</h6>
                            <ul class="small text-muted">
                                <li>{{ t('campaignForm.tips.1') }}</li>
                                <li>{{ t('campaignForm.tips.2') }}</li>
                                <li>{{ t('campaignForm.tips.3') }}</li>
                                <li>{{ t('campaignForm.tips.4') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Modal para aplicar mesmo valor de frete -->
        <div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="shippingModalLabel">Aplicar mesmo valor para todas as regiões</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Digite o valor para todas as regiões:</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input 
                                v-model="shippingModalValue" 
                                type="text" 
                                class="form-control" 
                                placeholder="15,00"
                                @input="formatMoneyInput($event, 'modal', { modal: true })"
                                @keyup.enter="applyShippingToAll"
                            />
                        </div>
                        <div class="form-text">Use vírgula para decimais (ex: 15,00)</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="applyShippingToAll">Aplicar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { apiDelete, apiGet, apiPost, apiPut } from '../api';
import { categories } from '../categories';

const props = defineProps({
    id: { type: [String, Number], required: true },
});

const router = useRouter();
const { t } = useI18n({ useScope: 'global' });

const loading = ref(true);
const submitting = ref(false);
const error = ref('');

const coverFile = ref(null);
const shippingModalValue = ref('');
const currentRewardForShipping = ref(null);
let shippingModalInstance = null;
let tooltipInstances = [];

const form = ref({
    title: '',
    description: '',
    niche: '',
    goal_amount: '0.00',
    ends_at: '',
    cover_image_path: '',
    rewards: [],
});

function onCoverFileChange(e) {
    const file = e?.target?.files?.[0] ?? null;
    coverFile.value = file;
}

function toDateInput(iso) {
    if (!iso) return '';
    return String(iso).slice(0, 10);
}

function centsToMoney(cents) {
    const value = (Number(cents || 0) / 100).toFixed(2);
    return value.replace('.', ',');
}

function formatMoneyInput(event, field, target) {
    let value = event.target.value;
    
    // Remove tudo exceto números e vírgula
    value = value.replace(/[^\d,]/g, '');
    
    // Garante apenas uma vírgula
    const parts = value.split(',');
    if (parts.length > 2) {
        value = parts[0] + ',' + parts.slice(1).join('');
    }
    
    // Limita a 2 casas decimais após vírgula
    if (parts.length === 2 && parts[1].length > 2) {
        value = parts[0] + ',' + parts[1].slice(0, 2);
    }
    
    if (target.modal) {
        shippingModalValue.value = value;
    } else if (field === 'min_amount') {
        target.min_amount = value;
    } else {
        target[field] = value;
    }
}

function addReward() {
    form.value.rewards.push({ 
        title: '', 
        description: '', 
        min_amount: '0,00', 
        quantity: '',
        has_shipping: false,
        shipping_costs: {
            'norte': '',
            'nordeste': '',
            'centro-oeste': '',
            'sudeste': '',
            'sul': ''
        }
    });
    
    // Inicializar tooltip após adicionar recompensa
    nextTick(() => {
        initTooltips();
    });
}

function initTooltips() {
    // Destruir tooltips existentes
    tooltipInstances.forEach(instance => {
        if (instance && typeof instance.disable === 'function') {
            instance.disable();
        }
    });
    tooltipInstances = [];
    
    // Inicializar novos tooltips
    if (typeof window !== 'undefined' && window.bootstrap && window.bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            const tooltip = new window.bootstrap.Tooltip(el);
            tooltipInstances.push(tooltip);
        });
    }
}

function openShippingModal(reward) {
    currentRewardForShipping.value = reward;
    shippingModalValue.value = '';
    
    // Inicializar modal Bootstrap se ainda não foi
    if (!shippingModalInstance && typeof window !== 'undefined' && window.bootstrap) {
        const modalEl = document.getElementById('shippingModal');
        shippingModalInstance = new window.bootstrap.Modal(modalEl);
    }
    
    if (shippingModalInstance) {
        shippingModalInstance.show();
    }
}

function applyShippingToAll() {
    if (!currentRewardForShipping.value) return;
    
    const valor = shippingModalValue.value || '0,00';
    const reward = currentRewardForShipping.value;
    
    reward.shipping_costs.norte = valor;
    reward.shipping_costs.nordeste = valor;
    reward.shipping_costs['centro-oeste'] = valor;
    reward.shipping_costs.sudeste = valor;
    reward.shipping_costs.sul = valor;
    
    if (shippingModalInstance) {
        shippingModalInstance.hide();
    }
    shippingModalValue.value = '';
    currentRewardForShipping.value = null;
}

function validateShipping() {
    for (const reward of form.value.rewards) {
        if (reward.has_shipping) {
            const regions = ['norte', 'nordeste', 'centro-oeste', 'sudeste', 'sul'];
            for (const region of regions) {
                if (!reward.shipping_costs[region] || reward.shipping_costs[region].trim() === '') {
                    return `A recompensa "${reward.title || 'Sem título'}" possui frete marcado, mas o valor da região "${region}" não foi preenchido.`;
                }
            }
        }
    }
    return null;
}

async function load() {
    loading.value = true;
    const payload = await apiGet(`/api/me/campaigns/${props.id}`);
    const campaign = payload?.data ?? payload;

    form.value = {
        title: campaign.title,
        description: campaign.description,
        niche: campaign.niche ?? '',
        goal_amount: (Number(campaign.goal_amount || 0) / 100).toFixed(2), // Converter de centavos para reais com ponto
        ends_at: toDateInput(campaign.ends_at),
        cover_image_path: campaign.cover_image_path ?? '',
        rewards: (campaign.rewards ?? []).map(r => {
            const shippingCosts = {};
            if (r.fretes) {
                Object.keys(r.fretes).forEach(regiao => {
                    shippingCosts[regiao] = centsToMoney(r.fretes[regiao]);
                });
            }
            
            return {
                title: r.title,
                description: r.description,
                min_amount: centsToMoney(r.min_amount),
                quantity: r.quantity ?? '',
                has_shipping: !!r.fretes && Object.keys(r.fretes).length > 0,
                shipping_costs: {
                    'norte': shippingCosts.norte || '',
                    'nordeste': shippingCosts.nordeste || '',
                    'centro-oeste': shippingCosts['centro-oeste'] || '',
                    'sudeste': shippingCosts.sudeste || '',
                    'sul': shippingCosts.sul || ''
                }
            };
        }),
    };

    loading.value = false;
    
    // Inicializar tooltips após carregar
    nextTick(() => {
        initTooltips();
    });
}

async function submit() {
    error.value = '';
    
    // Validar fretes obrigatórios
    const shippingError = validateShipping();
    if (shippingError) {
        error.value = shippingError;
        submitting.value = false;
        return;
    }
    
    submitting.value = true;
    try {
        // Converter valores de dinheiro de vírgula para ponto antes de enviar
        const convertMoneyToNumber = (moneyStr) => {
            return String(moneyStr || '0').replace(',', '.');
        };

        if (coverFile.value) {
            const fd = new FormData();
            fd.append('_method', 'PUT');
            fd.append('title', form.value.title);
            fd.append('description', form.value.description);
            fd.append('niche', form.value.niche);
            fd.append('goal_amount', String(form.value.goal_amount || ''));
            fd.append('ends_at', String(form.value.ends_at || ''));
            if (form.value.cover_image_path) fd.append('cover_image_path', form.value.cover_image_path);
            fd.append('cover_image', coverFile.value);

            (form.value.rewards || []).forEach((r, idx) => {
                if (r.title) {
                    fd.append(`rewards[${idx}][title]`, r.title);
                    fd.append(`rewards[${idx}][description]`, r.description || '');
                    fd.append(`rewards[${idx}][min_amount]`, convertMoneyToNumber(r.min_amount || '0'));
                    if (r.quantity !== '' && r.quantity !== null && r.quantity !== undefined) {
                        fd.append(`rewards[${idx}][quantity]`, String(r.quantity));
                    }
                    
                    // Adicionar fretes
                    if (r.has_shipping && r.shipping_costs) {
                        fd.append(`rewards[${idx}][has_shipping]`, '1');
                        Object.keys(r.shipping_costs).forEach(regiao => {
                            if (r.shipping_costs[regiao]) {
                                fd.append(`rewards[${idx}][shipping_costs][${regiao}]`, convertMoneyToNumber(r.shipping_costs[regiao]));
                            }
                        });
                    }
                }
            });

            await apiPost(`/api/me/campaigns/${props.id}`, fd);
        } else {
            const payload = {
                ...form.value,
                goal_amount: String(form.value.goal_amount || '').replace(',', '.'), // Garantir ponto no goal_amount
                rewards: form.value.rewards
                    .filter(r => r.title)
                    .map(r => {
                        const rewardPayload = {
                            title: r.title,
                            description: r.description,
                            min_amount: convertMoneyToNumber(r.min_amount || '0'),
                            quantity: r.quantity === '' ? null : Number(r.quantity),
                        };
                        
                        // Adicionar fretes se existirem
                        if (r.has_shipping && r.shipping_costs) {
                            rewardPayload.has_shipping = true;
                            rewardPayload.shipping_costs = {};
                            Object.keys(r.shipping_costs).forEach(regiao => {
                                if (r.shipping_costs[regiao]) {
                                    rewardPayload.shipping_costs[regiao] = convertMoneyToNumber(r.shipping_costs[regiao]);
                                }
                            });
                        }
                        
                        return rewardPayload;
                    }),
            };

            await apiPut(`/api/me/campaigns/${props.id}`, payload);
        }

        router.push('/dashboard');
    } catch (e) {
        error.value = e?.response?.data?.message ?? t('campaignEdit.error');
    } finally {
        submitting.value = false;
    }
}

async function destroyCampaign() {
    error.value = '';
    if (!confirm(t('campaignEdit.deleteConfirm'))) return;

    submitting.value = true;
    try {
        await apiDelete(`/api/me/campaigns/${props.id}`);
        router.push('/dashboard');
    } catch (e) {
        error.value = e?.response?.data?.message ?? t('campaignEdit.error');
    } finally {
        submitting.value = false;
    }
}

onMounted(() => {
    load().then(() => {
        initTooltips();
    });
});
watch(() => props.id, load);
</script>
