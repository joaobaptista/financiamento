<template>
    <div class="container py-3" style="max-width: 920px">
        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
            <div>
                <div class="text-uppercase text-muted small">{{ t('profile.sectionLabel') }}</div>
                <h1 class="h3 fw-normal mb-0">{{ t('profile.title') }}</h1>
            </div>
        </div>

        <div v-if="!user" class="alert alert-warning" role="alert">
            {{ t('errors.loginRequired') }}
        </div>

        <template v-else>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            <img
                                v-if="previewUrl || user.profile_photo_url"
                                :src="previewUrl || user.profile_photo_url"
                                alt="Avatar"
                                class="rounded-circle border"
                                style="width: 56px; height: 56px; object-fit: cover"
                            />
                            <div
                                v-else
                                class="rounded-circle border bg-light d-flex align-items-center justify-content-center text-muted"
                                style="width: 56px; height: 56px"
                            >
                                <i class="bi bi-person" style="font-size: 1.5rem"></i>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ t('profile.photoTitle') }}</div>
                            <div class="text-muted small">{{ t('profile.photoHelp') }}</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <input
                            type="file"
                            class="form-control"
                            accept="image/*"
                            :disabled="saving"
                            @change="onPhotoSelected"
                        />
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="fw-semibold mb-2">{{ t('profile.basicTitle') }}</div>

                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label mb-1">{{ t('profile.nameLabel') }}</label>
                            <input v-model="name" type="text" class="form-control" :disabled="saving" />
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-1">{{ t('profile.emailLabel') }}</label>
                            <input v-model="email" type="email" class="form-control" :disabled="saving" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" :disabled="saving" @click="saveProfile">
                            {{ saving ? t('common.saving') : t('common.save') }}
                        </button>
                        <span v-if="message" class="text-muted small ms-2">{{ message }}</span>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="fw-semibold mb-2">{{ t('profile.contactTitle') }}</div>

                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1">{{ t('campaignShow.postalCodeLabel') }}</label>
                            <div class="input-group">
                                <input v-model="postalCode" type="text" class="form-control" :disabled="saving" />
                                <button type="button" class="btn btn-outline-secondary" :disabled="saving || !postalCode" @click="lookupCep">
                                    {{ saving ? t('common.ellipsis') : t('campaignShow.lookupCep') }}
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-1">{{ t('campaignShow.addressStreetLabel') }}</label>
                            <input v-model="addressStreet" type="text" class="form-control" :disabled="saving" />
                        </div>

                        <div class="col-6">
                            <label class="form-label mb-1">{{ t('campaignShow.addressNumberLabel') }}</label>
                            <input v-model="addressNumber" type="text" class="form-control" :disabled="saving" />
                        </div>

                        <div class="col-6">
                            <label class="form-label mb-1">{{ t('campaignShow.addressComplementLabel') }}</label>
                            <input v-model="addressComplement" type="text" class="form-control" :disabled="saving" />
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1">{{ t('campaignShow.addressNeighborhoodLabel') }}</label>
                            <input v-model="addressNeighborhood" type="text" class="form-control" :disabled="saving" />
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1">{{ t('campaignShow.addressCityLabel') }}</label>
                            <input v-model="addressCity" type="text" class="form-control" :disabled="saving" />
                        </div>

                        <div class="col-12 col-md-2">
                            <label class="form-label mb-1">{{ t('campaignShow.addressStateLabel') }}</label>
                            <input v-model="addressState" type="text" maxlength="2" class="form-control" :disabled="saving" />
                        </div>

                        <div class="col-12">
                            <label class="form-label mb-1">{{ t('campaignShow.phoneLabel') }}</label>
                            <input v-model="phone" type="text" class="form-control" :disabled="saving" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" :disabled="saving" @click="saveProfile">
                            {{ saving ? t('common.saving') : t('common.save') }}
                        </button>
                        <span v-if="message" class="text-muted small ms-2">{{ message }}</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="fw-semibold mb-2">{{ t('profile.passwordTitle') }}</div>

                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label mb-1">{{ t('profile.currentPasswordLabel') }}</label>
                            <div class="input-group">
                                <input
                                    v-model="currentPassword"
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    class="form-control"
                                    :disabled="passwordSaving"
                                    autocomplete="current-password"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    :disabled="passwordSaving"
                                    :aria-label="showCurrentPassword ? 'Ocultar senha' : 'Mostrar senha'"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                >
                                    <i :class="showCurrentPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1">{{ t('profile.newPasswordLabel') }}</label>
                            <div class="input-group">
                                <input
                                    v-model="newPassword"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    class="form-control"
                                    :disabled="passwordSaving"
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    :disabled="passwordSaving"
                                    :aria-label="showNewPassword ? 'Ocultar senha' : 'Mostrar senha'"
                                    @click="showNewPassword = !showNewPassword"
                                >
                                    <i :class="showNewPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1">{{ t('profile.newPasswordConfirmLabel') }}</label>
                            <div class="input-group">
                                <input
                                    v-model="newPasswordConfirm"
                                    :type="showNewPasswordConfirm ? 'text' : 'password'"
                                    class="form-control"
                                    :disabled="passwordSaving"
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    :disabled="passwordSaving"
                                    :aria-label="showNewPasswordConfirm ? 'Ocultar senha' : 'Mostrar senha'"
                                    @click="showNewPasswordConfirm = !showNewPasswordConfirm"
                                >
                                    <i :class="showNewPasswordConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary" :disabled="passwordSaving" @click="savePassword">
                            {{ passwordSaving ? t('common.saving') : t('profile.updatePassword') }}
                        </button>
                        <span v-if="passwordMessage" class="text-muted small ms-2">{{ passwordMessage }}</span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { apiGet, apiPost } from '../api';

