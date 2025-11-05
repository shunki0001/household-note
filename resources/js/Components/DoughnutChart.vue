<script setup>
import { ref, onMounted, watch } from 'vue'
import { Doughnut } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    ArcElement
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { CHART_ANIMATION_DURATION, CHART_BORDER_COLOR, CHART_BORDER_WIDTH, DOUGHNUT_CUTOUT, FALLBACK_COLOR, LABEL_FONT_SIZE_DESKTOP, LABEL_TEXT_COLOR } from '@/config/constants'

// Chart.jsのプラグイン登録
ChartJS.register(Title, Tooltip, Legend, ArcElement, ChartDataLabels)

// propsを定義
const props = defineProps({
    label: { type: String, default: 'カテゴリー別支出合計' },
    apiUrl: { type: String, default: 'api/chart-data/doughnut' },
    chartData: Object,
    chartOptions: Object,
    refreshKey: { type: Number, default: 0 },
})

// labelsはカテゴリーを格納
// datasetsはカテゴリー毎の合計値を格納
const chartData = ref({ labels: [], datasets: [] })
const chartOptions = ref(null) // 最初はnullでOK

// Chartオプション生成関数
function createChartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        cutout: DOUGHNUT_CUTOUT,
        plugins: {
            datalabels: {
                color: LABEL_TEXT_COLOR,
                font: { weight: 'bold', size: LABEL_FONT_SIZE_DESKTOP },
                formatter: (value, context) => {
                    // 💡 context.dataset.data を参照（Chart.js公式推奨）
                    const data = context.dataset?.data || [];
                    const total = data.reduce((a, b) => a + b, 0) || 1; // 万が一0でもエラー防止
                    if (!total || isNaN(total)) return ' ';

                    const percentage = (value / total * 100);
                    // 0%の場合は非表示にする
                    if(percentage === 0) return '';

                    // 小数1桁で表示
                    const formatted = percentage.toFixed(1);
                    return `${formatted}%`;
                }
            },
            legend: {
                display: true,
                position: 'right'
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const label = context.label || '';
                        const value = context.parsed;
                        return `${label}: ¥${value.toLocaleString()}`;
                    }
                }
            }
        },
        animation: {
            animateRotate: true,
            animateScale: true,
            duration: CHART_ANIMATION_DURATION
        }
    }
}

// refreshKeyの変更を監視して再取得
watch(() => props.refreshKey, async () => {
    console.log('refreshKey changed:', props.refreshKey);
    await fetchChartData();
})

// API取得ロジックを関数化
async function fetchChartData() {
    try {
        const response = await fetch(props.apiUrl)
        const json = await response.json()

        console.log('API response:', json);

        const totals = json.totals.map(t => Number(t))
        const colors = json.colors && json.colors.length > 0
            ? json.colors
            : [FALLBACK_COLOR] // fallback 色未設定時

        chartData.value = {
            labels: json.labels,
            datasets: [
                {
                    labels: props.label,
                    data: totals,
                    backgroundColor: colors.slice(0, json.labels.length),
                    borderColor: CHART_BORDER_COLOR,
                    borderWidth: CHART_BORDER_WIDTH,
                }
            ]
        }
        chartOptions.value = createChartOptions()
    } catch (error) {
        console.error('データ取得エラー:', error)
    }
}

onMounted(() => {
    fetchChartData()
})

</script>

<template>
    <div style="height: 400px;">
        <!-- オプションが作成された時のみ描画 -->
        <Doughnut v-if="chartOptions" :data="chartData" :options="chartOptions" />
    </div>
</template>
