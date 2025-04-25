<x-guest-layout>
    <div class="min-h-screen flex flex-col md:flex-row w-full">
        <!-- Left Panel with Logo and Background -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-amber-500 to-amber-100 dark:from-amber-600 dark:to-amber-900 p-8 flex flex-col justify-center items-center min-h-[40vh] md:min-h-screen">
            <div class="mb-8">
                <a href="/">
                    <x-application-logo class="w-32 h-32" />
                </a>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-4 text-center">
                サウジャナ・ウタマ国立学校
            </h1>
            <p class="text-xl text-gray-700 dark:text-gray-200 text-center">
                スクール管理システムへようこそ
            </p>
        </div>

        <!-- Right Panel with Login Form -->
        <div class="w-full md:w-1/2 bg-gray-50 dark:bg-gray-900 p-8 flex items-center justify-center min-h-[60vh] md:min-h-screen">
            <div class="w-full max-w-md">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
                        {{ __('ログイン') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('アカウント情報を入力してください') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <x-primary-button class="ms-3">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>

                    @if (Route::has('register'))
                        <div class="text-center mt-6">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('アカウントをお持ちでない方は') }}</span>
                            <a href="{{ route('register') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ __('新規登録') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
