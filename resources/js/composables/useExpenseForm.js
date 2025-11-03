import { reactive, watch } from 'vue';
import axios from 'axios';
// import Swal from 'sweetalert2';
// import { useForm } from '@inertiajs/vue3';
import { SWEET_ALERT2_TIMER, VALIDATE_ERROR_STATUS } from '@/config/constants';
import { showAlert } from '@/utils/alert';


export function useExpenseFrom(props, emit) {

    const form = reactive({
        amount: props.expense.amount ?? '',
        date: props.expense.date ?? '',
        title: props.expense.title ?? '',
        category_id: props.expense.category_id ?? '',
        back: props.back,
    });

    // エラーメッセージを管理するためのreactiveオブジェクト
    const errors = reactive({
        amount: '',
        date: '',
        title: '',
        category_id: ''
    });

    watch(() => props.expanse, (newExpense) => {
        form.amount = newExpense.amount ?? '';
        form.date = newExpense.date ?? '';
        form.title = newExpense.title ?? '';
        form.category_id = newExpense.category_id ?? '';
    });

    const submit = async () => {

        // 送信前にエラーリセット
        Object.keys(errors).forEach(key => (errors[key] = ''));

        try {
            console.log('useExpenseForm submit started'); // デバッグログ
            console.log('Form data:', form); // デバッグログ

            const response = props.method === 'post'
                ? await axios.post(props.submitUrl, form)
                : await axios.put(props.submitUrl, form);
            console.log('API response:', response.data); // デバッグログ

            // Swal.fire({
            //     toast: true,
            //     position: 'top-end',
            //     icon: 'success',
            //     title: response.data.message,
            //     showConfirmButton: false,
            //     timer: SWEET_ALERT2_TIMER,
            //     timerProgressBar: true,
            // }).then(() => {
            showAlert(response.data.message, 'success').then(() => {
                // 編集時（PUT）はページ遷移、新規登録時（POST）はページ遷移しない
                if (props.method === 'put' && form.back) {
                    window.location.href = window.location.origin + '/' + form.back;
                } else if (props.method === 'put') {
                    window.location.href = '/dashboard';
                } else if (props.method === 'post') {
                    // 新規登録時はフォームをリセット
                    form.amount = '';
                    form.date = '';
                    form.title = '';
                    form.category_id = '';
                    // 親コンポーネントに登録完了を通知
                    emit('submitted');
                }
            });
        }   catch(error) {
            console.error('Submit error:', error); // デバッグログ

            // Laravelの422バリデーションエラー対応
            if (error.response && error.response.status == VALIDATE_ERROR_STATUS) {
                const validationErrors = error.response.data.errors;
                for (const key in validationErrors) {
                    if (errors.hasOwnProperty(key)) {
                        errors[key] = validationErrors[key][0];
                    }
                }
            } else {
                // 通信エラー・サバーエラー時
                // Swal.fire({
                //     toast: true,
                //     position: 'top-end',
                //     icon: 'error',
                //     title: '登録失敗しました',
                //     showConfirmButton: false,
                //     timer: SWEET_ALERT2_TIMER,
                //     timerProgressBar: true,
                // });
                showAlert('登録失敗しました', 'error');
                console.error('登録失敗', error);
            }
        }
    };

    return { form, errors, submit };
}
