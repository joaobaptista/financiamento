export async function apiGet(url) {
    const response = await window.axios.get(url);
    return response.data;
}

export function apiErrorMessage(error, fallbackMessage = 'Erro inesperado.') {
    const data = error?.response?.data;
    if (!data) {
        return fallbackMessage;
    }

    const message = typeof data.message === 'string' ? data.message.trim() : '';
    const errors = data?.errors;

    if (errors && typeof errors === 'object') {
        for (const key of Object.keys(errors)) {
            const entry = errors[key];
            if (Array.isArray(entry) && entry.length > 0 && String(entry[0]).trim() !== '') {
                return String(entry[0]);
            }
            if (typeof entry === 'string' && entry.trim() !== '') {
                return entry;
            }
        }
    }

    if (message && message.toLowerCase() !== 'the given data was invalid.') {
        return message;
    }

    return message || fallbackMessage;
}

export async function apiPost(url, payload) {
    const response = await window.axios.post(url, payload);
    return response.data;
}

export async function apiPut(url, payload) {
    const response = await window.axios.put(url, payload);
    return response.data;
}

export async function apiDelete(url) {
    const response = await window.axios.delete(url);
    return response.data;
}
