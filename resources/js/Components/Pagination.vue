<template>
    <div v-if="links.length > 3" class="mt-4 flex justify-center">
        <nav>
            <ul class="inline-flex items-center">
                <li v-for="link in links" :key="link.label" class="mx-1">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        preserve-scroll
                        class="flex h-10 w-10 items-center justify-center rounded-md border border-gray-400 text-gray-700 transition hover:bg-blue-500"
                        :class="{
                            'bg-blue-300 text-black hover:bg-blue-500':
                                link.active,
                        }"
                    >
                        <span v-if="link.label === 'pagination.previous'"
                            >◀︎</span
                        >
                        <span v-else-if="link.label === 'pagination.next'"
                            >▶︎</span
                        >
                        <!-- <span v-else v-html="link.label" /> -->
                        <span v-else>
                            {{ decodeHtml(link.label) }}
                        </span>
                    </Link>

                    <span
                        v-else
                        class="flex h-10 w-10 items-center justify-center rounded-md border border-gray-400 bg-white text-gray-700"
                    >
                        <span v-if="link.label === 'pagination.previous'"
                            >◀︎</span
                        >
                        <span v-else-if="link.label === 'pagination.next'"
                            >▶︎</span
                        >
                        <!-- <span v-else v-html="link.label" /> -->
                        <span v-else> {{ decodeHtml(link.label) }} </span>.
                    </span>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    // links: Array,
    links: {
        type: Array,
        default: () => [],
    },
    // details: Object,
    details: {
        type: Object,
        default: () => ({}),
    },
});
</script>
