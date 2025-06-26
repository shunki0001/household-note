<script setup>
import Swal from 'sweetalert2';
import {router} from '@inertiajs/vue3'

const props = defineProps({
    expenseId: Number,
});

const destroy = () => {
    Swal.fire({
        title: '本当に削除しますか？',
        text: 'この操作は取り消せません!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '削除する',
        cancelButtonText: 'キャンセル',
    }).then((result) => {
        if(result.isConfirmed) {
            router.delete(route('expenses.destroy', props.expenseId), {
                onSuccess: () => {
                    // ページ更新
                    router.reload();

                    // 削除完了通知
                    Swal.fire('削除しました', 'データは正常に削除されました', 'success');
                }
            });
        }
    });
    // if(confirm('本当に削除しますか？')) {
    //     router.delete(route('expenses.destroy', props.expenseId), {
    //         onSuccess: () => {
    //             // 削除成功後にページ全体をリロード
    //             router.reload();
    //         }
    //     });
    // }
};
</script>

<template>
    <button @click="destroy" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
        削除
    </button>
</template>
