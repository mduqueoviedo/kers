<x-layouts::auth title="auth.create_account">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('auth.create_account_title')" :description="__('auth.create_account_description')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('common.fields.name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('auth.full_name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('auth.email_address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('auth.password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('auth.password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('auth.confirm_password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('auth.confirm_password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('auth.create_account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('auth.already_have_an_account') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('auth.log_in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
