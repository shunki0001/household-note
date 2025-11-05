import { MONTH_PER_QUARTER, QUARTERS, TOTAL_QUARTER } from "@/config/constants";
import { computed, ref } from "vue";

// 四半期操作
export function useQuarter(availableYears) {
    const currentYear = ref(new Date().getFullYear());

    // =============================
    // 今月の月を取得
    // =============================
    const currentMonth = new Date().getMonth() + 1;
    const getQuarter = (month) => Math.ceil(month / MONTH_PER_QUARTER);
    const currentQuarter = ref(getQuarter(currentMonth));

    const currentQuarterLabel = computed(() => QUARTERS[currentQuarter.value -1].label);
    const currentRange = computed(() =>QUARTERS[currentQuarter.value - 1]);

    // =============================
    // 四半期切り替え
    // =============================
    const nextQuarter = () => {
        if (currentQuarter.value < TOTAL_QUARTER) currentQuarter.value++;
        else if (currentYear.value < availableYears.value.at(-1)) {
            currentYear.value++;
            currentQuarter.value = 1;
        }
    };

    const prevQuarter = () => {
        if (currentQuarter.value > 1) currentQuarter.value--;
        else if(currentYear.value > availableYears.value[0]) {
            currentYear.value--;
            currentQuarter.value = TOTAL_QUARTER;
        }
    };

    // 年変更
    const changeYear = (year) => {
        currentYear.value = year;
        currentQuarter.value = getQuarter(currentMonth);
    }

    const resetQuarterToCurrentMonth = () => {
        currentQuarter.value = getQuarter(currentMonth);
    };

    return {
        currentYear,
        currentQuarter,
        currentQuarterLabel,
        currentRange,
        nextQuarter,
        prevQuarter,
        changeYear,
        resetQuarterToCurrentMonth,
    };
}
