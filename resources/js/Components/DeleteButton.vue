<script setup>
import Swal from 'sweetalert2';
import { defineEmits } from 'vue';
import axios from 'axios';

const props = defineProps({
    // transactionId: Number,
    transactionId: {
        type: [Number, String],
        required: true,
    },
    // transactionType: String,
    transactionType: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['deleted']); // 削除通知イベント

const deleteTransaction = async () => {
    try {
        const confirmed = await Swal.fire({
            title: '本当に削除しますか？',
            text: 'この操作は取り消せません',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '削除する',
            cancelButtonText: 'キャンセル',
        });

        if (confirmed.isConfirmed) {
            await axios.delete(
                route('transaction.destroy', {
                    type: props.transactionType,
                    id: props.transactionId,
                }),
            );
            emit('deleted');
            Swal.fire('削除しました', '', 'success');
        }
    } catch (e) {
        Swal.fire('エラー', '削除に失敗しました', 'error');
    }
};
</script>

<template>
    <button
        class="inline-block rounded bg-red-500 px-4 py-2 text-sm text-white hover:bg-red-600"
        @click="deleteTransaction"
    >
        削除
    </button>
</template>
