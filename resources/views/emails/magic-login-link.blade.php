@component('mail::message')
    Hey, here is the sign in link you requested. It can only be used once and expires after 24 hours.
    @component('mail::button', ['url' => $url])
        Sign in
    @endcomponent
@endcomponent
