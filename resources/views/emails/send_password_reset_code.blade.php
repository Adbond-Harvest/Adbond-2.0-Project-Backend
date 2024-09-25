@component('mail::message')
# Password Reset

@component('mail::panel')
<h2>You have requested for a password reset</h2>

<div>Use the Code below to reset your password.</div>

<div><b>{{ $code }}</b></div><br>
<div>Please note that this token will expire in the next 1 hour.</div>
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
