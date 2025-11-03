import { ref } from 'vue';
import { YEAR_START_MONTH, YEAR_END_MONTH } from '@/config/constants';

export function useYearMonth(initialYear = [2023, 2024, 2025, 2026, 2027]) {
    // 現在の月と年を取得
    const currentMonth = ref(new Date().getMonth() + 1);
    const currentYear = ref(new Date().getFullYear());

    // 選択可能な年度リスト
    const availableYears = ref([2023, 2024, 2025, 2026, 2027]);

    // 年度の変更
    const changeYear = (year) => {
        currentYear.value = year;
    }

    // 月の変更
    const changeMonths = (month) => {
        currentMonth.value = month;
    }

    // 次の月
    const nextMonth = () => {
        if (currentMonth.value < YEAR_END_MONTH) {
            currentMonth.value++;
        } else {
            // 12月->翌年1月
            currentMonth.value = YEAR_START_MONTH;
            if (availableYears.value.includes(currentYear.value + 1)) {
                currentYear.value++;
            }
        }
    };

    // 前の月
    const prevMonth = () => {
        if (currentMonth.value > YEAR_START_MONTH) {
            currentMonth.value--;
        } else {
            // 1月->前年12月
            currentMonth.value = YEAR_END_MONTH;
            if (availableYears.value.includes(currentYear.value - 1)) {
                currentYear.value--;
            }
        }
    };

    return {
        currentMonth,
        currentYear,
        availableYears,
        changeYear,
        changeMonths,
        nextMonth,
        prevMonth,
    };
}
