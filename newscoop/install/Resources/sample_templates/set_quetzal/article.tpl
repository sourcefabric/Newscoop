{{ config_load file="{{ $gimme->language->english_name }}.conf" }}
{{ include file="_tpl/_html-head.tpl" }}

<body>
<!--[if lt IE 7]>
    <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->
          
{{ include file="_tpl/header.tpl" }}

<section role="main" class="internal-page">
    <div class="wrapper">

        {{ include file="_tpl/article-header.tpl" }}

        <div class="container">
            <section id="content">
                <div class="row">

                {{ if $gimme->article->type_name == "debate" }}
                {{ include file="_tpl/article-debate.tpl" }}
                {{ else }}
                {{ include file="_tpl/article-cont.tpl" }}
                {{ /if }}

                {{ include file="_tpl/article-aside.tpl" }}          
                </div> <!--end div class="row"-->

                {{ include file="_tpl/tablet-more-tabs.tpl" }}          

            </section> <!-- end section id=content -->
        </div> <!-- end div class='container' -->
    </div> <!-- end div class='wrapper' -->
</section> <!-- end section role main -->

{{ include file="_tpl/footer.tpl" }}

{{ include file="_tpl/_html-foot.tpl" }}