const props = defineProps({
    user: { type: Object, default: null },
});

const emit = defineEmits(['auth-updated', 'flash-success', 'flash-error']);

const { t } = useI18n({ useScope: 'global' });

const name = ref('');
const email = ref('');
const postalCode = ref('');
const addressStreet = ref('');
const addressNumber = ref('');
const addressComplement = ref('');
const addressNeighborhood = ref('');
const addressCity = ref('');
const addressState = ref('');
const phone = ref('');

const selectedPhoto = ref(null);
const previewUrl = ref('');

const saving = ref(false);
const message = ref('');

const currentPassword = ref('');
const newPassword = ref('');
const newPasswordConfirm = ref('');
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showNewPasswordConfirm = ref(false);
const passwordSaving = ref(false);
const passwordMessage = ref('');

function hydrateFromUser(u) {
    name.value = String(u?.name ?? '');
    email.value = String(u?.email ?? '');
    postalCode.value = String(u?.postal_code ?? '');
    addressStreet.value = String(u?.address_street ?? '');
    addressNumber.value = String(u?.address_number ?? '');
    addressComplement.value = String(u?.address_complement ?? '');
    addressNeighborhood.value = String(u?.address_neighborhood ?? '');
    addressCity.value = String(u?.address_city ?? '');
    addressState.value = String(u?.address_state ?? '');
    phone.value = String(u?.phone ?? '');
}

function onPhotoSelected(e) {
    const file = e?.target?.files?.[0];
    selectedPhoto.value = file || null;
    if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = file ? URL.createObjectURL(file) : '';
}

async function lookupCep() {
    message.value = '';
    const cep = String(postalCode.value || '').trim();
    if (!cep) return;

    try {
        const data = await apiGet(`/api/cep/${encodeURIComponent(cep)}`);
        if (data?.postal_code) postalCode.value = String(data.postal_code);
        if (data?.address_street) addressStreet.value = String(data.address_street);
        if (data?.address_neighborhood) addressNeighborhood.value = String(data.address_neighborhood);
        if (data?.address_city) addressCity.value = String(data.address_city);
        if (data?.address_state) addressState.value = String(data.address_state);

        // Auto-save the updated address fields after a successful CEP lookup.
        await saveProfile({ includePhoto: false, silent: true });
    } catch (e) {
        message.value = e?.response?.data?.message ?? t('errors.loadFailed');
    }
}

async function saveProfile(options = {}) {
    if (!props.user) return;

    const includePhoto = options.includePhoto !== false;
    const silent = options.silent === true;

    saving.value = true;
    if (!silent) message.value = '';

    try {
        const fd = new FormData();
        fd.set('name', String(name.value || '').trim());
        fd.set('email', String(email.value || '').trim());
        if (postalCode.value) fd.set('postal_code', String(postalCode.value || '').trim());
        if (addressStreet.value) fd.set('address_street', String(addressStreet.value || '').trim());
        if (addressNumber.value) fd.set('address_number', String(addressNumber.value || '').trim());
        if (addressComplement.value) fd.set('address_complement', String(addressComplement.value || '').trim());
        if (addressNeighborhood.value) fd.set('address_neighborhood', String(addressNeighborhood.value || '').trim());
        if (addressCity.value) fd.set('address_city', String(addressCity.value || '').trim());
        if (addressState.value) fd.set('address_state', String(addressState.value || '').trim());
        if (phone.value) fd.set('phone', String(phone.value || '').trim());
        if (includePhoto && selectedPhoto.value) fd.set('profile_photo', selectedPhoto.value);

        const res = await apiPost('/api/me/profile', fd);

        emit('auth-updated');
        if (!silent) message.value = t('profile.saved');

        if (res?.user) hydrateFromUser(res.user);
        if (includePhoto) selectedPhoto.value = null;
    } catch (e) {
        message.value = e?.response?.data?.message ?? t('errors.saveFailed');
        emit('flash-error', message.value);
    } finally {
        saving.value = false;
    }
}

async function savePassword() {
    passwordSaving.value = true;
    passwordMessage.value = '';

    try {
        await apiPost('/api/me/password', {
            current_password: currentPassword.value,
            password: newPassword.value,
            password_confirmation: newPasswordConfirm.value,
        });

        currentPassword.value = '';
        newPassword.value = '';
        newPasswordConfirm.value = '';
        passwordMessage.value = t('profile.passwordSaved');
        emit('flash-success', passwordMessage.value);
    } catch (e) {
        passwordMessage.value = e?.response?.data?.message ?? t('errors.saveFailed');
        emit('flash-error', passwordMessage.value);
    } finally {
        passwordSaving.value = false;
    }
}

onMounted(() => {
    if (props.user) hydrateFromUser(props.user);
});

watch(
    () => props.user,
    (u) => {
        if (u) hydrateFromUser(u);
    }
);
</script>
