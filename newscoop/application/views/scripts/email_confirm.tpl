Hello,

please confirm your e-mail address by clicking on the link below:

http://{{ $site }}{{ $view->url(['user' => $user->identifier, 'token' => $token], 'confirm-email') }}

Thanks!
{{ set_placeholder subject=sprintf("E-mail confirmation at %s", $site) }}
