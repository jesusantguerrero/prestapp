<div>
  Hello {{ $userName }}, to finish logging in please click the link below

  @component('mail::button', ['url' => $url])
    Click to login
  @endcomponent
</div>
