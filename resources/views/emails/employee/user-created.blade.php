@component('mail::message')
# Your Account Information

Hello {{ $user->employee->full_name }},

Your account has been created.

**Username:** {{ $user->username }}<br>
**Temporary Password:** {{ $password }}

Please change your password after logging in.

@component('mail::button', ['url' => config('app.url')])
Login Now
@endcomponent

Best Regards,<br>
Administrator
@endcomponent
