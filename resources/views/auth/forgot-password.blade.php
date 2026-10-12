<x-layouts.guest
    title="Forgot password"
    heading="Forgot your password?"
    subheading="Enter your email and we'll send you a link to choose a new one."
>
    <form method="POST" action="{{ route('password.email') }}" class="form">
        @csrf

        <x-input name="email" label="Email" type="email" required autofocus autocomplete="username" />

        <button type="submit" class="btn btn--primary btn--block">Email reset link</button>
    </form>

    <p class="auth-card__footer">
        <a href="{{ route('login') }}" class="link">&larr; Back to log in</a>
    </p>
</x-layouts.guest>
