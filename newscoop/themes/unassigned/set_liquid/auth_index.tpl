{{if isset($smarty.post.ajax) }}
{{ if $form->isErrors() }}ERROR{{/if}}
{{else}}

{{ include file="_tpl/_html-head.tpl" }}


    {{ include file="_tpl/header.tpl" }}


<div id="page" class="container">

    <!-- Content -->
    <section id="content">



{{ assign var="userindex" value=1 }}

<div class="article_content bloger content_text white-box">
  <div class="clearfix">





 <h3 class="normal_header">{{'Login'|translate}}</h3>

<form action="{{ $form->getAction() }}" method="{{ $form->getMethod() }}">
    <fieldset>
    {{ if $form->isErrors() }}
    <div class="alert alert-error">
        <h5>{{'loginFailed'|translate}}</h5>
        <p>{{'somethingWrong'|translate}}</p>
        <p>{{'tryAgain'|translate}}</p>
        <p><a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{'forgotPassword'|translate}}</a></p>
    </div>
    {{ /if }}
    </fieldset>
    <fieldset class="background-block login">
    <dl>
        {{ $form->email->setLabel("{{'email'|translate}}")->removeDecorator('Errors') }}
        {{ $form->password->setLabel("{{'password'|translate}}")->removeDecorator('Errors') }}
        <dt class="empty">&nbsp;</dt>
        <dd>
            <span class="input-info">

                <a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{'forgotPassword'|translate}}</a><br />
                <a class="register-link" href="{{ $view->url(['controller' => 'register', 'action' => 'index']) }}">{{'Register'|translate}}</a>
            </span>
        </dd>
    </dl>
    <div class="form-buttons right">
        <input type="submit" id="submit" class="button big" value="{{'Login'|translate}}" />
    </div>
    </fieldset>
</form>

</div>
</div>


    </section>
    <!-- End Content -->




</div>


{{ include file="_tpl/footer.tpl" }}
{{ include file="_tpl/_html-foot.tpl" }}

{{/if}}