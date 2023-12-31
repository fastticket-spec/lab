<x-mail::message>
### Hello {{$name}},

You requested a password reset. Your reset token is.

## {{$resetToken}}

This token will expire in 10 minutes.

If you didn't request for a password reset, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
