{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>{{ #resetPassword# }}</h2>
            </div>

            <section class="grid-6 extended-small">

{{ assign var="userindex" value=1 }}
<div class="form-content horizontal-form">
<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <fieldset>
        {{ if $form->email->hasErrors() }}
        <div class="alert alert-error">
            <h5>{{ #emailNotCorrect# }}</h5>
            <p>{{ #maybeRegisteredOn# }} <em>{{ $gimme->publication->name }}</em> {{ #withAnotherEmail# }}</p>
        </div>
        {{ /if }}
    </fieldset>
    <fieldset class="background-block login">
        <dl> {{ $form->email->setLabel("{{ #email# }}")->removeDecorator('Errors') }}</dl>
        <div class="form-buttons right">
            <input type="submit" id="submit" class="button" value="{{ #requestNewPassword# }}" />
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
