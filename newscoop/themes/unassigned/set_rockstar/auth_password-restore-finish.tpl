{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>SET NEW <span>PASSWORD</span></h2>
            </div>

            <section class="grid-6 extended-small">

{{ assign var="userindex" value=1 }}

<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
	<fieldset>
        {{ if $form->isErrors() }}
        <div class="alert alert-error">
            <p>Your password could not be changed. Please follow the instructions and try again.</p>
        </div>
        {{ /if }}
    </fieldset>
    <fieldset class="fixBackground background-block login">
        <dl>
                {{ $form->password->setLabel("Neues Passwort")->removeDecorator('Errors') }}
                {{ if $form->password->hasErrors() }}
                <dt class="info-block">&nbsp;</dt>
                <dd class="info-block">
                	<span class="error-info">Please enter your new password (minimum 6 characters)</span>
                </dd>
                {{ /if }}
        </dl>
        <dl>
                {{ $form->password_confirm->setLabel("Retype your password")->removeDecorator('Errors') }}
                {{ if $form->password_confirm->hasErrors() && !$form->password->hasErrors() }}
                <dt class="info-block">&nbsp;</dt>
                <dd class="info-block">
                	<span class="error-info">The confirmation of your password does not match your password.</span>
                </dd>
                {{ /if }}
         </dl>

		<div class="form-buttons right">
            <input type="submit" id="submit" class="button" value="Save password" />
        </div>
    </fieldset>
    </form>

            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
