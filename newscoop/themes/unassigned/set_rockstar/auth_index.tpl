{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>LOGIN <span> </span></h2>
            </div>

            <section class="grid-6 extended-small">
<div class="form-content horizontal-form">
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <fieldset>
    {{ if $form->isErrors() }}
    <div class="alert alert-error">
        <h5>Login failed</h5>
        <p>Either your email or password is wrong.</p>
        <p>Try again please!</p>
        <p><a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">Forgot your password?</a></p>
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
                <a class="register-link" href="{{ $view->url(['controller' => 'register', 'action' => 'index']) }}">Register</a>
                <a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">Forgot password?</a>
            </span>
        </dd>
    </dl>
    <div class="form-buttons right">
        <input type="submit" id="submit" class="button big" value="Login" />
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
