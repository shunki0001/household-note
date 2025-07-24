import { reactive, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

export function useExpenseFrom(props, emit) {
    const form = reactive({
        amount: props.expense.amount ?? '',
        date: props.expense.date ?? '',
        title: props.expense.title ?? '',
        category_id: props.expense.category_id ?? '',
        back: props.back,
    });

    watch(() => props.expanse, (newExpense) => {
        form.amount = newExpense.amount ?? '';
        form.date = newExpense.date ?? '';
        form.title = newExpense.title ?? '';
        form.category_id = newExpense.category_id ?? '';
    });

    const submit = async () => {
        try {
            const response = props.method === 'post'
                ? await axios.post(props.submitUrl, form)
                : await axios.put(props.submitUrl, form);

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    title: response.data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                }).then(() => {
                    if(props.method === 'put') {
                        window.location.href = '/dashboard';
                    }
                });

                emit('expense-added');

                form.amount='';
                form.date='';
                form.title='';
                form.category_id='';
        }   catch(error) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '登録失敗しました',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
            console.error('登録失敗', error);
        }
    };

    return { form, submit };
}
