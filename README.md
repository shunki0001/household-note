# カケボノート
![アプリロゴ](docs/images/app_logo.png)
シンプルな機能で使いやすさを重視した家計簿アプリです。
支出・収入を登録して、グラフでお金の流れを一目で確認できます。

## サービス概要
|項目|内容|
|---|---|
|URL|https://household-note.onrendre.com|
|開発メモ/記事|[Qiita:@shunki0001](https://qiita.com/shunki0001)|
|目的|支出・収入を簡単に管理し、グラフで可視化できる家計簿アプリ|
|特徴|シンプルなUI/SPA構成|

## 主な画面・機能

<div align="center" 
     style="display: flex; flex-direction: column; align-items: center; gap: 20px; color: #000;">

  <!-- 1. トップページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/top.png" alt="トップページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>トップページ</b><br>キャッチコピーとサービスの特徴を掲載。初見ユーザーにも目的が伝わる設計。</p>
  </div>

  <!-- 2. ユーザー登録ページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/register.png" alt="ユーザー登録ページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>ユーザー登録ページ</b><br>名前・メール・パスワードを入力して登録。入力ミスはエラーで警告。</p>
  </div>

  <!-- 3. ログインページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/login.png" alt="ログインページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>ログインページ</b><br>登録済みユーザーはここからログイン。</p>
  </div>

  <!-- 4. マイページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/dashboard.png" alt="マイページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>マイページ</b><br>今月の収支を自動集計。登録・編集・削除が即時反映され、グラフもリアルタイム更新。</p>
  </div>

  <!-- 5. 一覧ページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/list.png" alt="一覧ページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>一覧ページ</b><br>月ごとの登録データを確認・編集・削除可能。</p>
  </div>

  <!-- 6. 月別支出グラフページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/monthly_graph.png" alt="月別支出グラフページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>月別支出グラフページ</b><br>指定年度の支出を棒グラフで表示。使いすぎた月を視覚的に把握。</p>
  </div>

  <!-- 7. カテゴリー別支出グラフページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/category_graph.png" alt="カテゴリー別支出グラフページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>カテゴリー別支出グラフページ</b><br>カテゴリー別に支出を可視化し、支出傾向を把握。</p>
  </div>

  <!-- 8. プロフィールページ -->
  <div style="width: 90%; max-width: 600px; background-color: #f9f9f9; border-radius: 10px; padding: 10px; 
              box-shadow: 0 0 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center;">
    <img src="docs/images/profile.png" alt="プロフィールページ" 
         style="width:100%; height:auto; max-height:240px; object-fit: contain; object-position: top; border-radius: 8px; background-color:#fff;"/>
    <p><b>プロフィールページ</b><br>名前・メール・パスワード変更やアカウント削除が可能。</p>
  </div>

</div>



## 使用技術
### バックエンド
- 言語: PHP 8.x
- フレームワーク: Laravel12
- 認証: Laravel Breeze / Sanctum
- DB: PostgreSQL(本番環境), MySQL(開発環境)
- 開発環境: Docker(Laravel Sail)
- バージョン管理: Git/GitHub

### フロントエンド
- フレームワーク: Vue3 + Inertia.js
- ビルド: Vite
- スタイル: Tailwind CSS
- グラフ: Chart.js + vue-chartjs + datalabels
- UI改善: SweetAlert2

### 主なパッケージ一覧
#### Laravel関連
|分類|パッケージ|バージョン|役割|
|---|---|---|---|
|フレームワーク|`larabel/framework`|12.33.3|Laravel本体|
|認証|`laravel/breeze`|2.3.8|ログイン・登録などの認証機能|
|API認証|`laravel/sanctum`|4.2.0|SPA向け軽量API認証|
|ローカライズ|`laravel-lang/*`| - |エラーメッセージ・UIの日本語化|
|ORM/DB|`nesbot/carbon`|3.10.3|日付操作ライブラリ(Eloquentで利用)|

#### フロントエンド関連
|分類|パッケージ|バージョン|役割|
|---|---|---|---|
|フレームワーク|`vue`|3.5.17|フロントエンドSPA構築|
|ルーティング統合|`@inertiajs/vue3`|2.0.13|Laravel × Vueのブリッジ|
|開発ビルド|`vite`|6.3.5|高速ビルドツール|
|Laravel連携|`laravel-vite-plugin`|1.3.0|Laravelとの統合|
|スタイル|`tailwindcss`|3.4.17|CSSユーティリティフレームワーク|
|Tailwind補助|`@tailwindcss/forms`|0.5.10|フォームデザイン拡張|
|HTTP通信|`axion`|1.10.0|非同期通信(API呼び出し)|
|グラフ描画|`chart.js`/`vue-chartjs`|4.5.0/5.3.2|支出・収入グラフ描画|
|グラフラベル|`chartjs-plugin-datalavels`|2.2.0|グラフ内データラベル表示|
|アラート|`sweetalert2`|11.22.1|モーダル・アラート表示|

## ページ遷移図
![ページ遷移図](docs/images/page_transition_diagram.png)

## ER図
```mermaid
---
title: ER図
---
erDiagram
    direction LR
    USERS ||--|{ EXPENSES : "has many"
    EXPENSES }|--|| CATEGORIES : "belongs to"
    USERS ||--|{ INCOMES : "has many"
    INCOMES }|--|| INCOME_CATEGORIES: "belongs to"

    USERS {
        bigint_unsigned id PK
        varchar(255) name
        varchar(255) email(UNI)
        timestamp email_verified_at
        varchar(255) password
        varchar(100) remember_token
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint_unsigned id PK
        varchar(255) name
        varchar(255) icon_path
        bigint_unsigned sort_order
        varchar(7) color
        timestamp created_at
        timestamp updated_at
    }

    INCOME_CATEGORIES {
        bigint_unsigned id PK
        varchar(255) name
        timestamp created_at
        timestamp updated_at
    }

    EXPENSES {
        bigint_unsigned id PK
        bigint_unsigned user_id FK 
        bigint_unsigned category_id FK
        int amount
        date date
        varchar(255) title
        timestamp created_at
        timestamp updated_at
    }

    INCOMES {
        bigint_unsigned id PK
        bigint_unsigned user_id FK 
        bigint_unsigned income_category_id FK
        int amount
        date date
        varchar(255) title
        timestamp created_at
        timestamp updated_at
    }

    %% カラースタイル
    style USERS fill:#cce5ff,stroke:#004085,stroke-width:2px,color:#002752
    style EXPENSES fill:#fff3cd,stroke:#856404,stroke-width:2px,color:#533f03
    style INCOMES fill:#fff3cd,stroke:#856404,stroke-width:2px,color:#533f03
    style CATEGORIES fill:#d4edda,stroke:#155724,stroke-width:2px,color:#0b2e13
    style INCOME_CATEGORIES fill:#d4edda,stroke:#155724,stroke-width:2px,color:#0b2e13
```

## インフラ構成図
```mermaid
graph TD

    A[ユーザー<br>ブラウザ] -->|HTTPS通信| B[フロントエンド<br>Vue3 + Inertia.js]
    B --> C[バックエンド<br>Laravel 12（Render）]
    C --> D[認証機能<br>Laravel Breeze]
    C --> E[(PostgreSQL<br>データベース)]

    subgraph Renderクラウド環境
        C
        E
    end

    style A fill:#1e1e2f,stroke:#5f5fff,color:#fff
    style B fill:#2b2b3c,stroke:#5f5fff,color:#fff
    style C fill:#2b2b3c,stroke:#5f5fff,color:#fff
    style D fill:#3b3b4f,stroke:#888,color:#fff
    style E fill:#3b3b4f,stroke:#888,color:#fff
    style Renderクラウド環境 fill:#1a1a1a,stroke:#555,color:#aaa
```
