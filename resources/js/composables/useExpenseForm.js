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
            console.log('useExpenseForm submit started'); // デバッグログ
            console.log('Form data:', form); // デバッグログ

            const response = props.method === 'post'
                ? await axios.post(props.submitUrl, form)
                : await axios.put(props.submitUrl, form);
            console.log('API response:', response.data); // デバッグログ

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: response.data.message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            }).then(() => {
                if(form.back) {
                    window.location.href = window.location.origin + '/' + form.back;
                } else {
                    window.location.href = '/dashboard';
                }
            });

            // イベントを発火
            console.log('Emitting expense-added event'); // デバッグログ
            emit('expense-added');
            console.log('useExpenseForm: expense-added event emitted'); // デバッグログ

            // フォームをリセット
            console.log('Resetting form'); // デバッグログ
            form.amount='';
            form.date='';
            form.title='';
            form.category_id='';

            console.log('Form reset completed'); // デバッグログ
        }   catch(error) {
            console.error('Submit error:', error); // デバッグログ
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
