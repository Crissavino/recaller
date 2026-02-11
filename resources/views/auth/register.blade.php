<x-guest-layout>
    <h1 class="auth-title">{{ __('auth.register.title') }}</h1>
    <p class="auth-subtitle">{{ __('auth.register.subtitle') }}</p>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="clinic_name" class="form-label">{{ __('auth.register.clinic_name') }}</label>
            <input
                id="clinic_name"
                type="text"
                name="clinic_name"
                value="{{ old('clinic_name') }}"
                class="form-input"
                placeholder="{{ __('auth.register.clinic_name_placeholder') }}"
                required
                autofocus
            >
            @error('clinic_name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">{{ __('auth.register.email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-input"
                placeholder="{{ __('auth.register.email_placeholder') }}"
                required
                autocomplete="username"
            >
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">{{ __('auth.register.password') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input"
                placeholder="{{ __('auth.register.password_placeholder') }}"
                required
                autocomplete="new-password"
            >
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">{{ __('auth.register.password_confirm') }}</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                placeholder="{{ __('auth.register.password_confirm_placeholder') }}"
                required
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="terms"
                    id="terms"
                    value="1"
                    class="mt-1 w-4 h-4 text-sky-500 border-gray-300 rounded focus:ring-sky-500"
                    {{ old('terms') ? 'checked' : '' }}
                    required
                >
                <span class="text-sm text-gray-600">
                    {{ __('legal.accept_terms') }}
                    <a href="{{ route('terms') }}" target="_blank" class="text-sky-500 hover:text-sky-600 underline">{{ __('legal.terms_link') }}</a>
                    {{ __('legal.and') }}
                    <a href="{{ route('privacy') }}" target="_blank" class="text-sky-500 hover:text-sky-600 underline">{{ __('legal.privacy_link') }}</a>
                </span>
            </label>
            @error('terms')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            {{ __('auth.register.submit') }}
        </button>
    </form>

    <div class="auth-footer">
        {{ __('auth.register.has_account') }} <a href="{{ route('login') }}">{{ __('auth.register.login_link') }}</a>
    </div>
</x-guest-layout>
