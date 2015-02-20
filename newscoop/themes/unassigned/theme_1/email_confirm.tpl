{{'mailHello'|translate}}

{{'mailAutomaticalMessage'|translate}} {{ $gimme->publication->name }} ({{ $gimme->publication->site }})

{{'mailPleaseConfirm'|translate}}

http://{{ $publication }}{{ $view->url(['user' => $user, 'token' => $token], 'confirm-email') }}

{{'mailOtherwise'|translate}}

{{'mailThanks'|translate}}

{{ $view->placeholder('subject')->set(sprintf("{{'emailConfirmationAt'|translate}} %s", $site)) }}