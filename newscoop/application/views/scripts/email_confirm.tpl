Hello,

please confirm your e-mail address via clicking on link below:

http://{{ $publication }}{{ $view->url(['user' => $user, 'token' => $token], 'confirm-email') }}

Thanks!
{{ $view->placeholder('subject')->set('Confirm Email') }}
