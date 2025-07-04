<script setup>
import { onMounted, ref, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    }
});

const canvasRef = ref(null);
let chartInstance = null;

const renderChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(canvasRef.value, {
        type: 'bar',
        data: props.chartData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: props.chartData.title }
            }
        }
    });
}

onMounted(renderChart);

// データが切り替わったとき再描写
watch(() => props.chartData, renderChart, { deep: true });
</script>

<template>
    <canvas ref="canvasRef"></canvas>
</template>
