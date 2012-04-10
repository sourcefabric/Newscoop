{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

{{ assign var="userindex" value=1 }}

            <div class="title page-title">
            	<h2>RESET <span>PASSWORD</span></h2>
            </div>

            <section class="grid-6 extended-small">

{{ assign var="userindex" value=1 }}
<div class="form-content horizontal-form">
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
</div>
            </section><!-- / 6 articles grid -->
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>
