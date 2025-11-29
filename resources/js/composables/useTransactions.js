import { YEAR_END_MONTH, YEAR_START_MONTH } from '@/config/constants';
import { showAlert } from '@/utils/alert';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { onMounted } from 'vue';

export function useTransactions(initialData = []) {
    const page = usePage();

    // reactive states
    const transactions = ref(initialData);
    const month = ref(new Date().getMonth() + 1);
    const year = ref(new Date().getFullYear());

    // データ取得
    const fetchTransactions = async () => {
        try {
            const { data } = await axios.get(
                '/api/report-data/monthly-transactions',
                {
                    params: { year: year.value, month: month.value },
                },
            );
            transactions.value =
                data.transactions?.data ?? data.transactions ?? [];
        } catch (error) {
            if (process.env.NODE_ENV !== 'production') {
                console.error('データ取得失敗', error);
            }
        }
    };

    // 月変更ロジック
    const changeMonth = (delta) => {
        month.value += delta;

        if (month.value > YEAR_END_MONTH) {
            month.value = YEAR_START_MONTH;
            year.value++;
        } else if (month.value < YEAR_START_MONTH) {
            month.value = YEAR_END_MONTH;
            year.value--;
        }

        fetchTransactions();
    };

    // 初期化処理
    onMounted(() => {
        fetchTransactions();

        const message = page.props.flash?.message;
        if (message) {
            showAlert(message, 'success');
        }
    });

    // propsからの変更更新
    const updateTransactions = (newList) => {
        if (newList && newList.length > 0) {
            transactions.value = newList;
        }
    };

    return {
        transactions,
        month,
        year,
        changeMonth,
        fetchTransactions,
        updateTransactions,
    };
}
