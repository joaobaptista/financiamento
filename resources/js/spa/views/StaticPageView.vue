<template>
    <div class="container">
        <div class="py-4 py-md-5">
            <div class="text-uppercase text-muted small">Institucional</div>
            <h1 class="display-6 mb-2">{{ title }}</h1>
            <p v-if="lead" class="lead text-muted mb-4">{{ lead }}</p>

            <div class="card">
                <div class="card-body">
                    <p v-for="(p, idx) in paragraphs" :key="idx" class="mb-3 text-muted">
                        {{ p }}
                    </p>

                    <p v-if="paragraphs.length === 0" class="mb-0 text-muted">Conteúdo em breve.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const title = computed(() => String(route.meta?.title || 'Página'));
const lead = computed(() => String(route.meta?.lead || ''));

const paragraphs = computed(() => {
    const content = route.meta?.content;
    if (Array.isArray(content)) return content.map((x) => String(x));
    if (typeof content === 'string' && content.trim()) return [content.trim()];
    return [];
});
</script>
