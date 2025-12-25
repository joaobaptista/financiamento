// Minimal Mercado Pago SDK loader + helpers (client-side)
// Uses https://sdk.mercadopago.com/js/v2 loaded on demand.

let sdkLoadingPromise = null;

export function loadMercadoPagoSdk() {
    if (globalThis.MercadoPago) return Promise.resolve(globalThis.MercadoPago);

    if (sdkLoadingPromise) return sdkLoadingPromise;

    sdkLoadingPromise = new Promise((resolve, reject) => {
        const existing = document.querySelector('script[data-mercadopago-sdk="v2"]');
        if (existing) {
            existing.addEventListener('load', () => resolve(globalThis.MercadoPago));
            existing.addEventListener('error', reject);
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://sdk.mercadopago.com/js/v2';
        script.async = true;
        script.defer = true;
        script.dataset.mercadopagoSdk = 'v2';
        script.onload = () => resolve(globalThis.MercadoPago);
        script.onerror = (e) => reject(e);
        document.head.appendChild(script);
    });

    return sdkLoadingPromise;
}

export function normalizeCardNumber(raw) {
    return String(raw || '').replace(/\D+/g, '');
}

export function parseExpiry(raw) {
    const value = String(raw || '').trim();
    // MM/AA, MM/YY, MM/YYYY
    const m = value.match(/^(\d{1,2})\s*\/\s*(\d{2}|\d{4})$/);
    if (!m) return null;

    const month = Number(m[1]);
    let year = Number(m[2]);

    if (!Number.isFinite(month) || month < 1 || month > 12) return null;

    if (String(m[2]).length === 2) {
        // 20YY
        year = 2000 + year;
    }

    return { month, year };
}

export async function createCardToken({
    publicKey,
    cardNumber,
    cardholderName,
    cardExpirationMonth,
    cardExpirationYear,
    securityCode,
    identificationType,
    identificationNumber,
}) {
    if (!publicKey) throw new Error('missing_public_key');

    await loadMercadoPagoSdk();
    const mp = new globalThis.MercadoPago(publicKey);

    const payload = {
        cardNumber,
        cardholderName,
        cardExpirationMonth: String(cardExpirationMonth),
        cardExpirationYear: String(cardExpirationYear),
        securityCode,
    };

    if (identificationType && identificationNumber) {
        payload.identificationType = identificationType;
        payload.identificationNumber = identificationNumber;
    }

    const result = await mp.createCardToken(payload);

    const token = result?.id;
    if (!token) throw new Error('tokenization_failed');

    return token;
}

export async function fetchPaymentMethodIdByBin({ publicKey, bin }) {
    const url = `https://api.mercadopago.com/v1/payment_methods/search?bin=${encodeURIComponent(bin)}&public_key=${encodeURIComponent(publicKey)}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error('payment_methods_failed');
    const data = await res.json();

    const list = Array.isArray(data?.results) ? data.results : (Array.isArray(data) ? data : []);
    const first = list[0];
    const id = first?.id;
    if (!id) return null;
    return String(id);
}

export async function fetchIssuerId({ publicKey, paymentMethodId, bin }) {
    const url = `https://api.mercadopago.com/v1/payment_methods/card_issuers?payment_method_id=${encodeURIComponent(paymentMethodId)}&bin=${encodeURIComponent(bin)}&public_key=${encodeURIComponent(publicKey)}`;
    const res = await fetch(url);
    if (!res.ok) return null;
    const data = await res.json();
    const list = Array.isArray(data) ? data : [];
    const first = list[0];
    const issuerId = first?.id;
    if (!issuerId) return null;
    return String(issuerId);
}

export async function fetchInstallments({ publicKey, amount, bin, paymentMethodId }) {
    const url = `https://api.mercadopago.com/v1/payment_methods/installments?amount=${encodeURIComponent(amount)}&bin=${encodeURIComponent(bin)}&payment_method_id=${encodeURIComponent(paymentMethodId)}&public_key=${encodeURIComponent(publicKey)}`;
    const res = await fetch(url);
    if (!res.ok) return [];
    const data = await res.json();
    const first = Array.isArray(data) ? data[0] : null;
    const costs = Array.isArray(first?.payer_costs) ? first.payer_costs : [];
    return costs.map((c) => ({
        installments: Number(c?.installments ?? 1),
        recommended_message: String(c?.recommended_message ?? ''),
    })).filter((c) => Number.isFinite(c.installments) && c.installments >= 1);
}
