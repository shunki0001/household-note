/**
 * ユーティリティ関数
 * SweetAlert2の共通部分を関数化
 * 共通項目
 * Toast
 * top-endに配置
 *
 * iconとtitleを引数として渡して使う
 * 必要に応じて上書き・追加可能
 */
import Swal from 'sweetalert2';
import { SWEET_ALERT2_TIMER } from '@/config/constants';

export function showAlert(title, icon = 'success', options = {}) {
    return Swal.fire({
        toast: true,
        position: 'top-end',
        icon,
        title,
        showConfirmButton: false,
        timer: SWEET_ALERT2_TIMER,
        timerProgressBar: true,
        ...options, // 呼び出し時に上書き・追加が可能
    });
}
