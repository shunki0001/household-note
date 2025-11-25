<!-- SPA構成では動作しない -->
<!-- 使うにはSPA構成に対応したコードに修正が必要 -->
<script setup>
import Swal from 'sweetalert2';
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

watch(
    () => page.props.flash?.message,
    (message) => {
        if (message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
        }
    },
);
</script>

<template>
    <span class="sr-only" aria-hidden="true"></span>
</template>
