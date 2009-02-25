<!-- This is the issue template -->
<html>
<body bgcolor=white>
{{ include file="fastnews/header.tpl" }}
<div align="center">
<table bgcolor=white width=800>
<tr bgcolor=white>
	<td align=center><h1>{{ $campsite->publication->name }}</h1></td>
	<td><h2>{{ $campsite->issue->name }} (#{{ $campsite->issue->number }}) {{ if $campsite->issue->is_current }} (current issue){{ /if }}</h2> on {{ $smarty.now|camp_date_format:"%W, %M %e %Y" }}</td>
	<td align=right>{{ include file="fastnews/userinfo.tpl" }}</td>
</tr>
<tr bgcolor=white><td colspan=3><hr></td></tr>
<tr>
	<td width="20%" valign=top>
	<table bgcolor=#eaeaea width="100%" bgcolor=#eaeaea>
	<tr><td align=center width="20%"><h3>Sections</h3></td></tr>
	<tr><td><p>
<!-- This is the menu for sections -->
	{{ list_sections }}
	<li><a href="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</a>
	{{ /list_sections }}
<!-- End of sections menu -->
	</td></tr>
	<tr><td><hr size=1 noshade></td>
	<tr>
		<td>
		<b>Search articles</b>
		{{ search_form template="search.tpl" submit_button="Search" }}
			<p>{{ camp_edit object="search" attribute="keywords" }} <p>{{ camp_select object="search" attribute="mode" }} match all keywords<p>
		{{ /search_form }}
		</td>
	</tr>
	</table>
	</td>
	<td colspan=2>
<!-- This is the presentation of issue -->
{{ local }}
{{ unset_section }}
{{ list_articles constraints="OnFrontPage is on" order="bynumber desc" }}
	{{ if $campsite->current_list->at_beginning }}
	<p>List of front-page articles<p>
	<p><font color=red>The following articles were marked as 'Show article on front page' 
		by the journalist. This template contains the 'List article OnFrontPage is on'
		command.</font></p>
		<ul>
		<ul>
	{{ /if }}
	{{ if $campsite->article->translated_to == "ro" }}
	<p>This article is translated into Romanian
	{{ /if }}
	<li><b><a href="{{ uri options="reset_subtitle_list" }}">
	{{ $campsite->article->name }}</a></b><br>
		<font size=-1 face="arial">
	{{ if $campsite->article->type_name == "extended" }}
		<i>{{ $campsite->article->author }}</i>, {{ $campsite->article->date|camp_date_format:"%W, %M %e, %Y" }}
	{{ /if }}
	{{ if $campsite->article->type_name == "fastnews" }}
		{{ $campsite->article->date|camp_date_format:"%W, %M %e, %Y" }}
	{{ /if }}
		</font>
	<br>{{ $campsite->article->intro }}<br><br>
	{{ if $campsite->current_list->at_end }}
		</ul>
	{{ /if }}
	{{ /list_articles }}
{{ if $campsite->prev_list_empty }}
	<p>No articles to show on frontpage
{{ /if }}
{{ /local }}
<!-- End of Issue presentation -->
	</td>
</tr>
</table>
</div>
{{ include file="fastnews/footer.tpl" }}
</body>
</html>
