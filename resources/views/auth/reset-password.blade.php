<x-guest-layout>
    <h1 class="auth-title">{{ __('auth.reset.title') }}</h1>
    <p class="auth-subtitle">{{ __('auth.reset.subtitle') }}</p>

    <form method="POST" action="{{ route('password.store') }}" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email" class="form-label">{{ __('auth.reset.email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                class="form-input"
                required
                autofocus
                autocomplete="username"
            >
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">{{ __('auth.reset.password') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input"
                placeholder="{{ __('auth.reset.password_placeholder') }}"
                required
                autocomplete="new-password"
            >
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">{{ __('auth.reset.password_confirm') }}</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                placeholder="{{ __('auth.reset.password_confirm_placeholder') }}"
                required
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            {{ __('auth.reset.submit') }}
        </button>
    </form>
</x-guest-layout>
