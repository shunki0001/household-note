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

export const VALIDATE_ERROR_STATUS = 422;

export const DEFAULT_CHART_COLOR = '#42b983';     // 棒グラフのデフォルト色
export const CHART_MAX_PADDING = 10000;     // グラフの上限を補正するための補正
export const CHART_ROUND_UNIT = 1000;       // 上限値の丸め単位
export const CHART_ASPECT_RATIO = 1;        // グラフの縦横比
export const LABEL_TEXT_COLOR = '#333';     // データラベル文字色
export const LABEL_FONT_SIZE_MOBILE = 10;   // フォントサイズ(モバイル)
export const LABEL_FONT_SIZE_DESKTOP = 12;  // フォントサイズ(PC)
export const LABEL_ANCHOR_POSITION = 'end'; // ahchorの位置指定
export const LABEL_ALIGN_POSITION = 'end';  // alignの位置指定

export const CHART_HEIGHT = "400";          // グラフの高さ

export const CATEGORY_ICON_SIZE_MOBILE = 16;    // カテゴリーアイコンサイズ(モバイル)
export const CATEGORY_ICON_SIZE_DESKTOP = 24; // カテゴリーアイコンサイズ(PC)

export const ICON_OFFSET_Y_DESKTOP = 5;         // グラフ下のアイコンをy軸方向の微調整用(PC)
export const ICON_OFFSET_Y_MOBILE = 5;         // グラフ下のアイコンをy軸方向の微調整用(モバイル)
export const ICON_OFFSET_X_DESKTOP = 11;    // グラフ下のアイコンをx軸方向の微調整(PC)
export const ICON_OFFSET_X_MOBILE = 7;     // グラフ下のアイコンをx軸方向の微調整(モバイル)

