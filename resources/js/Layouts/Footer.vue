<template>
    <footer class="text-800 border-t bg-gray-100 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- アイコン部分中央配列 -->
            <div class="flex flex-col items-center space-y-6">
                <!-- ロゴ
                <img
                    src="/images/applogo.png"
                    alt="ロゴ"
                    class="w-12 h-12 mb-2"
                    style="width: 50px; height: 50px;"
                /> -->

                <!-- アイコン+テキスト -->
                <div
                    class="flex flex-col items-center justify-center gap-10 sm:flex-row"
                >
                    <!-- AppLogo -->
                    <div class="flex flex-col items-center">
                        <a href="/dashboard">
                            <img
                                src="/images/applogo.png"
                                alt="ロゴ"
                                class="mb-2 h-10 w-10"
                                style="width: 40px; height: 40px"
                            />
                            <p class="text-sm text-gray-600">ホーム</p>
                        </a>
                    </div>
                    <!-- Qiita -->
                    <div class="felx flex-col items-center">
                        <a href="https://qiita.com/shunki0001">
                            <img
                                src="/images/qiita-icon.png"
                                alt="Qiitaロゴ"
                                class="mb-2 h-10 w-10"
                                style="width: 40px; height: 40px"
                            />
                            <p class="text-sm text-gray-600">
                                制作過程の記事はこちら
                            </p>
                        </a>
                    </div>
                    <!-- GitHub -->
                    <div class="felx flex-col items-center">
                        <a href="https://github.com/shunki0001/household-note">
                            <img
                                src="/images/github-mark.png"
                                alt="GitHubロゴ"
                                class="mb-2 h-10 w-10"
                                style="width: 40px; height: 40px"
                            />
                            <p class="text-sm text-gray-600">GitHubリンク</p>
                        </a>
                    </div>
                </div>
            </div>
            <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-8"> -->
            <!-- ロゴ + アイコン -->
            <!-- <div class="flex flex-col items-start">
                    <div class="mb-2">
                        <a href="#"><img src="/images/applogo.png" alt="ロゴ" class="w-12 h-12 mb-4" style="width: 50px; height: 50px;" /></a>
                    </div>
                    <div class="flex gap-3">
                        <img src="/images/qiita-icon.png" alt="Qiitaロゴ" class="w-10 h-10" style="width: 40px; height: 40px;" />
                        <img src="/images/github-mark.png" alt="GitHubロゴ" class="w-10 h-10" style="width: 40px; height: 40px;" />
                    </div>
                </div> -->

            <!-- メニューリンク -->
            <!--  未実装なので削除 -->
            <!-- <div>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">ホーム</a></li>
                        <li><a href="#" class="hover:underline">機能紹介</a></li>
                        <li><a href="#" class="hover:underline">お問い合わせ</a></li>
                        <li><a href="#" class="hover:underline">よくある質問(FAQ)</a></li>
                        <li><a href="#" class="hover:underline">利用規約/プライバシーポリシー</a></li>
                    </ul>
                </div> -->

            <!-- 作成者情報 -->
            <!-- <div>
                    <ul class="space-y-2">
                        <li class="font-semibold">作成者：　◯◯</li>
                        <li><a href="#" class="hover:underline">Githubリンク</a></li>
                    </ul>
                </div> -->
            <!-- </div> -->
        </div>
        <!-- コピーライト -->
        <div class="flex items-end justify-center">
            <small class="text-center text-sm text-gray-500 md:text-right">
                ©︎ 2025 カケボノート All right reserved.
            </small>
        </div>
    </footer>
</template>

<script></script>

<style scoped>
footer a:hover {
    text-decoration: underline;
}
</style>
