<template>
    <div>
        <div v-if="loading" class="container py-4 text-muted">{{ t('common.loading') }}</div>

        <div v-else-if="campaign">
            <!-- Hero (padrão Catarse) -->
            <div class="bg-light border-bottom">
                <div class="container py-4 py-md-5">
                    <div class="text-uppercase text-muted small">{{ t('campaignShow.sectionLabel') }}</div>
                    <h1 class="display-6 mb-2">{{ campaign.title }}</h1>
                    <div class="text-muted d-flex flex-wrap align-items-center gap-2">
                        <div>
                            {{ t('campaignShow.by') }} <strong>{{ campaign.user?.name ?? '—' }}</strong>
                        </div>

                        <template
                            v-if="campaign.creator_page?.id && (!user || user.id !== campaign.creator_page.owner_user_id)"
                        >
                            <template v-if="user">
                                <button
                                    type="button"
                                    class="btn btn-sm"
                                    :class="isFollowingPage ? 'btn-outline-secondary' : 'btn-outline-primary'"
                                    :disabled="supportingBusy"
                                    @click="togglePageFollow"
                                >
                                    {{ supportingBusy ? t('common.ellipsis') : isFollowingPage ? t('campaignShow.unfollowPage') : t('campaignShow.followPage') }}
                                </button>
                                <span v-if="pageFollowersCount != null" class="small text-muted">
                                    {{ t('campaignShow.followersCount', { count: pageFollowersCount }) }}
                                </span>
                            </template>
                            <template v-else>
                                <RouterLink to="/login" class="btn btn-sm btn-outline-primary">{{ t('campaignShow.loginToFollow') }}</RouterLink>
                            </template>
                        </template>

                        <span class="mx-2">•</span>
                        <span v-if="isCampaignOpen">{{ t('common.daysRemaining', { days: daysRemaining }) }}</span>
                        <span v-else>{{ t('campaignShow.finished') }}</span>
                    </div>
                </div>
            </div>

            <div class="container py-4">
                <div class="row g-4">
                    <!-- Conteúdo / História -->
                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="ratio ratio-16x9 bg-light">
                                <template v-if="campaign.cover_image_path">
                                    <picture v-if="campaign.cover_image_webp_path">
                                        <source :srcset="absoluteUrl(campaign.cover_image_webp_path)" type="image/webp" />
                                        <img
                                            :src="absoluteUrl(campaign.cover_image_path)"
                                            class="w-100 h-100"
                                            :alt="campaign.title"
                                            style="object-fit: cover"
                                        />
                                    </picture>
                                    <img
                                        v-else
                                        :src="absoluteUrl(campaign.cover_image_path)"
                                        class="w-100 h-100"
                                        :alt="campaign.title"
                                        style="object-fit: cover"
                                    />
                                </template>
                                <div v-else class="d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-image" style="font-size: 2rem"></i>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h5 fw-normal mb-3">{{ t('campaignShow.aboutTitle') }}</h2>
                                <div
                                    v-if="storyHtml"
                                    class="campaign-story text-muted"
                                    v-html="storyHtml"
                                ></div>
                                <div v-else class="text-muted" style="white-space: pre-line">
                                    {{ campaign.description }}
                                </div>
                            </div>
                        </div>

                        <div v-if="(campaign.rewards || []).length" class="card" id="recompensas">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <h2 class="h5 fw-normal mb-0">{{ t('campaignShow.rewardsTitle') }}</h2>
                                    <span class="text-muted small">{{ t('campaignShow.rewardsSubtitle') }}</span>
                                </div>

                                <div class="mt-3">
                                    <div class="card border mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="text-uppercase text-muted small">{{ t('campaignShow.freeSupport.label') }}</div>
                                                    <div class="fw-semibold">{{ t('campaignShow.freeSupport.title') }}</div>
                                                    <div class="text-muted small">{{ t('campaignShow.freeSupport.subtitle') }}</div>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary"
                                                    @click="selectNoReward()"
                                                >
                                                    {{ t('common.select') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-for="r in sortedRewards" :key="r.id" class="card border mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="text-success fw-semibold">A partir de {{ formatMoney(r.min_amount) }}</div>
                                                    <div class="fw-semibold">{{ r.title }}</div>
                                                    <div v-if="r.description" class="text-muted small mt-1">{{ r.description }}</div>
                                                    <div v-if="r.quantity" class="text-muted small mt-2">
                                                        <span v-if="isRewardAvailable(r)">{{ t('campaignShow.rewardAvailability', { remaining: r.remaining, quantity: r.quantity }) }}</span>
                                                        <span v-else class="text-danger">{{ t('campaignShow.soldOut') }}</span>
                                                    </div>
                                                </div>

                                                <button
                                                    type="button"
                                                    class="btn"
                                                    :class="isRewardAvailable(r) ? 'btn-primary' : 'btn-outline-secondary'"
                                                    :disabled="!isRewardAvailable(r)"
                                                    @click="selectReward(r)"
                                                >
                                                    {{ isRewardAvailable(r) ? t('common.select') : t('campaignShow.unavailable') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar de apoio -->
                    <div class="col-lg-4">
                        <div ref="supportBox" class="card sticky-top" style="top: 16px">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-uppercase text-muted small">{{ t('campaignShow.supportTitle') }}</div>
                                    <a
                                        v-if="(campaign.rewards || []).length"
                                        class="small text-decoration-none"
                                        href="#recompensas"
                                    >
                                        {{ t('campaignShow.seeRewards') }}
                                    </a>
                                </div>

                                <div class="mt-3">
                                    <div class="progress" style="height: 10px">
                                        <div class="progress-bar bg-success" :style="{ width: `${progress}%` }"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <div>
                                            <div class="h4 text-success mb-0">{{ formatMoney(campaign.pledged_amount) }}</div>
                                            <div class="text-muted small">{{ t('common.ofGoal', { goal: formatMoney(campaign.goal_amount) }) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="h4 mb-0">{{ Math.round(progress) }}%</div>
                                            <div class="text-muted small">{{ t('campaignShow.reached') }}</div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mt-2 text-center">
                                        <div class="col-6">
                                            <div class="fw-semibold">{{ campaign.supporters_count ?? 0 }}</div>
                                            <div class="text-muted small">{{ t('campaignShow.supporters') }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-semibold">{{ daysRemaining }}</div>
                                            <div class="text-muted small">{{ t('campaignShow.days') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3" />

                                <template v-if="user">
                                    <div v-if="!isCampaignOpen" class="alert alert-secondary mb-3" role="alert">
                                        {{ t('campaignShow.supportClosed') }}
                                    </div>

                                    <form @submit.prevent="submit">
                                        <div class="mb-2">
                                            <label class="form-label">{{ t('campaignShow.amountLabel') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input
                                                    v-model="amount"
                                                    type="number"
                                                    class="form-control"
                                                    min="1"
                                                    step="0.01"
                                                    :disabled="submitting || !isCampaignOpen"
                                                    required
                                                />
                                            </div>
                                            <div v-if="selectedReward" class="form-text">
                                                {{ t('campaignShow.minForReward', { min: formatMoney(selectedReward.min_amount) }) }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">{{ t('campaignShow.paymentMethodLabel') }}</label>
                                            <div class="form-check">
                                                <input
                                                    id="pay-pix"
                                                    v-model="paymentMethod"
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="payment_method"
                                                    value="pix"
                                                    :disabled="submitting || !isCampaignOpen"
                                                />
                                                <label class="form-check-label" for="pay-pix">{{ t('campaignShow.paymentPix') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    id="pay-card"
                                                    v-model="paymentMethod"
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="payment_method"
                                                    value="card"
                                                    :disabled="submitting || !isCampaignOpen"
                                                />
                                                <label class="form-check-label" for="pay-card">{{ t('campaignShow.paymentCard') }}</label>
                                            </div>
                                        </div>

                                        <div v-if="pixNextAction?.copy_paste" class="alert alert-info mb-3" role="alert">
                                            <div class="small">{{ t('campaignShow.pixInstructions') }}</div>
                                            <div class="mt-2">
                                                <div v-if="pixQrCodeDataUrl" class="mb-2">
                                                    <div class="form-text">{{ t('campaignShow.pixQrCodeLabel') }}</div>
                                                    <img
                                                        :src="pixQrCodeDataUrl"
                                                        alt="Pix QR Code"
                                                        class="d-block border rounded"
                                                        style="max-width: 220px"
                                                    />
                                                </div>

                                                <div class="form-text">{{ t('campaignShow.pixCopyPasteLabel') }}</div>
                                                <textarea
                                                    class="form-control font-monospace"
                                                    rows="3"
                                                    readonly
                                                    :value="pixNextAction.copy_paste"
                                                ></textarea>

                                                <div class="d-flex gap-2 mt-2">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" @click="copyPixCode">
                                                        {{ t('campaignShow.pixCopy') }}
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-primary btn-sm"
                                                        :disabled="confirmingPix"
                                                        @click="confirmPix"
                                                    >
                                                        {{ confirmingPix ? t('common.ellipsis') : t('campaignShow.pixConfirm') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="paymentMethod === 'card'" class="alert alert-light border mb-3" role="alert">
                                            <div class="small mb-2">{{ t('campaignShow.cardMockInfo') }}</div>
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label class="form-label mb-1">{{ t('campaignShow.cardNumberLabel') }}</label>
                                                    <input
                                                        v-model="cardNumber"
                                                        type="text"
                                                        class="form-control"
                                                        autocomplete="cc-number"
                                                        inputmode="numeric"
                                                        :disabled="submitting || !isCampaignOpen"
                                                    />
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label mb-1">{{ t('campaignShow.cardNameLabel') }}</label>
                                                    <input
                                                        v-model="cardName"
                                                        type="text"
                                                        class="form-control"
                                                        autocomplete="cc-name"
                                                        :disabled="submitting || !isCampaignOpen"
                                                    />
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label mb-1">{{ t('campaignShow.cardExpiryLabel') }}</label>
                                                    <input
                                                        v-model="cardExpiry"
                                                        type="text"
                                                        class="form-control"
                                                        autocomplete="cc-exp"
                                                        placeholder="MM/AA"
                                                        :disabled="submitting || !isCampaignOpen"
                                                    />
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label mb-1">{{ t('campaignShow.cardCvvLabel') }}</label>
                                                    <input
                                                        v-model="cardCvv"
                                                        type="password"
                                                        class="form-control"
                                                        autocomplete="cc-csc"
                                                        inputmode="numeric"
                                                        :disabled="submitting || !isCampaignOpen"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="(campaign.rewards || []).length" class="mb-3">
                                            <label class="form-label">{{ t('campaignShow.rewardLabel') }}</label>
                                            <select v-model="rewardId" class="form-select" :disabled="submitting || !isCampaignOpen">
                                                <option :value="null">{{ t('campaignShow.noReward') }}</option>
                                                <option
                                                    v-for="r in sortedRewards"
                                                    :key="r.id"
                                                    :value="r.id"
                                                    :disabled="!isRewardAvailable(r)"
                                                >
                                                    {{ r.title }} — {{ formatMoney(r.min_amount) }}
                                                    <template v-if="!isRewardAvailable(r)"> ({{ t('campaignShow.soldOut') }})</template>
                                                </option>
                                            </select>
                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-success w-100"
                                            :disabled="submitting || !isCampaignOpen"
                                        >
                                            <i class="bi bi-heart-fill"></i>
                                            {{ submitting ? t('campaignShow.processing') : t('campaignShow.supportNow') }}
                                        </button>

                                        <div v-if="message" class="text-muted small mt-2">{{ message }}</div>
                                    </form>
                                </template>

                                <template v-else>
                                    <p class="text-muted mb-3">{{ t('campaignShow.loginToSupport') }}</p>
                                    <RouterLink to="/login" class="btn btn-primary w-100">{{ t('navbar.login') }}</RouterLink>
                                    <RouterLink to="/register" class="btn btn-outline-primary w-100 mt-2">{{ t('auth.register.submit') }}</RouterLink>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="container py-4 text-muted">{{ t('campaignShow.notFound') }}</div>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiDelete, apiGet, apiPost } from '../api';
import { absoluteUrl, applyCampaignSeo } from '../seo';
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import QRCode from 'qrcode';

const props = defineProps({
    slug: { type: String, required: true },
    user: { type: Object, default: null },
});

const loading = ref(true);
const campaign = ref(null);
const amount = ref('10.00');
const rewardId = ref(null);
const submitting = ref(false);
const message = ref('');
const supportBox = ref(null);

const paymentMethod = ref('pix');
const pixNextAction = ref(null);
const pixQrCodeDataUrl = ref('');
const lastPledgeId = ref(null);
const confirmingPix = ref(false);

const cardNumber = ref('');
const cardName = ref('');
const cardExpiry = ref('');
const cardCvv = ref('');

const supportingBusy = ref(false);
const isFollowingPage = ref(false);
const pageFollowersCount = ref(null);

const { t, locale } = useI18n({ useScope: 'global' });

function formatMoney(cents) {
    const value = Number(cents || 0) / 100;
    const intlLocale = String(locale.value || 'pt_BR').replace('_', '-');
    return new Intl.NumberFormat(intlLocale, { style: 'currency', currency: 'BRL' }).format(value);
}

function isRewardAvailable(r) {
    if (r.quantity == null) return true;
    return Number(r.remaining || 0) > 0;
}

function calcProgress(c) {
    const goal = Number(c?.goal_amount || 0);
    const pledged = Number(c?.pledged_amount || 0);
    if (!goal) return 0;
    return Math.min((pledged / goal) * 100, 100);
}

function calcDaysRemaining(c) {
    if (!c?.ends_at) return 0;
    const end = new Date(c.ends_at);
    const now = new Date();
    const diff = end.getTime() - now.getTime();
    return Math.max(Math.ceil(diff / (1000 * 60 * 60 * 24)), 0);
}

const progress = ref(0);
const daysRemaining = ref(0);

const isCampaignOpen = computed(() => {
    const c = campaign.value;
    if (!c) return false;
    if (String(c.status || '').toLowerCase() !== 'active') return false;
    return Number(daysRemaining.value || 0) > 0;
});

const sortedRewards = computed(() => {
    const rewards = campaign.value?.rewards || [];
    return [...rewards].sort((a, b) => Number(a.min_amount || 0) - Number(b.min_amount || 0));
});

const selectedReward = computed(() => {
    if (!rewardId.value) return null;
    return (campaign.value?.rewards || []).find((r) => String(r.id) === String(rewardId.value)) || null;
});

const storyHtml = computed(() => {
    const raw = String(campaign.value?.description || '').trim();
    if (!raw) return '';
    try {
        const html = marked.parse(raw, {
            gfm: true,
            breaks: true,
            headerIds: false,
            mangle: false,
        });
        return DOMPurify.sanitize(String(html || ''), {
            USE_PROFILES: { html: true },
        });
    } catch {
        return '';
    }
});

function centsToAmountInput(cents) {
    return (Number(cents || 0) / 100).toFixed(2);
}

async function scrollToSupport() {
    await nextTick();
    const el = supportBox.value;
    if (!el) return;
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function selectReward(r) {
    rewardId.value = r?.id ?? null;
    const min = Number(r?.min_amount || 0);
    amount.value = centsToAmountInput(min);
    await scrollToSupport();
}

async function selectNoReward() {
    rewardId.value = null;
    await scrollToSupport();
}

async function fetchCampaign() {
    loading.value = true;
    message.value = '';

    try {
        const data = await apiGet(`/api/campaigns/${props.slug}`);
        campaign.value = data?.data ?? data;

        progress.value = calcProgress(campaign.value);
        daysRemaining.value = calcDaysRemaining(campaign.value);

        applyCampaignSeo(campaign.value);

        // Escolha inicial de valor: se houver recompensa selecionada, usa mínimo; senão, um valor sugerido.
        if (selectedReward.value) {
            amount.value = centsToAmountInput(selectedReward.value.min_amount);
        } else if (!amount.value) {
            amount.value = '25.00';
        }

        await fetchPageFollow();
    } catch (e) {
        campaign.value = null;
        message.value = e?.response?.data?.message ?? '';
    } finally {
        loading.value = false;
    }
}

async function fetchPageFollow() {
    const creatorPageSlug = campaign.value?.creator_page?.slug;
    if (!creatorPageSlug) return;

    try {
        const data = await apiGet(`/api/creator-pages/${creatorPageSlug}/follow`);
        isFollowingPage.value = !!data?.is_following;
        pageFollowersCount.value = Number(data?.followers_count ?? 0);
    } catch {
        // Ignore (e.g. creator deleted)
    }
}

async function togglePageFollow() {
    const creatorPageSlug = campaign.value?.creator_page?.slug;
    if (!creatorPageSlug) return;
    if (!props.user) return;

    supportingBusy.value = true;
    try {
        const data = isFollowingPage.value
            ? await apiDelete(`/api/creator-pages/${creatorPageSlug}/follow`)
            : await apiPost(`/api/creator-pages/${creatorPageSlug}/follow`, {});
        isFollowingPage.value = !!data?.is_following;
        pageFollowersCount.value = Number(data?.followers_count ?? pageFollowersCount.value ?? 0);
    } finally {
        supportingBusy.value = false;
    }
}

async function submit() {
    message.value = '';
    submitting.value = true;

    pixNextAction.value = null;
    pixQrCodeDataUrl.value = '';
    lastPledgeId.value = null;

    try {
        if (selectedReward.value) {
            const min = Number(selectedReward.value.min_amount || 0) / 100;
            if (Number(amount.value) < min) {
                message.value = t('campaignShow.minRewardError', { min: formatMoney(selectedReward.value.min_amount) });
                submitting.value = false;
                return;
            }
        }

        const result = await apiPost('/api/pledges', {
            campaign_id: campaign.value.id,
            amount: amount.value,
            reward_id: rewardId.value,
            payment_method: paymentMethod.value,
            card_number: paymentMethod.value === 'card' ? cardNumber.value : undefined,
            card_name: paymentMethod.value === 'card' ? cardName.value : undefined,
            card_expiry: paymentMethod.value === 'card' ? cardExpiry.value : undefined,
            card_cvv: paymentMethod.value === 'card' ? cardCvv.value : undefined,
        });

        const nextAction = result?.payment?.next_action;
        if (result?.payment?.method === 'pix' && nextAction?.type === 'pix') {
            pixNextAction.value = nextAction;
            lastPledgeId.value = result?.pledge_id ?? null;
            message.value = '';
            return;
        }

        message.value = t('campaignShow.supportSuccess');
        await fetchCampaign();
    } catch (e) {
        message.value = e?.response?.data?.message ?? t('campaignShow.supportError');
    } finally {
        submitting.value = false;
    }
}

async function generatePixQrCode() {
    const code = pixNextAction.value?.copy_paste;
    if (!code) {
        pixQrCodeDataUrl.value = '';
        return;
    }

    try {
        pixQrCodeDataUrl.value = await QRCode.toDataURL(String(code), {
            width: 220,
            margin: 1,
        });
    } catch {
        pixQrCodeDataUrl.value = '';
    }
}

async function copyPixCode() {
    const code = pixNextAction.value?.copy_paste;
    if (!code) return;
    try {
        await navigator.clipboard.writeText(String(code));
    } catch {
        // ignore
    }
}

async function confirmPix() {
    const id = lastPledgeId.value;
    if (!id) return;

    confirmingPix.value = true;
    try {
        await apiPost(`/api/pledges/${id}/confirm`, {});
        pixNextAction.value = null;
        lastPledgeId.value = null;
        message.value = t('campaignShow.pixConfirmed');
        await fetchCampaign();
    } catch (e) {
        message.value = e?.response?.data?.message ?? t('campaignShow.supportError');
    } finally {
        confirmingPix.value = false;
    }
}

watch(
    () => pixNextAction.value?.copy_paste,
    () => {
        generatePixQrCode();
    }
);

watch(
    () => paymentMethod.value,
    (method) => {
        if (method !== 'pix') {
            pixNextAction.value = null;
            pixQrCodeDataUrl.value = '';
            lastPledgeId.value = null;
        }
    }
);

onMounted(fetchCampaign);
watch(() => props.slug, fetchCampaign);
</script>

<style scoped>
.campaign-story :deep(p) {
    margin-bottom: 0.75rem;
}

.campaign-story :deep(h1),
.campaign-story :deep(h2),
.campaign-story :deep(h3),
.campaign-story :deep(h4) {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.campaign-story :deep(ul),
.campaign-story :deep(ol) {
    margin-bottom: 0.75rem;
}

.campaign-story :deep(a) {
    text-decoration: none;
}

.campaign-story :deep(a:hover) {
    text-decoration: underline;
}
</style>
