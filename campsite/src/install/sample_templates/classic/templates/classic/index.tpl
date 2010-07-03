{{ include file="classic/tpl/header.tpl" }}
<body id="index">
<div id="container">
<div id="wrapbg">
<div id="wrapper">

{{ include file="classic/tpl/headernav.tpl" is_index=true }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerleaderboard.tpl" }}

<div class="colmask rightmenu">
    <div class="colleft">
        <div class="col1wrap">
            <div class="col1">

{{ if !$campsite->url->is_valid }}
	<h3>The requested page was not found.</h3>
{{ else }}

<!-- Column 1 start -->
<!-- section 10 -->
{{ local }}
{{ set_section number=10 }}
{{ list_articles name="articles" constraints="OnFrontPage is on" ignore_issue=true }}
{{ include file="classic/tpl/teaserframe_articlelistleft.tpl" }}
{{ /list_articles }}
{{ /local }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerleftcol.tpl" }}

<!-- section 20 -->
{{ local }}
{{ set_section number=20 }}
{{ list_articles name="articles" constraints="OnFrontPage is on" ignore_issue=true }}
{{ include file="classic/tpl/teaserframe_articlelistleft.tpl" }}
{{ /list_articles }}
{{ /local }}
<!-- section 30 -->
{{ local }}
{{ set_section number=30 }}
{{ list_articles name="articles" constraints="OnFrontPage is on" ignore_issue=true }}
{{ include file="classic/tpl/teaserframe_articlelistleft.tpl" }}
{{ /list_articles }}
{{ /local }}

<!-- Column 1 end -->

{{ /if }}

            </div>
        </div>
        <div class="col2">

<!-- Column 2 start -->

{{ include file="classic/tpl/search-box.tpl" }}
{{* include file="classic/tpl/blog/entrylistright.tpl" *}}
{{ include file="classic/tpl/poll/latestpoll.tpl" }}

<!-- section 40 -->
{{ local }}
{{ set_section number="40" }}
{{ list_articles name="articles" constraints="OnFrontPage is on" ignore_issue=true }}
{{ include file="classic/tpl/teaserframe_articlelistright.tpl" }}
{{ /list_articles }}
{{ /local }}

<!-- Banner -->
{{ include file="classic/tpl/banner/bannerrightcol.tpl" }}

<!-- section 50 -->
{{ local }}
{{ set_section number="50" }}
{{ list_articles name="articles" constraints="OnFrontPage is on" ignore_issue=true }}
{{ include file="classic/tpl/teaserframe_articlelistright.tpl" }}
{{ /list_articles }}
{{ /local }}
<!-- section 60 -->
{{ local }}
{{ set_section number="60" }}
{{ list_articles name="articles" constraints="OnFrontPage is on" ignore_issue=true }}
{{ include file="classic/tpl/teaserframe_articlelistright.tpl" }}
{{ /list_articles }}
{{ /local }}

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