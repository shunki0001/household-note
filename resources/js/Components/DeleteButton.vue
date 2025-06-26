<script setup>
import {router} from '@inertiajs/vue3'

const props = defineProps({
    expenseId: Number,
});

const destroy = () => {
    if(confirm('本当に削除しますか？')) {
        router.delete(route('expenses.destroy', props.expenseId));
    }
};
</script>

<template>
    <button @click="destroy" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
        削除
    </button>
</template>
