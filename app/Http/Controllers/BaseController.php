<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function jsonResponse(array|string $data, int $status = 200)
    {
        if (is_string($data)) {
            // 文字列なら message として返す
            $data = ['message' => $data];
        }
        return response()->json($data, $status, [], JSON_UNESCAPED_UNICODE);
    }
}
