<!-- This is the issue template in CSS-->
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<meta name="generator" content="Campsite 2.1.6b3" /> <!-- leave this for stats -->

<style type="text/css" media="screen">
<!** include /look/fastnews/style.css.tpl>
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
<!** print publication name>
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
<!** print issue name>(#<!** print issue number>)<!** if issue iscurrent> (current issue)<!** endif></div>
<div class="small-sans-serif-text">on <!** date "%W, %M %e %Y"></div>
</div>
<!--ends the middle header column-->
<!--=============================-->
<!-- header-right-->
<div class="header-right">
<div class="small-sans-serif-text">
<!** include userinfo.tpl>
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
<!** List Section><li><a href="<!** URI reset_article_list>">
<!** Print Section Name></a>
<!** EndList Section>
<!-- End of sections menu -->
<br> </li> </div>

<!--=================================-->
<!-- This is the search box -->
<!--=================================-->

<div class="search-box">
<div class="medium-text">Search</div>
<!** Search search.tpl Search>
<div class="small-sans-serif-text"> <p>
<!** Edit Search keywords></p>
<p><!** Select Search mode> match all keywords</p>
<p><!** EndSearch></p>
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

<!** local>
<!** section off>
<!** List article OnFrontPage is on order bynumber desc>

<!** if List start>
<div class="small-sans-serif-text">
<p>List of front-page articles</p>
<p> </p>
The following articles were marked as 'Show article on front page' by the journalist. This template contains the 'List article OnFrontPage is on' command.
<!** endif>

<!** if article translated_to ro>
<div class="small-sans-serif-text"><p>This article is translated into Romanian
<!** endif></p></div>

<div class="article-name">
<a href="<!** URI reset_subtitle_list>">
<!** print article name></a></div>
<div class="small-serif-type">
<!** if article type extended>
<div class="small-sans-serif-type"><i><!** print article author></i>, <!** print article date "%W, %M %e, %Y"></div>
<!** endif>

<!** if article type fastnews>
<div class="small-sans-serif-type"><!** print article date "%W, %M %e, %Y"></div>
<!** endif>

<div class="deck">
<!** print article intro>

<!** if list end>
</div>
<!** endif>

<!** foremptylist>
<div class="error">
No articles to show on front page.
</div>
<!** EndList>
<!** endlocal>
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
<div class="small-sans-serif-text"><!** include footer.tpl></div>
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