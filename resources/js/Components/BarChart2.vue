<!-- 棒グラフの汎用コンポーネント設定 -->
<script setup>
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    BarElement, CategoryScale, LinearScale,
    plugins,
    scales
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { callback } from 'chart.js/helpers'

// propsを定義
const props = defineProps({
    // 汎用ラベル(表の上にあるやつ)
    label: {
        type: String,
        default: '月別支出合計'
    },
    // apiルート
    apiUrl: {
        type: String,
        default: 'api/chart-data',
    },
    // 下記の色はグラフの左から適用していく
    // 棒の色
    color1: {
        type: String,
        default: '#42b983' // 緑
    },
    // 棒の色
    color2: {
        type: String,
        default: '#42b983'
    },
    // 棒の色
    color3: {
        type: String,
        default: '#42b983'
    },
    // 棒の色
    color4: {
        type: String,
        default: '#42b983'
    }
})

// Chart.jsのプラグイン登録
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ChartDataLabels)

const chartData = ref({
    labels: [],
    datasets: [],
})

const chartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    layout: {
      padding: {
        top: 20,
      }
    },
    plugins: {
        datalabels: {
            anchor: 'end',
            align: 'end',
            formatter: (value) => `¥${value.toLocaleString()}`,
            color: '#333',
            font: {
                weight: 'bold',
                size: 12,
            }
        },
        legend: {
            display: true,
        },
        Tooltip: {
            callbacks: {
                label: (context) => `¥${context.parsed.y.toLocaleString()}`
            }
        }
    },
    scales: {
        y: {
            ticks: {
                callback: (value) => `¥${value.toLocaleString()}`
            },
            beginAtZero: true,
        }
    },
    aspectRatio: 2,
})

onMounted(async () => {
    try {
        const response = await fetch(props.apiUrl)
        const json = await response.json()

        const maxValue = Math.max(...(json.totals || json.datasets?.flatMap(ds => ds.data) || [0]))
        const adjustedMax = Math.ceil((maxValue + 10000) / 1000 ) * 1000 // 1000単位で切り上げ

        chartOptions.value.scales.y.max = adjustedMax

        // APIのレスポンスがdatasetsを含むかで分岐
        if (json.datasets) {
            // 複数カテゴリ対応
            chartData.value = {
                labels: json.labels,
                datasets: json.datasets.map((dataset, index) => ({
                    ...dataset,
                    backgroundColor: [props.color1, props.color2, props.color3, props.color4][index % 4],
                }))
            }
        } else {
            // 単一データセット
            chartData.value = {
                labels: json.labels,
                datasets: [
                    {
                        label: props.label,
                        data: json.totals,
                        backgroundColor: [
                            props.color1,
                        ],
                    },
                ],
            }
        }

        chartOptions.value = {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 20,
                }
            },
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: (value) => `¥${value.toLocaleString()}`,
                    color: '#333',
                    font: {
                        weight: 'bold',
                        size: 12,
                    }
                },
                legend: {
                    display: true,
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `¥${context.parsed.y.toLocaleString()}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callbacks: (value) => `¥${value.toLocaleString()}`
                    },
                    max: adjustedMax,
                }
            },
            aspectRatio: 2,
        }

    } catch (error) {
        console.error('データ取得エラー:', error)
    }
})
</script>

<template>
    <div style="height: 400px;">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
    <!-- <canvas ref="canvasRef"></canvas> -->
</template>
