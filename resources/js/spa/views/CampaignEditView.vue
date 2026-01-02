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
                                            <input v-model="r.min_amount" type="number" class="form-control" :placeholder="t('campaignForm.rewardMinPlaceholder')" step="0.01" />
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <input v-model="r.quantity" type="number" class="form-control" :placeholder="t('campaignForm.rewardQtyPlaceholder')" />
                                        </div>
                                        <div class="col-12">
                                            <textarea v-model="r.description" class="form-control" rows="2" :placeholder="t('campaignForm.rewardDescPlaceholder')"></textarea>
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
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
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
    return (Number(cents || 0) / 100).toFixed(2);
}

function addReward() {
    form.value.rewards.push({ title: '', description: '', min_amount: '0.00', quantity: '' });
}

async function load() {
    loading.value = true;
    const payload = await apiGet(`/api/me/campaigns/${props.id}`);
    const campaign = payload?.data ?? payload;

    form.value = {
        title: campaign.title,
        description: campaign.description,
        niche: campaign.niche ?? '',
        goal_amount: centsToMoney(campaign.goal_amount),
        ends_at: toDateInput(campaign.ends_at),
        cover_image_path: campaign.cover_image_path ?? '',
        rewards: (campaign.rewards ?? []).map(r => ({
            title: r.title,
            description: r.description,
            min_amount: centsToMoney(r.min_amount),
            quantity: r.quantity ?? '',
        })),
    };

    loading.value = false;
}

async function submit() {
    error.value = '';
    submitting.value = true;
    try {
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
                    fd.append(`rewards[${idx}][min_amount]`, String(r.min_amount || '0'));
                    if (r.quantity !== '' && r.quantity !== null && r.quantity !== undefined) {
                        fd.append(`rewards[${idx}][quantity]`, String(r.quantity));
                    }
                }
            });

            await apiPost(`/api/me/campaigns/${props.id}`, fd);
        } else {
            const payload = {
                ...form.value,
                rewards: form.value.rewards
                    .filter(r => r.title)
                    .map(r => ({
                        title: r.title,
                        description: r.description,
                        min_amount: r.min_amount,
                        quantity: r.quantity === '' ? null : Number(r.quantity),
                    })),
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

onMounted(load);
watch(() => props.id, load);
</script>
