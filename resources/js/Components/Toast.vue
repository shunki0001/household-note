<script setup>
import Swal from 'sweetalert2';
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

watch(
    () => page.props.flash?.message,
    (message) => {
        if(message) {
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
    }
);

</script>

<template>
    <!-- UIは無し -->
</template>
