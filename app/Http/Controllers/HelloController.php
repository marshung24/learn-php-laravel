<?php

namespace App\Http\Controllers;

/**
 * 最簡易的 Controller，用於快速驗證應用程式是否正常運作
 *
 * 示範如何建立基本端點並回傳文字或 JSON 回應
 */
class HelloController extends Controller
{
    /**
     * 回傳簡單的歡迎訊息
     *
     * @return string
     */
    public function index(): string
    {
        return 'Hello, Laravel!';
    }

    /**
     * 回傳包含名字的 JSON 回應
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function whoami()
    {
        return response()->json([
            'name' => '學員',
        ]);
    }

    /**
     * 健康檢查端點，部署時可讓 load balancer / K8s probe 呼叫
     *
     * @return string
     */
    public function health(): string
    {
        return 'OK';
    }
}
