<x-guest-layout>
    <h1 class="auth-title">{{ __('auth.forgot.title') }}</h1>
    <p class="auth-subtitle">{{ __('auth.forgot.subtitle') }}</p>

    @if (session('status'))
        <div class="auth-status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">{{ __('auth.forgot.email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-input"
                placeholder="{{ __('auth.forgot.email_placeholder') }}"
                required
                autofocus
            >
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            {{ __('auth.forgot.submit') }}
        </button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}">{{ __('auth.forgot.back') }}</a>
    </div>
</x-guest-layout>
