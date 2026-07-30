<x-layouts::auth title="auth.log_in">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('auth.log_in_title')" :description="__('auth.log_in_description')" />

        <div class="rounded-lg border border-teal-200 bg-teal-50 p-4 text-sm text-teal-950 dark:border-teal-900 dark:bg-teal-950/40 dark:text-teal-100">
            <p class="font-semibold">{{ __('auth.demo_credentials_heading') }}</p>
            <p class="mt-1">{{ __('auth.demo_credentials_description') }}</p>
            <dl class="mt-3 grid gap-1">
                <div><dt class="inline font-medium">{{ __('auth.email_address') }}:</dt> <dd class="inline">{{ config('kers.demo_user.email') }}</dd></div>
                <div><dt class="inline font-medium">{{ __('auth.password') }}:</dt> <dd class="inline">{{ config('kers.demo_user.password') }}</dd></div>
            </dl>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />


        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('auth.email_address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <flux:input
                name="password"
                :label="__('auth.password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('auth.password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('auth.log_in') }}
                </flux:button>
            </div>
        </form>

    </div>
</x-layouts::auth>
