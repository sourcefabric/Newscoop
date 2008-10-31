<div class="printbox" align=left>
<!-- PRINTBOX CODE -->

<p class="opt"><b>Printbox</b></p>
<script src="/printbox/printbox.js"></script>
<script src="/printbox/popup.js"></script>

<!** if article defined>
	<!** if allowed>
	<!--
	if you want to change the look of printbox, you 
	must change this template (for non-JS users) 
	AS WELL AS /printbox/printbox.js (for JS users) 
	-->
	<script>
	PB_displayLinks();
	</script>
	<noscript>
	<a href="/printbox/?action=add"><img width=16 height=16
	src="/printbox/add.gif" width=15 height=16 alt="[ADD]" border=0></a><a
	href="/printbox/"><img src="/printbox/show.gif" width=15 height=16
	alt="[SHOW]" border=0></a> (<a href="/printbox/?action=help">what's
	this?</a>)</p></noscript>

	<!** else>
	<script>
	PB_displayShowLink();
	</script>
	<noscript>
	<a
	href="/printbox/"><img src="/printbox/print.gif" width=16 height=16
	alt="[SHOW]" border=0></a> (<a href="/printbox/?action=help">what's
	this?</a>)</p></noscript>
	
	<p class=opt>Quickly print out some or all articles from the current edition of <!** print publication name>!</p>
	<!** endif>

<!** else>

<script>
PB_displayShowLink();
</script>
<noscript>
<a
href="/printbox/"><img src="/printbox/print.gif" width=16 height=16
alt="[SHOW]" border=0></a> (<a href="/printbox/?action=help">what's
this?</a>)</p></noscript>

<p class=opt>Quickly print out some or all articles from the current edition of <!** print publication name>!</p>

<!** endif>

<!-- END PRINTBOX CODE -->
</div>
<!** endif>