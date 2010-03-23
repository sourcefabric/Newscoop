<!-- campsite user tracking -->
{{ if $campsite->article->defined }}
	{{ include file="tracker/tracker-article.tpl" }}
{{ else }}
	{{ if $campsite->section->defined }}
		{{ include file="tracker/tracker-section.tpl" }}
	{{ else }}
		{{ include file="tracker/tracker-publication.tpl" }}
	{{ /if }}
{{ /if }}
<script language="JavaScript">
pot_client = 1;
cs_ref = document.referer;
cs_lang = '{{ $campsite->language->number }}';
</script>
<script language="JavaScript" src="http://campsitestats.jdpipe.dyndns.org/tracking.js"></script>
<noscript>
<img width="6" height="6" border="0" src="http://campsitestats.jdpipe.dyndns.org/image.php" />
</noscript>