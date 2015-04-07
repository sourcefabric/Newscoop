
{{'hello'|translate}}

{{'resetPassRestoreMessage'|translate}}

http://{{ $publication }}{{ $view->url(['controller' => 'auth', 'action' => 'password-restore-finish', 'user' => $user, 'token' => $token], 'default') }}

{{'thanks'|translate}}
{{ $view->placeholder('subject')->set(sprintf("{{'restorePasswordAt'|translate}} %s", $site)) }}