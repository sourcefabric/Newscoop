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
                            <h2>{{ #welcome# }} {{ $user->name }}</h2>
                        </div>
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
                    <div class="span4 visible-desktop">
                        <center>
                        <span class="label">{{ #currentAvatar# }}</span><br>
                            <figure class="user-image">
                                <img src="{{ include file="_tpl/user-image.tpl" user=$user width=140 height=210 }}" style="max-width: 100%" rel="resizable" />
                            </figure>
                        </center>
                    </div>
                    <div class="span8 hidden-desktop">
                        <center>
                        <span class="label">{{ #currentAvatar# }}</span><br>
                            <figure class="user-image">
                                <img src="{{ include file="_tpl/user-image.tpl" user=$user width=175 height=210 }}" style="max-width: 100%" rel="resizable" />
                            </figure>
                        </center>
                    </div>
                    <div class="span8">
                        <div class="quetzal-form well">
                            <link rel="stylesheet" href="{{ url static_file="_css/datepicker.css"}}">
                            <script src="{{ url static_file='_js/vendor/bootstrap-datepicker.js'}}"></script>
                            <script type="text/javascript">
                                $(function() {
                                     // Date picker
                                     $('#attributes-birth_date').datepicker({
                                        format:'yyyy/mm/dd',
                                        startView:2,
                                        autoclose:'true'
                                     });
                                });
                            </script>
                            {{ $form }}
                        </div>
                    </div>

                </div> <!--end div class="row"-->
            </section> <!-- end section id=content -->
        </div> <!-- end div class='container' -->
    </div> <!-- end div class='wrapper' -->
</section> <!-- end section role main -->

{{ include file="_tpl/footer.tpl" }}

{{ include file="_tpl/_html-foot.tpl" }}

