{{ include file="classic/tpl/header.tpl" }}

<body id="section" class="section-{{ $gimme->section->number }}">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

{{ include file="classic/tpl/headernav.tpl" }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerleaderboard.tpl" }}

<div class="colmask rightmenu">
    <div class="colleft">
        <div class="col1wrap">
            <div class="col1">
                <!-- Column 1 start -->

{{ list_articles name="articles_left" constraints="OnSection is on" ignore_issue="true"}}
{{ include file="classic/tpl/teaserframe_articlelistleft.tpl" }}
{{ /list_articles }}

<!-- Banner -->
{{ include file="classic/tpl/banner/banner-leftcol-section.tpl" }}

        <!-- Column 1 end -->
            </div>
        </div>
        <div class="col2">
            <!-- Column 2 start -->

{{ include file="classic/tpl/search-box.tpl" }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerrightcol.tpl" }}

{{ list_articles name="articles_right" constraints="OnSection is off" ignore_issue="true" length="9" }}
{{ include file="classic/tpl/teaserframe_articlelistright.tpl" }}

{{ local }}
{{ unset_article }}
{{ include file="classic/tpl/pagination.tpl" }}
{{ /local }}

{{ /list_articles }}


      <!-- Column 2 end -->

        </div>
    </div>
</div>

{{ include file="classic/tpl/footer.tpl" }}

</div><!-- id="wrapbg"-->
</div><!-- id="wrapper"-->
</div><!-- id="container"-->
</body>
</html>