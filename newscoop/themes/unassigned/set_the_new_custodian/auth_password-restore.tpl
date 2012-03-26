{{extends file="layout.tpl"}}
{{block content}}
{{ assign var="userindex" value=1 }}
<header>
	<h3>Reset Password</h3>
</header>
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <fieldset>
        {{ if $form->email->hasErrors() }}
        <div class="alert alert-error">
            <h5>E-mail is not correct</h5>
            <p>Maybe you registered on <em>{{ $gimme->publication->name }}</em> with another e-mail account?</p>
        </div>
        {{ /if }}
    </fieldset>
    <fieldset class="background-block login">
        <dl> {{ $form->email->setLabel("E-Mail")->removeDecorator('Errors') }}</dl>
        <div class="form-buttons right">
            <input type="submit" id="submit" class="button" value="Request new password" />
        </div>
    </fieldset>
</form>
{{/block}}
