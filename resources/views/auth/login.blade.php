<x-guest-layout>
    <div class="flex justify-center mb-4">
        <a href="/">
            <img src="{{ asset('images/SKSU-logo.png') }}" alt="Logo" class="w-30 h-30">
        </a>
    </div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4">
        <h2 class="text-2xl font-semibold text-primary dark:text-base">
            {{ __('Log in') }}
        </h2>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="abcd@schoolemail.com" />
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
                <input id="remember_me" type="checkbox" class="rounded border-secondary text-primary shadow-sm focus:ring-primary" name="remember">
                <span class="ms-2 text-sm text-primary dark:text-base">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-center mt-4">
            <x-primary-button class="w-full justify-center" style="background-color: #AAB99A; border-radius: 20px;">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-4">
                <a class="font-bold text-sm text-primary dark:text-base hover:text-secondary dark:hover:text-accent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('password.request') }}">
                    <u>{{ __('Forgot your password?') }}</u>
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
