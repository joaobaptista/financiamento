<template>
    <div class="container">
        <div class="py-3">
            <div class="text-uppercase text-muted small">{{ t('campaignForm.sectionCreator') }}</div>
            <h1 class="h3 fw-normal mb-0">{{ t('campaignCreate.title') }}</h1>
        </div>

        <form @submit.prevent="submit">
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
                                <label class="form-label">{{ t('campaignForm.coverUploadLabel') }}</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept="image/*"
                                    @change="onCoverFileChange"
                                />
                                <div class="form-text">
                                    {{ t('campaignForm.coverUploadHelpCreate') }}
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
                            <i class="bi bi-save"></i> {{ submitting ? t('common.saving') : t('campaignCreate.saveDraft') }}
                        </button>
                        <RouterLink to="/dashboard" class="btn btn-outline-secondary">{{ t('common.cancel') }}</RouterLink>
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { apiPost } from '../api';
import { categories } from '../categories';

const router = useRouter();
const { t } = useI18n({ useScope: 'global' });

const submitting = ref(false);
const error = ref('');

const coverFile = ref(null);

const form = ref({
    title: '',
    description: '',
    niche: '',
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
            fd.append('niche', form.value.niche);
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

        const createdCampaign = created?.data ?? created;
        router.push(`/dashboard/campaigns/${createdCampaign.id}`);
    } catch (e) {
        error.value = e?.response?.data?.message ?? t('campaignCreate.error');
    } finally {
        submitting.value = false;
    }
}
</script>
