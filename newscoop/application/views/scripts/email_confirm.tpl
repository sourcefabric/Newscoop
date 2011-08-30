Hello {{ $user }},

please confirm your e-mail address via clicking on link below:

http://{{ $publication }}{{ $view->url(['user' => $user->identifier, 'token' => $token], 'confirm-email') }}

Thanks!
