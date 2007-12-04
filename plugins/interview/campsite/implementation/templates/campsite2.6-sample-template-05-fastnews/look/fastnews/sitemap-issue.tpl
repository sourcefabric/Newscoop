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

<!** include search-sidebar.tpl>

<!** include archive-link.tpl>

<!** include rss-link.tpl>

</td>