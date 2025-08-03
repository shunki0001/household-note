<script setup>
import { ref, onMounted, watch } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    BarElement, CategoryScale, LinearScale
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ChartDataLabels)

const props = defineProps({
    label: { type: String, default: '月別支出合計' },
    apiUrl: { type: String, default: '/api/chart-data' },
    colors: {
        type: Array,
        default: () => ['#42b983']
    },
    year: {
        type: Number,
        default: new Date().getFullYear()
    }
})

const chartData = ref({ labels: [], datasets: [] })
const chartOptions = ref({})

const fetchData = async () => {
    try {
        let json;
        if(props.apiUrl === 'mock') {
            // テスト用データを定義
            json = {
                labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
                totals: [1000, 2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000, 11000, 12000, ]
            }
        } else {
            const response = await fetch(`${props.apiUrl}?year=${props.year}`);
            json = await response.json();
        }
        // const response = await fetch(`${props.apiUrl}?year=${props.year}`)
        // const json = await response.json()

        const maxValue = Math.max(...(json.totals || [0]))
        const adjustedMax = Math.ceil((maxValue + 10000) / 1000) * 1000

        chartData.value = {
            labels: json.labels,
            datasets: [
                {
                    label: `${props.year}年 ${props.label}`,
                    data: json.totals,
                    backgroundColor: props.colors[0],
                }
            ]
        }

        chartOptions.value = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: (value) => `¥${value.toLocaleString()}`,
                    color: '#333',
                    font: { weight: 'bold', size: 12 }
                },
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: (context) => `¥${context.parsed.y.toLocaleString()}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: adjustedMax,
                    ticks: {
                        callback: (value) => `¥${value.toLocaleString()}`
                    }
                }
            }
        }
    } catch (err) {
        console.error('データ取得エラー:', err)
    }
}

onMounted(fetchData)
watch(() => props.year, fetchData)
</script>

<template>
    <div style="height: 400px;">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
