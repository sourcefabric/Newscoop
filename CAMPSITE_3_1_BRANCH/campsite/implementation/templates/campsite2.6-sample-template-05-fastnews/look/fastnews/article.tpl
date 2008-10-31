<!** include htmlheader.tpl>

<!-- This is the article template -->

<html>
<head>
<title><!** print article name> (<!** print publication name>, #<!** print issue number>) <!** if issue iscurrent> -- current issue<!** endif></title>

<!** include meta.tpl>

</head>
<body>

<!** include header.tpl>

<!** include sitemap-article.tpl>

<td valign=top>

<div class="rightfloat">
<!** include userinfo.tpl>
<!** include printbox.tpl>
<!** include subnav.tpl>
<!** include topics.tpl>
</div>

<div style="max-width:42em;" align=left>
<!** if article type extended>
<!** include article-extended.tpl>
<!** endif>
<!** if article type fastnews>
<!** include article-fastnews.tpl>
<!** endif>
</div>
</td>

<!** include footer.tpl>
</body>
</html>
