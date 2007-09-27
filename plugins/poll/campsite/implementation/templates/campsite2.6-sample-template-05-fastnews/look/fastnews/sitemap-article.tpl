<td valign=top class="sitemap">

<!** include homelink.tpl>

<h4>Sections</h4>
<ul>
<!** List Section>
<!** if section fromstart>
	<li><b><a href="/look/fastnews/section.tpl?<!** URLParameters section reset_article_list>"><!** Print Section Name></a></b>
<!** else>
	<li><a href="/look/fastnews/section.tpl?<!** URLParameters section reset_article_list>"><!** Print Section Name></a>
<!** endif>
<!** EndList Section>
</ul>

<h4><!** print section name></h4>
<ul>
<!** List Article>
<!** if article fromstart>
	<li><!** print article name><!** if not allowed>&nbsp;<img src="/look/fastnews/subscriber.png" width=11 height=11" alt="[S]"><!**endif>
<!** else>
	<li><a href="<!** URI article reset_subtitle_list>"><!** Print Article Name></a><!** if not allowed>&nbsp;<img src="/look/fastnews/subscriber.png" width=11 height=11" alt="[S]"><!**endif>
<!** endif>
<!** EndList Article>
</ul>

<!** include search-sidebar.tpl>

<!** include archive-link.tpl>

<!** include rss-link.tpl>

</td>