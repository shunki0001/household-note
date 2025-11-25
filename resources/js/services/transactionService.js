import axios from 'axios';
import { INITIAL_TOTAL_VALUE } from '@/config/constants';

export async function fetchTransactions(page = 1) {
    const res = await axios.get(route('transaction.latestJson', { page }));
    return res.data.transactions.data;
}

export async function fetchTotalExpense() {
    const res = await axios.get(route('dashboard.totalExpense'));
    return Number(res.data.totalExpense) || INITIAL_TOTAL_VALUE;
}

export async function fetchTotalIncome() {
    const res = await axios.get(route('dashboard.totalIncome'));
    return Number(res.data.totalIncome) || INITIAL_TOTAL_VALUE;
}
