<template>
    <div class="container">
        <div class="py-4 py-md-5">
            <div class="text-uppercase text-muted small">{{ t('pages.sectionLabel') }}</div>
            <h1 class="display-6 mb-2">{{ title }}</h1>
            <p v-if="lead" class="lead text-muted mb-4">{{ lead }}</p>

            <div class="card">
                <div class="card-body">
                    <p v-for="(p, idx) in paragraphs" :key="idx" class="mb-3 text-muted">
                        {{ p }}
                    </p>

                    <p v-if="paragraphs.length === 0" class="mb-0 text-muted">{{ t('pages.contentSoon') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';

const route = useRoute();
const { t } = useI18n({ useScope: 'global' });

const title = computed(() => {
    if (route.meta?.titleKey) return String(t(route.meta.titleKey));
    return String(route.meta?.title || t('pages.fallbackTitle'));
});

const lead = computed(() => {
    if (route.meta?.leadKey) return String(t(route.meta.leadKey));
    return String(route.meta?.lead || '');
});

const paragraphs = computed(() => {
    if (Array.isArray(route.meta?.contentKeys)) {
        return route.meta.contentKeys.map((k) => String(t(k)));
    }
    if (route.meta?.contentKey) {
        return [String(t(route.meta.contentKey))];
    }

    const content = route.meta?.content;
    if (Array.isArray(content)) return content.map((x) => String(x));
    if (typeof content === 'string' && content.trim()) return [content.trim()];
    return [];
});
</script>
