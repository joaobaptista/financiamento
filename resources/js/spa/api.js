export async function apiGet(url) {
    const response = await window.axios.get(url);
    return response.data;
}

export async function apiPost(url, payload) {
    const response = await window.axios.post(url, payload);
    return response.data;
}

export async function apiPut(url, payload) {
    const response = await window.axios.put(url, payload);
    return response.data;
}
