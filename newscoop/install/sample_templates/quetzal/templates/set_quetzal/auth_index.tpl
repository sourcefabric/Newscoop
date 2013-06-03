{{ config_load file="{{ $gimme->language->english_name }}.conf" }}
{{ include file="_tpl/_html-head.tpl" }}

<body>
<!--[if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->
 
{{ include file="_tpl/header.tpl" }}
<section role="main" class="internal-page section-page">
    <div class="wrapper">

        <header class="section-header">
            <div class="container">
                <div class="row">
                    <div class="span10">
                        <div class="breadcrumbs">
                            <h2>{{ #login# }}</h2>
                        </div>
                    </div>
                    <div class="span2 section-rss">

                    </div>
                </div> 
                <div class="row">
                    <div class="span12 more-news-tabs tab-sections">
                        <a class="back-link visible-phone" href="javascript:history.back()">&larr; {{ #back# }}</a>
                    </div>
                </div>                       
            </div>
        </header>

        <div class="container">
            <section id="content">
                <div class="row home-featured-news">
                    <div class="span8 auth-page">
                        <div class="quetzal-form">
                            <form action="{{ $form->getAction() }}" class="zend_form" method="{{ $form->getMethod() }}">
                                {{ if $form->isErrors() }}
                                <div class="alert alert-error">
                                    <h5>{{ #loginFailed# }}</h5>
                                    <p>{{ #loginFailedMessage# }}</p>
                                    <p>{{ #tryAgainPlease# }}</p>
                                    <p><a class="register-link" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{ #forgotYourPassword# }}</a></p>
                                </div>
                                {{ /if }}
                                <dl>
                                    {{ $form->email->setLabel("E-mail")->removeDecorator('Errors') }}
                                    {{ $form->password->setLabel("Password")->removeDecorator('Errors') }}
                                    <dt class="empty">&nbsp;</dt>
                                    <dd>
                                        <span class="input-info">
                                            <a class="register-link link-color" href="{{ $view->url(['controller' => 'register', 'action' => 'index']) }}">Register | </a>
                                            <a class="register-link link-color" href="{{ $view->url(['controller' => 'auth', 'action' => 'password-restore']) }}">{{ #forgotYourPassword# }}</a>
                                        </span>
                                    </dd>
                                </dl>
                                <div class="form-buttons right">
                                    <input type="submit" id="submit" class="button big" value="{{ #login# }}" />
                                </div>
                            </form>
                        </div>
                    </div> 
                    {{ include file="_tpl/user-sidebar.tpl" }}          

                </div> <!--end div class="row"-->
            </section> <!-- end section id=content -->
        </div> <!-- end div class='container' -->
    </div> <!-- end div class='wrapper' -->
</section> <!-- end section role main -->

{{ include file="_tpl/footer.tpl" }}

{{ include file="_tpl/_html-foot.tpl" }}
