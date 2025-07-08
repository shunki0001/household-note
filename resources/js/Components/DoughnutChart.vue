<script setup>
import { ref, onMounted } from 'vue'
import { Doughnut } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    ArcElement
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'

// Chart.jsのプラグイン登録
ChartJS.register(Title, Tooltip, Legend, ArcElement, ChartDataLabels)

// propsを定義
const props = defineProps({
    label: { type: String, default: 'カテゴリー別支出合計' },
    apiUrl: { type: String, default: 'api/chart-data/doughnut' },
    colors: {
        type: Array,
        default: () => [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#66FF66', '#FF66B2', '#C9CBCF', '#FF6666'
        ]
    }
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
        cutout: '60%',
        plugins: {
            datalabels: {
                color: '#333',
                font: { weight: 'bold', size: 12 },
                formatter: (value, context) => {
                    // 💡 context.dataset.data を参照（Chart.js公式推奨）
                    const data = context.dataset?.data || [];
                    const total = data.reduce((a, b) => a + b, 0) || 1; // 万が一0でもエラー防止
                    if (!total || isNaN(total)) return '0%';
                    const percentage = (value / total * 100).toFixed(1);
                    return isNaN(percentage) ? '0%' : `${percentage}%`;
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
            duration: 1500
        }
    }
}

// APIからデータ取得
onMounted(async () => {
    try {
        const response = await fetch(props.apiUrl)
        const json = await response.json()

        // 数値化してから渡す
        const totals = json.totals.map(t => Number(t));

        // ドーナツグラフ用に必ず単一データセットにまとめる
        chartData.value = {
            labels: json.labels,
            datasets: [
                {
                    label: props.label,
                    data: totals, // APIは labels と totals を返すこと
                    backgroundColor: props.colors.slice(0, json.labels.length)
                }
            ]
        }

        // オプションをデータ取得後に作成
        chartOptions.value = createChartOptions()

    } catch (error) {
        console.error('データ取得エラー:', error)
    }
})
</script>

<template>
    <div style="height: 400px;">
        <!-- オプションが作成された時のみ描画 -->
        <Doughnut v-if="chartOptions" :data="chartData" :options="chartOptions" />
    </div>
</template>
