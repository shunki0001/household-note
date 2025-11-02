import { reactive, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { SWEET_ALERT2_TIMER, VALIDATE_ERROR_STATUS } from '@/config/constants';


export function useIncomeForm(props, emit) {

    const form = reactive({
        amount: props.income.amount ?? '',
        income_date: props.income.income_date ?? '',
        income_category_id: props.income.income_category_id ?? '',
        back: props.back,
    });

    // エラーメッセージを管理するためのreactiveオブジェクト
    const errors = reactive({
        amount: '',
        income_date: '',
        income_category_id: ''
    });

    watch(() => props.income, (newIncome) => {
        form.amount = newIncome.amount ?? '';
        form.income_date = newIncome.income_date ?? '';
        form.income_category_id = newIncome.income_category_id ?? '';
    });

    const submit = async () => {

        // 送信前にエラーリセット
        Object.keys(errors).forEach(key => (errors[key] = ''));

        try {
            console.log('useIncomeForm submit started'); // デバッグログ
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
                timer: SWEET_ALERT2_TIMER,
                timerProgressBar: true,
            }).then(() => {
                // 編集時（PUT）はページ遷移、新規登録時（POST）はページ遷移しない
                if (props.method === 'put' && form.back) {
                    window.location.href = window.location.origin + '/' + form.back;
                } else if (props.method === 'put') {
                    window.location.href = '/dashboard';
                } else if (props.method === 'post') {
                    // 新規登録時はフォームをリセット
                    form.amount = '';
                    form.income_date = '';
                    form.income_category_id = '';
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
                // 通信エラー・サーバーエラー時
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '登録失敗しました',
                    showConfirmButton: false,
                    timer: SWEET_ALERT2_TIMER,
                    timerProgressBar: true,
                });
                console.error('登録失敗', error);
            }
        }
    };

    return { form, errors, submit };
}
