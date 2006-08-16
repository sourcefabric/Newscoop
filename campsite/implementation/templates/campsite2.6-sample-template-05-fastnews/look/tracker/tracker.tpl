<!-- campsite user tracking -->
<!** if article defined >
	<!** include tracker-article.tpl >
<!** else >
	<!** if section defined >
		<!** include tracker-section.tpl >
	<!** else >
		<!** include tracker-publication.tpl >
	<!** endif >
<!** endif >
<script language="JavaScript">
pot_client = 1;
cs_ref = document.referer;
cs_lang = '<!** print language number >';
</script>
<script language="JavaScript" src="http://campsitestats.jdpipe.dyndns.org/tracking.js"></script>
<noscript>
<img width="6" height="6" border="0" src="http://campsitestats.jdpipe.dyndns.org/image.php" />
</noscript>