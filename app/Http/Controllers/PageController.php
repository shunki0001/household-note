<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    // 一覧ページに移動
    public function list() {
        return Inertia::render('ListPage'); // resources/js/Pages/ListPage.vu
    }

    // 合計支出グラフに移動
    public function monthlyGraph() {
        return Inertia::render('MonthlyGraph'); // resources/js/MonthlyGraph.vue
    }

    // カテゴリー別合計グラフに移動
    public function categoryGraph() {
        return Inertia::render('CategoryGraph'); // resources/js/CategoryGraph.vue
    }

}
