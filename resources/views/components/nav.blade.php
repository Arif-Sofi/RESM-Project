<div class="nav-links">
    @auth
        <x-nav-link :href="url('/dashboard')" :active="request()->routeIs('dashboard')">
            ダッシュボード
        </x-nav-link>
    @else
        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
            ログイン
        </x-nav-link>
        @if(Route::has('register'))
            <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                新規登録
            </x-nav-link>
        @endif
    @endauth

    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
        イベントカレンダー
    </x-nav-link>
    <x-nav-link href="https://laravel.com/docs" :active="false" target="_blank">
        ドキュメント
    </x-nav-link>
</div>
