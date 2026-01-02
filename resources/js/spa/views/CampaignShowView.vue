<template>
    <div>
        <div v-if="loading" class="container py-4 text-muted">{{ t('common.loading') }}</div>

        <div v-else-if="campaign">
            <!-- Hero (padrão Origo) -->
            <div class="bg-light border-bottom">
                <div class="container py-4 py-md-5">
                    <div class="text-uppercase text-muted small">{{ t('campaignShow.sectionLabel') }}</div>
                    <h1 class="display-6 mb-2">{{ campaign.title }}</h1>
                    <div class="text-muted d-flex flex-wrap align-items-center gap-2">
                        <div>
                            {{ t('campaignShow.by') }} <strong>{{ campaign.creator_page?.name ?? campaign.user?.name ?? '—' }}</strong>
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
                                        <div class="border rounded p-3 mb-3">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                <div class="fw-semibold">{{ t('campaignShow.supporterProfileTitle') }}</div>
                                                <button
                                                    v-if="supporterProfileReady && !supporterProfileEditing"
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    @click="supporterProfileEditing = true"
                                                >
                                                    {{ t('common.edit') }}
                                                </button>
                                            </div>

                                            <div v-if="!supporterProfileReady" class="alert alert-warning mb-3" role="alert">
                                                {{ t('campaignShow.supporterProfileRequired') }}
                                            </div>

                                            <div v-if="supporterProfileReady && !supporterProfileEditing" class="small text-muted">
                                                <div><span class="fw-semibold">{{ t('campaignShow.postalCodeLabel') }}:</span> {{ supporterPostalCode }}</div>
                                                <div>
                                                    <span class="fw-semibold">{{ t('campaignShow.addressStreetLabel') }}:</span>
                                                    {{ supporterAddressStreet }}, {{ supporterAddressNumber }}
                                                    <template v-if="supporterAddressComplement"> — {{ supporterAddressComplement }}</template>
                                                </div>
                                                <div>
                                                    <span class="fw-semibold">{{ t('campaignShow.addressNeighborhoodLabel') }}:</span>
                                                    {{ supporterAddressNeighborhood }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold">{{ t('campaignShow.addressCityLabel') }}:</span>
                                                    {{ supporterAddressCity }}
                                                    <template v-if="supporterAddressState">/{{ supporterAddressState }}</template>
                                                </div>
                                                <div><span class="fw-semibold">{{ t('campaignShow.phoneLabel') }}:</span> {{ supporterPhone }}</div>
                                            </div>

                                            <div v-else class="row g-2">
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label mb-1">{{ t('campaignShow.postalCodeLabel') }}</label>
                                                    <div class="input-group">
                                                        <input
                                                            v-model="supporterPostalCode"
                                                            type="text"
                                                            class="form-control"
                                                            inputmode="numeric"
                                                            autocomplete="postal-code"
                                                            :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                        />
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-secondary"
                                                            :disabled="submitting || !isCampaignOpen || supporterProfileSaving || !supporterPostalCode"
                                                            @click="lookupCep"
                                                        >
                                                            {{ t('campaignShow.lookupCep') }}
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label mb-1">{{ t('campaignShow.addressStreetLabel') }}</label>
                                                    <input
                                                        v-model="supporterAddressStreet"
                                                        type="text"
                                                        class="form-control"
                                                        autocomplete="address-line1"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    />
                                                </div>

                                                <div class="col-6">
                                                    <label class="form-label mb-1">{{ t('campaignShow.addressNumberLabel') }}</label>
                                                    <input
                                                        v-model="supporterAddressNumber"
                                                        type="text"
                                                        class="form-control"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    />
                                                </div>

                                                <div class="col-6">
                                                    <label class="form-label mb-1">{{ t('campaignShow.addressComplementLabel') }}</label>
                                                    <input
                                                        v-model="supporterAddressComplement"
                                                        type="text"
                                                        class="form-control"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    />
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label class="form-label mb-1">{{ t('campaignShow.addressNeighborhoodLabel') }}</label>
                                                    <input
                                                        v-model="supporterAddressNeighborhood"
                                                        type="text"
                                                        class="form-control"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    />
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label class="form-label mb-1">{{ t('campaignShow.addressCityLabel') }}</label>
                                                    <input
                                                        v-model="supporterAddressCity"
                                                        type="text"
                                                        class="form-control"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    />
                                                </div>

                                                <div class="col-12 col-md-2">
                                                    <label class="form-label mb-1">{{ t('campaignShow.addressStateLabel') }}</label>
                                                    <input
                                                        v-model="supporterAddressState"
                                                        type="text"
                                                        class="form-control"
                                                        maxlength="2"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    />
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label mb-1">{{ t('campaignShow.phoneLabel') }}</label>
                                                    <input
                                                        v-model="supporterPhone"
                                                        type="text"
                                                        class="form-control"
                                                        inputmode="tel"
                                                        autocomplete="tel"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                    />
                                                </div>

                                                <div class="col-12">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-primary w-100"
                                                        :disabled="submitting || !isCampaignOpen || supporterProfileSaving"
                                                        @click="saveSupporterProfile"
                                                    >
                                                        {{ supporterProfileSaving ? t('common.ellipsis') : t('campaignShow.saveSupporterProfile') }}
                                                    </button>

                                                    <div v-if="supporterProfileMessage" class="text-muted small mt-2">
                                                        {{ supporterProfileMessage }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
                                                    :disabled="submitting || !isCampaignOpen || supporterProfileSaving || !supporterProfileReady || supporterProfileEditing"
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
                                                    :disabled="submitting || !isCampaignOpen || supporterProfileSaving || !supporterProfileReady || supporterProfileEditing"
                                                />
                                                <label class="form-check-label" for="pay-card">{{ t('campaignShow.paymentCard') }}</label>
                                            </div>
                                        </div>

                                        <!-- Campos de identificação para Pix -->
                                        <div v-if="paymentMethod === 'pix' && !pixNextAction?.copy_paste" class="alert alert-light border mb-3" role="alert">
                                            <div class="small mb-2">{{ t('campaignShow.pixIdentificationInfo') || 'Informe seu CPF ou CNPJ para gerar o Pix' }}</div>
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label class="form-label mb-1">{{ t('campaignShow.pixIdentificationTypeLabel') || 'Tipo de Documento' }}</label>
                                                    <select
                                                        v-model="pixIdentificationType"
                                                        class="form-select"
                                                        :disabled="submitting || !isCampaignOpen"
                                                    >
                                                        <option value="CPF">CPF</option>
                                                        <option value="CNPJ">CNPJ</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label mb-1">{{ t('campaignShow.pixIdentificationNumberLabel') || 'Número do Documento' }}</label>
                                                    <input
                                                        v-model="pixIdentificationNumber"
                                                        type="text"
                                                        class="form-control"
                                                        inputmode="numeric"
                                                        autocomplete="off"
                                                        :placeholder="pixIdentificationType === 'CPF' ? '000.000.000-00' : '00.000.000/0000-00'"
                                                        :disabled="submitting || !isCampaignOpen"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="pixNextAction?.copy_paste" class="alert alert-info mb-3" role="alert">
                                            <div class="small">
                                                {{ pixNextAction.confirmable === false ? t('campaignShow.pixWaitingConfirmation') : t('campaignShow.pixInstructions') }}
                                            </div>
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
                                                        v-if="pixNextAction.confirmable !== false"
                                                        type="button"
                                                        class="btn btn-primary btn-sm"
                                                        :disabled="confirmingPix"
                                                        @click="confirmPix"
                                                    >
                                                        {{ confirmingPix ? t('common.ellipsis') : t('campaignShow.pixConfirm') }}
                                                    </button>
                                                    <span v-else class="small text-muted align-self-center">
                                                        {{ pollingPix ? t('campaignShow.pixPolling') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="paymentMethod === 'card'" class="alert alert-light border mb-3" role="alert">
                                            <div class="small mb-2">{{ t('campaignShow.cardInfo') }}</div>
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
                                                <div class="col-12">
                                                    <label class="form-label mb-1">{{ t('campaignShow.cardCpfLabel') }}</label>
                                                    <input
                                                        v-model="cardCpf"
                                                        type="text"
                                                        class="form-control"
                                                        inputmode="numeric"
                                                        autocomplete="off"
                                                        placeholder="000.000.000-00"
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
                                                    <div class="input-group">
                                                        <input
                                                            v-model="cardCvv"
                                                            :type="showCardCvv ? 'text' : 'password'"
                                                            class="form-control"
                                                            autocomplete="cc-csc"
                                                            inputmode="numeric"
                                                            :disabled="submitting || !isCampaignOpen"
                                                        />
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-secondary"
                                                            :disabled="submitting || !isCampaignOpen"
                                                            :aria-label="showCardCvv ? 'Ocultar' : 'Mostrar'"
                                                            @click="showCardCvv = !showCardCvv"
                                                        >
                                                            <i :class="showCardCvv ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                                        </button>
                                                    </div>
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
                                            :disabled="submitting || !isCampaignOpen || supporterProfileSaving || !supporterProfileReady || supporterProfileEditing"
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
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiDelete, apiGet, apiPost } from '../api';
import { absoluteUrl, applyCampaignSeo } from '../seo';
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import QRCode from 'qrcode';
import { fetchIssuerId, fetchPaymentMethodIdByBin } from '../mercadopago';

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
const pollingPix = ref(false);
let pixPollIntervalId = null;

const cardNumber = ref('');
const cardName = ref('');
const cardCpf = ref('');
const cardExpiry = ref('');
const cardCvv = ref('');
const showCardCvv = ref(false);
const cardInstallments = ref(1);

// Campos para identificação no Pix
const pixIdentificationType = ref('CPF');
const pixIdentificationNumber = ref('');

let mercadoPagoInstance = null;
let mercadoPagoSdkPromise = null;

function getMercadoPagoPublicKey() {
    return String(import.meta.env?.VITE_MERCADOPAGO_PUBLIC_KEY || '').trim();
}

function loadMercadoPagoSdk() {
    if (mercadoPagoSdkPromise) return mercadoPagoSdkPromise;

    mercadoPagoSdkPromise = new Promise((resolve, reject) => {
        if (typeof window !== 'undefined' && window.MercadoPago) {
            resolve(window.MercadoPago);
            return;
        }

        const existing = document.querySelector('script[data-mp-sdk="v2"]');
        if (existing) {
            existing.addEventListener('load', () => resolve(window.MercadoPago));
            existing.addEventListener('error', reject);
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://sdk.mercadopago.com/js/v2';
        script.async = true;
        script.defer = true;
        script.dataset.mpSdk = 'v2';
        script.onload = () => resolve(window.MercadoPago);
        script.onerror = reject;
        document.head.appendChild(script);
    });

    return mercadoPagoSdkPromise;
}

async function getMercadoPago() {
    const publicKey = getMercadoPagoPublicKey();
    if (!publicKey) {
        throw new Error('Mercado Pago não configurado.');
    }

    if (mercadoPagoInstance) return mercadoPagoInstance;

    const MercadoPago = await loadMercadoPagoSdk();
    mercadoPagoInstance = new MercadoPago(publicKey, { locale: 'pt-BR' });
    return mercadoPagoInstance;
}

function onlyDigits(value) {
    return String(value || '').replace(/\D+/g, '');
}

function parseExpiry(value) {
    const raw = String(value || '').trim();
    const match = raw.match(/^(\d{1,2})\s*\/\s*(\d{2,4})$/);
    if (!match) return null;
    const month = Number(match[1]);
    const yearRaw = match[2];
    if (!month || month < 1 || month > 12) return null;
    let year = Number(yearRaw);
    if (yearRaw.length === 2) year += 2000;
    if (!year || year < 2000) return null;
    return { month, year };
}

async function createMercadoPagoCardToken() {
    const mp = await getMercadoPago();
    const number = onlyDigits(cardNumber.value);
    const cvv = onlyDigits(cardCvv.value);
    const cpf = onlyDigits(cardCpf.value);
    const expiry = parseExpiry(cardExpiry.value);

    if (!number || !cvv || !expiry || !String(cardName.value || '').trim()) {
        throw new Error('Dados do cartão incompletos.');
    }

    if (cpf.length !== 11) {
        throw new Error('CPF inválido.');
    }

    const tokenResult = await mp.createCardToken({
        cardNumber: number,
        cardholderName: String(cardName.value || '').trim(),
        cardExpirationMonth: String(expiry.month).padStart(2, '0'),
        cardExpirationYear: String(expiry.year),
        securityCode: cvv,
        identificationType: 'CPF',
        identificationNumber: cpf,
    });

    const token = tokenResult?.id || tokenResult?.token || null;
    if (!token) {
        throw new Error('Não foi possível tokenizar o cartão.');
    }

    const publicKey = getMercadoPagoPublicKey();
    const bin = number.slice(0, 6);

    let paymentMethodId = null;
    let issuerId = null;
    try {
        if (bin.length === 6 && publicKey) {
            paymentMethodId = await fetchPaymentMethodIdByBin({ publicKey, bin });
            if (paymentMethodId) {
                issuerId = await fetchIssuerId({ publicKey, paymentMethodId, bin });
            }
        }
    } catch {
        // ignore
    }

    if (!paymentMethodId) {
        throw new Error('Não foi possível identificar a bandeira do cartão.');
    }

    return {
        token,
        paymentMethodId,
        issuerId,
        identification: { type: 'CPF', number: cpf },
    };
}

const supporterProfileReady = ref(false);
const supporterProfileEditing = ref(true);
const supporterProfileSaving = ref(false);
const supporterProfileMessage = ref('');
const supporterPostalCode = ref('');
const supporterAddressStreet = ref('');
const supporterAddressNumber = ref('');
const supporterAddressComplement = ref('');
const supporterAddressNeighborhood = ref('');
const supporterAddressCity = ref('');
const supporterAddressState = ref('');
const supporterPhone = ref('');

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
        if (props.user) {
            await fetchSupporterProfile();
        } else {
            supporterProfileReady.value = true;
            supporterProfileEditing.value = false;
        }
    } catch (e) {
        campaign.value = null;
        message.value = e?.response?.data?.message ?? '';
    } finally {
        loading.value = false;
    }
}

function isSupporterProfileComplete() {
    return (
        String(supporterPostalCode.value || '').trim() !== '' &&
        String(supporterAddressStreet.value || '').trim() !== '' &&
        String(supporterAddressNumber.value || '').trim() !== '' &&
        String(supporterAddressNeighborhood.value || '').trim() !== '' &&
        String(supporterAddressCity.value || '').trim() !== '' &&
        String(supporterAddressState.value || '').trim().length === 2 &&
        String(supporterPhone.value || '').trim() !== ''
    );
}

async function fetchSupporterProfile() {
    supporterProfileMessage.value = '';
    try {
        const data = await apiGet('/api/me/supporter-profile');
        supporterPostalCode.value = String(data?.postal_code ?? '');
        supporterAddressStreet.value = String(data?.address_street ?? '');
        supporterAddressNumber.value = String(data?.address_number ?? '');
        supporterAddressComplement.value = String(data?.address_complement ?? '');
        supporterAddressNeighborhood.value = String(data?.address_neighborhood ?? '');
        supporterAddressCity.value = String(data?.address_city ?? '');
        supporterAddressState.value = String(data?.address_state ?? '');
        supporterPhone.value = String(data?.phone ?? '');
    } catch (e) {
        supporterProfileReady.value = false;
        supporterProfileEditing.value = true;
        supporterProfileMessage.value = e?.response?.data?.message ?? t('errors.loadFailed');
        return;
    }

    const complete = isSupporterProfileComplete();
    supporterProfileReady.value = complete;
    supporterProfileEditing.value = !complete;
}

async function lookupCep() {
    supporterProfileMessage.value = '';
    const cep = String(supporterPostalCode.value || '').trim();
    if (!cep) return;

    try {
        const data = await apiGet(`/api/cep/${encodeURIComponent(cep)}`);
        if (data?.postal_code) supporterPostalCode.value = String(data.postal_code);
        if (data?.address_street) supporterAddressStreet.value = String(data.address_street);
        if (data?.address_neighborhood) supporterAddressNeighborhood.value = String(data.address_neighborhood);
        if (data?.address_city) supporterAddressCity.value = String(data.address_city);
        if (data?.address_state) supporterAddressState.value = String(data.address_state);
    } catch (e) {
        supporterProfileMessage.value = e?.response?.data?.message ?? '';
    }
}

async function saveSupporterProfile() {
    supporterProfileMessage.value = '';
    supporterProfileSaving.value = true;

    try {
        await apiPost('/api/me/supporter-profile', {
            postal_code: String(supporterPostalCode.value || '').trim(),
            address_street: String(supporterAddressStreet.value || '').trim(),
            address_number: String(supporterAddressNumber.value || '').trim(),
            address_complement: String(supporterAddressComplement.value || '').trim(),
            address_neighborhood: String(supporterAddressNeighborhood.value || '').trim(),
            address_city: String(supporterAddressCity.value || '').trim(),
            address_state: String(supporterAddressState.value || '').trim().toUpperCase(),
            phone: String(supporterPhone.value || '').trim(),
        });

        supporterProfileReady.value = isSupporterProfileComplete();
        supporterProfileEditing.value = !supporterProfileReady.value;
        if (supporterProfileReady.value) supporterProfileMessage.value = t('campaignShow.supporterProfileSaved');
    } catch (e) {
        supporterProfileMessage.value = e?.response?.data?.message ?? '';
    } finally {
        supporterProfileSaving.value = false;
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

        let cardToken = undefined;
        let paymentMethodId = undefined;
        let issuerId = undefined;
        let payerIdentificationType = undefined;
        let payerIdentificationNumber = undefined;

        if (paymentMethod.value === 'card') {
            try {
                const cardData = await createMercadoPagoCardToken();
                cardToken = cardData.token;
                paymentMethodId = cardData.paymentMethodId || undefined;
                issuerId = cardData.issuerId || undefined;
                payerIdentificationType = cardData.identification.type;
                payerIdentificationNumber = cardData.identification.number;
            } catch (e) {
                message.value = e?.message || t('campaignShow.supportError');
                submitting.value = false;
                return;
            }
        } else if (paymentMethod.value === 'pix') {
            // Validar e enviar identificação para Pix
            const pixDocNumber = onlyDigits(pixIdentificationNumber.value);
            const expectedLength = pixIdentificationType.value === 'CPF' ? 11 : 14;
            
            if (pixDocNumber.length !== expectedLength) {
                message.value = pixIdentificationType.value === 'CPF' 
                    ? 'CPF inválido. Deve conter 11 dígitos.' 
                    : 'CNPJ inválido. Deve conter 14 dígitos.';
                submitting.value = false;
                return;
            }
            
            payerIdentificationType = pixIdentificationType.value;
            payerIdentificationNumber = pixDocNumber;
        }

        const result = await apiPost('/api/pledges', {
            campaign_id: campaign.value.id,
            amount: amount.value,
            reward_id: rewardId.value,
            payment_method: paymentMethod.value,
            // Card: send only a token (mock for now). Never send raw card data to the backend.
            card_token: cardToken,
            installments: paymentMethod.value === 'card' ? Number(cardInstallments.value || 1) : undefined,
            payment_method_id: paymentMethodId,
            issuer_id: issuerId,
            payer_identification_type: payerIdentificationType,
            payer_identification_number: payerIdentificationNumber,
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

function stopPixPolling() {
    if (pixPollIntervalId) {
        clearInterval(pixPollIntervalId);
        pixPollIntervalId = null;
    }
    pollingPix.value = false;
}

async function pollPixStatusOnce() {
    const id = lastPledgeId.value;
    if (!id) return;

    try {
        const data = await apiGet(`/api/pledges/${id}`);
        const status = String(data?.status || '').toLowerCase();

        if (status === 'paid') {
            stopPixPolling();
            pixNextAction.value = null;
            lastPledgeId.value = null;
            message.value = t('campaignShow.pixConfirmed');
            await fetchCampaign();
            return;
        }

        if (status === 'canceled' || status === 'refunded') {
            stopPixPolling();
            pixNextAction.value = null;
            lastPledgeId.value = null;
            message.value = t('campaignShow.supportError');
        }
    } catch {
        // ignore transient errors
    }
}

function startPixPolling() {
    if (pixPollIntervalId) return;
    if (pixNextAction.value?.confirmable !== false) return;
    if (!lastPledgeId.value) return;

    pollingPix.value = true;
    pollPixStatusOnce();
    pixPollIntervalId = setInterval(() => {
        pollPixStatusOnce();
    }, 5000);
}

watch(
    () => pixNextAction.value?.copy_paste,
    () => {
        generatePixQrCode();
    }
);

watch(
    () => [lastPledgeId.value, pixNextAction.value?.confirmable],
    () => {
        if (pixNextAction.value?.confirmable === false && lastPledgeId.value) {
            startPixPolling();
        } else {
            stopPixPolling();
        }
    },
    { immediate: true }
);

onBeforeUnmount(() => {
    stopPixPolling();
});

watch(
    () => paymentMethod.value,
    (method) => {
        if (method !== 'pix') {
            pixNextAction.value = null;
            pixQrCodeDataUrl.value = '';
            lastPledgeId.value = null;
        } else {
            // Limpar campos de Pix ao selecionar
            pixIdentificationType.value = 'CPF';
            pixIdentificationNumber.value = '';
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
