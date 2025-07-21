<script setup>
import Swal from 'sweetalert2';
import { defineEmits } from 'vue';
import axios from 'axios';

const props = defineProps({
    expenseId: Number,
});

const emit = defineEmits(['deleted']); // 削除通知イベント

const handleDelete = async () => {
    try {
        const confirmed = await Swal.fire({
            title: '本当に削除しますか？',
            text: 'この操作は取り消せません!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '削除する',
            cancelButtonText: 'キャンセル',
        })

        if (confirmed.isConfirmed) {
            await axios.delete(`/expenses/${props.expenseId}`)
            emit('deleted')
            Swal.fire('削除しました', '', 'success')
        }
    } catch (e) {
        Swal.fire('エラー', '削除に失敗しました', 'error')
    }
}


</script>

<template>

    <button @click="handleDelete" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
        削除
    </button>
</template>
