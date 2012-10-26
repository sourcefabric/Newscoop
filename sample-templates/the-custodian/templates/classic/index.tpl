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

{{ if !$gimme->url->is_valid }}
  <h3>{{ if $gimme->language->name == "English" }}The requested page was not found.{{ else }}La página solicitada no fue encontrada.{{ /if }}</h3>
  {{ set_language name=`$gimme->publication->default_language->english_name` }}
  {{ set_current_issue }}
{{ else }}

<!-- Column 1 start -->
<!-- section 10 -->
{{ local }}

{{ list_sections constraints="number smaller_equal 30" }}

{{ list_articles constraints="OnFrontPage is on" ignore_issue=true }}
{{ include file="classic/tpl/teaserframe_articlelistleft.tpl" }}
{{ /list_articles }}

{{ if $gimme->current_sections_list->index == 1 }}
<!-- Banner -->
{{ include file="classic/tpl/banner/bannerleftcol.tpl" }}
{{ /if }}

{{ /list_sections }}

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

{{ include file="classic/tpl/news-slider.tpl" }}

<h3>{{ if $gimme->language->name == "English" }}Other articles{{ else }}Otros artículos{{ /if }}</h3>

<!-- section 40 -->
{{ local }}
{{ set_issue number="1" }} 
{{ set_section number="40" }}

<!-- lists only articles from issues that are not current, because current issue articles of this section are already shown in the news slider -->

{{ list_articles constraints="OnFrontPage is on" }}
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