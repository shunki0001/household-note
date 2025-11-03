<script setup>
import { onMounted, ref, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import axios from 'axios'
import { CATEGORY_ICON_SIZE_DESKTOP, CATEGORY_ICON_SIZE_MOBILE, ICON_OFFSET_X_DESKTOP, ICON_OFFSET_X_MOBILE, ICON_OFFSET_Y_DESKTOP, ICON_OFFSET_Y_MOBILE, MOBILE_BREAKPOINT } from '@/config/constants'

Chart.register(...registerables)

const props = defineProps({
    apiUrl: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: 'カテゴリー別支出',
    },
    month: {
        type: Number,
        required: true,
    },
    year: {
        type: Number,
        default: new Date().getFullYear(),
    }
})

const chartRef = ref(null)
let chartInstance = null

// Chart.jsで描画
const renderChart = (labels, datasets, icons) => {
    if (chartInstance) chartInstance.destroy()

    // カスタムプラグインでアイコンを描画
    const iconPlugin = {
        id: 'categoryIcons',
        afterDraw: chart => {
            const { ctx, chartArea, scales } = chart
            const xAxis = scales.x
            const yBottom = chartArea.bottom

            // 画面幅に応じてアイコンサイズを変更
            const isMobile = window.innerWidth < MOBILE_BREAKPOINT
            const iconSize = isMobile ? CATEGORY_ICON_SIZE_MOBILE : CATEGORY_ICON_SIZE_DESKTOP

            labels.forEach((label, index) => {
                const xBottom = xAxis.getPixelForTick(index)
                const img = new Image()
                img.src = icons[index]
                img.onload = () => {
                    if(isMobile) {
                        ctx.drawImage(img, xBottom - ICON_OFFSET_X_MOBILE, yBottom + ICON_OFFSET_Y_MOBILE, iconSize, iconSize)
                    } else {
                        ctx.drawImage(img, xBottom - ICON_OFFSET_X_DESKTOP, yBottom + ICON_OFFSET_Y_DESKTOP, iconSize, iconSize)
                    }
                }
            })
        }
    }

    chartInstance = new Chart(chartRef.value, {
        type: 'bar',
        data: {
            labels,
            datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                // title: {
                //     display: true,
                //     font: { size: 18 },
                // },
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return `${context.dataset.label}: ¥${context.parsed.y.toLocaleString()}`
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => `¥${value.toLocaleString()}`,
                    },
                    title: {
                        display: true,
                        text: '金額（円）',
                    },
                },
                x: {
                    ticks: { display: false },
                    title: {
                        display: true,
                        text: ' ',
                    },
                },
            },
        },
        plugins: [iconPlugin],
    })
}

// データ取得
const fetchChartData = async () => {
    try {
        const response = await axios.get(props.apiUrl, {
            params: {
                month: props.month,
                year: props.year,
            },
        });
        const { labels, datasets, icons, colors } = response.data

        // 各データポイントに個別の色を設定
        const coloredDatasets = datasets.map((d, i) => {
            return {
                ...d,
                backgroundColor: colors,
                borderColor: colors,
                borderWidth: 1,
            };
        })

        renderChart(labels, coloredDatasets, icons)
    } catch (error) {
        console.error('グラフデータの取得に失敗しました:', error)
    }
}

onMounted(fetchChartData)
watch([() => props.month, () => props.year], fetchChartData)
</script>

<template>
    <div class="w-full h-[400px]">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
