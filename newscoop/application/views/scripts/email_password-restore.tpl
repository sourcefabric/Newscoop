Hello,

reset your password by clicking on the link below:

http://{{ $site }}{{ $view->url(['controller' => 'auth', 'action' => 'password-restore-finish', 'user' => $user->identifier, 'token' => $token], 'default') }}

Thanks!
{{ set_placeholder subject=sprintf("Restore password at %s", $site) }}
