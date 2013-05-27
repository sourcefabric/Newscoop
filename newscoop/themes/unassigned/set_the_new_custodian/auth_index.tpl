{{extends file="layout.tpl"}}

{{block content}}

{{ assign var="userindex" value=1 }}

<header>
	<h3>{{ #Login# }}</h3>
</header>
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <fieldset>
    {{ if $form->isErrors() }}
    <div class="alert alert-error">
        <h5>{{ #loginFailed# }}</h5>
        <p>{{ #somethingWrong# }}</p>
        <p>{{ #tryAgain# }}</p>
        <p><a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{ #forgotPassword# }}</a></p>
    </div>
    {{ /if }}
    </fieldset>
    <fieldset class="background-block login">
    <dl>
        {{ $form->email->setLabel("E-mail")->removeDecorator('Errors') }}
        {{ $form->password->setLabel("Password")->removeDecorator('Errors') }}
        <dt class="empty">&nbsp;</dt>
        <dd>
            <span class="input-info">
                <a class="register-link" href="{{ $view->url(['controller' => 'register', 'action' => 'index']) }}">{{ #Register# }}</a>
                <a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{ #forgotPassword# }}</a>
            </span>
        </dd>
    </dl>
    <div class="form-buttons right">
        <input type="submit" id="submit" class="button big" value="{{ #Login# }}" />
    </div>
    </fieldset>
</form>

{{/block}}
