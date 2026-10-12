<x-layouts.guest title="Log in" heading="Welcome back" subheading="Log in to your account to continue.">
    <form method="POST" action="{{ route('login.store') }}" class="form">
        @csrf

        <x-input name="email" label="Email" type="email" required autofocus autocomplete="username" />

        <div>
            <x-input name="password" label="Password" type="password" required autocomplete="current-password" />
            <a href="{{ route('password.request') }}" class="link link--small field__aside">Forgot your password?</a>
        </div>

        <label class="checkbox">
            <input type="checkbox" name="remember" @checked(old('remember'))>
            <span>Remember me</span>
        </label>

        <button type="submit" class="btn btn--primary btn--block">Log in</button>
    </form>
</x-layouts.guest>
