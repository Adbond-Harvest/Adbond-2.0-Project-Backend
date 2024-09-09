<x-mail::message>
# Email Verification

<x-mail::panel>
    Use the code below to verify your account <br>
    {{$code}} <br>
    <div>
        Please note that this token will expire in the next 30mins
    </div>
</x-mail::panel>

<!-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent -->

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
