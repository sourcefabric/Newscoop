{{ extends file="layout.tpl" }}

{{ block content }}
{{ assign var="userindex" value=1 }}
<header>
	<h3>{{ #setNewPassword# }}</h3>
</header>
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
	<fieldset>
        {{ if $form->isErrors() }}
        <div class="alert alert-error">
            <p>{{ #couldNotChange# }}</p>
        </div>
        {{ /if }}
    </fieldset>
    <fieldset class="fixBackground background-block login">
        <dl>
                {{ $form->password->setLabel("{{ #newPassword# }}")->removeDecorator('Errors') }}
                {{ if $form->password->hasErrors() }}
                <dt class="info-block">&nbsp;</dt>
                <dd class="info-block">
                	<span class="error-info">{{ #enterNewPassword# }}</span>
                </dd>
                {{ /if }}
        </dl>
        <dl>
                {{ $form->password_confirm->setLabel("Retype your password")->removeDecorator('Errors') }}
                {{ if $form->password_confirm->hasErrors() && !$form->password->hasErrors() }}
                <dt class="info-block">&nbsp;</dt>
                <dd class="info-block">
                	<span class="error-info">{{ #doesNotMatch# }}</span>
                </dd>
                {{ /if }}
         </dl>

		<div class="form-buttons right">
            <input type="submit" id="submit" class="button" value="{{ #savePassword# }}" />
        </div>
    </fieldset>
    </form>

{{ /block }}
