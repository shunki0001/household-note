<template>
    <footer class="bg-gray-100 border-t py-8 text-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- ロゴ + アイコン -->
                <div class="flex flex-col items-start">
                    <div class="mb-2">
                        <img src="/images/applogo.png" alt="ロゴ" class="w-12 h-12 mb-4" style="width: 50px; height: 50px;" />
                    </div>
                    <div class="flex gap-3">
                        <img src="/images/qiita-icon.png" alt="Qiitaロゴ" class="w-10 h-10" style="width: 40px; height: 40px;" />
                        <img src="/images/github-mark.png" alt="GitHubロゴ" class="w-10 h-10" style="width: 40px; height: 40px;" />
                    </div>
                </div>

                <!-- メニューリンク -->
                <div>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">ホーム</a></li>
                        <li><a href="#" class="hover:underline">機能紹介</a></li>
                        <li><a href="#" class="hover:underline">お問い合わせ</a></li>
                        <li><a href="#" class="hover:underline">よくある質問(FAQ)</a></li>
                        <li><a href="#" class="hover:underline">利用規約/プライバシーポリシー</a></li>
                    </ul>
                </div>

                <!-- 作成者情報 -->
                <div>
                    <ul class="space-y-2">
                        <li class="font-semibold">作成者：　◯◯</li>
                        <li><a href="#" class="hover:underline">Githubリンク</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- コピーライト -->
        <div class="flex items-end justify-center">
            <small class="text-gray-500 text-sm text-center md:text-right">
                ©︎ 2025 カケボノート All right reserved.
            </small>
        </div>
    </footer>
</template>

<script>

</script>

<style scoped>
footer a:hover {
    text-decoration: underline;
}
</style>
