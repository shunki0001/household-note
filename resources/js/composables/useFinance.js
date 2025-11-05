import { ref, computed} from 'vue';
import { INITIAL_TOTAL_VALUE } from "@/config/constants";
import { fetchTotalExpense, fetchTotalIncome } from '@/services/transactionService';

export function useFinance(initialExpense, initialIncome) {
    const totalExpense = ref(Number(initialExpense) || INITIAL_TOTAL_VALUE);
    const totalIncome = ref(Number(initialIncome) || INITIAL_TOTAL_VALUE);
    const balance = computed(() => totalIncome.value - totalExpense.value);

    const formattedExpense = computed(() => totalExpense.value.toLocaleString());
    const formattedIncome = computed(() => totalIncome.value.toLocaleString());
    const formattedBalance = computed(() => balance.value.toLocaleString());

    const updateExpense = async () => { totalExpense.value = await fetchTotalExpense(); };
    const updateIncome = async () => { totalIncome.value = await fetchTotalIncome(); };

    return {
        totalExpense,
        totalIncome,
        balance,
        formattedExpense,
        formattedIncome,
        formattedBalance,
        updateExpense,
        updateIncome,
    };
}
