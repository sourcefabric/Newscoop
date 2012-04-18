Hello,

please confirm your e-mail address by clicking on the link below:

http://{{ $publication }}{{ $view->url(['user' => $user, 'token' => $token], 'confirm-email') }}

Thanks!
{{ $view->placeholder('subject')->set(sprintf("E-mail confirmation at %s", $site)) }}
