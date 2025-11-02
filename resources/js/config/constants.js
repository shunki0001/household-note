// ここにVueのScript内の処理を定数設定できる
/**
 * 定義例
 * export const MOBILE_BREAKPOINT = 768;
 *
 * インポート方法
 * import { MOBILE_BREAKPOINT , } from '@/config/constants';
*/

export const MOBILE_BREAKPOINT = 768;  // 単位px
export const MONTH_PER_QUARTER = 3;    // 四半期あたりの月数
export const TOTAL_QUARTER = 4;        // 四半期の数
export const YEAR_START_MONTH = 1;     // 年初月
export const YEAR_END_MONTH = 12;      // 年末月

// 四半期の定義
export const QUARTERS = [
    { start: 1, end: 3, label: '1~3月' },
    { start: 4, end: 6, label: '4~6月' },
    { start: 7, end: 9, label: '7~9月' },
    { start: 10, end: 12, label: '10~12月' },
];

export const SWEET_ALERT2_TIMER = 2000;     // SweetAlert2のポップアップ表示時間
export const DEFAULT_CURRENT_PAGE = 1;
export const DEFAULT_PAGE_NUMBER = 1;       // 最初のページ

export const DEFAULT_TOTAL_EXPENSE = 0;     // 合計支出のデフォルト
export const DEFAULT_TOTAL_INCOME = 0;      // 合計収入のデフォルト

export const INITIAL_TOTAL_VALUE = 0;       // 金額が未定義の場合の初期値

// SweetAlert2の共通部分
// title,iconを引数にした関数にする予定
export const DEFAULT_ALERT_CONFIG = {
    toast: true,
    position: 'top-end',
    icon: 'success',
    showConfirmButton: false,
    timerProgressBar: true,
};
