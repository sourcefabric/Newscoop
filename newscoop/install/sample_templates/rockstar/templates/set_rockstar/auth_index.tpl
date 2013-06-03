{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>{{ #login# }}</h2>
            </div>

            <section class="grid-6 extended-small">
<div class="form-content horizontal-form">
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <fieldset>
    {{ if $form->isErrors() }}
    <div class="alert alert-error">
        <h5>{{ #loginFailed# }}</h5>
        <p>{{ #eitherEmailPassword# }}</p>
        <p>{{ #tryAgain# }}</p>
        <p><a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{ #forgotPassword# }}</a></p>
    </div>
    {{ /if }}
    </fieldset>
    <fieldset class="background-block login">
    <dl>
        {{ $form->email->setLabel("{{ #email# }}")->removeDecorator('Errors') }}
        {{ $form->password->setLabel("{{ #password# }}")->removeDecorator('Errors') }}
        <dt class="empty">&nbsp;</dt>
        <dd>
            <span class="input-info">
                <a class="register-link" href="{{ $view->url(['controller' => 'register', 'action' => 'index']) }}">{{ #register# }}</a>
                <a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{ #forgotPassword# }}</a>
            </span>
        </dd>
    </dl>
    <div class="form-buttons right">
        <input type="submit" id="submit" class="button big" value="{{ #login# }}" />
    </div>
    </fieldset>
</form>
</div>
            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
