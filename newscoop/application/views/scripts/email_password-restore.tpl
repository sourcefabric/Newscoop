Hello,

reset your password by clicking on the link below:

http://{{ $publication }}{{ $view->url(['controller' => 'auth', 'action' => 'password-restore-finish', 'user' => $user, 'token' => $token], 'default') }}

Thanks!
{{ $view->placeholder('subject')->set(sprintf("Restore password at %s", $site)) }}
