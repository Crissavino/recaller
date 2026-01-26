<x-guest-layout>
    <h1 class="auth-title">{{ __('auth.login.title') }}</h1>
    <p class="auth-subtitle">{{ __('auth.login.subtitle') }}</p>

    @if (session('status'))
        <div class="auth-status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">{{ __('auth.login.email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-input"
                placeholder="{{ __('auth.login.email_placeholder') }}"
                required
                autofocus
                autocomplete="username"
            >
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">{{ __('auth.login.password') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input"
                placeholder="{{ __('auth.login.password_placeholder') }}"
                required
                autocomplete="current-password"
            >
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-row">
            <label class="form-checkbox">
                <input type="checkbox" name="remember">
                {{ __('auth.login.remember') }}
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="form-link">
                    {{ __('auth.login.forgot') }}
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">
            {{ __('auth.login.submit') }}
        </button>
    </form>

    <div class="auth-footer">
        {{ __('auth.login.no_account') }} <a href="{{ route('pricing') }}">{{ __('auth.login.register_link') }}</a>
    </div>
</x-guest-layout>
