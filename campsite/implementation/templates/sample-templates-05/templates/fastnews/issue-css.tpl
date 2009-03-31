<!-- This is the issue template in CSS-->
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<meta name="generator" content="Campsite 2.1.6b3" /> <!-- leave this for stats -->

<style type="text/css" media="screen">
<!** include /tpl/fastnews/style.css.tpl>
</style>

</head>

<!--=========================================-->
<!-- Enough with the header info, here comes the HTML ! -->
<!--=========================================-->
<body>
<div class="whole-page"><!--spans the whole page-->
<div class="header-container"><!--container for the whole header-->
<div class="header-left">
<div class="big-text">
{{ $campsite->publication->name }}
</div>
</div>
<!--ends the left header column-->
<!--============================-->
<!--makes a container for the right-aligned options on the header bar-->
<div class="header-right-container">
<!--============================-->
<div class="header-middle">
<!--the middle header column-->
<div class="medium-text">
{{ $campsite->issue->name }}(#{{ $campsite->issue->number }}){{ if $campsite->issue->is_current }} (current issue){{ /if }}</div>
<div class="small-sans-serif-text">on {{ $smarty.now|camp_date_format:"%W, %M %e %Y" }}</div>
</div>
<!--ends the middle header column-->
<!--=============================-->
<!-- header-right-->
<div class="header-right">
<div class="small-sans-serif-text">
{{ include file="fastnews/userinfo.tpl" }}
</div>
</div><!--ends header-right-->
</div><!--ends the right-container-->
<!--=======================-->
</div><!--end header-container -->


<!--=======================-->
<div class="clear"> </div>
<!--clears column info for next part, just to be sure...--> <!--================================-->

<!--=====================BEGIN MAIN LAYOUT==============-->
<div class="main-container">
<div class="main-left-column">
<div class="medium-text">Sections</div>
<div class="small-sans-serif-text">
<!-- This is the menu for sections -->
{{ list_sections }}<li><a href="{{ uri options="reset_article_list" }}">
{{ $campsite->section->name }}</a>
{{ /list_sections }}
<!-- End of sections menu -->
<br> </li> </div>

<!--=================================-->
<!-- This is the search box -->
<!--=================================-->

<div class="search-box">
<div class="medium-text">Search</div>
{{ search_form template="search.tpl" submit_button="Search" }}
<div class="small-sans-serif-text"> <p>
{{ camp_edit object="search" attribute="keywords" }}</p>
<p>{{ camp_select object="search" attribute="mode" }} match all keywords</p>
<p>{{ /search_form }}</p>
</div>
</div>
<!--end of search box--> 
</div> 
<!--end of main left column-->
<!--=============================-->

<!--=================================-->
<!--main middle column-->
<!--=================================-->

<div class="main-middle-column">

<!-- This is the presentation of issue -->

{{ local }}
{{ unset_section }}
{{ list_articles constraints="OnFrontPage is on" order="bynumber desc" }}

{{ if $campsite->current_list->at_beginning }}
<div class="small-sans-serif-text">
<p>List of front-page articles</p>
<p> </p>
The following articles were marked as 'Show article on front page' by the journalist. This template contains the 'List article OnFrontPage is on' command.
{{ /if }}

{{ if $campsite->article->translated_to("ro") }}
<div class="small-sans-serif-text"><p>This article is translated into Romanian
{{ /if }}</p></div>

<div class="article-name">
<a href="{{ uri options="reset_subtitle_list" }}">
{{ $campsite->article->name }}</a></div>
<div class="small-serif-type">
{{ if $campsite->article->type_name == "extended" }}
<div class="small-sans-serif-type"><i>{{ $campsite->article->author }}</i>, {{ $campsite->article->date|camp_date_format:"%W, %M %e, %Y" }}</div>
{{ /if }}

{{ if $campsite->article->type_name == "fastnews" }}
<div class="small-sans-serif-type">{{ $campsite->article->date|camp_date_format:"%W, %M %e, %Y" }}</div>
{{ /if }}

<div class="deck">
{{ $campsite->article->intro }}

{{ if $campsite->current_list->at_end }}
</div>
{{ /if }}

{{ /list_articles }}
{{ if $campsite->prev_list_empty }}
<div class="error">
No articles to show on front page.
</div>
{{ /if }}
{{ /local }}
</div>

<!-- End of Issue presentation -->
<!-- End main-middle-column -->
<!--=============================-->
<!-- =======END OF MAIN PART ======-->
<!--=====================================-->
<div class="clear"> </div>
<!--=====================================-->

<!--===========FOOTER===============-->
<div class="footer-container">
<div class="small-sans-serif-text">{{ include file="fastnews/footer.tpl" }}</div>
</div>
</div>

<div class="clear"> </div>
<br>
<br>
<br>
<br>
</div>

</body>
</html>