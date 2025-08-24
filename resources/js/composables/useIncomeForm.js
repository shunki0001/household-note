import { reactive, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';


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

    // バリデーション関数
    const validateForm = () => {
        let isValid = true;

        // エラーメッセージをリセット
        Object.keys(errors).forEach(key => {
            errors[key] = '';
        });

        // 金額のバリデーション
        if (!form.amount || form.amount.toString().trim() === '') {
            errors.amount = '金額を入力して下さい';
            isValid = false;
        } else if (parseFloat(form.amount) < 0) {
            errors.amount = '金額は0以上の値を入力して下さい';
            isValid = false;
        }

        // 日付のバリデーション
        if (!form.income_date || form.income_date.toString().trim() === '') {
            errors.income_date = '日付を入力して下さい';
            isValid = false;
        }

        // カテゴリーのバリデーション
        if (!form.income_category_id || form.income_category_id.toString().trim() === '') {
            errors.income_category_id = 'カテゴリーを選択して下さい';
            isValid = false;
        }

        return isValid;
    };

    watch(() => props.income, (newIncome) => {
        form.amount = newIncome.amount ?? '';
        form.income_date = newIncome.income_date ?? '';
        form.income_category_id = newIncome.income_category_id ?? '';
    });

    const submit = async () => {
        // バリデーションを実行
        if (!validateForm()) {
            return; // バリデーションエラーがある場合は送信を中止
        }

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
                timer: 2000,
                timerProgressBar: true,
            }).then(() => {
                // 編集時（PUT）はページ遷移、新規登録時（POST）はページ遷移しない
                if (props.method === 'put' && form.back) {
                    window.location.href = window.location.origin + '/' + form.back;
                } else if (props.method === 'put') {
                    window.location.href = '/dashboard';
                }
            });

            // 新規登録時（POST）のみイベントを発火
            if (props.method === 'post') {
                console.log('Emitting Income-added event'); // デバッグログ
                emit('submitted');
                // emit('income-added');
                console.log('useIncomeForm: income-added event emitted'); // デバッグログ
            }

            // 新規登録時（POST）のみフォームをリセット
            if (props.method === 'post') {
                console.log('Resetting form'); // デバッグログ
                form.amount='';
                form.income_date='';
                form.income_category_id='';
                console.log('Form reset completed'); // デバッグログ
            }
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

    return { form, errors, submit };
}
