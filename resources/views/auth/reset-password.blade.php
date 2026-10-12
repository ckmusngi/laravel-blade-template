<x-layouts.guest title="Reset password" heading="Choose a new password">
    <form method="POST" action="{{ route('password.store') }}" class="form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-input name="email" label="Email" type="email" :value="$email" required autocomplete="username" />
        <x-input name="password" label="New password" type="password" required autofocus autocomplete="new-password" />
        <x-input name="password_confirmation" label="Confirm password" type="password" required autocomplete="new-password" />

        <button type="submit" class="btn btn--primary btn--block">Reset password</button>
    </form>
</x-layouts.guest>
