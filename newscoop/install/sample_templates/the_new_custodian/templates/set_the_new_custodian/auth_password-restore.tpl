{{extends file="layout.tpl"}}
{{block content}}
{{ assign var="userindex" value=1 }}
<header>
	<h3>{{ #resetPassword# }}</h3>
</header>
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <fieldset>
        {{ if $form->email->hasErrors() }}
        <div class="alert alert-error">
            <h5>{{ #emailNotCorrect# }}</h5>
            <p>{{ #maybeYouRegistered# }} <em>{{ $gimme->publication->name }}</em> {{ #withAnotherEmail# }}</p>
        </div>
        {{ /if }}
    </fieldset>
    <fieldset class="background-block login">
        <dl> {{ $form->email->setLabel("E-Mail")->removeDecorator('Errors') }}</dl>
        <div class="form-buttons right">
            <input type="submit" id="submit" class="button" value="{{ #requestNewPassword# }}" />
        </div>
    </fieldset>
</form>
{{/block}}
