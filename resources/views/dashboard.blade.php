<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="w-full py-12">
        <div class="w-full max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Welcome Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg col-span-1 md:col-span-2">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                            {{ __('おかえりなさい、') }} {{ Auth::user()->name }} {{ __('殿') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('ここからは残業が待っています。') }}
                        </p>
                    </div>
                </div>

                <!-- Quick Links Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                            {{ __('クイックリンク') }}
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('events.index') }}" class="block p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <span class="text-gray-700 dark:text-gray-300">{{ __('イベントカレンダー') }}</span>
                            </a>
                            <a href="#" class="block p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <span class="text-gray-700 dark:text-gray-300">{{ __('プロフィール設定') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events Card -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                        {{ __('今後のイベント') }}
                    </h3>

                    <!-- Placeholder for upcoming events list -->
                    <div class="border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 flex flex-col items-center justify-center">
                        <p class="text-gray-500 dark:text-gray-400 text-center">
                            {{ __('現在イベントが設定できません') }}
                        </p>
                        <a href="{{ route('events.index') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('イベントを作成') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status Card -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-4">
                        {{ __('システム情報') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('アプリバージョン') }}</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ config('app.version', '1.0.0') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ユーザーステータス') }}</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->role ?? __('雑魚') }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('最終ログイン') }}</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ now()->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
